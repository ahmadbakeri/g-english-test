<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Evaluasi Siswa - <?php echo e($evaluation->student->name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #333;
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 16px;
            color: #555;
        }
        .content-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .content-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .content-table .label {
            width: 30%;
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .score-box {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background-color: #f0fdf4;
            border: 2px solid #22c55e;
            border-radius: 8px;
        }
        .score-box h2 {
            margin: 0;
            font-size: 36px;
            color: #166534;
        }
        .score-box p {
            margin: 5px 0 0 0;
            font-size: 16px;
            color: #15803d;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .signature {
            margin-top: 80px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>LAPORAN EVALUASI SISWA</h1>
            <p><?php echo e(\App\Models\Setting::where('key', 'institute_name')->value('value') ?? 'G-English Course'); ?></p>
        </div>

        <table class="content-table">
            <tr>
                <td class="label">Nama Siswa</td>
                <td><strong><?php echo e($evaluation->student->name); ?></strong></td>
            </tr>
            <tr>
                <td class="label">Nomor Induk Siswa (NIS)</td>
                <td><?php echo e($evaluation->student->nis); ?></td>
            </tr>
            <tr>
                <td class="label">Mata Pelajaran</td>
                <td><?php echo e($evaluation->subject->name); ?></td>
            </tr>
            <tr>
                <td class="label">Tanggal Evaluasi</td>
                <td><?php echo e(\Carbon\Carbon::parse($evaluation->date)->format('d F Y')); ?></td>
            </tr>
            <tr>
                <td class="label">Catatan / Keterangan</td>
                <td><?php echo nl2br(e($evaluation->notes ?? '-')); ?></td>
            </tr>
        </table>

        <div class="score-box">
            <p>NILAI AKHIR</p>
            <h2><?php echo e($evaluation->score); ?> / 100</h2>
        </div>

        <div class="footer">
            <p>Dicetak pada: <?php echo e(now()->format('d F Y')); ?></p>
            <p>Penilai / Tutor,</p>
            <div class="signature">
                <?php echo e(auth()->check() ? auth()->user()->name : 'Tutor / Admin'); ?>

            </div>
        </div>
    </div>

</body>
</html>
<?php /**PATH C:\Users\ahmad\Downloads\Laravel\g-english\resources\views/reports/evaluation.blade.php ENDPATH**/ ?>