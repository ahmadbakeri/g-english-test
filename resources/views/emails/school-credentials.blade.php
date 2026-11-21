<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Portal Sekolah G-English Brain Champion</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #0f172a;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #f1f5f9; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width: 640px; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #cbd5e1; box-shadow: 0 10px 25px -5px rgba(10, 66, 130, 0.08);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #0a4282; padding: 28px 32px; border-bottom: 5px solid #fbb900; text-align: center;">
                            <div style="display: inline-block; background-color: #fbb900; color: #0a4282; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; padding: 4px 12px; border-radius: 999px; margin-bottom: 10px;">
                                Official School Coordinator Credentials
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 900; letter-spacing: 0.5px;">
                                G-ENGLISH BRAIN CHAMPION
                            </h1>
                            <p style="margin: 4px 0 0 0; color: #fbb900; font-size: 13px; font-weight: 800; letter-spacing: 1px;">
                                "ONE MIND, ONE CHAMPION"
                            </p>
                            <p style="margin: 6px 0 0 0; color: #dbeafe; font-size: 12px;">
                                Kompetisi Offline di Gedung &bull; Sistem Gugur (Whiteboard &amp; Kertas — Soal Dibacakan Langsung)
                            </p>
                        </td>
                    </tr>

                    <!-- Greeting & Credentials -->
                    <tr>
                        <td style="padding: 28px 32px;">
                            <p style="margin: 0 0 12px 0; font-size: 15px; line-height: 1.6; color: #334155;">
                                Yth. Bapak/Ibu <strong>{{ $coordinatorName }}</strong>,<br>
                                Koordinator Delegasi <strong>{{ $schoolName }}</strong>
                            </p>
                            <p style="margin: 0 0 20px 0; font-size: 14px; line-height: 1.6; color: #475569;">
                                Terima kasih telah mendaftarkan delegasi peserta sekolah Anda pada ajang <strong>G-English Brain Champion</strong>. Berikut adalah akun resmi Portal Koordinator Sekolah untuk mengelola biodata peserta dan mencetak Kartu Peserta (QR Check-in):
                            </p>

                            <!-- Credential Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #f8fafc; border: 2px solid #0a4282; border-radius: 12px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 18px 20px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748b; width: 40%; font-weight: 600;">Sekolah</td>
                                                <td style="padding: 6px 0; font-size: 14px; color: #0f172a; font-weight: 800;">: {{ $schoolName }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 600;">URL Login Portal</td>
                                                <td style="padding: 6px 0; font-size: 14px; font-weight: 800;">
                                                    : <a href="{{ $loginUrl }}" style="color: #0a4282; text-decoration: underline;">{{ $loginUrl }}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 600;">Username (No. WA)</td>
                                                <td style="padding: 6px 0; font-size: 15px; color: #0a4282; font-family: monospace; font-weight: 900;">: {{ $username }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748b; font-weight: 600;">Password / PIN</td>
                                                <td style="padding: 6px 0;">
                                                    : <span style="display: inline-block; background-color: #fbb900; color: #0a4282; font-family: monospace; font-size: 15px; font-weight: 900; padding: 3px 10px; border-radius: 6px;">{{ $plainPassword }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <div style="text-align: center; margin-bottom: 28px;">
                                <a href="{{ $loginUrl }}" style="display: inline-block; background-color: #0a4282; color: #ffffff; font-size: 14px; font-weight: 800; text-decoration: none; padding: 12px 28px; border-radius: 999px; border: 2px solid #0a4282;">
                                    Masuk ke Portal Sekolah &amp; Cetak Kartu Peserta
                                </a>
                            </div>

                            <!-- Participants List -->
                            @if(count($participants) > 0)
                                <h3 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 800; color: #0a4282; text-transform: uppercase; letter-spacing: 0.5px;">
                                    Daftar Peserta Terdaftar ({{ count($participants) }} Siswa)
                                </h3>
                                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse: collapse; border: 1px solid #cbd5e1; font-size: 12px; margin-bottom: 20px;">
                                    <thead>
                                        <tr style="background-color: #0a4282; color: #ffffff;">
                                            <th align="left" style="padding: 8px 10px; border: 1px solid #0a4282;">Kode</th>
                                            <th align="left" style="padding: 8px 10px; border: 1px solid #0a4282;">NISN</th>
                                            <th align="left" style="padding: 8px 10px; border: 1px solid #0a4282;">Nama Siswa</th>
                                            <th align="center" style="padding: 8px 10px; border: 1px solid #0a4282;">Kelas</th>
                                            <th align="center" style="padding: 8px 10px; border: 1px solid #0a4282;">L/P</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($participants as $p)
                                            <tr>
                                                <td style="padding: 8px 10px; border: 1px solid #cbd5e1; font-family: monospace; font-weight: 800; color: #0a4282;">
                                                    {{ $p['competition_code'] ?? ($p['registration_number'] ?? '-') }}
                                                </td>
                                                <td style="padding: 8px 10px; border: 1px solid #cbd5e1; font-family: monospace;">
                                                    {{ $p['nisn'] ?? '-' }}
                                                </td>
                                                <td style="padding: 8px 10px; border: 1px solid #cbd5e1; font-weight: 700;">
                                                    {{ $p['name'] ?? '-' }}
                                                </td>
                                                <td align="center" style="padding: 8px 10px; border: 1px solid #cbd5e1;">
                                                    {{ $p['class_name'] ?? '-' }}
                                                </td>
                                                <td align="center" style="padding: 8px 10px; border: 1px solid #cbd5e1;">
                                                    {{ $p['gender'] ?? 'L' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            <p style="margin: 0; font-size: 12px; line-height: 1.5; color: #64748b;">
                                * Harap mencetak Kartu Peserta dari Portal Sekolah dan membawanya saat daftar ulang di Gedung Genza English Center (Tanjung PHM Noor) 30 menit sebelum lomba dimulai.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #0f172a; color: #94a3b8; padding: 18px 32px; text-align: center; font-size: 12px;">
                            &copy; {{ date('Y') }} <strong style="color: #fbb900;">Genza English Center</strong> — Tanjung PHM Noor.<br>
                            Jl. Ir. PHM Noor RT.4 No. 119, Pembataan, Kec. Murung Pudak, Kab. Tabalong.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
