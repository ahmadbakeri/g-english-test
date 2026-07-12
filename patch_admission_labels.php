<?php
// 1. Update translations for Admission, Admissions, New admission, No admission
$additions = [
    "Admission" => [
        "id" => "Pendaftaran",
        "zh_CN" => "招生",
        "zh_PY" => "Zhāoshēng"
    ],
    "Admissions" => [
        "id" => "Pendaftaran",
        "zh_CN" => "招生",
        "zh_PY" => "Zhāoshēng"
    ],
    "New Admission" => [
        "id" => "Pendaftaran Baru",
        "zh_CN" => "新招生",
        "zh_PY" => "Xīn Zhāoshēng"
    ],
    "No admissions" => [
        "id" => "Tidak ada pendaftaran",
        "zh_CN" => "没有招生",
        "zh_PY" => "Méiyǒu Zhāoshēng"
    ],
    "New admission" => [
        "id" => "Pendaftaran Baru",
        "zh_CN" => "新招生",
        "zh_PY" => "Xīn Zhāoshēng"
    ],
    "No admission" => [
        "id" => "Tidak ada pendaftaran",
        "zh_CN" => "没有招生",
        "zh_PY" => "Méiyǒu Zhāoshēng"
    ]
];

$langs = ['id', 'zh_CN', 'zh_PY'];
foreach ($langs as $lang) {
    $file = "lang/{$lang}.json";
    $data = [];
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true) ?: [];
    }
    
    foreach ($additions as $key => $translations) {
        $data[$key] = $translations[$lang];
    }
    
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

echo "Translations updated.\n";
