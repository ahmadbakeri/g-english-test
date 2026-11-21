<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    public static function getNavigationLabel(): string { return __('Employee Management'); }
    public static function getModelLabel(): string { return __('Employee / User'); }
    public static function getPluralModelLabel(): string { return __('Employees'); }
    public static function getNavigationGroup(): ?string
    {
        return __('Master Data');
    }
    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function canCreate(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            \Filament\Schemas\Components\Section::make(__('Employee Data'))->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))->translateLabel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label(__('Email'))->translateLabel()
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->label(__('Password'))->translateLabel()
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Select::make('role')
                    ->label(__('Role'))
                    ->options([
                        'admin' => 'Administrator',
                        'karyawan' => 'Karyawan Biasa',
                        'tutor' => 'Tutor',
                    ])
                    ->default('karyawan')
                    ->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label(__('Role'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'tutor' => 'success',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))->translateLabel()
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ])->from('md'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}


