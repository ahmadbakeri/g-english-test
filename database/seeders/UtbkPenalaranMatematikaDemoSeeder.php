<?php

namespace Database\Seeders;

use App\Models\CbtExam;
use App\Models\CbtImportBatch;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\User;
use App\Services\CbtDocx\DocxImportService;
use App\Services\CbtDocx\DraftPublishService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class UtbkPenalaranMatematikaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $class = ClassRoom::where('name', 'like', '%SMA%')->first() ?? ClassRoom::first();
        $subject = Subject::firstOrCreate(
            ['name' => 'Penalaran Matematika (UTBK-SNBT)'],
            ['code' => 'UTBK-PM']
        );

        $exam = CbtExam::updateOrCreate(
            ['title' => 'G-Tryout UTBK-SNBT 2026/27 — Penalaran Matematika'],
            [
                'description' => 'Tryout UTBK-SNBT Penalaran Matematika (Mode Penilaian Ditunda / Proses Admin IRT). Seluruh jawaban terekam tanpa nilai langsung.',
                'class_id' => $class?->id ?? 1,
                'subject_id' => $subject->id,
                'cbt_mode' => 'internal_excel',
                'scoring_mode' => 'deferred',
                'duration_minutes' => 45,
                'max_attempts' => 10,
                'is_active' => true,
                'shuffle_questions' => false,
                'shuffle_options' => false,
                'show_result_immediately' => false,
                'allow_review' => false,
            ]
        );

        // Build clean demo .docx mirroring the real G-Tryout UTBK-SNBT Penalaran Matematika document
        $storedPath = 'cbt_docx_imports/demo_g_tryout_utbk_pm_2026.docx';
        $fullPath = Storage::disk('local')->path($storedPath);
        @mkdir(dirname($fullPath), 0777, true);
        $this->buildDemoDocx($fullPath);

        // Remove previous demo batch if re-running seeder
        CbtImportBatch::where('original_filename', 'G-Tryout-UTBK-SNBT-Penalaran-Matematika-Demo.docx')->get()->each->delete();

        $batch = (new DocxImportService())->import(
            storedLocalPath: $storedPath,
            originalFilename: 'G-Tryout-UTBK-SNBT-Penalaran-Matematika-Demo.docx',
            cbtExamId: $exam->id,
            uploadedBy: $admin?->id,
            customTitle: 'G-Tryout UTBK-SNBT 2026/27 — Penalaran Matematika (Demo)'
        );

        (new DraftPublishService())->publish($batch, $exam, (int) ($admin?->id ?? 1), 'replace');
    }

    protected function buildDemoDocx(string $outputPath): void
    {
        $zip = new ZipArchive();
        $zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
  <Override PartName="/word/numbering.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.numbering+xml"/>
</Types>
XML);

        $zip->addFromString('_rels/.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>
XML);

        $zip->addFromString('word/_rels/document.xml.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rIdNum" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/numbering" Target="numbering.xml"/>
</Relationships>
XML);

        $zip->addFromString('word/numbering.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:numbering xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:abstractNum w:abstractNumId="1">
    <w:lvl w:ilvl="0"><w:numFmt w:val="decimal"/><w:lvlText w:val="%1."/></w:lvl>
    <w:lvl w:ilvl="1"><w:numFmt w:val="upperLetter"/><w:lvlText w:val="%2."/></w:lvl>
  </w:abstractNum>
  <w:abstractNum w:abstractNumId="2">
    <w:lvl w:ilvl="0"><w:numFmt w:val="bullet"/><w:lvlText w:val="✓"/></w:lvl>
  </w:abstractNum>
  <w:num w:numId="1"><w:abstractNumId w:val="1"/></w:num>
  <w:num w:numId="2"><w:abstractNumId w:val="2"/></w:num>
</w:numbering>
XML);

        $zip->addFromString('word/document.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
            xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math">
  <w:body>
    <w:p><w:r><w:t>G-Tryout UTBK-SNBT 2026/27 : November 2026</w:t></w:r></w:p>
    <w:p><w:r><w:t>Penalaran Matematika</w:t></w:r></w:p>

    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>Sebuah dealer sepeda motor menjual motor bekas seharga Rp 12.000.000,00. Jika dibeli tunai, pembeli mendapat diskon 15% dari harga tersebut. Pembelian juga dapat dilakukan secara kredit tanpa DP dengan bunga tunggal 1,5% per bulan dari harga awal. Selain itu, dealer menyediakan pilihan pembayaran dengan DP Rp 3.000.000,00 dan angsuran tetap sebesar Rp 635.000,00 per bulan.</w:t></w:r></w:p>
    <w:p><w:r><w:t>Raka membawa uang sebesar Rp 3.500.000,00. Ia memilih membeli motor dengan membayar DP. Selain itu, ia membeli sarung tangan seharga Rp 150.000,00 dan helm seharga Rp 200.000,00 di dealer tersebut. Sisa uang Raka adalah ....</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>Rp 100.000,00</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>Rp 150.000,00</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>Rp 200.000,00</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>Rp 250.000,00</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>Rp 300.000,00</w:t></w:r></w:p>

    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>Suatu mesin penggiling padi dilengkapi dengan dua buah roda C dan D, yang masing-masing beradius 70 cm dan 40 cm. Kedua roda terhubung oleh sebuah sabuk. Titik M dan N masing-masing berada pada permukaan roda.</w:t></w:r></w:p>
    <w:p>
      <w:r><w:t xml:space="preserve">Jika jarak M ke N adalah </w:t></w:r>
      <m:oMath><m:rad><m:deg/><m:e><m:r><m:t>89.100</m:t></m:r></m:e></m:rad></m:oMath>
      <w:r><w:t xml:space="preserve"> cm, jarak antara kedua pusat adalah ....</w:t></w:r>
    </w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>2 meter</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>2,2 meter</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>2,5 meter</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>3 meter</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>3,2 meter</w:t></w:r></w:p>

    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>Pada mesin penggiling padi yang sama, jika jarak antar pusat roda 1 m dan titik M' bergerak sampai N' (roda berputar searah jarum jam), maka roda C berputar sebanyak .... kali.</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><m:oMath><m:f><m:num><m:rad><m:deg/><m:e><m:r><m:t>91</m:t></m:r></m:e></m:rad></m:num><m:den><m:r><m:t>14𝜋</m:t></m:r></m:den></m:f></m:oMath></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><m:oMath><m:f><m:num><m:rad><m:deg/><m:e><m:r><m:t>92</m:t></m:r></m:e></m:rad></m:num><m:den><m:r><m:t>14𝜋</m:t></m:r></m:den></m:f></m:oMath></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><m:oMath><m:f><m:num><m:rad><m:deg/><m:e><m:r><m:t>93</m:t></m:r></m:e></m:rad></m:num><m:den><m:r><m:t>14𝜋</m:t></m:r></m:den></m:f></m:oMath></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><m:oMath><m:f><m:num><m:rad><m:deg/><m:e><m:r><m:t>94</m:t></m:r></m:e></m:rad></m:num><m:den><m:r><m:t>14𝜋</m:t></m:r></m:den></m:f></m:oMath></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><m:oMath><m:f><m:num><m:rad><m:deg/><m:e><m:r><m:t>95</m:t></m:r></m:e></m:rad></m:num><m:den><m:r><m:t>14𝜋</m:t></m:r></m:den></m:f></m:oMath></w:p>

    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>Sebuah toko alat tulis mencatat penjualan kertas dalam tiga bulan terakhir dalam tabel berikut:</w:t></w:r></w:p>
    <w:tbl>
      <w:tr>
        <w:tc><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Bulan Ke -</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Jumlah Kertas (Dalam Satuan Rim)</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Harga Kertas Per Rim</w:t></w:r></w:p></w:tc>
      </w:tr>
      <w:tr>
        <w:tc><w:p><w:r><w:t>1</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>5.000</w:t></w:r></w:p></w:tc>
        <w:tc><w:tcPr><w:vMerge w:val="restart"/></w:tcPr><w:p><w:r><w:t>Rp 30.000,00</w:t></w:r></w:p></w:tc>
      </w:tr>
      <w:tr>
        <w:tc><w:p><w:r><w:t>2</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>4.000</w:t></w:r></w:p></w:tc>
        <w:tc><w:tcPr><w:vMerge/></w:tcPr><w:p/></w:tc>
      </w:tr>
      <w:tr>
        <w:tc><w:p><w:r><w:t>3</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>6.000</w:t></w:r></w:p></w:tc>
        <w:tc><w:tcPr><w:vMerge/></w:tcPr><w:p/></w:tc>
      </w:tr>
    </w:tbl>
    <w:p><w:r><w:t>Setiap bulan 75% dari seluruh transaksi dibayar tunai. Berdasarkan informasi di atas, tentukan benar atau salah dari pilihan yang tersedia pada setiap baris.</w:t></w:r></w:p>
    <w:tbl>
      <w:tr>
        <w:tc><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>No</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Pernyataan</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Benar</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Salah</w:t></w:r></w:p></w:tc>
      </w:tr>
      <w:tr>
        <w:tc><w:p><w:r><w:t>1</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>Rata-rata pembelian kertas per bulan dengan pembayaran non tunai adalah 1.250 rim</w:t></w:r></w:p></w:tc>
        <w:tc><w:p/></w:tc><w:tc><w:p/></w:tc>
      </w:tr>
      <w:tr>
        <w:tc><w:p><w:r><w:t>2</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>Biaya tambahan dari pembayaran non tunai pada bulan ke-2 adalah Rp 3.300.000,00</w:t></w:r></w:p></w:tc>
        <w:tc><w:p/></w:tc><w:tc><w:p/></w:tc>
      </w:tr>
      <w:tr>
        <w:tc><w:p><w:r><w:t>3</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>Pajak yang harus dibayarkan dari pembayaran non tunai pada bulan ke-3 adalah Rp 4.545.000,00</w:t></w:r></w:p></w:tc>
        <w:tc><w:p/></w:tc><w:tc><w:p/></w:tc>
      </w:tr>
    </w:tbl>

    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>Dari survei terhadap 120 siswa di SMA Gen Jaya, diperoleh data sebagai berikut:</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="2"/></w:numPr></w:pPr><w:r><w:t>70 siswa mengikuti ekstrakurikuler Basket</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="2"/></w:numPr></w:pPr><w:r><w:t>50 siswa menyukai mata pelajaran Matematika, 15 di antaranya tidak mengikuti Basket</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="2"/></w:numPr></w:pPr><w:r><w:t>45 siswa menyukai mata pelajaran Kimia, 30 di antaranya mengikuti Basket</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="2"/></w:numPr></w:pPr><w:r><w:t>15 siswa menyukai Matematika dan Kimia</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="2"/></w:numPr></w:pPr><w:r><w:t>40 siswa tidak menyukai Matematika maupun Kimia, 18 di antaranya mengikuti Basket</w:t></w:r></w:p>
    <w:p><w:r><w:t>Banyak siswa yang mengikuti Basket serta menyukai Matematika dan Kimia adalah ....</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>13 siswa</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>14 siswa</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>15 siswa</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>16 siswa</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>17 siswa</w:t></w:r></w:p>
  </w:body>
</w:document>
XML);

        $zip->close();
    }
}
