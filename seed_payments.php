<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$students = \App\Models\Student::all();
$year = date('Y');

foreach ($students as $student) {
    // Clear existing payments for this student to avoid duplicates
    \App\Models\StudentPayment::where('student_id', $student->id)->delete();

    // Create 12 months of payments
    for ($month = 1; $month <= 12; $month++) {
        // Let's make Jan to July (7 months) PAID, and Aug to Dec UNPAID
        $status = ($month <= 7) ? 'paid' : 'unpaid';
        $paidAt = ($status === 'paid') ? \Carbon\Carbon::create($year, $month, rand(1, 15), rand(8, 16), rand(0, 59), 0) : null;

        \App\Models\StudentPayment::create([
            'student_id' => $student->id,
            'month' => $month,
            'year' => $year,
            'amount' => 250000,
            'status' => $status,
            'paid_at' => $paidAt,
            'notes' => 'Pembayaran SPP Bulan ke-' . $month,
        ]);
    }
}
echo "Dummy payment data generated for all students.\n";
