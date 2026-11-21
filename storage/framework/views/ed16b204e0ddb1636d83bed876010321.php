<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Pembayaran - <?php echo e($payment->student->name); ?></title>
    <style>
        @page {
            margin: 40px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .header-container {
            background-color: #1a368b; /* Warna biru yang lebih pas dan elegan */
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            color: #fff;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-left h1 {
            margin: 0 0 5px 0;
            font-size: 24px;
            font-weight: bold;
            color: #fff;
        }
        .header-left p {
            margin: 0;
            font-size: 14px;
            color: #cbd5e1;
            font-weight: normal;
        }
        .header-right {
            text-align: right;
        }
        .logo {
            max-width: 160px;
            max-height: 50px;
            background-color: #ffffff;
            padding: 8px 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .badge {
            background-color: rgba(0,0,0,0.4);
            color: #fff;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.data-table td, table.data-table th {
            border: 1px solid #d1d5db;
            padding: 10px;
        }
        .label-cell {
            background-color: #f3f4f6;
            font-weight: bold;
            width: 25%;
            text-align: center;
        }
        .value-cell {
            width: 75%;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 50px;
        }
        table.items-table th, table.items-table td {
            border: 1px solid #d1d5db;
            padding: 10px;
        }
        table.items-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
        }
        .center-text {
            text-align: center;
        }
        .sisa-saldo td {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
        }
        .signature-table {
            width: 100%;
        }
        .signature-table td {
            vertical-align: top;
        }
        .signature-box {
            float: right;
            text-align: right;
            width: 250px;
        }
        .signature-date {
            margin-bottom: 70px;
        }
        .signature-name {
            font-weight: bold;
            text-transform: uppercase;
        }
        .signature-role {
            font-size: 12px;
            color: #4b5563;
        }
    </style>
</head>
<body>
    
    <div class="header-container">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <h1>Kwitansi Pembayaran</h1>
                    <p><?php echo e(\App\Models\Setting::where('key', 'institute_name')->value('value') ?? 'Genza Education Tanjung'); ?></p>
                </td>
                <td class="header-right">
                    <?php
                        $logoPath = public_path('images/logo.png');
                        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
                        $receiptNo = 'GC-' . str_pad($payment->month, 3, '0', STR_PAD_LEFT) . '-' . str_pad($payment->id, 7, '0', STR_PAD_LEFT);
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logoData): ?>
                        <img src="data:image/png;base64,<?php echo e($logoData); ?>" class="logo" alt="Logo"><br>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="badge"><?php echo e($receiptNo); ?></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabel Info Siswa -->
    <table class="data-table">
        <tr>
            <td class="label-cell">Nama Siswa</td>
            <td class="value-cell"><?php echo e(strtoupper($payment->student->name)); ?></td>
        </tr>
        <tr>
            <td class="label-cell">No. Siswa</td>
            <td class="value-cell"><?php echo e($payment->student->user ? $payment->student->user->username : '-'); ?></td>
        </tr>
        <tr>
            <td class="label-cell">Kelas</td>
            <td class="value-cell"><?php echo e($payment->student->classes->first() ? $payment->student->classes->first()->name : '-'); ?></td>
        </tr>
        <tr>
            <td class="label-cell">Total Biaya</td>
            <td class="value-cell">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></td>
        </tr>
    </table>

    <!-- Tabel Pembayaran -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 20%;">Tanggal</th>
                <th style="width: 30%;">Uraian</th>
                <th style="width: 20%;">Nominal</th>
                <th style="width: 30%;">Terbilang</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center-text"><?php echo e($payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y') : now()->format('d M Y')); ?></td>
                <td>
                    SPP Bulan <?php echo e($monthName); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->notes): ?>
                        <br><small style="color: #6b7280; font-style: italic;"><?php echo e($payment->notes); ?></small>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>
                <td class="center-text">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></td>
                <td>
                    <?php
                        if (class_exists('NumberFormatter')) {
                            $f = new NumberFormatter('id', NumberFormatter::SPELLOUT);
                            echo ucfirst($f->format($payment->amount)) . ' rupiah';
                        } else {
                            echo 'Rp ' . number_format($payment->amount, 0, ',', '.');
                        }
                    ?>
                </td>
            </tr>
            <tr class="sisa-saldo">
                <td colspan="2">SISA SALDO</td>
                <td colspan="2">Rp <?php echo e(number_format($remainingBalance, 0, ',', '.')); ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%;">
                <div class="signature-box">
                    <div class="signature-date">
                        <?php echo e(\App\Models\Setting::where('key', 'city_name')->value('value') ?? 'Tabalong'); ?>, 
                        <?php echo e($payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->isoFormat('D MMMM Y') : now()->isoFormat('D MMMM Y')); ?>

                    </div>
                    <div class="signature-name"><?php echo e($picName); ?></div>
                    <div class="signature-role">Administrasi</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
<?php /**PATH C:\Users\ahmad\Downloads\Laravel\g-english\resources\views/reports/student-payment.blade.php ENDPATH**/ ?>