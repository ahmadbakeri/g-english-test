<?php
$json = file_get_contents('lang/id.json');
$data = json_decode($json, true);

$new_translations = [
    "Today's Class Schedule" => "Jadwal Kelas Hari Ini",
    "Today's Teaching Schedule" => "Jadwal Mengajar Hari Ini",
    "No schedule today" => "Tidak ada jadwal hari ini",
    "You have no classes scheduled for today." => "Anda tidak memiliki kelas yang dijadwalkan hari ini.",
    "Class Schedule" => "Jadwal Kelas",
    "You have no classes scheduled for today. Have a good rest! dYZ%" => "Anda tidak memiliki jadwal kelas hari ini. Selamat beristirahat! dYZ%",
    "You have no classes scheduled for today. Have a good rest!" => "Anda tidak memiliki jadwal kelas hari ini. Selamat beristirahat!"
];

foreach ($new_translations as $key => $val) {
    if (!isset($data[$key])) {
        $data[$key] = $val;
    }
}

file_put_contents('lang/id.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo "id.json updated for schedule widgets.\n";
