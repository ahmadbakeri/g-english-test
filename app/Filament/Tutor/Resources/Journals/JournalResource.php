<?php

namespace App\Filament\Tutor\Resources\Journals;

use App\Filament\Tutor\Resources\Journals\Pages\ManageJournals;
use App\Models\Journal;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JournalResource extends Resource
{
    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }

    protected static ?string $model = Journal::class;

    public static function getNavigationLabel(): string { return __('Teaching Journal'); }
    public static function getPluralModelLabel(): string { return __('Teaching Journal'); }
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('classRoom')
            ->where('tutor_id', auth()->user()->tutor->id)
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('class_id')
                    ->relationship('classRoom', 'name', fn (Builder $query) => $query->where('tutor_id', auth()->user()->tutor->id))
                    ->required(),
                Hidden::make('tutor_id')
                    ->default(fn () => auth()->user()->tutor->id)
                    ->required(),
                DatePicker::make('date')
                    ->required(),
                TextInput::make('topic')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('materials')
                    ->columnSpanFull(),
                FileUpload::make('photo')
                    ->label(__('Documentation Photo'))
                    ->image()
                    ->multiple()
                    ->directory('journals')
                    ->columnSpanFull(),
                TextInput::make('link')
                    ->label(__('Resource Link (e.g. YouTube)'))
                    ->url()
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                TextInput::make('jp_count')
                    ->required()
                    ->numeric()
                    ->default(2),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('classRoom.name')
                    ->label(__('Class Room')),
                TextEntry::make('date')
                    ->date(),
                TextEntry::make('topic'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('materials')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('photo')
                    ->label(__('Documentation Photo'))
                    ->columnSpanFull(),
                IconEntry::make('link')
                    ->label(__('Link'))
                    ->icon(fn ($state) => $state ? 'heroicon-o-link' : null)
                    ->url(fn ($state) => $state)
                    ->openUrlInNewTab()
                    ->columnSpanFull(),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Journal $record): bool => $record->trashed()),
                TextEntry::make('jp_count')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                TextColumn::make('classRoom.name')
                    ->label(__('Class Room'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('topic')
                    ->searchable(),
                ImageColumn::make('photo')
                    ->label(__('Photo')),
                IconColumn::make('link')
                    ->label(__('Link'))
                    ->icon(fn ($state) => $state ? 'heroicon-o-link' : null)
                    ->url(fn ($state) => $state)
                    ->openUrlInNewTab(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('jp_count')
                    ->numeric()
                    ->sortable(),
                ])->from('md'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageJournals::route('/'),
        ];
    }


    
    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return true;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return true;
    }
}
