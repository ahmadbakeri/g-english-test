<?php

namespace App\Filament\Resources\Academic\CbtImportBatches\Pages;

use App\Filament\Resources\Academic\CbtImportBatches\CbtImportBatchResource;
use App\Models\CbtExam;
use App\Models\CbtImportBatch;
use App\Models\CbtQuestionDraft;
use App\Services\CbtDocx\DraftPublishService;
use App\Services\CbtDocx\HtmlSanitizer;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;

class ReviewCbtImportBatch extends Page
{
    use InteractsWithRecord;

    protected static string $resource = CbtImportBatchResource::class;

    protected string $view = 'filament.admin.pages.review-cbt-import-batch';

    public ?int $targetExamId = null;
    public string $publishMode = 'append';
    public string $activeFilter = 'all';

    // Edit Draft Modal state
    public bool $showEditModal = false;
    public ?int $editingDraftId = null;
    public string $editQuestionType = 'mcq';
    public string $editQuestionHtml = '';
    public string $editOptionA = '';
    public string $editOptionB = '';
    public string $editOptionC = '';
    public string $editOptionD = '';
    public string $editOptionE = '';
    /** @var array<int, array{no: int, text: string, answer: ?string}> */
    public array $editStatements = [];
    public string $editCorrectAnswer = '';
    public int $editPoints = 1;
    public string $editExplanationHtml = '';
    public bool $editClearWarnings = false;

    // Paste / Upload Image Modal state
    public bool $showImageModal = false;
    public ?int $imageTargetDraftId = null;
    /** @var array<int, int> */
    public array $imageApplyDraftIds = [];
    public string $imagePosition = 'after'; // after | before | replace_imgs

