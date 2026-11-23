<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\SchoolResource\Pages;
use App\Mail\SchoolAccountCredentialsMail;
use App\Models\Admission;
use App\Models\Student;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SchoolResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $slug = 'h-r/schools';

    public static function getNavigationLabel(): string
    {
        return __('Manage Schools');
    }

    public static function getModelLabel(): string
    {
        return __('School');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Manage Schools');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Master Data');
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'school')
            ->withCount('schoolStudents');
    }

    public static function generateNextSchoolCode(): string
    {
        $maxSeq = User::where('role', 'school')
            ->whereNotNull('username')
            ->pluck('username')
            ->map(fn ($u): int => preg_match('/^48(\d{3})$/', (string) $u, $m) ? (int) $m[1] : 0)
            ->max() ?? 0;

        $nextSeq = $maxSeq + 1;
        do {
            $code = '48' . str_pad((string) $nextSeq, 3, '0', STR_PAD_LEFT);
            $nextSeq++;
        } while (User::where('username', $code)->exists());

        return $code;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make(__('School Data'))->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('School Name'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('username')
                    ->label(__('School Code / Username'))
                    ->placeholder('48xxx (otomatis jika kosong)')
                    ->maxLength(50)
                    ->unique(table: User::class, column: 'username', ignoreRecord: true),
                Forms\Components\TextInput::make('email')
                    ->label(__('Email'))->translateLabel()
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(table: User::class, column: 'email', ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->label(__('Password'))->translateLabel()
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('role')
                    ->default('school'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->recordAction('edit')
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->label('Kode')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('School Name'))
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->description(fn (User $record): string => (string) ($record->email ?? '-'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('school_students_count')
                    ->label('Siswa')
                    ->badge()
                    ->color(fn ($state): string => (int) $state > 0 ? 'success' : 'gray')
                    ->suffix(' siswa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))->translateLabel()
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                EditAction::make()
                    ->button()
                    ->size('sm')
                    ->slideOver()
                    ->modalWidth('2xl')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['role'] = 'school';
                        if (blank($data['username'] ?? null)) {
                            $data['username'] = self::generateNextSchoolCode();
                        }

                        return $data;
                    })
                    ->after(function (User $record): void {
                        Student::where('school_user_id', $record->id)->update(['school_name' => $record->name]);
                        Admission::where('school_user_id', $record->id)->update(['school_name' => $record->name]);
                    }),
                ActionGroup::make([
                    Action::make('resend_credentials')
                        ->label('Reset & Kirim Akun')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Reset Password & Kirim Akun Sekolah?')
                        ->modalDescription(fn (User $record): string => "Password baru akan dibuat dan dikirim ke {$record->email}.")
                        ->action(function (User $record): void {
                            if (blank($record->email)) {
                                Notification::make()
                                    ->title('Email sekolah belum diisi')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $plainPassword = Str::upper(Str::random(6));
                            $record->update(['password' => Hash::make($plainPassword)]);

                            $participants = Student::where('school_user_id', $record->id)
                                ->orderBy('class_name')
                                ->orderBy('name')
                                ->get()
                                ->map(fn (Student $s): array => [
                                    'name' => $s->name,
                                    'nisn' => $s->nisn,
                                    'grade' => $s->class_name,
                                    'code' => $s->competition_code ?: $s->nis,
                                ])
                                ->all();

                            Mail::to($record->email)->send(new SchoolAccountCredentialsMail(
                                schoolName: $record->name,
                                coordinatorName: $record->name,
                                loginUrl: url('/school/login'),
                                username: (string) $record->username,
                                plainPassword: $plainPassword,
                                participants: $participants,
                            ));

                            Notification::make()
                                ->title("Akun terkirim ke {$record->email}")
                                ->body("Username: {$record->username} | Password baru: {$plainPassword}")
                                ->success()
                                ->send();
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchools::route('/'),
        ];
    }
}
