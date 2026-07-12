<?php
// 1. Rename files if needed
if (file_exists('lang/zh-CN.json')) rename('lang/zh-CN.json', 'lang/zh_CN.json');
if (file_exists('lang/zh-PY.json')) rename('lang/zh-PY.json', 'lang/zh_PY.json');

// 2. Define the missing translations
$additions = [
    "Accept & Create Student" => [
        "id" => "Setujui & Buat Siswa",
        "zh_CN" => "接受并创建学生",
        "zh_PY" => "Jiēshòu Bìng Chuàngjiàn Xuéshēng"
    ],
    "Registration No." => [
        "id" => "No. Pendaftaran",
        "zh_CN" => "注册号",
        "zh_PY" => "Zhùcè Hào"
    ],
    "Registration Date" => [
        "id" => "Tanggal Pendaftaran",
        "zh_CN" => "注册日期",
        "zh_PY" => "Zhùcè Rìqī"
    ],
    "Email" => [
        "id" => "Email",
        "zh_CN" => "电子邮件",
        "zh_PY" => "Diànzǐ Yóujiàn"
    ],
    "WhatsApp No." => [
        "id" => "No. WhatsApp",
        "zh_CN" => "WhatsApp 号码",
        "zh_PY" => "WhatsApp Hàomǎ"
    ],
    "Phone / WhatsApp No." => [
        "id" => "No. HP / WhatsApp",
        "zh_CN" => "电话 / WhatsApp",
        "zh_PY" => "Diànhuà / WhatsApp"
    ],
    "Previous School" => [
        "id" => "Asal Sekolah",
        "zh_CN" => "原学校",
        "zh_PY" => "Yuán Xuéxiào"
    ],
    "Internal Notes" => [
        "id" => "Catatan Internal",
        "zh_CN" => "内部备注",
        "zh_PY" => "Nèibù Bèizhù"
    ],
    // Common Filament Actions for Pinyin fallback
    "Edit" => [
        "id" => "Ubah",
        "zh_CN" => "编辑",
        "zh_PY" => "Biānjí"
    ],
    "View" => [
        "id" => "Lihat",
        "zh_CN" => "查看",
        "zh_PY" => "Chákàn"
    ],
    "Delete" => [
        "id" => "Hapus",
        "zh_CN" => "删除",
        "zh_PY" => "Shānchú"
    ],
    "Create" => [
        "id" => "Buat",
        "zh_CN" => "创建",
        "zh_PY" => "Chuàngjiàn"
    ],
    "Save" => [
        "id" => "Simpan",
        "zh_CN" => "保存",
        "zh_PY" => "Bǎocún"
    ],
    "Cancel" => [
        "id" => "Batal",
        "zh_CN" => "取消",
        "zh_PY" => "Qǔxiāo"
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
        if (!isset($data[$key]) && isset($translations[$lang])) {
            $data[$key] = $translations[$lang];
        }
    }
    
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

echo "Translations updated.\n";
