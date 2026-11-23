<?php

namespace App\Filament\Resources\HR\ExternalStudentResource\Pages;

use App\Filament\Resources\HR\ExternalStudentResource;
use App\Models\Student;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExternalStudents extends ListRecords
{
    protected static string $resource = ExternalStudentResource::class;

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
            Actions\Action::make('print_brain_champion_roster')
                ->label('Cetak Daftar Hadir')
                ->icon('heroicon-o-printer')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('grade')
                        ->label('Pilih Jenjang Kelas Brain Champion')
                        ->options([
                            'all' => 'Semua Kelas (Kelas 4 s/d Kelas 9)',
                            '4' => 'Kelas 4 (SD / MI)',
                            '5' => 'Kelas 5 (SD / MI)',
                            '6' => 'Kelas 6 (SD / MI)',
                            '7' => 'Kelas 7 (SMP / MTs)',
                            '8' => 'Kelas 8 (SMP / MTs)',
                            '9' => 'Kelas 9 (SMP / MTs)',
                        ])
                        ->default('all')
                        ->required(),
                ])
                ->action(function (array $data) {
                    return redirect()->to(route('report.brain-champion-roster', ['grade' => $data['grade'] ?? 'all']));
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
