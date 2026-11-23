<?php

namespace App\Filament\Student\Pages;

use App\Models\CbtAnswer;
use App\Models\CbtAttempt;
use App\Models\CbtExam;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class TakeCbtExam extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'take-cbt-exam/{record}';

    public $record;
    public $exam;
    public $attempt;
    public array $questionsData = [];
    public array $answers = [];
    public bool $isFinished = false;
    public bool $isLocked = false;
    public ?string $prefilledGformUrl = null;

    protected string $view = 'filament.student.pages.take-cbt-exam';

    public function mount($record): void
    {
        $this->record = $record;
        $this->exam = CbtExam::with('subject')->findOrFail($record);

        $user = auth()->user();
        if (!$user || !$user->student) {
            abort(403);
        }

        $student = $user->student;
        if ($student->course_level === 'G-English Brain Champion' || !empty($student->competition_code)) {
            abort(403, 'Peserta G-English Brain Champion mengikuti kompetisi secara offline di gedung (Whiteboard & Kertas).');
        }
        $mode = $this->exam->cbt_mode ?? 'google_form';

        if ($mode === 'google_form') {
            // Mode A: Do NOT query cbt_questions
            $this->prefilledGformUrl = $this->buildPrefilledGformUrl($this->exam, $student);

            // Upsert cbt_attempts once on open
            $this->attempt = CbtAttempt::firstOrCreate(
                [
                    'cbt_exam_id' => $this->exam->id,
                    'student_id' => $student->id,
                ],
                [
                    'attempt_number' => 1,
                    'start_time' => now(),
                    'status' => 'in_progress',
                    'is_completed' => false,
                    'violation_count' => 0,
                    'max_violations' => 3,
                    'sync_status' => 'pending',
                ]
            );

            if ($this->attempt->status === 'locked' || ($this->attempt->violation_count >= ($this->attempt->max_violations ?: 3) && !$this->attempt->is_completed)) {
                $this->isLocked = true;
                $this->isFinished = true;
            } elseif ($this->attempt->is_completed || $this->attempt->status === 'finished') {
                $this->isFinished = true;
            }
            return;
        }

        // Mode B: internal_excel (200-User Low-RAM Optimization via Cache::remember)
        $this->questionsData = $this->getCachedQuestions($this->exam);

        $latestAttempt = CbtAttempt::where('cbt_exam_id', $this->exam->id)
            ->where('student_id', $student->id)
            ->latest('id')
            ->first();

        if ($latestAttempt) {
            $this->attempt = $latestAttempt;

            if ($latestAttempt->status === 'locked' || ($latestAttempt->violation_count >= ($latestAttempt->max_violations ?: 3) && !$latestAttempt->is_completed)) {
                $this->isLocked = true;
                $this->isFinished = true;
                return;
            }

            if ($latestAttempt->is_completed || $latestAttempt->status === 'finished') {
                $this->isFinished = true;
                return;
            }

            $existingAnswers = CbtAnswer::where('cbt_attempt_id', $this->attempt->id)->get();
            foreach ($existingAnswers as $ans) {
                if ($ans->selected_answer) {
                    $this->answers[$ans->cbt_question_id] = $ans->selected_answer;
                }
            }

            if (now()->greaterThanOrEqualTo($this->attempt->start_time->copy()->addMinutes($this->exam->duration_minutes))) {
                $this->submitExam($this->answers);
            }
        }
    }

    protected function buildPrefilledGformUrl(CbtExam $exam, $student): string
    {
        $url = trim((string) $exam->gform_embed_url);
        if ($url === '') {
            return '';
        }

        $params = [];
        if (!str_contains($url, 'embedded=true')) {
            $params['embedded'] = 'true';
        }
        if (!empty($exam->gform_nis_entry_id)) {
            $key = str_starts_with($exam->gform_nis_entry_id, 'entry.')
                ? $exam->gform_nis_entry_id
                : 'entry.' . $exam->gform_nis_entry_id;
            $params[$key] = $student->nis;
        }
        if (!empty($exam->gform_name_entry_id)) {
            $key = str_starts_with($exam->gform_name_entry_id, 'entry.')
                ? $exam->gform_name_entry_id
                : 'entry.' . $exam->gform_name_entry_id;
            $params[$key] = $student->name;
        }

        if (empty($params)) {
            return $url;
        }

        $separator = str_contains($url, '?') ? '&' : '?';
        return $url . $separator . http_build_query($params);
    }

    protected function getCachedQuestions(CbtExam $exam): array
    {
        $questions = Cache::remember("exam_q_{$exam->id}", 3600, function () use ($exam) {
            return $exam->questions()
                ->select([
                    'id',
                    'cbt_exam_id',
                    'question_text',
                    'attachment',
                    'option_a',
                    'option_b',
                    'option_c',
                    'option_d',
                    'option_e',
                    'correct_answer',
                    'points',
                ])
                ->get()
                ->map(function ($q) {
                    $ext = $q->attachment ? strtolower(pathinfo($q->attachment, PATHINFO_EXTENSION)) : null;
                    return [
                        'id' => $q->id,
                        'question_text' => $q->question_text,
                        'attachment_url' => $q->attachment ? Storage::url($q->attachment) : null,
                        'attachment_type' => $ext
                            ? (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'], true)
                                ? 'image'
                                : (in_array($ext, ['mp3', 'wav', 'ogg', 'm4a'], true) ? 'audio' : 'file'))
                            : null,
                        'attachment_ext' => $ext,
                        'options' => array_filter([
                            'A' => $q->option_a,
                            'B' => $q->option_b,
                            'C' => $q->option_c,
                            'D' => $q->option_d,
                            'E' => $q->option_e,
                        ], fn ($v) => $v !== null && $v !== ''),
                        'correct_answer' => strtoupper((string) $q->correct_answer),
                        'points' => (int) ($q->points ?? 1),
                    ];
                })
                ->values()
                ->toArray();
        });

        if ($exam->shuffle_questions && count($questions) > 1) {
            shuffle($questions);
        }

        return $questions;
    }

    public function startExam()
    {
        $user = auth()->user();
        $student = $user->student;

        $attemptsCount = CbtAttempt::where('cbt_exam_id', $this->exam->id)
            ->where('student_id', $student->id)
            ->count();

        if ($attemptsCount >= $this->exam->max_attempts) {
            Notification::make()
                ->title('Maximum attempts reached')
                ->danger()
                ->send();
            return redirect(route('filament.student.pages.student-cbt-exams'));
        }

        $this->attempt = CbtAttempt::create([
            'cbt_exam_id' => $this->exam->id,
            'student_id' => $student->id,
            'attempt_number' => $attemptsCount + 1,
            'start_time' => now(),
            'status' => 'in_progress',
            'is_completed' => false,
            'violation_count' => 0,
            'max_violations' => 3,
            'sync_status' => 'internal',
        ]);
    }

    public function recordViolation(string $reason = 'tab_switch'): array
    {
        if (!$this->attempt || $this->attempt->is_completed || $this->attempt->status === 'locked') {
            return [
                'violation_count' => (int) ($this->attempt?->violation_count ?? 0),
                'max_violations' => (int) ($this->attempt?->max_violations ?? 3),
                'locked' => $this->isLocked,
            ];
        }

        $this->attempt->refresh();
        $max = (int) ($this->attempt->max_violations ?: 3);
        $newCount = (int) $this->attempt->violation_count + 1;

        if ($newCount >= $max) {
            $this->attempt->update([
                'violation_count' => $newCount,
                'status' => 'locked',
                'is_force_submitted' => true,
                'end_time' => now(),
            ]);
            $this->isLocked = true;
            $this->isFinished = true;
        } else {
            $this->attempt->update([
                'violation_count' => $newCount,
            ]);
            $this->skipRender();
        }

        return [
            'violation_count' => $newCount,
            'max_violations' => $max,
            'locked' => $this->isLocked,
        ];
    }

    public function cancelExam(): void
    {
        if (!$this->attempt || $this->attempt->end_time) {
            return;
        }

        $this->attempt->update([
            'end_time' => now(),
            'score' => 0,
            'status' => 'locked',
            'is_force_submitted' => true,
        ]);

        $this->isFinished = true;
    }

    public function submitExam(array $submittedAnswers = []): void
    {
        if (!$this->attempt || $this->attempt->is_completed) {
            return;
        }

        if (!empty($submittedAnswers)) {
            $this->answers = $submittedAnswers;
        }

        if (($this->exam->cbt_mode ?? 'google_form') === 'google_form') {
            $this->attempt->update([
                'end_time' => now(),
                'status' => 'finished',
                'is_completed' => true,
            ]);
            $this->isFinished = true;
            return;
        }

        $cachedQuestions = Cache::remember("exam_q_{$this->exam->id}", 3600, function () {
            return $this->exam->questions()->get()->toArray();
        });

        $earnedPoints = 0;
        $totalPoints = 0;
        $now = now();
        $rowsToUpsert = [];

        foreach ($cachedQuestions as $question) {
            $qId = $question['id'];
            $points = (int) ($question['points'] ?? 1);
            $correct = strtoupper(trim((string) ($question['correct_answer'] ?? 'A')));
            $selected = isset($this->answers[$qId]) ? strtoupper(trim((string) $this->answers[$qId])) : null;

            $totalPoints += $points;
            $isCorrect = ($selected !== null && $selected === $correct);

            if ($isCorrect) {
                $earnedPoints += $points;
            }

            if ($selected !== null && $selected !== '') {
                $rowsToUpsert[] = [
                    'cbt_attempt_id' => $this->attempt->id,
                    'cbt_question_id' => $qId,
                    'selected_answer' => $selected,
                    'is_correct' => $isCorrect,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($rowsToUpsert)) {
            CbtAnswer::where('cbt_attempt_id', $this->attempt->id)->delete();
            CbtAnswer::insert($rowsToUpsert);
        }

        $finalScore = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;

        $this->attempt->update([
            'end_time' => $now,
            'score' => $finalScore,
            'status' => 'finished',
            'is_completed' => true,
            'sync_status' => 'internal',
        ]);

        $this->isFinished = true;
    }
}
