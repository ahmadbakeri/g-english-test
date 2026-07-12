<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi SPP - {{ $payment->student->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #555;
        }
        .receipt-no {
            text-align: right;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .content-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        .content-table td {
            vertical-align: top;
        }
        .content-table .label {
            width: 25%;
            font-weight: bold;
        }
        .content-table .colon {
            width: 5%;
            text-align: center;
        }
        .content-table .value {
            width: 70%;
            border-bottom: 1px dotted #999;
        }
        .amount-box {
            background-color: #f3f4f6;
            border: 2px solid #333;
            padding: 10px 20px;
            font-size: 18px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 30px;
        }
        .footer {
            width: 100%;
            margin-top: 20px;
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
            <h1>KUITANSI PEMBAYARAN</h1>
            <p>{{ \App\Models\Setting::where('key', 'institute_name')->value('value') ?? 'G-English Course' }}</p>
        </div>

        <div class="receipt-no">
            No: SPP/{{ $payment->year }}/{{ str_pad($payment->month, 2, '0', STR_PAD_LEFT) }}/{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}
        </div>

        <table class="content-table">
            <tr>
                <td class="label">Telah terima dari</td>
                <td class="colon">:</td>
                <td class="value"><strong>{{ $payment->student->name }}</strong></td>
            </tr>
            <tr>
                <td class="label">Uang sejumlah</td>
                <td class="colon">:</td>
                <td class="value">
                    <em>
                        @php
                            if (class_exists('NumberFormatter')) {
                                $f = new NumberFormatter('id', NumberFormatter::SPELLOUT);
                                echo ucwords($f->format($payment->amount)) . ' Rupiah';
                            } else {
                                echo 'Rp ' . number_format($payment->amount, 0, ',', '.');
                            }
                        @endphp
                    </em>
                </td>
            </tr>
            <tr>
                <td class="label">Untuk pembayaran</td>
                <td class="colon">:</td>
                <td class="value">
                    Biaya Kursus (SPP) Bulan <strong>{{ $monthName }} {{ $payment->year }}</strong>
                    @if($payment->notes)
                        <br><small>Catatan: {{ $payment->notes }}</small>
                    @endif
                </td>
            </tr>
        </table>

        <div class="amount-box">
            Rp {{ number_format($payment->amount, 0, ',', '.') }}
        </div>

        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td></td>
                    <td>
                        Tanggal: {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d F Y') : now()->format('d F Y') }}<br>
                        Penerima / Kasir,
                        <div class="signature">
                            {{ auth()->check() ? auth()->user()->name : 'Admin' }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
