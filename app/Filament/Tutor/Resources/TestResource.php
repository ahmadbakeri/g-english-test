<?php

namespace App\Filament\Tutor\Resources;

use App\Filament\Resources\Evaluation\TestResource as AdminTestResource;
use App\Filament\Tutor\Resources\TestResource\Pages;
use App\Models\Test;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TestResource extends Resource
{
    protected static ?string $model = Test::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $slug = 'tests';

    public static function getNavigationLabel(): string { return __('Manage Tests'); }
    public static function getModelLabel(): string { return __('Test'); }
    public static function getPluralModelLabel(): string { return __('Manage Tests'); }
    public static function getNavigationSort(): ?int { return 5; }

    public static function form(Schema $schema): Schema
    {
        return AdminTestResource::form($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminTestResource::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTests::route('/'),
        ];
    }
}
