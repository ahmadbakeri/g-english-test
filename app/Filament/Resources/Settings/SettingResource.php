<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected static ?string $model = Setting::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    public static function getNavigationLabel(): string { return __('Settings'); }
    public static function getModelLabel(): string { return __('Settings'); }
    public static function getPluralModelLabel(): string { return __('Settings'); }
    public static function getNavigationGroup(): ?string
    {
        return __('System');
    }
    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            \Filament\Schemas\Components\Section::make(__('Setting Data'))->schema([
                Forms\Components\TextInput::make('key')->label(__('Key'))->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('value')->label(__('Score'))->required(),
                Forms\Components\TextInput::make('group')->label(__('Group'))->default('general')->required(),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            \Filament\Tables\Columns\Layout\Split::make([
            Tables\Columns\TextColumn::make('key')->label(__('Key'))->searchable()->sortable(),
            Tables\Columns\TextColumn::make('value')->label(__('Score'))->searchable()->limit(50),
            Tables\Columns\TextColumn::make('group')->label(__('Group'))->searchable()->sortable(),
            ])->from('md'),
        ])
        ->defaultGroup('group')
        ->actions([\Filament\Actions\EditAction::make(), \Filament\Actions\DeleteAction::make()])
        ->bulkActions([\Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
        ];
    }
}


