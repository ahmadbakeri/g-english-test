<?php

$dict = [
    'ID' => 'ID',
    'Data Tahun Ajaran' => 'Academic Year Data',
    'Tahun Ajaran' => 'Academic Year',
    'Data Kelas' => 'Class Room Data',
    'Jadwal Kelas' => 'Class Schedule',
    'Daftar Siswa' => 'Student List',
    'Data Semester' => 'Semester Data',
    'Semester' => 'Semester',
    'Jurnal' => 'Journal',
    'Amount' => 'Amount',
    'Data Pengaturan' => 'Setting Data'
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
            $content = preg_replace("/->label\(\s*['\"]" . preg_quote($indo, '/') . "['\"]\s*\)/", "->label(__('{$en}'))", $content);
            $content = preg_replace("/Section::make\(\s*['\"]" . preg_quote($indo, '/') . "['\"]\s*\)/", "Section::make(__('{$en}'))", $content);
            $content = preg_replace("/->title\(\s*['\"]" . preg_quote($indo, '/') . "['\"]\s*\)/", "->title(__('{$en}'))", $content);
            $content = preg_replace("/return\s+['\"]" . preg_quote($indo, '/') . "['\"]\s*;/", "return __('{$en}');", $content);
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
