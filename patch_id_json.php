<?php
$json = file_get_contents('lang/id.json');
$data = json_decode($json, true);

$new_translations = [
    "Materials & Description" => "Materi & Deskripsi",
    "Summary of today's learning." => "Ringkasan pembelajaran hari ini.",
    "Discussion Topic" => "Topik Pembahasan",
    "Learning Materials" => "Materi Pelajaran",
    "Additional Notes" => "Catatan Tambahan",
    "Documentation & References" => "Dokumentasi & Referensi",
    "Activity photos and reference sources." => "Foto kegiatan dan sumber referensi.",
    "Reference Link / Video" => "Link Referensi / Video",
    "Open Link" => "Buka Tautan",
    "Class Information" => "Informasi Kelas",
    "Execution Date" => "Tanggal Pelaksanaan",
    "Class" => "Kelas",
    "Tutor" => "Pengajar",
    "Attendance Status" => "Status Kehadiran",
    "Not Attended Yet" => "Belum Diabsen",
    "See Detail" => "Lihat Detail",
    "Journal Details" => "Detail Jurnal",
    "Profile" => "Profil"
];

foreach ($new_translations as $key => $val) {
    if (!isset($data[$key])) {
        $data[$key] = $val;
    }
}

file_put_contents('lang/id.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo "id.json updated.";
