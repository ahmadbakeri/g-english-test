<?php
$additions = [
    "Filament Shield" => [
        "id" => "Hak Akses & Peran",
        "zh_CN" => "权限与角色",
        "zh_PY" => "Quánxiàn yǔ juésè",
        "ja" => "権限と役割",
        "ja_RM" => "Kengen to yakuwari"
    ]
];

$langs = ['id', 'zh_CN', 'zh_PY', 'ja', 'ja_RM'];
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

echo "Filament Shield Translations updated.\n";
