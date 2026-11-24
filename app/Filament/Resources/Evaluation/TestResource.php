<?php

namespace App\Filament\Resources\Evaluation;

use App\Filament\Resources\Evaluation\TestResource\Pages;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Test;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TestResource extends Resource
{
    protected static ?string $model = Test::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $slug = 'evaluation/tests';

    public static function getNavigationLabel(): string { return __('Manage Tests'); }
    public static function getModelLabel(): string { return __('Test'); }
    public static function getPluralModelLabel(): string { return __('Manage Tests'); }
    public static function getNavigationGroup(): ?string { return __('Exams & Evaluation'); }
    public static function getNavigationSort(): ?int { return 1; }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make(__('Test Data'))->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Test Name'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Select::make('class_id')
                    ->label(__('Class Room'))
                    ->options(ClassRoom::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('subject_id')
                    ->label(__('Subject'))
                    ->options(Subject::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label(__('Test Type'))
                    ->options([
                        'daily' => 'Harian / Quiz',
                        'midterm' => 'UTS / Midterm',
                        'final' => 'UAS / Final Test',
                        'placement' => 'Placement Test',
                        'competition' => 'Brain Champion / Lomba',
                    ])
                    ->default('daily')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->label(__('Date'))
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('max_score')
                    ->label('Skor Maksimal')
                    ->numeric()
                    ->default(100)
                    ->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->recordAction('edit')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Test Name'))
                    ->weight('bold')
                    ->description(fn (Test $record): string => ($record->classRoom?->name ?? '-') . ' • ' . ($record->subject?->name ?? '-'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'competition' => 'warning',
                        'final' => 'danger',
                        'placement' => 'success',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'daily', 'quiz' => 'Harian / Quiz',
                        'midterm' => 'Midterm',
                        'final' => 'Final',
                        'placement' => 'Placement',
                        'competition' => 'Lomba',
                        default => ucfirst((string) $state),
                    }),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_score')
                    ->label('Max Skor')
                    ->numeric()
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\Filter::make('date_range')
                    ->label('Rentang Tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->columns(2)
                    ->query(fn ($query, array $data) => $query
                        ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('date', '>=', $d))
                        ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('date', '<=', $d))),
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('Type'))
                    ->options([
                        'quiz' => 'Quiz',
                        'midterm' => 'Midterm',
                        'final' => 'Final',
                        'placement' => 'Placement',
                        'competition' => 'Lomba',
                    ]),
            ])
            ->actions([
                EditAction::make()->button()->size('sm')->slideOver()->modalWidth('2xl'),
                ActionGroup::make([
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTests::route('/'),
        ];
    }
}
