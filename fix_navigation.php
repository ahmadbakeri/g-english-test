<?php
$dir = new RecursiveDirectoryIterator('app/Filament');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$replacements = [
    "return 'Akademik';" => "return __('Academic');",
    "return 'Evaluasi';" => "return __('Evaluation');",
    "return 'Akuntansi';" => "return __('Accounting');",
    "return 'Laporan';" => "return __('Reports');",
    "return 'Data Master';" => "return __('Master Data');",
    "return 'Sistem';" => "return __('System');",
    "return 'Kehadiran';" => "return __('Attendances');",
    "return 'Pendaftaran';" => "return __('Admissions');",
    "return 'Manajemen Tutor';" => "return __('Tutor Management');",
    "return 'Manajemen Karyawan';" => "return __('Employee Management');"
];

foreach ($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    $changed = false;
    foreach ($replacements as $search => $replace) {
        if (strpos($content, $search) !== false) {
            $content = str_replace($search, $replace, $content);
            $changed = true;
        }
    }
    if ($changed) {
        file_put_contents($path, $content);
        echo "Updated $path\n";
    }
}
echo "Done.\n";
