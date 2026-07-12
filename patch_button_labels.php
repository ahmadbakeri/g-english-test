<?php
$additions = [
    "Create" => [
        "id" => "Buat",
        "zh_CN" => "创建",
        "zh_PY" => "Chuàngjiàn",
        "ja" => "作成",
        "ja_RM" => "Sakusei"
    ],
    "Create & create another" => [
        "id" => "Buat & buat lainnya",
        "zh_CN" => "创建并创建另一个",
        "zh_PY" => "Chuàngjiàn bìng chuàngjiàn lìng yīgè",
        "ja" => "作成して次を作成",
        "ja_RM" => "Sakusei shite tsugi o sakusei"
    ],
    "Cancel" => [
        "id" => "Batal",
        "zh_CN" => "取消",
        "zh_PY" => "Qǔxiāo",
        "ja" => "キャンセル",
        "ja_RM" => "Kyanseru"
    ],
    "Save changes" => [
        "id" => "Simpan perubahan",
        "zh_CN" => "保存更改",
        "zh_PY" => "Bǎocún gēnggǎi",
        "ja" => "変更を保存",
        "ja_RM" => "Henkō o hozon"
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

echo "Button Translations updated.\n";
