<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Peserta Brain Champion - {{ $student->competition_code ?: $student->nis }}</title>
    <style>
        @page {
            margin: 14px;
            size: A5 landscape;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #0f172a;
        }
        .card-outer {
            border: 4px solid #0a4282;
            border-radius: 14px;
            overflow: hidden;
            width: 100%;
            box-sizing: border-box;
        }
        .card-header {
            background-color: #0a4282;
            color: #ffffff;
            padding: 14px 20px;
            border-bottom: 5px solid #fbb900;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .event-badge {
            display: inline-block;
            background-color: #fbb900;
            color: #0a4282;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 10px;
            letter-spacing: 0.5px;
        }
        .event-title {
            font-size: 20px;
            font-weight: bold;
            margin: 4px 0 2px 0;
            letter-spacing: 0.5px;
            color: #ffffff;
        }
        .event-tagline {
            font-size: 11px;
            font-weight: bold;
            color: #fbb900;
            margin: 0;
            letter-spacing: 1px;
        }
        .event-format {
            font-size: 9px;
            color: #dbeafe;
            margin-top: 3px;
        }
        .card-body {
            padding: 16px 20px;
        }
        .body-table {
            width: 100%;
            border-collapse: collapse;
        }
        .code-box {
            background-color: #f8fafc;
            border: 2px solid #0a4282;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 12px;
        }
        .code-label {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
        }
        .code-value {
            font-size: 26px;
            font-weight: bold;
            color: #0a4282;
            letter-spacing: 1.5px;
            margin-top: 2px;
        }
        .grade-pill {
            display: inline-block;
            background-color: #fbb900;
            color: #0a4282;
            font-weight: bold;
            font-size: 13px;
            padding: 4px 12px;
            border-radius: 12px;
            float: right;
            margin-top: 4px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px 4px;
            font-size: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-label {
            width: 34%;
            color: #64748b;
            font-weight: bold;
        }
        .info-val {
            color: #0f172a;
            font-weight: bold;
        }
        .qr-panel {
            text-align: center;
            border-left: 2px dashed #cbd5e1;
            padding-left: 16px;
            vertical-align: middle;
        }
        .qr-box {
            display: inline-block;
            padding: 8px;
            border: 2px solid #0a4282;
            border-radius: 10px;
            background: #ffffff;
        }
        .qr-caption {
            font-size: 9px;
            font-weight: bold;
            color: #0a4282;
            margin-top: 6px;
            text-transform: uppercase;
        }
        .card-footer {
            background-color: #f1f5f9;
            border-top: 1px solid #cbd5e1;
            padding: 8px 20px;
            font-size: 9px;
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="card-outer">
        <div class="card-header">
            <table class="header-table">
                <tr>
                    <td>
                        <span class="event-badge">Official Participant Pass &amp; Table Card</span>
                        <div class="event-title">G-ENGLISH BRAIN CHAMPION</div>
                        <div class="event-tagline">"ONE MIND, ONE CHAMPION"</div>
                        <div class="event-format">Kompetisi Offline di Gedung &bull; Sistem Gugur (Whiteboard &amp; Kertas — Soal Dibacakan Langsung)</div>
                    </td>
                    <td style="text-align: right; vertical-align: top;">
                        <div style="font-size: 11px; font-weight: bold; color: #fbb900;">GENZA ENGLISH CENTER</div>
                        <div style="font-size: 9px; color: #dbeafe;">Tanjung PHM Noor</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="card-body">
            <table class="body-table">
                <tr>
                    <td style="width: 72%; padding-right: 14px; vertical-align: top;">
                        <div class="code-box">
                            <span class="grade-pill">{{ $student->class_name ?: 'Peserta' }}</span>
                            <div class="code-label">Nomor / Kode Peserta Lomba</div>
                            <div class="code-value">{{ $student->competition_code ?: $student->nis }}</div>
                        </div>

                        <table class="info-table">
                            <tr>
                                <td class="info-label">Nama Lengkap Peserta</td>
                                <td class="info-val">: {{ $student->name }} ({{ $student->gender ?: 'L' }})</td>
                            </tr>
                            <tr>
                                <td class="info-label">NISN</td>
                                <td class="info-val">: {{ $student->nisn ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Asal Sekolah</td>
                                <td class="info-val">: {{ $student->school_name ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Kategori &amp; Jenjang</td>
                                <td class="info-val">: {{ $student->class_name ?: '-' }} ({{ $student->education_level ?: 'SD/SMP' }})</td>
                            </tr>
                            <tr>
                                <td class="info-label">Orang Tua / Pendamping</td>
                                <td class="info-val">: {{ $student->parent_name ?: '-' }} ({{ $student->parent_phone ?: '-' }})</td>
                            </tr>
                        </table>
                    </td>

                    <td class="qr-panel" style="width: 28%;">
                        <div class="qr-box">
                            <img src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" width="115" height="115" alt="QR Check-in">
                        </div>
                        <div class="qr-caption">Scan Check-In Gedung</div>
                        <div style="font-size: 8px; color: #64748b; margin-top: 3px;">
                            Tunjukkan kartu ini di meja registrasi ulang sebelum memasuki arena lomba.
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="card-footer">
            <strong>Catatan Arena:</strong> Peserta wajib hadir 30 menit sebelum babak dimulai. Kompetisi dilaksanakan secara <strong>OFFLINE</strong> menggunakan sistem gugur di gedung (jawaban ditulis pada Whiteboard / Kertas saat soal dibacakan langsung).
        </div>
    </div>
</body>
</html>
