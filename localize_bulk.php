<?php

$dict = [
    // Indo -> English
    'Data Absensi' => 'Attendance Data',
    'Data Orang Tua' => 'Parent Data',
    'Hapus (Reset)' => 'Remove (Reset)',
    'Status Pendaftaran' => 'Admission Status',
    'Data Jurnal' => 'Journal Data',
    'Siswa' => 'Student',
    'Mata Pelajaran' => 'Subject',
    'Nilai (0-100)' => 'Score (0-100)',
    'Catatan/Evaluasi' => 'Notes/Evaluation',
    'Tanggal Evaluasi' => 'Evaluation Date',
    'Nilai' => 'Score',
    'Cetak' => 'Print',
    'Kelas' => 'Class Room',
    'Nama' => 'Name',
    'Halaman Dibuka' => 'Page Opened',
    'IP' => 'IP Address',
    'Waktu' => 'Time',
    'Jam Mulai' => 'Start Time',
    'Jam Selesai' => 'End Time',
    'Jurnal Disetujui' => 'Journal Approved',
    'Jurnal Ditolak' => 'Journal Rejected',
    'Jadwal Berhasil Dibuat' => 'Schedule Successfully Created',
    'Siswa berhasil diterima dan data siswa telah dibuat.' => 'Student successfully admitted and student data created.',
    'Berhasil dihitung otomatis!' => 'Successfully calculated automatically!',
    'Display QR Absensi' => 'Display Attendance QR',
    'Display QR & Live Hadir' => 'Display QR & Live Attendance',
    
    // Already English, just needs wrapper
    'Class Room' => 'Class Room',
    'Status' => 'Status',
    'Date' => 'Date',
    'Type' => 'Type',
    'Name' => 'Name',
    'Email' => 'Email',
    'Password' => 'Password',
    'Role' => 'Role',
    'Created At' => 'Created At',
    'Student' => 'Student',
    'Subject' => 'Subject',
    'Tutor' => 'Tutor',
    'User' => 'User',
    'No' => 'No',
    'Start Time' => 'Start Time',
    'End Time' => 'End Time'
];

$translationsToSave = [];

$dir = __DIR__ . '/app/Filament';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        $original = $content;

        foreach ($dict as $indo => $en) {
            // Replaces ->label('Indo') with ->label(__('English'))
            $content = preg_replace("/->label\(\s*['\"]" . preg_quote($indo, '/') . "['\"]\s*\)/", "->label(__('{$en}'))", $content);
            // Replaces Section::make('Indo') with Section::make(__('English'))
            $content = preg_replace("/Section::make\(\s*['\"]" . preg_quote($indo, '/') . "['\"]\s*\)/", "Section::make(__('{$en}'))", $content);
            // Replaces ->title('Indo') with ->title(__('English'))
            $content = preg_replace("/->title\(\s*['\"]" . preg_quote($indo, '/') . "['\"]\s*\)/", "->title(__('{$en}'))", $content);
            
            // Replaces return 'Indo'; with return __('English');
            $content = preg_replace("/return\s+['\"]" . preg_quote($indo, '/') . "['\"]\s*;/", "return __('{$en}');", $content);
        }
        
        // Custom regex for variable interpolation: ->title("Berhasil membuat $count tagihan baru")
        if (preg_match('/->title\(\s*"Berhasil membuat \$([a-zA-Z0-9_]+) tagihan baru"\s*\)/', $content, $m)) {
            $var = $m[1];
            $content = str_replace($m[0], "->title(__('Successfully created :count new bills', ['count' => \$$var]))", $content);
            $translationsToSave['Successfully created :count new bills'] = 'Berhasil membuat :count tagihan baru';
        }
        
        if (preg_match('/->title\(\s*"Berhasil generate \$([a-zA-Z0-9_]+) data gaji tutor!"\s*\)/', $content, $m)) {
            $var = $m[1];
            $content = str_replace($m[0], "->title(__('Successfully generated :count tutor salary data!', ['count' => \$$var]))", $content);
            $translationsToSave['Successfully generated :count tutor salary data!'] = 'Berhasil generate :count data gaji tutor!';
        }
        
        if (preg_match('/->title\(\s*"Berhasil menyetujui \$([a-zA-Z0-9_]+) jurnal!"\s*\)/', $content, $m)) {
            $var = $m[1];
            $content = str_replace($m[0], "->title(__('Successfully approved :count journals!', ['count' => \$$var]))", $content);
            $translationsToSave['Successfully approved :count journals!'] = 'Berhasil menyetujui :count jurnal!';
        }

        if ($content !== $original) {
            file_put_contents($path, $content);
            echo "Updated $path\n";
        }
    }
}

// Prepare ID JSON
foreach ($dict as $indo => $en) {
    if ($indo !== $en) {
        $translationsToSave[$en] = $indo;
    }
}

// Update lang/id.json
$jsonPath = __DIR__ . '/lang/id.json';
if (file_exists($jsonPath)) {
    $currentJson = json_decode(file_get_contents($jsonPath), true) ?? [];
    $merged = array_merge($currentJson, $translationsToSave);
    file_put_contents($jsonPath, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "Updated lang/id.json\n";
} else {
    echo "lang/id.json not found!\n";
}

?>
