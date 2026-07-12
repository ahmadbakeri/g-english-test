<?php
$json = file_get_contents('lang/id.json');
$data = json_decode($json, true);

$new_translations = [
    "Financial & Admission Status" => "Status Keuangan & Pendaftaran",
    "Tutoring Fee (60x meetings, 3x/week). Includes Uniform and Books." => "Biaya Bimbingan (60x pertemuan, 3x/minggu). Sudah termasuk Baju dan Buku.",
    "Tuition Fee" => "Biaya Bimbingan",
    "Registration Fee" => "Biaya Pendaftaran",
    "Apply 25% Discount" => "Terapkan Diskon 25%",
    "Total Fee" => "Total Biaya",
    "Payment Method" => "Metode Pembayaran",
    "Full Payment" => "Lunas",
    "Installment (4x)" => "Angsuran (4x)",
    "Amount Paid (DP / Full)" => "Jumlah yang Dibayar (DP / Lunas)",
    "Minimum DP is Rp 600.000" => "Minimal DP adalah Rp 600.000",
    "Enter the full amount" => "Masukkan jumlah lunas",
    "For installments, the minimum payment is Rp 600.000 (DP + Registration)." => "Untuk angsuran, pembayaran minimal adalah Rp 600.000 (DP + Pendaftaran).",
    "For full payment, the amount must equal the Total Fee." => "Untuk pembayaran lunas, jumlah harus sama dengan Total Biaya."
];

foreach ($new_translations as $key => $val) {
    if (!isset($data[$key])) {
        $data[$key] = $val;
    }
}

file_put_contents('lang/id.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo "id.json updated for financial fields.\n";
