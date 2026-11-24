<?php

namespace App\Services\CbtDocx;

use ZipArchive;

class DocxTemplateGenerator
{
    /**
     * Build a clean, ready-to-edit .docx template file containing sample UTBK PK/PM questions
     * (MCQ + OMML equation + Complex True/False table + Short Answer) and return its temp file path.
     */
    public function generateTempFile(): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'cbt_tpl_') . '.docx';
        $zip = new ZipArchive();
        $zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('word/_rels/document.xml.rels', $this->docRelsXml());
        $zip->addFromString('word/document.xml', $this->documentXml());

        $zip->close();

        return $tempPath;
    }

    protected function contentTypesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
</Types>
XML;
    }

    protected function rootRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>
XML;
    }

    protected function docRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"/>
XML;
    }

    protected function documentXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
            xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math">
  <w:body>
    <w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Template Soal UTBK-SNBT (Pengetahuan Kuantitatif / Penalaran Matematika)</w:t></w:r></w:p>
    <w:p><w:r><w:t>Catatan: Sistem mendukung format Tag [SOAL]/[A]..[E] maupun format penomoran otomatis Word Genza (1., 2., ... dan A., B., ...). Tag [KUNCI] bersifat opsional untuk ujian dengan penilaian ditunda/IRT. Gunakan Insert -&gt; Equation untuk rumus dan gambar posisi In Line with Text.</w:t></w:r></w:p>
    <w:p><w:r><w:t></w:t></w:r></w:p>

    <w:p><w:r><w:rPr><w:b/></w:rPr><w:t>[SOAL]</w:t></w:r></w:p>
    <w:p>
      <w:r><w:t xml:space="preserve">Jika jarak M ke N adalah </w:t></w:r>
      <m:oMath><m:rad><m:radPr><m:degHide m:val="1"/></m:radPr><m:deg/><m:e><m:r><m:t>89.100</m:t></m:r></m:e></m:rad></m:oMath>
      <w:r><w:t xml:space="preserve"> cm dan nilai </w:t></w:r>
      <m:oMath><m:f><m:num><m:rad><m:radPr><m:degHide m:val="1"/></m:radPr><m:deg/><m:e><m:r><m:t>91</m:t></m:r></m:e></m:rad></m:num><m:den><m:r><m:t>14π</m:t></m:r></m:den></m:f></m:oMath>
      <w:r><w:t xml:space="preserve">, maka jarak antara kedua pusat adalah ....</w:t></w:r>
    </w:p>
    <w:p><w:r><w:t>[A] 2 meter</w:t></w:r></w:p>
    <w:p><w:r><w:t>[B] 2,2 meter</w:t></w:r></w:p>
    <w:p><w:r><w:t>[C] 2,5 meter</w:t></w:r></w:p>
    <w:p><w:r><w:t>[D] 3 meter</w:t></w:r></w:p>
    <w:p><w:r><w:t>[E] 3,2 meter</w:t></w:r></w:p>
    <w:p><w:r><w:t>[KUNCI] D</w:t></w:r></w:p>
    <w:p><w:r><w:t>[PEMBAHASAN] Opsional: masukkan pembahasan di sini (atau kosongkan jika penilaian IRT).</w:t></w:r></w:p>
    <w:p><w:r><w:t></w:t></w:r></w:p>

    <w:p><w:r><w:rPr><w:b/></w:rPr><w:t>[SOAL]</w:t></w:r></w:p>
    <w:p><w:r><w:t>Berdasarkan data penjualan kertas, tentukan benar atau salah dari pernyataan berikut pada setiap baris.</w:t></w:r></w:p>
    <w:tbl>
      <w:tr>
        <w:tc><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>No</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Pernyataan</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Benar</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Salah</w:t></w:r></w:p></w:tc>
      </w:tr>
      <w:tr>
        <w:tc><w:p><w:r><w:t>1</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>Rata-rata pembelian kertas per bulan non tunai adalah 1.250 rim</w:t></w:r></w:p></w:tc>
        <w:tc><w:p/></w:tc>
        <w:tc><w:p/></w:tc>
      </w:tr>
      <w:tr>
        <w:tc><w:p><w:r><w:t>2</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>Biaya tambahan non tunai pada bulan ke-2 adalah Rp 3.300.000,00</w:t></w:r></w:p></w:tc>
        <w:tc><w:p/></w:tc>
        <w:tc><w:p/></w:tc>
      </w:tr>
      <w:tr>
        <w:tc><w:p><w:r><w:t>3</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>Pajak pembayaran non tunai pada bulan ke-3 adalah Rp 4.545.000,00</w:t></w:r></w:p></w:tc>
        <w:tc><w:p/></w:tc>
        <w:tc><w:p/></w:tc>
      </w:tr>
    </w:tbl>
    <w:p><w:r><w:t></w:t></w:r></w:p>

    <w:p><w:r><w:rPr><w:b/></w:rPr><w:t>[SOAL]</w:t></w:r></w:p>
    <w:p><w:r><w:t>[TIPE] short_answer</w:t></w:r></w:p>
    <w:p><w:r><w:t>Jika 2x + 5 = 19, tuliskan nilai x (bilangan bulat).</w:t></w:r></w:p>
    <w:p><w:r><w:t>[KUNCI] 7</w:t></w:r></w:p>
  </w:body>
</w:document>
XML;
    }
}
