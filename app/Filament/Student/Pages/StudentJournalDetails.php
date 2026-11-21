<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Support\Enums\FontWeight;
use App\Models\Journal;
use App\Models\Attendance;
use Illuminate\Support\HtmlString;

class StudentJournalDetails extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-document-text';
    }

    protected static ?string $navigationLabel = 'Journal Details';
    protected static ?string $title = 'Journal Details';
    protected static ?string $slug = 'my-learning-journals/{record}';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return new \Illuminate\Support\HtmlString('
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                <a href="' . \App\Filament\Student\Pages\StudentJournals::getUrl() . '" style="display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 9999px; background-color: transparent; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border: 1px solid #e5e7eb; color: #6b7280; transition: all 0.3s;" onmouseover="this.style.color=\'var(--primary-600)\'; this.style.borderColor=\'var(--primary-500)\'; this.style.boxShadow=\'0 4px 6px -1px rgba(0, 0, 0, 0.1)\';" onmouseout="this.style.color=\'#6b7280\'; this.style.borderColor=\'#e5e7eb\'; this.style.boxShadow=\'0 1px 2px 0 rgba(0, 0, 0, 0.05)\';">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h1 style="font-size: 1.875rem; line-height: 2.25rem; font-weight: 800; letter-spacing: -0.025em; color: inherit; margin: 0;">
                    ' . static::$title . '
                </h1>
            </div>
        ');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected string $view = 'filament.student.pages.student-infolist-page';

    public ?Journal $journal = null;

    public function mount($record)
    {
        $student = auth()->user()->student;
        if (!$student) {
            abort(403);
        }

        $this->journal = Journal::with(['tutor', 'classRoom'])->findOrFail($record);

        if (!$student->classes()->where('classes.id', $this->journal->class_id)->exists()) {
            abort(403);
        }
    }

    public function infolist(Schema $schema): Schema
    {
        $student = auth()->user()->student;

        return $schema
            ->record($this->journal)
            ->schema([
                Grid::make(['default' => 1, 'lg' => 3])->schema([
                    Group::make([
                        Section::make(__('Materials & Description'))
                            ->description(__('Summary of today\'s learning.'))
                            ->icon('heroicon-m-book-open')
                            ->schema([
                                TextEntry::make('topic')
                                    ->label(__('Discussion Topic'))
                                    ->size('lg')
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-m-sparkles')
                                    ->iconColor('primary'),
                                
                                TextEntry::make('materials')
                                    ->label(__('Learning Materials'))
                                    ->html()
                                    ->prose()
                                    ->hidden(fn ($state) => empty(trim(strip_tags($state)))),

                                TextEntry::make('description')
                                    ->label(__('Additional Notes'))
                                    ->html()
                                    ->prose()
                                    ->hidden(fn ($state) => empty(trim(strip_tags($state)))),
                            ]),

                        Section::make(__('Documentation & References'))
                            ->icon('heroicon-m-camera')
                            ->description(__('Activity photos and reference sources.'))
                            ->schema([
                                ImageEntry::make('photo')
                                    ->hiddenLabel()
                                    ->hidden(fn ($state) => empty($state))
                                    ->getStateUsing(function (Journal $record) {
                                        $photos = [];
                                        if (is_array($record->photo)) {
                                            $photos = $record->photo;
                                        } elseif (is_string($record->photo)) {
                                            $decoded = json_decode($record->photo, true);
                                            $photos = is_array($decoded) ? $decoded : [$record->photo];
                                        }
                                        return array_map(fn($path) => asset('storage/' . $path), $photos);
                                    })
                                    ->extraImgAttributes([
                                        'class' => 'max-w-full w-full object-cover h-auto rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 transition-transform duration-300 hover:scale-[1.02] cursor-pointer', 
                                        'style' => 'max-height: 400px; object-fit: contain; object-position: left center;',
                                        'onclick' => 'window.open(this.src, \'_blank\')'
                                    ])
                                    ->width('100%')
                                    ->height('auto')
                                    ->limit(10),
                                
                                TextEntry::make('link')
                                    ->label(__('Reference Link / Video'))
                                    ->hidden(fn ($state) => empty($state))
                                    ->icon('heroicon-m-link')
                                    ->formatStateUsing(fn ($state) => new HtmlString('<a href="'.$state.'" target="_blank" class="text-primary-600 hover:text-primary-500 underline font-medium">'.__('Open Link').'</a>')),
                            ])
                            ->hidden(fn (Journal $record) => empty($record->photo) && empty($record->link)),
                    ])->columnSpan(['lg' => 2]),

                    Group::make([
                        Section::make(__('Class Information'))
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                TextEntry::make('date')
                                    ->label(__('Execution Date'))
                                    ->icon('heroicon-m-calendar-days')
                                    ->date('l, d F Y'),
                                
                                TextEntry::make('classRoom.name')
                                    ->label(__('Class'))
                                    ->icon('heroicon-m-academic-cap')
                                    ->weight(FontWeight::Medium),

                                TextEntry::make('tutor.name')
                                    ->label(__('Tutor'))
                                    ->icon('heroicon-m-user')
                                    ->weight(FontWeight::Medium),
                            ]),

                        Section::make(__('Attendance Status'))
                            ->icon('heroicon-m-check-badge')
                            ->schema([
                                TextEntry::make('student_attendance')
                                    ->hiddenLabel()
                                    ->getStateUsing(function (Journal $record) use ($student) {
                                        if (!$student) return __('Not Attended Yet');
                                        $attendance = Attendance::where('class_id', $record->class_id)
                                            ->where('date', $record->date)
                                            ->where('student_id', $student->id)
                                            ->first();
                                        return $attendance ? ucfirst($attendance->status) : __('Not Attended Yet');
                                    })
                                    ->badge()
                                    ->size('lg')
                                    ->color(fn (string $state): string => match (strtolower($state)) {
                                        'sakit', 'excused' => 'warning',
                                        'alpha', 'absent' => 'danger',
                                        'late' => 'warning',
                                        'belum diabsen', 'not attended yet' => 'gray',
                                        default => 'primary',
                                    }),
                                
                                TextEntry::make('time_in')
                                    ->label(__('Time In (Masuk)'))
                                    ->icon('heroicon-m-arrow-right-on-rectangle')
                                    ->iconColor('success')
                                    ->getStateUsing(function (Journal $record) use ($student) {
                                        if (!$student) return '-';
                                        $attendance = Attendance::where('class_id', $record->class_id)
                                            ->where('date', $record->date)
                                            ->where('student_id', $student->id)
                                            ->first();
                                        return $attendance && $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-';
                                    }),
                                
                                TextEntry::make('time_out')
                                    ->label(__('Time Out (Pulang)'))
                                    ->icon('heroicon-m-arrow-left-on-rectangle')
                                    ->iconColor('danger')
                                    ->getStateUsing(function (Journal $record) use ($student) {
                                        if (!$student) return '-';
                                        $attendance = Attendance::where('class_id', $record->class_id)
                                            ->where('date', $record->date)
                                            ->where('student_id', $student->id)
                                            ->first();
                                        return $attendance && $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '-';
                                    }),
                            ]),
                    ])->columnSpan(['lg' => 1]),
                ]),
            ]);
    }
}
