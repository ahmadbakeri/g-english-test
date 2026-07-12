<?php
$json = file_get_contents('lang/id.json');
$data = json_decode($json, true);

$new_translations = [
    "WELCOME TO TUTOR PORTAL!" => "SELAMAT DATANG DI PORTAL TUTOR!",
    "Manage your classes, attendance, and teaching journals" => "Kelola kelas, kehadiran, dan jurnal mengajar Anda",
    "Tutor data is not connected" => "Data tutor belum terhubung",
    "Active Classes" => "Kelas Aktif",
    "Classes you currently teach" => "Kelas yang Anda ajar saat ini",
    "Total Students" => "Total Siswa",
    "Students across all your classes" => "Siswa di seluruh kelas Anda",
    "Journals This Month" => "Jurnal Bulan Ini",
    "Journals filled this month" => "Jurnal yang diisi bulan ini"
];

foreach ($new_translations as $key => $val) {
    if (!isset($data[$key])) {
        $data[$key] = $val;
    }
}

file_put_contents('lang/id.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo "id.json updated for tutor widgets.";
