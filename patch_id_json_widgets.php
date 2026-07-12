<?php
$json = file_get_contents('lang/id.json');
$data = json_decode($json, true);

$new_translations = [
    "WELCOME TO STUDENT PORTAL!" => "SELAMAT DATANG DI PORTAL SISWA!",
    "View your class schedule, grades, and attendance history" => "Lihat jadwal kelas, nilai, dan riwayat absensi Anda",
    "Student data is not connected" => "Data siswa belum tertaut",
    "Registered Classes" => "Kelas Terdaftar",
    "Active classes you are enrolled in" => "Kelas aktif yang Anda ikuti",
    "Attendance This Month" => "Kehadiran Bulan Ini",
    "of" => "dari",
    "meetings" => "pertemuan",
    "Latest Score" => "Nilai Terakhir",
    "Exam" => "Ujian",
    "No score yet" => "Belum ada nilai"
];

foreach ($new_translations as $key => $val) {
    if (!isset($data[$key])) {
        $data[$key] = $val;
    }
}

file_put_contents('lang/id.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo "id.json updated for dashboard widgets.";
