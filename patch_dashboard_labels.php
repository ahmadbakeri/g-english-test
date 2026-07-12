<?php
$additions = [
    "Dashboard" => [
        "id" => "Dasbor",
        "zh_CN" => "仪表板",
        "zh_PY" => "Yíbiǎobǎn",
        "ja" => "ダッシュボード",
        "ja_RM" => "Dasshubōdo"
    ],
    "Recent Activity" => [
        "id" => "Aktivitas Terbaru",
        "zh_CN" => "最近活动",
        "zh_PY" => "Zuìjìn huódòng",
        "ja" => "最近の活動",
        "ja_RM" => "Saikin no katsudō"
    ],
    "activity log" => [
        "id" => "Log aktivitas",
        "zh_CN" => "活动日志",
        "zh_PY" => "Huódòng rìzhì",
        "ja" => "活動ログ",
        "ja_RM" => "Katsudō rogu"
    ],
    "activity logs" => [
        "id" => "Log aktivitas",
        "zh_CN" => "活动日志",
        "zh_PY" => "Huódòng rìzhì",
        "ja" => "活動ログ",
        "ja_RM" => "Katsudō rogu"
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

echo "Dashboard Translations updated.\n";
