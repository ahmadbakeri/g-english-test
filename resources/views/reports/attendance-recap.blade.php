<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi - {{ $selectedClass->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0369a1;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #0369a1;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
        }
        .info {
            margin-bottom: 20px;
        }
        .info table {
            width: 100%;
            border: none;
        }
        .info td {
            padding: 3px;
            vertical-align: top;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
        }
        .report-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .report-table th.name-col, .report-table td.name-cell {
            text-align: left;
            width: 150px;
        }
        .status-h { color: #16a34a; font-weight: bold; }
        .status-t { color: #f59e0b; font-weight: bold; }
        .status-a { color: #ef4444; font-weight: bold; }
        .status-i { color: #3b82f6; font-weight: bold; }
        .summary-box {
            float: right;
            width: 300px;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #f9fafb;
        }
        .summary-box table {
            width: 100%;
        }
        .summary-box th {
            text-align: left;
        }
        .footer {
            clear: both;
            margin-top: 30px;
            text-align: right;
        }
        .signature {
            display: inline-block;
            text-align: center;
        }
        .signature p {
            margin: 0;
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>REKAPITULASI KEHADIRAN SISWA</h1>
        <p>{{ \App\Models\Setting::where('key', 'institute_name')->value('value') ?? 'G-English Course' }}</p>
        <p>Bulan: {{ \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="10%"><strong>Kelas</strong></td>
                <td width="40%">: {{ $selectedClass->name }}</td>
                <td width="15%"><strong>Wali Kelas</strong></td>
                <td width="35%">: {{ $selectedClass->tutor ? $selectedClass->tutor->name : '-' }}</td>
            </tr>
            <tr>
                <td><strong>Mata Pelajaran</strong></td>
                <td>: {{ $selectedClass->subject->name }}</td>
                <td><strong>Tahun Ajaran</strong></td>
                <td>: {{ $selectedClass->academicYear->name }}</td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th rowspan="2" class="name-col">Nama Siswa</th>
                <th colspan="{{ count($dates) }}">Tanggal</th>
                <th colspan="4">Total</th>
            </tr>
            <tr>
                @foreach($dates as $date)
                    <th>{{ $date->format('d') }}</th>
                @endforeach
                <th>H</th>
                <th>T</th>
                <th>A</th>
                <th>I</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
                <tr>
                    <td class="name-cell">{{ $row['student']->name }}</td>
                    @foreach($row['days'] as $status)
                        <td>
                            @if($status === 'present')
                                <span class="status-h">H</span>
                            @elseif($status === 'late')
                                <span class="status-t">T</span>
                            @elseif($status === 'absent')
                                <span class="status-a">A</span>
                            @elseif($status === 'excused')
                                <span class="status-i">I</span>
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                    <td>{{ $row['totals']['present'] }}</td>
                    <td>{{ $row['totals']['late'] }}</td>
                    <td>{{ $row['totals']['absent'] }}</td>
                    <td>{{ $row['totals']['excused'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <h4 style="margin-top: 0;">Ringkasan Total Kelas</h4>
        <table>
            <tr>
                <th>✅ Hadir (H)</th>
                <td>: {{ $summary['present'] ?? 0 }}</td>
            </tr>
            <tr>
                <th>⏰ Terlambat (T)</th>
                <td>: {{ $summary['late'] ?? 0 }}</td>
            </tr>
            <tr>
                <th>❌ Tidak Hadir (A)</th>
                <td>: {{ $summary['absent'] ?? 0 }}</td>
            </tr>
            <tr>
                <th>📋 Izin (I)</th>
                <td>: {{ $summary['excused'] ?? 0 }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div class="signature">
            <span>{{ now()->translatedFormat('d F Y') }}</span><br>
            <span>Wali Kelas,</span>
            <p>{{ $selectedClass->tutor ? $selectedClass->tutor->name : '_________________________' }}</p>
        </div>
    </div>

</body>
</html>
