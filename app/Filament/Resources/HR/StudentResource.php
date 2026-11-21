<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\StudentResource\Pages;
use App\Models\Student;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected static ?string $model = Student::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    public static function getNavigationLabel(): string { return __('Student Management'); }
    public static function getModelLabel(): string { return __('Student'); }
    public static function getPluralModelLabel(): string { return __('Student'); }
    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }
    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
                \Filament\Schemas\Components\Section::make(__('Student Data'))
                    ->schema([
                        Forms\Components\TextInput::make('nis')
                            ->label(__('No. Siswa (NIS)'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        Forms\Components\Select::make('student_type')
                            ->label(__('Student Type'))
                            ->options([
                                'internal' => __('Internal'),
                                'external' => __('External'),
                            ])
                            ->default('internal')
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label(__('Full Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('gender')
                            ->label(__('Gender'))
                            ->options([
                                'L' => __('Male'),
                                'P' => __('Female'),
                            ]),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label(__('Date of Birth')),
                        Forms\Components\Textarea::make('address')
                            ->label(__('Address'))
                            ->rows(3),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('Phone No.'))
                            ->tel(),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make(__('Parent Data'))->schema([
                    \Filament\Schemas\Components\Fieldset::make(__('Mother\'s Data'))->schema([
                        Forms\Components\TextInput::make('mother_name')
                            ->label(__('Mother\'s Name')),
                        Forms\Components\TextInput::make('mother_phone')
                            ->label(__('Mother\'s Phone'))
                            ->tel(),
                        Forms\Components\TextInput::make('mother_email')
                            ->label(__('Mother\'s Email'))
                            ->email(),
                        Forms\Components\TextInput::make('mother_job')
                            ->label(__('Mother\'s Occupation')),
                    ])->columns(2),
                    \Filament\Schemas\Components\Fieldset::make(__('Father\'s Data'))->schema([
                        Forms\Components\TextInput::make('father_name')
                            ->label(__('Father\'s Name')),
                        Forms\Components\TextInput::make('father_phone')
                            ->label(__('Father\'s Phone'))
                            ->tel(),
                        Forms\Components\TextInput::make('father_email')
                            ->label(__('Father\'s Email'))
                            ->email(),
                        Forms\Components\TextInput::make('father_job')
                            ->label(__('Father\'s Occupation')),
                    ])->columns(2),
                ]),

                \Filament\Schemas\Components\Section::make(__('Status'))
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
                            ->directory('students')
                            ->maxSize(2048)
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
                        

                        

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Active'))
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                Tables\Columns\ImageColumn::make('photo')->label(__('Photo'))->circular()->disk('public')
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=2563eb&background=dbeafe'),
                Tables\Columns\TextColumn::make('user.username')
                    ->label(__('No. Siswa'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))->translateLabel()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student_type')
                    ->label(__('Type'))
                    ->badge()
                    ->color(fn (?string $state): string => $state === 'external' ? 'warning' : 'info')
                    ->formatStateUsing(fn (?string $state): string => $state === 'external' ? __('External') : __('Internal')),
                Tables\Columns\TextColumn::make('gender')
                    ->label(__('Gender'))
                    ->badge()
                    ->color(fn (string $state): string => $state === 'L' ? 'info' : 'danger'),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent_name')
                    ->label(__('Parent'))
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Registered'))
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ])->from('md'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('student_type')
                    ->label(__('Student Type'))
                    ->options([
                        'internal' => __('Internal'),
                        'external' => __('External'),
                    ]),
                Tables\Filters\SelectFilter::make('gender')
                    ->label(__('Gender'))
                    ->options([
                        'L' => __('Male'),
                        'P' => __('Female'),
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('Active Status')),
            ])
            ->actions([
                \Filament\Actions\Action::make('certificate')
                    ->label(__('Certificate'))
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->url(fn ($record) => route('report.certificate', $record->id))
                    ->openUrlInNewTab(),
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['user', 'classes', 'parents']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
        ];
    }
}



