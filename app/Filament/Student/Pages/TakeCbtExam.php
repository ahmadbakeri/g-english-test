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
        $this->exam = Cache::remember("cbt_exam_meta_{$record}", 300, function () use ($record) {
            return CbtExam::with('subject:id,name')->find($record);
        });
        if (!$this->exam) {
            abort(404);
        }

        $user = auth()->user();
        if (!$user || !$user->student) {
            abort(403);
        }

        $student = $user->student;
        if ($student->course_level === 'G-English Brain Champion' || !empty($student->competition_code)) {
            abort(403, 'Peserta G-English Brain Champion mengikuti kompetisi secara offline di gedung (Whiteboard atau Kertas).');
        }
        $mode = $this->exam->cbt_mode ?? 'google_form';

        if ($mode === 'google_form') {
            $this->prefilledGformUrl = $this->buildPrefilledGformUrl($this->exam, $student);

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

        // Mode B: Real CBT (Excel / Word .docx UTBK)
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
                if (is_array($ans->answer_payload) && !empty($ans->answer_payload)) {
                    $this->answers[$ans->cbt_question_id] = $ans->answer_payload;
                } elseif ($ans->selected_answer !== null && $ans->selected_answer !== '') {
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
                    'order_number',
                    'question_type',
                    'question_text',
                    'attachment',
                    'option_a',
                    'option_b',
                    'option_c',
                    'option_d',
                    'option_e',
                    'statements',
                    'points',
                ])
                ->orderBy('order_number')
                ->orderBy('id')
                ->get()
                ->map(function ($q) {
                    $ext = $q->attachment ? strtolower(pathinfo($q->attachment, PATHINFO_EXTENSION)) : null;
                    $type = $q->question_type ?: 'mcq';

                    // Strip answer keys from statements sent to student browser
                    $publicStatements = [];
                    if ($type === 'complex_tf' && is_array($q->statements)) {
                        foreach ($q->statements as $idx => $st) {
                            $publicStatements[] = [
                                'no' => (int) ($st['no'] ?? ($idx + 1)),
                                'text' => (string) ($st['text'] ?? ''),
                            ];
                        }
                    }

                    return [
                        'id' => $q->id,
                        'question_type' => $type,
                        'question_text' => $q->question_text,
                        'attachment_url' => $q->attachment ? Storage::url($q->attachment) : null,
                        'attachment_type' => $ext
                            ? (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'], true)
                                ? 'image'
                                : (in_array($ext, ['mp3', 'wav', 'ogg', 'm4a'], true) ? 'audio' : 'file'))
                            : null,
                        'attachment_ext' => $ext,
                        'options' => $type === 'mcq'
                            ? array_filter([
                                'A' => $q->option_a,
                                'B' => $q->option_b,
                                'C' => $q->option_c,
                                'D' => $q->option_d,
                                'E' => $q->option_e,
                            ], fn ($v) => $v !== null && $v !== '')
                            : [],
                        'statements' => $publicStatements,
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
            'scoring_status' => $this->exam->isDeferredScoring() ? 'pending_admin' : 'graded',
            'is_completed' => false,
            'violation_count' => 0,
            'max_violations' => 3,
            'sync_status' => 'internal',
        ]);

        $this->redirect(static::getUrl(['record' => $this->record], panel: 'student'), navigate: false);
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
            $this->redirect(static::getUrl(['record' => $this->record], panel: 'student'), navigate: false);
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
            'score' => $this->exam->isDeferredScoring() ? null : 0,
            'scoring_status' => $this->exam->isDeferredScoring() ? 'pending_admin' : 'graded',
            'status' => 'locked',
            'is_force_submitted' => true,
        ]);

        $this->isFinished = true;
        $this->redirect(static::getUrl(['record' => $this->record], panel: 'student'), navigate: false);
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
            $this->redirect(static::getUrl(['record' => $this->record], panel: 'student'), navigate: false);
            return;
        }

        $isDeferred = $this->exam->isDeferredScoring();
        $examQuestions = Cache::remember("exam_grade_{$this->exam->id}", 3600, function () {
            return $this->exam->questions()
                ->select(['id', 'question_type', 'statements', 'correct_answer', 'points', 'order_number'])
                ->orderBy('order_number')
                ->orderBy('id')
                ->get();
        });

        $earnedPoints = 0;
        $totalPoints = 0;
        $now = now();
        $rowsToInsert = [];

        foreach ($examQuestions as $question) {
            $qId = $question->id;
            $type = $question->question_type ?: 'mcq';
            $points = (int) ($question->points ?? 1);
            $rawAnswer = $this->answers[$qId] ?? null;

            $selectedSummary = null;
            $payloadJson = null;
            $isCorrect = null;

            if ($type === 'complex_tf') {
                $cleanMap = [];
                if (is_array($rawAnswer)) {
                    foreach ($rawAnswer as $k => $v) {
                        $val = strtoupper(trim((string) $v));
                        if (in_array($val, ['B', 'S'], true)) {
                            $cleanMap[(string) $k] = $val;
                        }
                    }
                }
                if (!empty($cleanMap)) {
                    ksort($cleanMap);
                    $parts = [];
                    foreach ($cleanMap as $k => $v) {
                        $parts[] = "{$k}:{$v}";
                    }
                    $selectedSummary = implode(' | ', $parts);
                    $payloadJson = json_encode($cleanMap);
                }

                if (!$isDeferred && is_array($question->statements) && !empty($question->statements)) {
                    $hasKeys = collect($question->statements)->every(fn ($st) => in_array(strtoupper((string) ($st['answer'] ?? '')), ['B', 'S'], true));
                    if ($hasKeys) {
                        $totalPoints += $points;
                        $allMatch = true;
                        foreach ($question->statements as $idx => $st) {
                            $no = (string) ($st['no'] ?? ($idx + 1));
                            $expected = strtoupper((string) $st['answer']);
                            if (($cleanMap[$no] ?? null) !== $expected) {
                                $allMatch = false;
                                break;
                            }
                        }
                        $isCorrect = $allMatch;
                        if ($allMatch) {
                            $earnedPoints += $points;
                        }
                    }
                }
            } elseif ($type === 'short_answer') {
                $val = is_scalar($rawAnswer) ? trim((string) $rawAnswer) : '';
                if ($val !== '') {
                    $selectedSummary = $val;
                }

                if (!$isDeferred && filled($question->correct_answer)) {
                    $totalPoints += $points;
                    $normExpected = mb_strtolower(str_replace(',', '.', trim((string) $question->correct_answer)));
                    $normGiven = mb_strtolower(str_replace(',', '.', $val));
                    $isCorrect = ($val !== '' && $normGiven === $normExpected);
                    if ($isCorrect) {
                        $earnedPoints += $points;
                    }
                }
            } else {
                // MCQ (A-E)
                $selected = is_scalar($rawAnswer) ? strtoupper(trim((string) $rawAnswer)) : '';
                if ($selected !== '') {
                    $selectedSummary = $selected;
                }

                if (!$isDeferred && filled($question->correct_answer)) {
                    $totalPoints += $points;
                    $expected = strtoupper(trim((string) $question->correct_answer));
                    $isCorrect = ($selected !== '' && $selected === $expected);
                    if ($isCorrect) {
                        $earnedPoints += $points;
                    }
                }
            }

            if ($selectedSummary !== null || $payloadJson !== null) {
                $rowsToInsert[] = [
                    'cbt_attempt_id' => $this->attempt->id,
                    'cbt_question_id' => $qId,
                    'selected_answer' => $selectedSummary,
                    'answer_payload' => $payloadJson,
                    'is_correct' => $isCorrect,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (CbtAnswer::where('cbt_attempt_id', $this->attempt->id)->exists()) {
            CbtAnswer::where('cbt_attempt_id', $this->attempt->id)->delete();
        }
        if (!empty($rowsToInsert)) {
            CbtAnswer::insert($rowsToInsert);
        }

        if ($isDeferred) {
            $this->attempt->update([
                'end_time' => $now,
                'score' => null,
                'scoring_status' => 'pending_admin',
                'status' => 'finished',
                'is_completed' => true,
                'sync_status' => 'internal',
            ]);
        } else {
            $finalScore = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
            $this->attempt->update([
                'end_time' => $now,
                'score' => $finalScore,
                'scoring_status' => 'graded',
                'status' => 'finished',
                'is_completed' => true,
                'sync_status' => 'internal',
            ]);
        }

        $this->isFinished = true;
        $this->redirect(static::getUrl(['record' => $this->record], panel: 'student'), navigate: false);
    }
}