    // Optional Bulk Answer Key Drawer
    public bool $showKeyDrawer = false;
    public string $bulkKeyInput = '';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->targetExamId = $this->record->cbt_exam_id;
    }

    public function getTitle(): string
    {
        return 'Staging & Preview Soal: ' . ($this->record->title ?: $this->record->original_filename);
    }

    public function getBatchProperty(): CbtImportBatch
    {
        return $this->record->fresh(['exam', 'drafts']);
    }

    public function getFilteredDraftsProperty()
    {
        $drafts = $this->record->drafts()->orderBy('sequence_no')->get();

        return match ($this->activeFilter) {
            'warning' => $drafts->where('validation_status', 'warning')->values(),
            'error' => $drafts->where('validation_status', 'error')->values(),
            'mcq' => $drafts->where('question_type', 'mcq')->values(),
            'complex_tf' => $drafts->where('question_type', 'complex_tf')->values(),
            'short_answer' => $drafts->where('question_type', 'short_answer')->values(),
            default => $drafts->values(),
        };
    }

    public function getAvailableExamsProperty()
    {
        return CbtExam::with(['classRoom:id,name', 'subject:id,name'])
            ->where('cbt_mode', 'internal_excel')
            ->orderByDesc('id')
            ->get();
    }

    public function setFilter(string $filter): void
    {
        $this->activeFilter = $filter;
        $this->dispatch('cbt-math-refresh');
    }

    public function openEditModal(int $draftId): void
    {
        $draft = CbtQuestionDraft::where('batch_id', $this->record->id)->findOrFail($draftId);

        $this->editingDraftId = $draft->id;
        $this->editQuestionType = $draft->question_type ?? 'mcq';
        $this->editQuestionHtml = (string) $draft->question_html;
        $this->editOptionA = (string) ($draft->option_a ?? '');
        $this->editOptionB = (string) ($draft->option_b ?? '');
        $this->editOptionC = (string) ($draft->option_c ?? '');
        $this->editOptionD = (string) ($draft->option_d ?? '');
        $this->editOptionE = (string) ($draft->option_e ?? '');
        $this->editStatements = is_array($draft->statements) && !empty($draft->statements)
            ? array_values($draft->statements)
            : [['no' => 1, 'text' => '', 'answer' => null]];
        $this->editCorrectAnswer = (string) ($draft->correct_answer ?? '');
        $this->editPoints = max(1, (int) ($draft->points ?? 1));
        $this->editExplanationHtml = (string) ($draft->explanation_html ?? '');
        $this->editClearWarnings = false;
        $this->showEditModal = true;

        $this->dispatch('cbt-math-refresh');
    }

    public function addStatementRow(): void
    {
        $nextNo = count($this->editStatements) + 1;
        $this->editStatements[] = ['no' => $nextNo, 'text' => '', 'answer' => null];
    }

    public function removeStatementRow(int $index): void
    {
        unset($this->editStatements[$index]);
        $this->editStatements = array_values($this->editStatements);
        foreach ($this->editStatements as $i => &$row) {
            $row['no'] = $i + 1;
        }
        unset($row);
    }

    public function saveEditDraft(): void
    {
        if (!$this->editingDraftId) {
            return;
        }

        $draft = CbtQuestionDraft::where('batch_id', $this->record->id)->findOrFail($this->editingDraftId);
        $sanitizer = new HtmlSanitizer();

        $statements = null;
        if ($this->editQuestionType === 'complex_tf') {
            $cleaned = [];
            $seq = 1;
            foreach ($this->editStatements as $row) {
                $txt = $sanitizer->clean((string) ($row['text'] ?? ''));
                if ($txt === '') {
                    continue;
                }
                $ans = strtoupper(trim((string) ($row['answer'] ?? '')));
                $cleaned[] = [
                    'no' => $seq++,
                    'text' => $txt,
                    'answer' => in_array($ans, ['B', 'S'], true) ? $ans : null,
                ];
            }
            $statements = $cleaned;
        }

        $update = [
            'question_type' => $this->editQuestionType,
            'question_html' => $sanitizer->clean($this->editQuestionHtml),
            'option_a' => $this->editQuestionType === 'mcq' ? ($sanitizer->clean($this->editOptionA) ?: null) : null,
            'option_b' => $this->editQuestionType === 'mcq' ? ($sanitizer->clean($this->editOptionB) ?: null) : null,
            'option_c' => $this->editQuestionType === 'mcq' ? ($sanitizer->clean($this->editOptionC) ?: null) : null,
            'option_d' => $this->editQuestionType === 'mcq' ? ($sanitizer->clean($this->editOptionD) ?: null) : null,
            'option_e' => $this->editQuestionType === 'mcq' ? ($sanitizer->clean($this->editOptionE) ?: null) : null,
            'statements' => $statements,
            'correct_answer' => trim($this->editCorrectAnswer) !== '' ? strtoupper(trim($this->editCorrectAnswer)) : null,
            'points' => max(1, (int) $this->editPoints),
            'explanation_html' => trim($this->editExplanationHtml) !== '' ? $sanitizer->clean($this->editExplanationHtml) : null,
        ];

        if ($this->editClearWarnings) {
            $update['has_shape_warning'] = false;
            $update['validation_issues'] = [];
        }

        $draft->update($update);
        $draft->revalidate();
        $this->record->recalculateCounts();

        $this->showEditModal = false;
        $this->editingDraftId = null;

        Notification::make()
            ->title("Soal #{$draft->sequence_no} berhasil diperbarui!")
            ->success()
            ->send();

        $this->dispatch('cbt-math-refresh');
    }

    public function clearDraftWarning(int $draftId): void
    {
        $draft = CbtQuestionDraft::where('batch_id', $this->record->id)->findOrFail($draftId);
        $draft->update([
            'has_shape_warning' => false,
            'validation_issues' => [],
        ]);
        $draft->revalidate();
        $this->record->recalculateCounts();

        Notification::make()
            ->title("Warning Soal #{$draft->sequence_no} ditandai selesai.")
            ->success()
            ->send();

        $this->dispatch('cbt-math-refresh');
    }

    public function openImageModal(int $draftId): void
    {
        $this->imageTargetDraftId = $draftId;
        $this->imageApplyDraftIds = [$draftId];
        $this->imagePosition = 'after';
        $this->showImageModal = true;
    }

    public function attachPastedImage(string $dataUrl): void
    {
        if (!preg_match('/^data:image\/(png|jpeg|jpg|webp|gif);base64,(.+)$/i', trim($dataUrl), $m)) {
            Notification::make()
                ->title('Format gambar tidak valid (gunakan PNG/JPG/WebP).')
                ->danger()
                ->send();
            return;
        }

        $ext = strtolower($m[1]) === 'jpeg' ? 'jpg' : strtolower($m[1]);
        $binary = base64_decode($m[2], true);
        if ($binary === false || strlen($binary) < 32) {
            Notification::make()
                ->title('Data gambar rusak atau kosong.')
                ->danger()
                ->send();
            return;
        }

        if (strlen($binary) > 8 * 1024 * 1024) {
            Notification::make()
                ->title('Ukuran gambar terlalu besar (maksimal 8 MB).')
                ->danger()
                ->send();
            return;
        }

        $mediaDir = $this->record->media_dir ?: "cbt_media/batch_{$this->record->id}";
        $hash = substr(sha1($binary), 0, 16);
        $publicPath = "{$mediaDir}/img_{$hash}.{$ext}";
        Storage::disk('public')->put($publicPath, $binary);
        $url = '/storage/' . $publicPath;

        $targetIds = array_values(array_unique(array_map('intval', $this->imageApplyDraftIds)));
        if (empty($targetIds) && $this->imageTargetDraftId) {
            $targetIds = [$this->imageTargetDraftId];
        }

        $drafts = CbtQuestionDraft::where('batch_id', $this->record->id)
            ->whereIn('id', $targetIds)
            ->get();

        $imgTag = '<p><img src="' . e($url) . '" alt="Diagram Soal" class="cbt-inline-img"></p>';

        foreach ($drafts as $draft) {
            $html = (string) $draft->question_html;
            if ($this->imagePosition === 'replace_imgs') {
                $html = preg_replace('/<img\b[^>]*>/i', '', $html) ?? $html;
                $html = trim($html) . "\n" . $imgTag;
            } elseif ($this->imagePosition === 'before') {
                $html = $imgTag . "\n" . trim($html);
            } else {
                $html = trim($html) . "\n" . $imgTag;
            }

            $media = is_array($draft->extracted_media) ? $draft->extracted_media : [];
            $media[] = $url;

            $draft->update([
                'question_html' => $html,
                'extracted_media' => array_values(array_unique($media)),
                'has_shape_warning' => false,
                'validation_issues' => [],
            ]);
            $draft->revalidate();
        }

        $this->record->recalculateCounts();
        $this->showImageModal = false;

        Notification::make()
            ->title('Gambar berhasil dipasang ke ' . $drafts->count() . ' soal!')
            ->success()
            ->send();

        $this->dispatch('cbt-math-refresh');
    }

    public function setQuickKey(int $draftId, ?string $key): void
    {
        $draft = CbtQuestionDraft::where('batch_id', $this->record->id)->findOrFail($draftId);
        $clean = $key !== null && trim($key) !== '' ? strtoupper(trim($key)) : null;
        $draft->update(['correct_answer' => $clean]);
        $draft->revalidate();
        $this->record->recalculateCounts();
    }

    public function applyBulkKeys(): void
    {
        $raw = strtoupper(trim($this->bulkKeyInput));
        if ($raw === '') {
            return;
        }

        $drafts = $this->record->drafts()->orderBy('sequence_no')->get()->keyBy('sequence_no');
        $updated = 0;

        // Supports "1:A 2:B 3:C" or "1.A, 2.B" OR space/newline separated "A B C D E ..."
        if (preg_match_all('/(\d+)\s*[:\.\-\)]\s*([A-E])/', $raw, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $seq = (int) $m[1];
                $ans = $m[2];
                if ($drafts->has($seq) && $drafts[$seq]->question_type === 'mcq') {
                    $drafts[$seq]->update(['correct_answer' => $ans]);
                    $drafts[$seq]->revalidate();
                    $updated++;
                }
            }
        } else {
            preg_match_all('/\b([A-E])\b/', $raw, $letters);
            $mcqDrafts = $this->record->drafts()->where('question_type', 'mcq')->orderBy('sequence_no')->get();
            foreach ($letters[1] ?? [] as $idx => $ans) {
                if (isset($mcqDrafts[$idx])) {
                    $mcqDrafts[$idx]->update(['correct_answer' => $ans]);
                    $mcqDrafts[$idx]->revalidate();
                    $updated++;
                }
            }
        }

        $this->record->recalculateCounts();
        $this->bulkKeyInput = '';

        Notification::make()
            ->title("Kunci jawaban diterapkan ke {$updated} soal PG.")
            ->success()
            ->send();
    }

    public function addNewDraft(): void
    {
        $nextSeq = ((int) $this->record->drafts()->max('sequence_no')) + 1;
        $draft = CbtQuestionDraft::create([
            'batch_id' => $this->record->id,
            'sequence_no' => $nextSeq,
            'question_type' => 'mcq',
            'question_html' => '<p>Tulis isi soal baru di sini...</p>',
            'option_a' => 'Pilihan A',
            'option_b' => 'Pilihan B',
            'option_c' => 'Pilihan C',
            'option_d' => 'Pilihan D',
            'option_e' => 'Pilihan E',
            'points' => 1,
            'validation_status' => 'valid',
        ]);
        $draft->revalidate();
        $this->record->recalculateCounts();
        $this->openEditModal($draft->id);
    }

    public function deleteDraft(int $draftId): void
    {
        CbtQuestionDraft::where('batch_id', $this->record->id)->where('id', $draftId)->delete();

        // Resequence remaining drafts cleanly
        $remaining = $this->record->drafts()->orderBy('sequence_no')->get();
        foreach ($remaining as $i => $d) {
            if ($d->sequence_no !== $i + 1) {
                $d->update(['sequence_no' => $i + 1]);
            }
        }
        $this->record->recalculateCounts();

        Notification::make()
            ->title('Draft soal dihapus & nomor diurutkan ulang.')
            ->success()
            ->send();

        $this->dispatch('cbt-math-refresh');
    }

    public function publishBatch(): void
    {
        if (!$this->targetExamId) {
            Notification::make()
                ->title('Pilih Target Ujian CBT terlebih dahulu!')
                ->warning()
                ->send();
            return;
        }

        $exam = CbtExam::find($this->targetExamId);
        if (!$exam) {
            Notification::make()
                ->title('Target ujian CBT tidak ditemukan.')
                ->danger()
                ->send();
            return;
        }

        try {
            $count = (new DraftPublishService())->publish(
                batch: $this->record,
                exam: $exam,
                userId: (int) auth()->id(),
                mode: $this->publishMode,
            );

            Notification::make()
                ->title("Berhasil mempublish {$count} soal ke \"{$exam->title}\"!")
                ->body('Cache soal CBT sudah dibersihkan otomatis. Siswa langsung melihat soal terbaru.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Gagal Publish')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        $this->dispatch('cbt-math-refresh');
    }
}
