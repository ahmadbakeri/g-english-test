<?php
$json = file_get_contents('lang/id.json');
$data = json_decode($json, true);

$new_translations = [
    "Mother's Data" => "Data Ibu",
    "Mother's Name" => "Nama Ibu",
    "Mother's Phone" => "No. HP Ibu",
    "Mother's Email" => "Email Ibu",
    "Mother's Occupation" => "Pekerjaan Ibu",
    "Father's Data" => "Data Ayah",
    "Father's Name" => "Nama Ayah",
    "Father's Phone" => "No. HP Ayah",
    "Father's Email" => "Email Ayah",
    "Father's Occupation" => "Pekerjaan Ayah",
    "Payment Status" => "Status Pembayaran",
    "Paid" => "Sudah Bayar / Lunas",
    "Unpaid" => "Belum Bayar"
];

foreach ($new_translations as $key => $val) {
    if (!isset($data[$key])) {
        $data[$key] = $val;
    }
}

file_put_contents('lang/id.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo "id.json updated for parent and payment fields.\n";
