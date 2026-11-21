<?php

namespace App\Filament\Parent\Resources\Journals;

use App\Filament\Parent\Resources\Journals\Pages\ManageJournals;
use App\Models\Journal;
use App\Models\Attendance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;

class JournalResource extends Resource
{
    protected static ?string $model = Journal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Progres & Jurnal Anak';
    protected static ?string $pluralModelLabel = 'Jurnal Belajar Anak';
    protected static ?string $modelLabel = 'Jurnal';
    protected static ?int $navigationSort = 4;

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
                return $query->whereIn('class_id', $classIds)->latest('date');
            })
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('date')
                            ->label('Tanggal')
                            ->date('d M Y')
                            ->weight('bold')
                            ->sortable()
                            ->searchable(),
                        TextColumn::make('topic')
                            ->label('Topik')
                            ->searchable()
                            ->wrap()
                            ->limit(50)
                            ->color('gray'),
                    ])->space(1),

                    Stack::make([
                        TextColumn::make('classRoom.name')
                            ->label('Kelas')
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
                            ->getStateUsing(function (Journal $record) {
                                return $record->status ?? 'Draft';
                            })
                            ->badge()
                            ->color(fn (string $state): string => match (strtolower($state)) {
                                'published' => 'success',
                                'draft' => 'warning',
                                default => 'gray',
                            }),
                    ])->alignment('end'),
                ])->from('md'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateHeading('Belum Ada Jurnal')
            ->emptyStateDescription('Belum ada catatan jurnal belajar untuk anak Anda saat ini.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageJournals::route('/'),
        ];
    }
}
