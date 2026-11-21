<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use App\Models\Subject;

class StudyMaterialsPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-book-open';

    protected string $view = 'filament.student.pages.study-materials-page';

    public static function getNavigationLabel(): string
    {
        return __('Study Materials');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public function getSubjectsProperty()
    {
        $studentId = auth()->user()->student->id;
        return Subject::whereHas('classes.students', function ($query) use ($studentId) {
            $query->where('student_id', $studentId);
        })
        ->whereHas('studyMaterials', function ($query) {
            $query->where('is_published', true);
        })
        ->with(['studyMaterials' => function ($query) {
            $query->where('is_published', true);
        }])
        ->get();
    }
}
