<?php

namespace App\Filament\Resources\Operations;

use App\Filament\Resources\Operations\JournalResource\Pages;
use App\Models\ClassRoom;
use App\Models\Journal;
use App\Models\Tutor;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
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
            \Filament\Schemas\Components\Section::make(__('Journal Data'))
                ->description('Informasi kelas, pengajar, tanggal pertemuan, beban JP, dan status validasi jurnal.')
                ->icon('heroicon-o-book-open')
                ->schema([
                    Forms\Components\Select::make('class_id')
                        ->label(__('Class Room'))
                        ->translateLabel()
                        ->options(function () {
                            if (auth()->user()->isTutor() && auth()->user()->tutor) {
                                return ClassRoom::where('tutor_id', auth()->user()->tutor->id)->where('is_active', true)->pluck('name', 'id');
                            }
                            return ClassRoom::where('is_active', true)->pluck('name', 'id');
                        })
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('tutor_id')
                        ->label(__('Tutor'))
                        ->translateLabel()
                        ->options(Tutor::where('is_active', true)->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->default(fn () => auth()->user()->isTutor() ? auth()->user()->tutor?->id : null)
                        ->disabled(fn () => auth()->user()->isTutor())
                        ->dehydrated(),
                    Forms\Components\DatePicker::make('date')
                        ->label(__('Date'))
                        ->translateLabel()
                        ->required()
                        ->default(now()),
                    Forms\Components\TextInput::make('jp_count')
                        ->label(__('Workload (JP)'))
                        ->numeric()
                        ->suffix('JP')
                        ->default(2)
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label(__('Status'))
                        ->options([
                            'draft' => 'Draft',
                            'submitted' => 'Diajukan',
                            'validated' => 'Divalidasi',
                            'rejected' => 'Ditolak',
                        ])
                        ->default('draft')
                        ->required(),
                    Forms\Components\TextInput::make('topic')
                        ->label(__('Topic'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                ])->columns(3),

            \Filament\Schemas\Components\Section::make('Materi & Dokumentasi Kelas')
                ->description('Uraian aktivitas pembelajaran, materi yang dibahas, foto dokumentasi, dan tautan pendukung.')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\RichEditor::make('description')
                        ->label(__('Activity Description'))
                        ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'h2', 'h3'])
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('materials')
                        ->label(__('Material / Subject Matter'))
                        ->rows(2)
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('photo')
                        ->label(__('Documentation Photo'))
                        ->image()
                        ->multiple()
                        ->directory('journals')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('link')
                        ->label(__('Resource Link (e.g. YouTube)'))
                        ->prefixIcon('heroicon-o-link')
                        ->url()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordAction('edit')
        ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->translateLabel()
                    ->date('d/m/Y')
                    ->description(fn (Journal $record): string => ($record->jp_count ?? 0) . ' JP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('topic')
                    ->label(__('Topic'))
                    ->weight('bold')
                    ->description(fn (Journal $record): string => ($record->classRoom?->name ?? '-') . ' • ' . ($record->tutor?->name ?? '-'))
                    ->searchable()
                    ->limit(35),
                Tables\Columns\TextColumn::make('status')
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
                    }),
                Tables\Columns\TextColumn::make('classRoom.name')
                    ->label(__('Class Room'))
                    ->translateLabel()
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tutor.name')
                    ->label(__('Tutor'))
                    ->translateLabel()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('jp_count')
                    ->label('JP')
                    ->badge()
                    ->color('gray')
                    ->suffix(' JP')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('photo')
                    ->label(__('Photo'))
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('link')
                    ->label(__('Link'))
                    ->icon(fn ($state) => $state ? 'heroicon-o-link' : null)
                    ->url(fn ($state) => $state)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->defaultSort('date', 'desc')
        ->filters([
            Tables\Filters\Filter::make('date_range')
                ->label('Rentang Tanggal')
                ->form([
                    Forms\Components\DatePicker::make('from')->label('Dari Tanggal'),
                    Forms\Components\DatePicker::make('until')->label('Sampai Tanggal'),
                ])
                ->columns(2)
                ->query(fn ($query, array $data) => $query
                    ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('date', '>=', $date))
                    ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('date', '<=', $date)))
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['from'] ?? null) {
                        $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['from'])->format('d/m/Y');
                    }
                    if ($data['until'] ?? null) {
                        $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['until'])->format('d/m/Y');
                    }
                    return $indicators;
                }),
            Tables\Filters\SelectFilter::make('status')
                ->label(__('Status'))
                ->options(['draft' => 'Draft', 'submitted' => 'Diajukan', 'validated' => 'Divalidasi', 'rejected' => 'Ditolak']),
            Tables\Filters\SelectFilter::make('class_id')
                ->label(__('Class Room'))
                ->translateLabel()
                ->options(ClassRoom::pluck('name', 'id')),
        ])
        ->actions([
            \Filament\Actions\EditAction::make()
                ->button()
                ->size('sm'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournals::route('/'),
        ];
    }
}
