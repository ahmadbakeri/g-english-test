<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir &amp; Eliminasi G-English Brain Champion</title>
    <style>
        @page {
            margin: 24px 28px;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #0f172a;
            font-size: 10.5px;
        }
        .page-break {
            page-break-after: always;
        }
        .header-box {
            background-color: #0a4282;
            color: #ffffff;
            padding: 12px 16px;
            border-radius: 8px;
            border-bottom: 4px solid #fbb900;
            margin-bottom: 12px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 10px;
            color: #fbb900;
            font-weight: bold;
            margin-top: 2px;
        }
        .format-note {
            font-size: 9px;
            color: #dbeafe;
            margin-top: 2px;
        }
        .grade-badge {
            background-color: #fbb900;
            color: #0a4282;
            font-weight: bold;
            font-size: 13px;
            padding: 6px 14px;
            border-radius: 6px;
            display: inline-block;
        }
        table.roster {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.roster th {
            background-color: #0a4282;
            color: #ffffff;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 6px;
            border: 1px solid #0a4282;
            text-align: left;
        }
        table.roster td {
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            font-size: 9.5px;
            vertical-align: middle;
        }
        table.roster tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .code-pill {
            font-weight: bold;
            color: #0a4282;
            font-family: monospace;
        }
        .empty-row td {
            color: #94a3b8;
            height: 16px;
        }
        .checkbox-box {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #475569;
            margin-right: 3px;
            vertical-align: middle;
        }
        .footer-signatures {
            margin-top: 14px;
            width: 100%;
            border-collapse: collapse;
        }
        .footer-signatures td {
            font-size: 10px;
            vertical-align: top;
        }
    </style>
</head>
<body>
    @foreach($rostersByGrade as $gradeNum => $participants)
        <div class="{{ !$loop->last ? 'page-break' : '' }}">
            <div class="header-box">
                <table class="header-table">
                    <tr>
                        <td>
                            <div class="title">G-ENGLISH BRAIN CHAMPION — "ONE MIND, ONE CHAMPION"</div>
                            <div class="subtitle">DAFTAR HADIR &amp; LEMBAR ELIMINASI ARENA (KUOTA MAKS. 35 PESERTA)</div>
                            <div class="format-note">Kompetisi Offline di Gedung &bull; Sistem Gugur (Whiteboard atau Kertas — Soal Dibacakan Langsung)</div>
                        </td>
                        <td style="text-align: right; vertical-align: middle;">
                            <span class="grade-badge">KELAS {{ $gradeNum }} ({{ $gradeNum <= 6 ? 'SD/MI' : 'SMP/MTs' }})</span>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="margin-bottom: 6px; font-size: 10px; display: flex; justify-content: space-between;">
                <span><strong>Terdaftar:</strong> {{ count($participants) }} / 35 Peserta</span>
                <span style="float: right;"><strong>Tanggal Cetak:</strong> {{ now()->translatedFormat('d F Y, H:i') }}</span>
            </div>

            <table class="roster">
                <thead>
                    <tr>
                        <th style="width: 4%; text-align: center;">No</th>
                        <th style="width: 13%;">Kode Peserta</th>
                        <th style="width: 25%;">Nama Siswa</th>
                        <th style="width: 12%;">NISN</th>
                        <th style="width: 20%;">Asal Sekolah</th>
                        <th style="width: 13%; text-align: center;">Tanda Tangan / Ceklis Hadir</th>
                        <th style="width: 13%; text-align: center;">Status Lolos/Gugur</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < 35; $i++)
                        @php
                            $p = $participants[$i] ?? null;
                        @endphp
                        @if($p)
                            <tr>
                                <td style="text-align: center; font-weight: bold;">{{ $i + 1 }}</td>
                                <td class="code-pill">{{ $p->competition_code ?: $p->nis }}</td>
                                <td><strong>{{ $p->name }}</strong> ({{ $p->gender ?: 'L' }})</td>
                                <td style="font-family: monospace;">{{ $p->nisn ?: '-' }}</td>
                                <td>{{ $p->school_name ?: '-' }}</td>
                                <td style="text-align: center;">
                                    <span class="checkbox-box"></span> Hadir
                                </td>
                                <td style="text-align: center;">
                                    <span class="checkbox-box"></span>Lolos &nbsp;
                                    <span class="checkbox-box"></span>Gugur
                                </td>
                            </tr>
                        @else
                            <tr class="empty-row">
                                <td style="text-align: center;">{{ $i + 1 }}</td>
                                <td>BC-K{{ $gradeNum }}-{{ str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT) }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="text-align: center;"><span class="checkbox-box"></span> Hadir</td>
                                <td style="text-align: center;"><span class="checkbox-box"></span>Lolos &nbsp; <span class="checkbox-box"></span>Gugur</td>
                            </tr>
                        @endif
                    @endfor
                </tbody>
            </table>

            <table class="footer-signatures">
                <tr>
                    <td style="width: 60%;">
                        <strong>Petunjuk Juri &amp; Panitia Arena:</strong><br>
                        1. Pastikan Kode Peserta di meja/dada sesuai dengan lembar ini.<br>
                        2. Beri tanda ceklis pada kolom <em>Lolos / Gugur</em> di akhir babak eliminasi whiteboard atau kertas.
                    </td>
                    <td style="width: 40%; text-align: center;">
                        Tanjung, {{ now()->translatedFormat('d F Y') }}<br>
                        <strong>Koordinator Juri Kelas {{ $gradeNum }}</strong>
                        <br><br><br>
                        (______________________________)
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</body>
</html>
