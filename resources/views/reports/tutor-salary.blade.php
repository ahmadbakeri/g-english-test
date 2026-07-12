<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $salary->tutor->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0369a1;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #0369a1;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th, .details-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .details-table th {
            background-color: #f3f4f6;
        }
        .details-table td.right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #e5e7eb;
        }
        .footer {
            margin-top: 40px;
            width: 100%;
        }
        .footer-table {
            width: 100%;
            text-align: center;
        }
        .footer-table td {
            width: 50%;
        }
        .signature {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>SLIP GAJI TUTOR</h1>
            <p>{{ \App\Models\Setting::where('key', 'institute_name')->value('value') ?? 'G-English Course' }}</p>
        </div>

        <table class="info-table">
            <tr>
                <td width="20%"><strong>Nama Tutor</strong></td>
                <td width="30%">: {{ $salary->tutor->name }}</td>
                <td width="20%"><strong>Bulan/Tahun</strong></td>
                <td width="30%">: {{ $monthName }} {{ $salary->year }}</td>
            </tr>
            <tr>
                <td><strong>NUPTK/NIP</strong></td>
                <td>: {{ $salary->tutor->nuptk ?? '-' }}</td>
                <td><strong>Status Pembayaran</strong></td>
                <td>: {{ $salary->status === 'paid' ? 'Lunas' : 'Belum Dibayar' }}</td>
            </tr>
            <tr>
                <td><strong>Bank Tujuan</strong></td>
                <td>: {{ $salary->tutor->bank_name ?? '-' }}</td>
                <td><strong>No. Rekening</strong></td>
                <td>: {{ $salary->tutor->bank_account ?? '-' }}</td>
            </tr>
        </table>

        <table class="details-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Keterangan</th>
                    <th class="right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Honor Mengajar ({{ $salary->total_jp }} Jam Pelajaran)</td>
                    <td class="right">Rp {{ number_format($salary->total_amount, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2" class="right">TOTAL DITERIMA</td>
                    <td class="right">Rp {{ number_format($salary->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td>
                        Penerima,
                        <div class="signature">
                            {{ $salary->tutor->name }}
                        </div>
                    </td>
                    <td>
                        Tanggal: {{ $salary->paid_at ? \Carbon\Carbon::parse($salary->paid_at)->format('d F Y') : now()->format('d F Y') }}<br>
                        Manajer / Keuangan,
                        <div class="signature">
                            _________________________
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
