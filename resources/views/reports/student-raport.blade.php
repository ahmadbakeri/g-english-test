<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Raport Kelas - {{ $selectedClass->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
            padding: 6px;
            text-align: center;
        }
        .report-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .report-table th.name-col, .report-table td.name-cell {
            text-align: left;
            width: 25%;
        }
        .footer {
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
        <h1>LAPORAN NILAI DAN KEHADIRAN SISWA (RAPORT)</h1>
        <p>{{ \App\Models\Setting::where('key', 'institute_name')->value('value') ?? 'G-English Course' }}</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="15%"><strong>Kelas</strong></td>
                <td width="35%">: {{ $selectedClass->name }}</td>
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
                <th colspan="{{ count($reportData[0]['tests'] ?? []) > 0 ? count($reportData[0]['tests']) : 1 }}">Daftar Nilai Ujian</th>
                <th rowspan="2">Rata-rata</th>
                <th colspan="4">Kehadiran</th>
            </tr>
            <tr>
                @if(count($reportData[0]['tests'] ?? []) > 0)
                    @foreach($reportData[0]['tests'] as $test)
                        <th>{{ $test['name'] }}</th>
                    @endforeach
                @else
                    <th>Belum ada ujian</th>
                @endif
                <th>H</th>
                <th>T</th>
                <th>I</th>
                <th>A</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
                <tr>
                    <td class="name-cell">{{ $row['student']->name }}</td>
                    @if(count($row['tests']) > 0)
                        @foreach($row['tests'] as $test)
                            <td style="{{ $test['score'] < ($test['max_score'] * 0.6) ? 'color: red;' : '' }}">
                                {{ $test['score'] }}
                            </td>
                        @endforeach
                    @else
                        <td>-</td>
                    @endif
                    <td><strong>{{ $row['avg_score'] }}</strong></td>
                    <td>{{ $row['attendance']['present'] }}</td>
                    <td>{{ $row['attendance']['late'] }}</td>
                    <td>{{ $row['attendance']['excused'] }}</td>
                    <td>{{ $row['attendance']['absent'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <span>{{ now()->translatedFormat('d F Y') }}</span><br>
            <span>Wali Kelas,</span>
            <p>{{ $selectedClass->tutor ? $selectedClass->tutor->name : '_________________________' }}</p>
        </div>
    </div>

</body>
</html>
