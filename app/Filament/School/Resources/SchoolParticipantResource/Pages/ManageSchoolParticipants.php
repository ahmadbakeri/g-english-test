<?php

namespace App\Filament\School\Resources\SchoolParticipantResource\Pages;

use App\Exports\BrainChampionStudentTemplateExport;
use App\Filament\School\Resources\SchoolParticipantResource;
use App\Filament\School\Widgets\SchoolQuotaOverviewWidget;
use App\Imports\BrainChampionStudentImport;
use App\Livewire\BrainChampionRegistration;
use App\Models\Admission;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ManageSchoolParticipants extends ManageRecords
{
    protected static string $resource = SchoolParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_template')
                ->label('Download Template Excel (.xlsx)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $schoolSlug = str()->slug(auth()->user()?->name ?: 'sekolah');
                    return Excel::download(
                        new BrainChampionStudentTemplateExport(auth()->id()),
                        "template_brain_champion_{$schoolSlug}.xlsx"
                    );
                }),

            Action::make('import_excel')
                ->label('Upload / Import Excel (.xlsx)')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->form([
                    FileUpload::make('excel_file')
                        ->label('Upload File Biodata Peserta (.xlsx)')
                        ->disk('local')
                        ->directory('brain_champion_imports')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                        ])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $filePath = storage_path('app/' . $data['excel_file']);
                    if (!file_exists($filePath)) {
                        $filePath = Storage::disk('local')->path($data['excel_file']);
                    }

                    $importer = new BrainChampionStudentImport(
                        schoolUserId: auth()->id(),
                        schoolName: auth()->user()?->name,
                    );
                    Excel::import($importer, $filePath);

                    $summary = "Ditambahkan: {$importer->createdCount} peserta, Diperbarui: {$importer->updatedCount} peserta.";
                    if ($importer->skippedCount > 0) {
                        $summary .= " Dilewati: {$importer->skippedCount} baris (" . implode('; ', array_slice($importer->skipReasons, 0, 3)) . ').';
                    }

                    Notification::make()
                        ->title('Proses Import Excel Selesai')
                        ->body($summary)
                        ->success()
                        ->send();
                }),

            CreateAction::make()
                ->label('Tambah Peserta Delegasi')
                ->icon('heroicon-o-plus')
                ->using(function (array $data): Student {
                    return DB::transaction(function () use ($data) {
                        $user = auth()->user();
                        $schoolName = trim((string) ($user?->name ?? 'Sekolah'));
                        $normalizedSchool = BrainChampionRegistration::normalizeSchoolName($schoolName);
                        $classLabel = trim((string) $data['class_name']);

                        if (!preg_match('/Kelas\s*(\d+)/i', $classLabel, $m)) {
                            throw ValidationException::withMessages([
                                'class_name' => 'Pilihan kelas tidak valid.',
                            ]);
                        }
                        $grade = (int) $m[1];
                        $maxPerSchool = BrainChampionRegistration::maxPerSchoolForGrade($grade);
                        $educationLevel = $grade <= 6 ? 'SD' : 'SMP';

                        $lockedRows = Admission::query()
                            ->where('course_level', BrainChampionRegistration::EVENT_TAG)
                            ->where('status', '!=', 'rejected')
                            ->where('class_name', $classLabel)
                            ->lockForUpdate()
                            ->get();

                        $globalUsed = $lockedRows->count();
                        if ($globalUsed >= BrainChampionRegistration::MAX_QUOTA_PER_GRADE) {
                            throw ValidationException::withMessages([
                                'class_name' => "Kuota {$classLabel} sudah penuh (35/35). Tidak dapat menambahkan peserta pada kelas ini.",
                            ]);
                        }

                        $schoolUsed = $lockedRows->filter(function ($row) use ($user, $normalizedSchool) {
                            if ($user && (int) $row->school_user_id === (int) $user->id) {
                                return true;
                            }
                            return $normalizedSchool !== ''
                                && BrainChampionRegistration::normalizeSchoolName((string) $row->school_name) === $normalizedSchool;
                        })->count();

                        if ($schoolUsed >= $maxPerSchool) {
                            throw ValidationException::withMessages([
                                'class_name' => "Sekolah Anda sudah memenuhi batas maksimal {$maxPerSchool} siswa untuk {$classLabel}.",
                            ]);
                        }

                        $prefix = "BC-K{$grade}-";
                        $existingCodes = Admission::where('registration_number', 'like', "{$prefix}%")
                            ->pluck('registration_number')
                            ->merge(Student::withTrashed()->where('nis', 'like', "{$prefix}%")->pluck('nis'));

                        $maxSeq = 0;
                        foreach ($existingCodes as $code) {
                            if (preg_match('/^BC-K' . $grade . '-(\d+)$/', (string) $code, $matches)) {
                                $num = (int) $matches[1];
                                if ($num > $maxSeq) {
                                    $maxSeq = $num;
                                }
                            }
                        }

                        $nextSeq = $maxSeq + 1;
                        do {
                            $regCode = $prefix . str_pad((string) $nextSeq, 3, '0', STR_PAD_LEFT);
                            $exists = Admission::where('registration_number', $regCode)->exists()
                                || Student::withTrashed()->where('nis', $regCode)->exists();
                            $nextSeq++;
                        } while ($exists);

                        $nisn = trim((string) $data['nisn']);
                        $name = trim((string) $data['name']);
                        $gender = $data['gender'] ?? 'L';
                        $parentName = trim((string) $data['parent_name']);
                        $parentPhone = trim((string) $data['parent_phone']);
                        $phone = trim((string) ($data['phone'] ?? '')) ?: $parentPhone;

                        Admission::create([
                            'registration_number' => $regCode,
                            'nisn' => $nisn,
                            'competition_code' => $regCode,
                            'student_type' => 'external',
                            'school_user_id' => $user->id,
                            'name' => $name,
                            'birth_place' => $data['birth_place'] ?? null,
                            'birth_date' => $data['birth_date'] ?? null,
                            'gender' => $gender,
                            'religion' => $data['religion'] ?? null,
                            'phone' => $phone,
                            'address' => $data['address'] ?? null,
                            'education_level' => $educationLevel,
                            'course_level' => BrainChampionRegistration::EVENT_TAG,
                            'class_name' => $classLabel,
                            'school_name' => $schoolName,
                            'previous_school' => $schoolName,
                            'father_name' => $data['father_name'] ?? null,
                            'father_phone' => $data['father_phone'] ?? null,
                            'father_job' => $data['father_job'] ?? null,
                            'mother_name' => ($data['mother_name'] ?? null) ?: $parentName,
                            'mother_phone' => ($data['mother_phone'] ?? null) ?: $parentPhone,
                            'mother_job' => $data['mother_job'] ?? null,
                            'parent_name' => $parentName,
                            'parent_phone' => $parentPhone,
                            'status' => 'approved',
                            'tuition_fee' => 0,
                            'registration_fee' => 0,
                            'has_discount' => false,
                            'total_fee' => 0,
                            'amount_paid' => 0,
                            'notes' => BrainChampionRegistration::EVENT_TAG . " (One Mind, One Champion) — {$classLabel} — NISN: {$nisn} — Sekolah: {$schoolName}",
                        ]);

                        $student = Student::create([
                            'school_user_id' => $user->id,
                            'nis' => $regCode,
                            'nisn' => $nisn,
                            'competition_code' => $regCode,
                            'student_type' => 'external',
                            'name' => $name,
                            'birth_place' => $data['birth_place'] ?? null,
                            'birth_date' => $data['birth_date'] ?? null,
                            'gender' => $gender,
                            'religion' => $data['religion'] ?? null,
                            'phone' => $phone,
                            'address' => $data['address'] ?? null,
                            'education_level' => $educationLevel,
                            'course_level' => BrainChampionRegistration::EVENT_TAG,
                            'class_name' => $classLabel,
                            'school_name' => $schoolName,
                            'father_name' => $data['father_name'] ?? null,
                            'father_phone' => $data['father_phone'] ?? null,
                            'father_job' => $data['father_job'] ?? null,
                            'mother_name' => ($data['mother_name'] ?? null) ?: $parentName,
                            'mother_phone' => ($data['mother_phone'] ?? null) ?: $parentPhone,
                            'mother_job' => $data['mother_job'] ?? null,
                            'parent_name' => $parentName,
                            'parent_phone' => $parentPhone,
                            'is_active' => true,
                        ]);

                        $student->parents()->syncWithoutDetaching([$user->id]);

                        return $student;
                    });
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SchoolQuotaOverviewWidget::class,
        ];
    }
}
