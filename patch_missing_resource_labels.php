<?php
$additions = [
    "Tutors" => [
        "id" => "Pengajar",
        "zh_CN" => "教师",
        "zh_PY" => "Jiàoshī",
        "ja" => "講師",
        "ja_RM" => "Kōshi",
        "ar" => "المعلمون",
        "ar_RM" => "Al-Mu'allimun"
    ],
    "Class Room" => [
        "id" => "Ruang Kelas",
        "zh_CN" => "教室",
        "zh_PY" => "Jiàoshì",
        "ja" => "教室",
        "ja_RM" => "Kyōshitsu",
        "ar" => "الفصل الدراسي",
        "ar_RM" => "Al-Fasl al-Dirasi"
    ],
    "Class Rooms" => [
        "id" => "Ruang Kelas",
        "zh_CN" => "教室",
        "zh_PY" => "Jiàoshì",
        "ja" => "教室",
        "ja_RM" => "Kyōshitsu",
        "ar" => "الفصول الدراسية",
        "ar_RM" => "Al-Fusul al-Dirasiyyah"
    ],
    "Subject" => [
        "id" => "Mata Pelajaran",
        "zh_CN" => "科目",
        "zh_PY" => "Kēmù",
        "ja" => "科目",
        "ja_RM" => "Kamoku",
        "ar" => "المادة",
        "ar_RM" => "Al-Maddah"
    ],
    "Subjects" => [
        "id" => "Mata Pelajaran",
        "zh_CN" => "科目",
        "zh_PY" => "Kēmù",
        "ja" => "科目",
        "ja_RM" => "Kamoku",
        "ar" => "المواد",
        "ar_RM" => "Al-Mawadd"
    ]
];

$langs = ['id', 'zh_CN', 'zh_PY', 'ja', 'ja_RM', 'ar', 'ar_RM'];
foreach ($langs as $lang) {
    $file = "lang/{$lang}.json";
    $data = [];
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true) ?: [];
    }
    
    foreach ($additions as $key => $translations) {
        if (!isset($data[$key])) {
            $data[$key] = $translations[$lang];
        }
    }
    
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

echo "Missing Resource Translations updated.\n";
