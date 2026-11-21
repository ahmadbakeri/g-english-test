<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use App\Models\StudyMaterial;

class ViewStudyMaterial extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.student.pages.view-study-material';

    protected static bool $shouldRegisterNavigation = false;

    public $record;
    public $material;

    public function mount()
    {
        $this->record = request()->query('record');
        
        if (!$this->record) {
            abort(404);
        }

        $this->material = StudyMaterial::findOrFail($this->record);

        // Verify access: Student must be in a class that has this subject
        $studentId = auth()->user()->student->id;
        $hasAccess = $this->material->subject->classes()->whereHas('students', function ($q) use ($studentId) {
            $q->where('student_id', $studentId);
        })->exists();

        if (!$hasAccess || !$this->material->is_published) {
            abort(403);
        }
    }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.student.pages.study-materials-page') => __('Study Materials'),
            '' => $this->material->title,
        ];
    }
}
