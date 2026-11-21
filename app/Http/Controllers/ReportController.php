<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TutorSalary;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function printTutorSalary($id)
    {
        $salary = TutorSalary::with('tutor')->findOrFail($id);

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $pdf = Pdf::loadView('reports.tutor-salary', [
            'salary' => $salary,
            'monthName' => $months[$salary->month]
        ]);

        return $pdf->stream("Slip_Gaji_Tutor_{$salary->tutor->name}_{$months[$salary->month]}_{$salary->year}.pdf");
    }

    public function printStudentPayment($id)
    {
        $payment = \App\Models\StudentPayment::with('student')->findOrFail($id);

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $activeMonth = $payment->paid_at ? $payment->paid_at->month : ($payment->due_date ? Carbon::parse($payment->due_date)->month : date('n'));
        $activeYear = $payment->paid_at ? $payment->paid_at->year : ($payment->due_date ? Carbon::parse($payment->due_date)->year : date('Y'));
        
        $monthName = $months[$activeMonth] ?? '';

        $remainingBalance = \App\Models\StudentPayment::where('student_id', $payment->student_id)
            ->where('status', 'unpaid')
            ->sum('amount');

        $picName = \App\Models\Setting::where('key', 'receipt_pic_name')->value('value') ?? auth()->user()?->name ?? 'Admin Keuangan';

        $pdf = Pdf::loadView('reports.student-payment', [
            'payment' => $payment,
            'monthName' => $monthName,
            'remainingBalance' => $remainingBalance,
            'picName' => $picName
        ]);

        return $pdf->stream("Kuitansi_Pembayaran_{$payment->student->name}_{$monthName}_{$activeYear}.pdf");
    }

    public function printCertificate($id)
    {
        $student = \App\Models\Student::findOrFail($id);

        $pdf = Pdf::loadView('reports.certificate', [
            'student' => $student
        ])->setPaper('a4', 'landscape');

        return $pdf->stream("Sertifikat_{$student->name}.pdf");
    }

    public function printEvaluation($id)
    {
        $evaluation = \App\Models\Evaluation::with(['student', 'subject'])->findOrFail($id);

        $pdf = Pdf::loadView('reports.evaluation', [
            'evaluation' => $evaluation
        ]);

        return $pdf->stream("Evaluasi_{$evaluation->student->name}_{$evaluation->subject->name}.pdf");
    }

    public function printBrainChampionCard($studentId)
    {
        $student = \App\Models\Student::findOrFail($studentId);
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && !($user->role === 'school' && (int) $student->school_user_id === (int) $user->id))) {
            abort(403, 'Unauthorized access to participant card.');
        }

        $code = $student->competition_code ?: $student->nis ?: ('BC-' . $student->id);
        $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(120)
            ->margin(1)
            ->generate($code);

        $pdf = Pdf::loadView('reports.brain-champion-card', [
            'student' => $student,
            'qrCodeBase64' => base64_encode((string) $qrSvg),
        ])->setPaper('a5', 'landscape');

        return $pdf->stream("Kartu_Peserta_Brain_Champion_{$code}.pdf");
    }

    public function printBrainChampionRoster($grade)
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Only administrators can print competition roll-call sheets.');
        }

        $allowedGrades = $grade === 'all'
            ? [4, 5, 6, 7, 8, 9]
            : (in_array((int) $grade, [4, 5, 6, 7, 8, 9], true) ? [(int) $grade] : abort(404));

        $rostersByGrade = [];
        foreach ($allowedGrades as $g) {
            $rostersByGrade[$g] = \App\Models\Student::query()
                ->where('course_level', \App\Livewire\BrainChampionRegistration::EVENT_TAG)
                ->where('class_name', "Kelas {$g}")
                ->orderByRaw('COALESCE(competition_code, nis) ASC')
                ->limit(35)
                ->get();
        }

        $pdf = Pdf::loadView('reports.brain-champion-roster', [
            'rostersByGrade' => $rostersByGrade,
            'selectedGrade' => $grade,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("Daftar_Hadir_Brain_Champion_Kelas_{$grade}.pdf");
    }
}
