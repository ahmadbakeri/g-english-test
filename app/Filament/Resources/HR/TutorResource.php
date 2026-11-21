<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\TutorResource\Pages;
use App\Models\Tutor;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TutorResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected static ?string $model = Tutor::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    public static function getNavigationLabel(): string { return __('Tutor Management'); }
    public static function getModelLabel(): string { return __('Tutor'); }
    public static function getPluralModelLabel(): string { return __('Tutors'); }
    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }
    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            \Filament\Schemas\Components\Section::make(__('Tutor Data'))
                ->description('Identitas pengajar, nomor induk (NIP), spesialisasi, dan kualifikasi grade honor.')
                ->icon('heroicon-o-academic-cap')
                ->schema([
                    Forms\Components\TextInput::make('nip')
                        ->label(__('NIP'))
                        ->prefixIcon('heroicon-o-identification')
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('name')
                        ->label(__('Full Name'))
                        ->required(),
                    Forms\Components\TextInput::make('phone')
                        ->label(__('Phone No.'))
                        ->prefixIcon('heroicon-o-phone')
                        ->tel(),
                    Forms\Components\TextInput::make('specialization')
                        ->label(__('Specialization'))
                        ->placeholder('Contoh: Speaking / TOEFL / Kids'),
                    Forms\Components\Select::make('grade')
                        ->label(__('Grade'))
                        ->options([
                            'A' => 'Grade A',
                            'B' => 'Grade B',
                            'C' => 'Grade C',
                        ])
                        ->default('C')
                        ->required(),
                    Forms\Components\Toggle::make('is_active')
                        ->label(__('Active'))
                        ->default(true),
                ])->columns(2),

            \Filament\Schemas\Components\Section::make('Foto Profil Pengajar')
                ->description('Unggah foto resmi tutor untuk ditampilkan pada kartu pengajar dan laporan.')
                ->icon('heroicon-o-user-circle')
                ->schema([
                    Forms\Components\FileUpload::make('photo')
                        ->label(__('Photo'))
                        ->avatar()
                        ->alignCenter()
                        ->columnSpanFull()
                        ->extraAttributes(['class' => 'flex justify-center text-center hide-edit-btn'])
                        ->circleCropper()
                        ->imageCropAspectRatio('1:1')
                        ->automaticallyOpenImageEditorForAspectRatio()
                        ->disk('public')
                        ->directory('tutors')
                        ->hintAction(
                            \Filament\Actions\Action::make('remove_photo')
                                ->label(__('Remove (Reset)'))
                                ->icon('heroicon-o-trash')
                                ->color('danger')
                                ->action(function ($record, callable $set) {
                                    $set('photo', null);
                                    if ($record) {
                                        $record->photo = null;
                                        $record->save();
                                    }
                                })
                        ),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            \Filament\Tables\Columns\Layout\Split::make([
                Tables\Columns\ImageColumn::make('photo')
                    ->label(__('Photo'))
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=0a4282&background=dbeafe'),
                Tables\Columns\TextColumn::make('nip')
                    ->label(__('NIP'))
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->translateLabel()
                    ->weight('bold')
                    ->description(fn (Tutor $record): string => $record->phone ?: 'Tanpa No. HP')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('specialization')
                    ->label(__('Specialization'))
                    ->badge()
                    ->color('info')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('grade')
                    ->label(__('Grade'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => "Grade {$state}")
                    ->color(fn (string $state): string => match ($state) {
                        'A' => 'success',
                        'B' => 'warning',
                        'C' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->placeholder('-'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
            ])->from('md'),
        ])
        ->actions([
            \Filament\Actions\ActionGroup::make([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->tooltip('Aksi'),
        ])
        ->bulkActions([
            \Filament\Actions\BulkActionGroup::make([
                \Filament\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['user']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTutors::route('/'),
        ];
    }
}
