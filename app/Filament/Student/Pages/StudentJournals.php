<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\Journal;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;

class StudentJournals extends Page implements HasTable
{
    use InteractsWithTable;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-book-open';
    }

    public static function getNavigationLabel(): string
    {
        return __('My Learning Journals');
    }

    public function getTitle(): string
    {
        return __('Learning Journals');
    }

    protected static ?string $slug = 'my-learning-journals';
    
    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }
    
    protected string $view = 'filament.student.pages.student-table-page';

    public function table(Table $table): Table
    {
        $student = auth()->user()->student;
        $classIds = $student ? $student->classes()->pluck('classes.id') : collect([]);

        return $table
            ->query(Journal::query()->whereIn('class_id', $classIds))
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('date')
                            ->date('d M Y')
                            ->weight('bold')
                            ->sortable()
                            ->searchable(),
                        TextColumn::make('topic')
                            ->searchable()
                            ->wrap()
                            ->limit(50)
                            ->color('gray'),
                    ])->space(1),

                    Stack::make([
                        TextColumn::make('classRoom.name')
                            ->icon('heroicon-m-academic-cap')
                            ->searchable(),
                        TextColumn::make('tutor.name')
                            ->icon('heroicon-m-user')
                            ->color('gray')
                            ->searchable(),
                    ])->space(1),

                    Stack::make([
                        TextColumn::make('student_attendance')
                            ->getStateUsing(function (Journal $record) use ($student) {
                                if (!$student) return 'Belum diabsen';
                                $attendance = Attendance::where('class_id', $record->class_id)
                                    ->where('date', $record->date)
                                    ->where('student_id', $student->id)
                                    ->first();
                                return $attendance ? ucfirst($attendance->status) : 'Belum diabsen';
                            })
                            ->badge()
                            ->color(fn (string $state): string => match (strtolower($state)) {
                                'hadir' => 'success',
                                'belum diabsen' => 'gray',
                                default => 'warning',
                            }),
                    ])->alignment('end'),
                ])->from('md'),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label(__('See Detail'))
                    ->icon('heroicon-m-eye')
                    ->button()
                    ->url(fn (Journal $record) => \App\Filament\Student\Pages\StudentJournalDetails::getUrl(['record' => $record->id])),
            ])
            ->defaultSort('date', 'desc')
            ->recordUrl(fn (Journal $record) => \App\Filament\Student\Pages\StudentJournalDetails::getUrl(['record' => $record->id]))
            ->emptyStateHeading('No Journals Yet')
            ->emptyStateDescription('There are no learning journals available for your classes at the moment.')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
