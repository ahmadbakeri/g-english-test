<?php

namespace App\Filament\Tutor\Resources\Journals;

use App\Filament\Tutor\Resources\Journals\Pages\ManageJournals;
use App\Models\Journal;
use BackedEnum;
use Filament\Actions\ActionGroup;
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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
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
                \Filament\Schemas\Components\Section::make('Detail Pertemuan & Beban Kerja')
                    ->description('Pilih kelas, tanggal mengajar, topik pertemuan, dan jumlah Jam Pelajaran (JP).')
                    ->icon('heroicon-o-book-open')
                    ->schema([
                        Select::make('class_id')
                            ->label(__('Class Room'))
                            ->relationship('classRoom', 'name', fn (Builder $query) => $query->where('tutor_id', auth()->user()->tutor->id))
                            ->required(),
                        Hidden::make('tutor_id')
                            ->default(fn () => auth()->user()->tutor->id)
                            ->required(),
                        DatePicker::make('date')
                            ->label(__('Date'))
                            ->required()
                            ->default(now()),
                        TextInput::make('jp_count')
                            ->label('Beban Kerja (JP)')
                            ->required()
                            ->numeric()
                            ->suffix('JP')
                            ->default(2),
                        Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'draft' => 'Draft',
                                'submitted' => 'Diajukan',
                                'validated' => 'Divalidasi',
                                'rejected' => 'Ditolak',
                            ])
                            ->required()
                            ->default('draft'),
                        TextInput::make('topic')
                            ->label(__('Topic'))
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Materi & Dokumentasi')
                    ->description('Catatan aktivitas kelas, materi bahan ajar, foto dokumentasi, dan tautan eksternal.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Textarea::make('description')
                            ->label(__('Activity Description'))
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('materials')
                            ->label(__('Material / Subject Matter'))
                            ->rows(2)
                            ->columnSpanFull(),
                        FileUpload::make('photo')
                            ->label(__('Documentation Photo'))
                            ->image()
                            ->multiple()
                            ->directory('journals')
                            ->columnSpanFull(),
                        TextInput::make('link')
                            ->label(__('Resource Link (e.g. YouTube)'))
                            ->prefixIcon('heroicon-o-link')
                            ->url()
                            ->columnSpanFull(),
                    ]),
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
                    TextColumn::make('date')
                        ->label(__('Date'))
                        ->date('d M Y')
                        ->sortable(),
                    TextColumn::make('topic')
                        ->label(__('Topic'))
                        ->weight('bold')
                        ->description(fn (Journal $record): string => $record->classRoom?->name ?? '-')
                        ->searchable(),
                    TextColumn::make('classRoom.name')
                        ->label(__('Class Room'))
                        ->badge()
                        ->color('info')
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('jp_count')
                        ->label('JP')
                        ->numeric()
                        ->badge()
                        ->color('gray')
                        ->suffix(' JP')
                        ->sortable(),
                    ImageColumn::make('photo')
                        ->label(__('Photo'))
                        ->circular(),
                    IconColumn::make('link')
                        ->label(__('Link'))
                        ->icon(fn ($state) => $state ? 'heroicon-o-link' : null)
                        ->url(fn ($state) => $state)
                        ->openUrlInNewTab(),
                    TextColumn::make('status')
                        ->label(__('Status'))
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'draft' => 'gray',
                            'submitted' => 'warning',
                            'validated' => 'success',
                            'rejected' => 'danger',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'draft' => 'Draft',
                            'submitted' => 'Diajukan',
                            'validated' => 'Divalidasi',
                            'rejected' => 'Ditolak',
                            default => $state,
                        })
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
                ])->from('md'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
                    RestoreAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->tooltip('Aksi'),
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
