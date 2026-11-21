<?php

namespace App\Filament\Resources\Operations;

use App\Filament\Resources\Operations\JournalResource\Pages;
use App\Models\Journal;
use App\Models\ClassRoom;
use App\Models\Tutor;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JournalResource extends Resource
{
    protected static ?string $model = Journal::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    public static function getNavigationLabel(): string { return __('Learning Journal'); }
    public static function getModelLabel(): string { return __('Journal'); }
    public static function getPluralModelLabel(): string { return __('Journal'); }
    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }
    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        if (auth()->user()->isTutor()) {
            return $record->status !== 'validated';
        }
        return true;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        if (auth()->user()->isTutor()) {
            return $record->status !== 'validated';
        }
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['classRoom', 'tutor']);
        
        if (auth()->user()->isTutor() && auth()->user()->tutor) {
            $query->where('tutor_id', auth()->user()->tutor->id);
        }
        
        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            \Filament\Schemas\Components\Section::make(__('Journal Data'))->schema([
                Forms\Components\Select::make('class_id')->label(__('Class Room'))->translateLabel()
                    ->options(function () {
                        if (auth()->user()->isTutor() && auth()->user()->tutor) {
                            return ClassRoom::where('tutor_id', auth()->user()->tutor->id)->where('is_active', true)->pluck('name', 'id');
                        }
                        return ClassRoom::where('is_active', true)->pluck('name', 'id');
                    })
                    ->searchable()->required(),
                Forms\Components\Select::make('tutor_id')->label(__('Tutor'))->translateLabel()
                    ->options(Tutor::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()->required()
                    ->default(fn () => auth()->user()->isTutor() ? auth()->user()->tutor?->id : null)
                    ->disabled(fn () => auth()->user()->isTutor())
                    ->dehydrated(),
                Forms\Components\DatePicker::make('date')->label(__('Date'))->translateLabel()->required()->default(now()),
                Forms\Components\TextInput::make('topic')->label(__('Topic'))->required()->maxLength(255),
                Forms\Components\RichEditor::make('description')->label(__('Activity Description'))
                    ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'h2', 'h3'])
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('materials')->label(__('Material / Subject Matter'))->rows(3)->columnSpanFull(),
                Forms\Components\FileUpload::make('photo')->label(__('Documentation Photo'))->image()->multiple()->directory('journals')->columnSpanFull(),
                Forms\Components\TextInput::make('link')->label(__('Resource Link (e.g. YouTube)'))->url()->columnSpanFull(),
                Forms\Components\Select::make('status')->label(__('Status'))
                    ->options([
                        'draft' => 'ðŸ“ Draft',
                        'submitted' => 'ðŸ“¤ Diajukan',
                        'validated' => 'âœ… Divalidasi',
                        'rejected' => 'âŒ Ditolak',
                    ])->default('draft')->required(),
                Forms\Components\TextInput::make('jp_count')->label(__('Workload (JP)'))->numeric()->default(2)->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            \Filament\Tables\Columns\Layout\Split::make([
            Tables\Columns\TextColumn::make('date')->label(__('Date'))->translateLabel()->date('d/m/Y')->sortable(),
            Tables\Columns\TextColumn::make('classRoom.name')->label(__('Class Room'))->translateLabel()->searchable(),
            Tables\Columns\TextColumn::make('tutor.name')->label(__('Tutor'))->translateLabel()->searchable(),
            Tables\Columns\TextColumn::make('topic')->label(__('Topic'))->searchable()->limit(30),
            Tables\Columns\ImageColumn::make('photo')->label(__('Photo')),
            Tables\Columns\IconColumn::make('link')->label(__('Link'))->icon(fn ($state) => $state ? 'heroicon-o-link' : null)->url(fn ($state) => $state)->openUrlInNewTab(),
            Tables\Columns\TextColumn::make('status')->label(__('Status'))
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'draft' => 'gray',
                    'submitted' => 'warning',
                    'validated' => 'success',
                    'rejected' => 'danger',
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'draft' => 'Draft',
                    'submitted' => 'Diajukan',
                    'validated' => 'Divalidasi',
                    'rejected' => 'Ditolak',
                }),
            ])->from('md'),
        ])
        ->defaultSort('date', 'desc')
        ->filters([
            Tables\Filters\SelectFilter::make('status')->label(__('Status'))
                ->options(['draft' => 'Draft', 'submitted' => 'Diajukan', 'validated' => 'Divalidasi', 'rejected' => 'Ditolak']),
            Tables\Filters\SelectFilter::make('class_id')->label(__('Class Room'))->translateLabel()
                ->options(ClassRoom::pluck('name', 'id')),
        ])
        ->actions([\Filament\Actions\ViewAction::make(), \Filament\Actions\EditAction::make(), \Filament\Actions\DeleteAction::make()])
        ->bulkActions([\Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournals::route('/'),
        ];
    }
}



