<?php

namespace Tests\Feature;

use App\Exports\CbtRawResponsesExport;
use App\Imports\CbtAdminScoresImport;
use App\Models\CbtAnswer;
use App\Models\CbtAttempt;
use App\Models\CbtExam;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\CbtDocx\DocxImportService;
use App\Services\CbtDocx\DocxTemplateGenerator;
use App\Services\CbtDocx\DraftPublishService;
use App\Services\CbtDocx\OmmlToLatexConverter;
use DOMDocument;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class CbtDocxImportTest extends TestCase
{
    use DatabaseTransactions;

    public function test_omml_converter_handles_nested_radical_fraction_and_unicode_pi(): void
    {
        $xml = <<<'XML'
<m:oMath xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math">
  <m:f>
    <m:num>
      <m:rad>
        <m:radPr><m:degHide m:val="1"/></m:radPr>
        <m:deg/>
        <m:e><m:r><m:t>91</m:t></m:r></m:e>
      </m:rad>
    </m:num>
    <m:den>
      <m:r><m:t>14𝜋</m:t></m:r>
    </m:den>
  </m:f>
</m:oMath>
XML;
        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $converter = new OmmlToLatexConverter();
        $result = $converter->convert($dom->documentElement, false);

        $this->assertSame('\frac{\sqrt{91}}{14\pi}', $result['latex']);
        $this->assertSame('\(\frac{\sqrt{91}}{14\pi}\)', $result['wrapped']);
        $this->assertEmpty($result['warnings']);
    }

    public function test_docx_import_parses_genza_autonumbering_equations_tables_complex_tf_shapes_and_images(): void
    {
        $storedPath = 'cbt_docx_imports/test_genza_utbk_' . uniqid() . '.docx';
        $fullPath = Storage::disk('local')->path($storedPath);
        @mkdir(dirname($fullPath), 0777, true);

        $this->createGenzaStyleSampleDocx($fullPath);

        $user = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $service = new DocxImportService();
        $batch = $service->import($storedPath, 'G-Tryout-UTBK-PM.docx', null, $user->id);

        try {
            $this->assertSame('parsed', $batch->status);
            $this->assertSame(5, $batch->total_questions);
            $this->assertSame(0, $batch->error_count);
            $this->assertStringContainsString('G-Tryout UTBK-SNBT', $batch->title);

            $drafts = $batch->drafts()->orderBy('sequence_no')->get();

            // Q1: Standard auto-numbered MCQ with radical OMML + extracted inline PNG
            $q1 = $drafts[0];
            $this->assertSame('mcq', $q1->question_type);
            $this->assertStringContainsString('\(\sqrt{89.100}\)', $q1->question_html);
            $this->assertStringContainsString('<img src="/storage/cbt_media/batch_' . $batch->id . '/', $q1->question_html);
            $this->assertSame('2 meter', trim(strip_tags((string) $q1->option_a)));
            $this->assertSame('3,2 meter', trim(strip_tags((string) $q1->option_e)));
            $this->assertNull($q1->correct_answer);
            $this->assertSame('valid', $q1->validation_status);

            // Q2: Nested OMML fraction+sqrt+unicode pi inside options A-E
            $q2 = $drafts[1];
            $this->assertSame('mcq', $q2->question_type);
            $this->assertStringContainsString('\(\frac{\sqrt{91}}{14\pi}\)', (string) $q2->option_a);
            $this->assertStringContainsString('\(\frac{\sqrt{95}}{14\pi}\)', (string) $q2->option_e);

            // Q3: Manual typed options ("A.  3" .. "E.  7") like Question #8
            $q3 = $drafts[2];
            $this->assertSame('mcq', $q3->question_type);
            $this->assertSame('3', trim(strip_tags((string) $q3->option_a)));
            $this->assertSame('7', trim(strip_tags((string) $q3->option_e)));

            // Q4: Merged-cell table (rowspan="3") + Word Shapes flowchart warning + Complex True/False statement table like Question #13
            $q4 = $drafts[3];
            $this->assertSame('complex_tf', $q4->question_type);
            $this->assertStringContainsString('rowspan="3"', $q4->question_html);
            $this->assertTrue($q4->has_shape_warning);
            $this->assertSame('warning', $q4->validation_status);
            $this->assertCount(3, $q4->statements);
            $this->assertStringContainsString('1.250 rim', $q4->statements[0]['text']);

            // Q5: Bullet list (✓) inside question body like Question #17
            $q5 = $drafts[4];
            $this->assertSame('mcq', $q5->question_type);
            $this->assertStringContainsString('<ul>', $q5->question_html);
            $this->assertStringContainsString('<li>70 siswa mengikuti ekstrakurikuler Basket</li>', $q5->question_html);
            $this->assertSame('13 siswa', trim(strip_tags((string) $q5->option_a)));

            // Publish to a deferred-scoring exam and verify raw responses & deferred scoring (no score shown!)
            $class = ClassRoom::first() ?? ClassRoom::create(['name' => 'UTBK Test Class']);
            $subject = Subject::first() ?? Subject::create(['name' => 'Penalaran Matematika']);
            $exam = CbtExam::create([
                'title' => 'UTBK Deferred Test Exam',
                'class_id' => $class->id,
                'subject_id' => $subject->id,
                'cbt_mode' => 'internal_excel',
                'scoring_mode' => 'deferred',
                'duration_minutes' => 30,
                'max_attempts' => 3,
                'is_active' => true,
                'show_result_immediately' => false,
            ]);

            $published = (new DraftPublishService())->publish($batch, $exam, $user->id, 'replace');
            $this->assertSame(5, $published);
            $this->assertSame('published', $batch->fresh()->status);

            // Simulate Carmen submitting raw answers (MCQ + Complex TF) under deferred mode
            $carmenUser = User::where('username', 'carmen')->first();
            if (!$carmenUser) {
                $carmenUser = User::create([
                    'name' => 'Carmen',
                    'username' => 'carmen_test_' . uniqid(),
                    'email' => 'carmen_test_' . uniqid() . '@g-english.com',
                    'password' => 'h2hcarmen',
                    'role' => 'siswa',
                ]);
            }
            $carmenStudent = $carmenUser->student ?? Student::create([
                'user_id' => $carmenUser->id,
                'nis' => 'CARMEN-T-' . uniqid(),
                'name' => 'Carmen',
                'student_type' => 'internal',
                'is_active' => true,
            ]);

            $attempt = CbtAttempt::create([
                'cbt_exam_id' => $exam->id,
                'student_id' => $carmenStudent->id,
                'attempt_number' => 1,
                'start_time' => now(),
                'status' => 'in_progress',
                'scoring_status' => 'pending_admin',
                'is_completed' => false,
            ]);

            $questions = $exam->questions()->orderBy('order_number')->get();
            $this->actingAs($carmenUser);
            \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('student'));
            $page = new \App\Filament\Student\Pages\TakeCbtExam();
            $page->record = $exam->id;
            $page->exam = $exam;
            $page->attempt = $attempt;
            $page->submitExam([
                $questions[0]->id => 'D',
                $questions[1]->id => 'A',
                $questions[2]->id => 'B',
                $questions[3]->id => ['1' => 'B', '2' => 'S', '3' => 'B'],
                $questions[4]->id => 'C',
            ]);

            $attempt->refresh();
            $this->assertTrue($attempt->is_completed);
            $this->assertNull($attempt->score);
            $this->assertSame('pending_admin', $attempt->scoring_status);
            $this->assertSame(5, CbtAnswer::where('cbt_attempt_id', $attempt->id)->count());

            $complexAnswer = CbtAnswer::where('cbt_attempt_id', $attempt->id)
                ->where('cbt_question_id', $questions[3]->id)
                ->first();
            $this->assertSame('1:B | 2:S | 3:B', $complexAnswer->selected_answer);
            $this->assertSame(['1' => 'B', '2' => 'S', '3' => 'B'], $complexAnswer->answer_payload);
            $this->assertNull($complexAnswer->is_correct);

            // Verify raw IRT export collection includes attempt & complex answer string
            $export = new CbtRawResponsesExport($exam);
            $rows = $export->collection();
            $this->assertCount(1, $rows);
            $this->assertSame('1:B | 2:S | 3:B', $rows[0][11]); // 8 metadata cols + Q4 (index 11)

            // Verify Admin Score upload updates deferred attempt cleanly
            $scoreImporter = new CbtAdminScoresImport($exam->id);
            $scoreImporter->collection(collect([
                ['attempt_id' => $attempt->id, 'nis' => $carmenStudent->nis, 'skor' => '742.50'],
            ]));
            $attempt->refresh();
            $this->assertSame(1, $scoreImporter->updatedCount);
            $this->assertEquals(742.50, (float) $attempt->score);
            $this->assertSame('manual_uploaded', $attempt->scoring_status);
        } finally {
            @unlink($fullPath);
            if ($batch->media_dir && Storage::disk('public')->exists($batch->media_dir)) {
                Storage::disk('public')->deleteDirectory($batch->media_dir);
            }
        }
    }

    public function test_docx_template_generator_outputs_parseable_explicit_tag_docx(): void
    {
        $tempPath = (new DocxTemplateGenerator())->generateTempFile();
        $storedPath = 'cbt_docx_imports/tpl_test_' . uniqid() . '.docx';
        Storage::disk('local')->put($storedPath, file_get_contents($tempPath));
        @unlink($tempPath);

        $user = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $batch = (new DocxImportService())->import($storedPath, 'template.docx', null, $user->id);

        try {
            $this->assertSame('parsed', $batch->status);
            $this->assertSame(3, $batch->total_questions);
            $this->assertSame(0, $batch->error_count);

            $drafts = $batch->drafts()->orderBy('sequence_no')->get();
            $this->assertSame('mcq', $drafts[0]->question_type);
            $this->assertSame('D', $drafts[0]->correct_answer);
            $this->assertStringContainsString('\(\sqrt{89.100}\)', $drafts[0]->question_html);

            $this->assertSame('complex_tf', $drafts[1]->question_type);
            $this->assertCount(3, $drafts[1]->statements);

            $this->assertSame('short_answer', $drafts[2]->question_type);
            $this->assertSame('7', $drafts[2]->correct_answer);
        } finally {
            Storage::disk('local')->delete($storedPath);
            if ($batch->media_dir && Storage::disk('public')->exists($batch->media_dir)) {
                Storage::disk('public')->deleteDirectory($batch->media_dir);
            }
        }
    }

    public function test_admin_staging_preview_renders_http_200(): void
    {
        $admin = User::where('role', 'admin')->first();
        $batch = \App\Models\CbtImportBatch::latest('id')->first();

        $this->assertNotNull($admin);
        $this->assertNotNull($batch);

        $this->actingAs($admin)
            ->get('/admin/academic/cbt-import-batches')
            ->assertOk();

        $this->actingAs($admin)
            ->get("/admin/academic/cbt-import-batches/{$batch->id}/review")
            ->assertOk()
            ->assertSee('vendor/katex/katex.min.css', false)
            ->assertSee('Publish', false);
    }

    public function test_carmen_cbt_pages_render_http_200(): void
    {
        $carmen = User::where('username', 'carmen')->first();
        $exam = CbtExam::where('title', 'like', '%Penalaran Matematika%')->first();

        $this->assertNotNull($carmen);
        $this->assertNotNull($exam);

        $this->actingAs($carmen)
            ->get('/student/student-cbt-exams')
            ->assertOk()
            ->assertSee('Penalaran Matematika', false);

        $this->actingAs($carmen)
            ->get("/student/take-cbt-exam/{$exam->id}")
            ->assertOk()
            ->assertSee('vendor/katex/katex.min.css', false);
    }

    protected function createGenzaStyleSampleDocx(string $outputPath): void
    {
        // Minimal 1x1 blue PNG for inline image test
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');

        $zip = new ZipArchive();
        $zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Default Extension="png" ContentType="image/png"/>
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
  <Relationship Id="rIdImg1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/roda.png"/>
</Relationships>
XML);

        $zip->addFromString('word/media/roda.png', $png);

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
            xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"
            xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math"
            xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"
            xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"
            xmlns:wps="http://schemas.microsoft.com/office/word/2010/wordprocessingShape">
  <w:body>
    <w:p><w:r><w:t>G-Tryout UTBK-SNBT 2026/27 : November 2026</w:t></w:r></w:p>
    <w:p><w:r><w:t>Penalaran Matematika</w:t></w:r></w:p>

    <w:p>
      <w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr></w:pPr>
      <w:r><w:t>Suatu mesin penggiling padi dilengkapi dua roda C dan D.</w:t></w:r>
    </w:p>
    <w:p>
      <w:r>
        <w:drawing>
          <wp:inline>
            <a:graphic><a:graphicData><a:blip r:embed="rIdImg1"/></a:graphicData></a:graphic>
          </wp:inline>
        </w:drawing>
      </w:r>
    </w:p>
    <w:p>
      <w:r><w:t xml:space="preserve">Jika jarak M ke N adalah </w:t></w:r>
      <m:oMath><m:rad><m:radPr><m:degHide m:val="1"/></m:radPr><m:deg/><m:e><m:r><m:t>89.100</m:t></m:r></m:e></m:rad></m:oMath>
      <w:r><w:t xml:space="preserve"> cm, jarak antara kedua pusat adalah ....</w:t></w:r>
    </w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>2 meter</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>2,2 meter</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>2,5 meter</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>3 meter</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>3,2 meter</w:t></w:r></w:p>

    <w:p>
      <w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr></w:pPr>
      <w:r><w:t>Jika jarak antar pusat roda 1 m, maka roda C berputar sebanyak .... kali.</w:t></w:r>
    </w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><m:oMath><m:f><m:num><m:rad><m:deg/><m:e><m:r><m:t>91</m:t></m:r></m:e></m:rad></m:num><m:den><m:r><m:t>14𝜋</m:t></m:r></m:den></m:f></m:oMath></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><m:oMath><m:f><m:num><m:rad><m:deg/><m:e><m:r><m:t>92</m:t></m:r></m:e></m:rad></m:num><m:den><m:r><m:t>14𝜋</m:t></m:r></m:den></m:f></m:oMath></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><m:oMath><m:f><m:num><m:rad><m:deg/><m:e><m:r><m:t>93</m:t></m:r></m:e></m:rad></m:num><m:den><m:r><m:t>14𝜋</m:t></m:r></m:den></m:f></m:oMath></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><m:oMath><m:f><m:num><m:rad><m:deg/><m:e><m:r><m:t>94</m:t></m:r></m:e></m:rad></m:num><m:den><m:r><m:t>14𝜋</m:t></m:r></m:den></m:f></m:oMath></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="1"/><w:numId w:val="1"/></w:numPr></w:pPr><m:oMath><m:f><m:num><m:rad><m:deg/><m:e><m:r><m:t>95</m:t></m:r></m:e></m:rad></m:num><m:den><m:r><m:t>14𝜋</m:t></m:r></m:den></m:f></m:oMath></w:p>

    <w:p>
      <w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr></w:pPr>
      <w:r><w:t>Posisi titik M dan N akan kembali pada posisi semula pada saat roda D berputar sebanyak .... kali</w:t></w:r>
    </w:p>
    <w:p><w:r><w:t>A.  3</w:t></w:r></w:p>
    <w:p><w:r><w:t>B.  4</w:t></w:r></w:p>
    <w:p><w:r><w:t>C.  5</w:t></w:r></w:p>
    <w:p><w:r><w:t>D.  6</w:t></w:r></w:p>
    <w:p><w:r><w:t>E.  7</w:t></w:r></w:p>

    <w:p>
      <w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr></w:pPr>
      <w:r><w:t>Sebuah toko alat tulis mencatat penjualan kertas dalam tiga bulan terakhir dalam tabel berikut:</w:t></w:r>
    </w:p>
    <w:tbl>
      <w:tr>
        <w:tc><w:p><w:r><w:t>Bulan Ke -</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>Jumlah Kertas (Rim)</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>Harga Kertas Per Rim</w:t></w:r></w:p></w:tc>
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
    <w:p>
      <w:r><w:t>Prosedur cara pembayaran dapat dilihat pada diagram alir berikut.</w:t></w:r>
      <w:r><w:drawing><wp:inline><wps:wsp/></wp:inline></w:drawing></w:r>
    </w:p>
    <w:tbl>
      <w:tr>
        <w:tc><w:p><w:r><w:t>No</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>Pernyataan</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>Benar</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>Salah</w:t></w:r></w:p></w:tc>
      </w:tr>
      <w:tr>
        <w:tc><w:p><w:r><w:t>1</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>Rata-rata pembelian kertas per bulan dengan pembayaran non tunai adalah 1.250 rim</w:t></w:r></w:p></w:tc>
        <w:tc><w:p/></w:tc><w:tc><w:p/></w:tc>
      </w:tr>
      <w:tr>
        <w:tc><w:p><w:r><w:t>2</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>Biaya tambahan non tunai bulan ke-2 adalah Rp 3.300.000,00</w:t></w:r></w:p></w:tc>
        <w:tc><w:p/></w:tc><w:tc><w:p/></w:tc>
      </w:tr>
      <w:tr>
        <w:tc><w:p><w:r><w:t>3</w:t></w:r></w:p></w:tc>
        <w:tc><w:p><w:r><w:t>Pajak non tunai bulan ke-3 adalah Rp 4.545.000,00</w:t></w:r></w:p></w:tc>
        <w:tc><w:p/></w:tc><w:tc><w:p/></w:tc>
      </w:tr>
    </w:tbl>

    <w:p>
      <w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr></w:pPr>
      <w:r><w:t>Dari survei terhadap 120 siswa di SMA Gen Jaya, diperoleh data:</w:t></w:r>
    </w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="2"/></w:numPr></w:pPr><w:r><w:t>70 siswa mengikuti ekstrakurikuler Basket</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="2"/></w:numPr></w:pPr><w:r><w:t>50 siswa menyukai mata pelajaran Matematika</w:t></w:r></w:p>
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
