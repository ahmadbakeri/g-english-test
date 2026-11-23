<?php

namespace App\Filament\Resources\HR\ExternalStudentResource\Pages;

use App\Filament\Resources\HR\ExternalStudentResource;
use App\Models\Student;
use App\Support\SchoolAgencyClassifier;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListExternalStudents extends ListRecords
{
    protected static string $resource = ExternalStudentResource::class;

    public function getTabs(): array
    {
        $levels = ['SD', 'MI', 'SMP', 'MTs'];
        $students = Student::query()
            ->select(['id', 'school_name', 'education_level', 'class_name'])
            ->where('student_type', 'external')
            ->get();

        $counts = ['SD' => 0, 'MI' => 0, 'SMP' => 0, 'MTs' => 0];
        foreach ($students as $student) {
            $lvl = SchoolAgencyClassifier::classifyLevel($student->school_name, $student->education_level, $student->class_name);
            $counts[$lvl] = ($counts[$lvl] ?? 0) + 1;
        }

        $tabs = [
            'all' => Tab::make('Semua')
                ->badge($students->count()),
        ];

        foreach ($levels as $lvl) {
            $tabs[$lvl] = Tab::make($lvl)
                ->badge($counts[$lvl] ?? 0)
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereIn('id', SchoolAgencyClassifier::externalStudentIdsForLevel($lvl) ?: [0]));
        }

        return $tabs;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_brain_champion_template')
                ->label('Download Template Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\BrainChampionStudentTemplateExport(null),
                        'template_peserta_brain_champion.xlsx'
                    );
                }),
            Actions\Action::make('import_brain_champion_excel')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->form([
                    \Filament\Forms\Components\Select::make('school_user_id')
                        ->label('Pilih Akun Sekolah (Opsional)')
                        ->options(fn () => \App\Models\User::where('role', 'school')->pluck('name', 'id')->toArray())
                        ->searchable(),
                    \Filament\Forms\Components\TextInput::make('school_name')
                        ->label('Nama Sekolah (Jika akun sekolah belum dipilih)')
                        ->placeholder('Contoh: SDN 1 Tanjung'),
                    \Filament\Forms\Components\FileUpload::make('excel_file')
                        ->label('Upload File Excel Peserta (.xlsx)')
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
                        $filePath = \Illuminate\Support\Facades\Storage::disk('local')->path($data['excel_file']);
                    }

                    $schoolUser = !empty($data['school_user_id']) ? \App\Models\User::find($data['school_user_id']) : null;
                    $importer = new \App\Imports\BrainChampionStudentImport(
                        schoolUserId: $schoolUser?->id,
                        schoolName: $schoolUser?->name ?: ($data['school_name'] ?? 'Sekolah Peserta'),
                    );
                    \Maatwebsite\Excel\Facades\Excel::import($importer, $filePath);

                    $summary = "Ditambahkan: {$importer->createdCount} peserta, Diperbarui: {$importer->updatedCount} peserta.";
                    if ($importer->skippedCount > 0) {
                        $summary .= " Dilewati: {$importer->skippedCount} baris.";
                    }

                    \Filament\Notifications\Notification::make()
                        ->title('Import Excel Brain Champion Selesai')
                        ->body($summary)
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make()
                ->label('Tambah Siswa Eksternal')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['student_type'] = 'external';
                    return $data;
                }),
        ];
    }
}
