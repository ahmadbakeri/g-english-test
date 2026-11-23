<?php

namespace App\Filament\Parent\Resources\Journals;

use App\Filament\Parent\Resources\Journals\Pages\ManageJournals;
use App\Models\Journal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JournalResource extends Resource
{
    protected static ?string $model = Journal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string { return __('Child Progress & Journals'); }
    public static function getPluralModelLabel(): string { return __('Child Learning Journals'); }
    public static function getModelLabel(): string { return __('Journal'); }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function table(Table $table): Table
    {
        $childrenIds = auth()->user()->children->pluck('id');
        $classIds = \DB::table('class_student')->whereIn('student_id', $childrenIds)->pluck('class_id');

        return $table
            ->modifyQueryUsing(function (Builder $query) use ($classIds) {
                return $query->with(['classRoom', 'tutor'])->whereIn('class_id', $classIds)->latest('date');
            })
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('topic')
                            ->label('Topik Pembelajaran')
                            ->weight('bold')
                            ->searchable()
                            ->wrap(),
                        TextColumn::make('date')
                            ->label('Tanggal')
                            ->date('d M Y')
                            ->icon('heroicon-m-calendar-days')
                            ->color('gray')
                            ->sortable(),
                    ])->space(1),

                    Stack::make([
                        TextColumn::make('classRoom.name')
                            ->label('Kelas')
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-m-academic-cap')
                            ->searchable(),
                        TextColumn::make('tutor.name')
                            ->label('Tutor')
                            ->icon('heroicon-m-user')
                            ->color('gray')
                            ->searchable(),
                    ])->space(1),

                    Stack::make([
                        TextColumn::make('status')
                            ->label('Status')
                            ->getStateUsing(fn (Journal $record): string => $record->status ?? 'draft')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match (strtolower($state)) {
                                'validated', 'published' => 'Tervalidasi',
                                'submitted' => 'Diajukan',
                                'rejected' => 'Ditolak',
                                default => 'Draft',
                            })
                            ->color(fn (string $state): string => match (strtolower($state)) {
                                'validated', 'published' => 'success',
                                'submitted' => 'warning',
                                'rejected' => 'danger',
                                default => 'gray',
                            }),
                    ])->alignment('end'),
                ])->from('md'),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([])
            ->emptyStateHeading('Belum Ada Jurnal')
            ->emptyStateDescription('Belum ada catatan jurnal belajar untuk kelas anak Anda saat ini.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageJournals::route('/'),
        ];
    }
}
