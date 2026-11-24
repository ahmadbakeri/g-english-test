<!DOCTYPE html>
<html lang="id" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>Akses Portal Sekolah — G-English Brain Champion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap');

        body, table, td, p, a, h1, h2, h3 {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
        }
        body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #eef3f9 !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table {
            border-collapse: collapse;
        }
        table.gx-rounded {
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }
        img {
            border: 0;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        @media only screen and (max-width: 620px) {
            .gx-shell {
                padding: 14px 10px !important;
            }
            .gx-pad {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
            .gx-hero-title {
                font-size: 22px !important;
                line-height: 1.25 !important;
            }
            .gx-cta-btn {
                padding: 13px 22px !important;
                font-size: 14px !important;
            }
            .gx-stack-cell {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            .gx-stack-gap {
                margin-top: 12px !important;
                padding-top: 0 !important;
            }
            .gx-hide-sm {
                display: none !important;
            }
            .gx-table-sm td,
            .gx-table-sm th {
                padding: 9px 6px !important;
                font-size: 11px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #eef3f9; font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #0f172a;">
    @php
        $appBaseUrl = rtrim((string) config('app.url', 'https://genzaenglish.my.id'), '/');
        $logoUrl = $appBaseUrl . '/images/logo.png';
        $participantCount = count($participants);
    @endphp

    <!-- Hidden Inbox Preheader -->
    <div style="display: none; font-size: 1px; color: #eef3f9; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
        Akses Portal Sekolah {{ $schoolName }} ({{ $participantCount }} siswa terdaftar) — Username: {{ $username }} &bull; G-English Brain Champion.
    </div>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="background-color: #eef3f9;">
        <tr>
            <td align="center" class="gx-shell" style="padding: 36px 16px;">
                <!-- Main Email Sheet -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" class="gx-rounded" style="max-width: 640px; background-color: #ffffff; border-radius: 20px; overflow: hidden; border: 1px solid #dbe4f0; border-collapse: separate; box-shadow: 0 18px 40px -16px rgba(10, 66, 130, 0.16);">

                    <!-- Top Gold Accent Bar -->
                    <tr>
                        <td style="height: 5px; background-color: #fbb900; font-size: 0; line-height: 0;">&nbsp;</td>
                    </tr>

                    <!-- White Brand Masthead with Official Genza Logo -->
                    <tr>
                        <td class="gx-pad" style="background-color: #ffffff; padding: 22px 36px; border-bottom: 1px solid #edf2f7;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
                                <tr>
                                    <td align="left" valign="middle">
                                        <a href="{{ $appBaseUrl }}" target="_blank" style="text-decoration: none; display: inline-block;">
                                            <img src="{{ $logoUrl }}" alt="Genza English" height="48" style="display: block; height: 48px; width: auto; max-width: 210px; border: 0;" />
                                        </a>
                                    </td>
                                    <td align="right" valign="middle">
                                        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #0a4282; line-height: 1.3;">
                                            Bukti Registrasi Resmi
                                        </div>
                                        <div style="font-size: 12px; font-weight: 500; color: #64748b; margin-top: 2px;">
                                            Tanjung PHM Noor
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Editorial Genza Blue Hero Banner -->
                    <tr>
                        <td class="gx-pad" style="background-color: #0a4282; background-image: linear-gradient(135deg, #0a4282 0%, #073161 100%); padding: 30px 36px 28px 36px;">
                            <h1 class="gx-hero-title" style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 800; line-height: 1.22; letter-spacing: -0.025em;">
                                Akses Portal Sekolah
                            </h1>
                            <p style="margin: 8px 0 0 0; color: #fbb900; font-size: 13px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase;">
                                G-English Brain Champion &bull; &ldquo;One Mind, One Champion&rdquo;
                            </p>
                            <p style="margin: 8px 0 0 0; color: #cfe0f5; font-size: 13px; font-weight: 400; line-height: 1.5;">
                                Kompetisi Offline di Gedung &middot; Sistem Gugur (Whiteboard &amp; Kertas &mdash; Soal Dibacakan Langsung)
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td class="gx-pad" style="padding: 32px 36px 34px 36px;">
                            <!-- Greeting -->
                            <p style="margin: 0 0 10px 0; font-size: 16px; font-weight: 600; line-height: 1.5; color: #0f172a;">
                                Yth. Bapak/Ibu {{ $coordinatorName }},
                            </p>
                            <p style="margin: 0 0 24px 0; font-size: 14.5px; font-weight: 400; line-height: 1.68; color: #475569;">
                                Pendaftaran delegasi <strong style="color: #0f172a; font-weight: 700;">{{ $schoolName }}</strong> pada ajang <strong style="color: #0a4282; font-weight: 700;">G-English Brain Champion</strong> telah kami terima. Gunakan kredensial resmi di bawah ini untuk masuk ke Portal Koordinator Sekolah, memverifikasi biodata peserta, dan mencetak Kartu Peserta (QR Check-in).
                            </p>

                            <!-- Executive Credential Block -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" class="gx-rounded" style="background-color: #f4f7fb; border: 1px solid #dbe6f5; border-radius: 16px; border-collapse: separate; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 22px 22px 20px 22px;">
                                        <!-- School & Coordinator Summary -->
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin-bottom: 16px; border-bottom: 1px solid #e2eaf4;">
                                            <tr>
                                                <td style="padding-bottom: 14px;">
                                                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b;">
                                                        Instansi / Sekolah
                                                    </div>
                                                    <div style="font-size: 17px; font-weight: 800; color: #0f172a; margin-top: 3px; letter-spacing: -0.015em;">
                                                        {{ $schoolName }}
                                                    </div>
                                                </td>
                                                <td align="right" valign="top" style="padding-bottom: 14px;">
                                                    <span style="display: inline-block; background-color: #e0edff; color: #0a4282; font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 999px; white-space: nowrap;">
                                                        {{ $participantCount }} Peserta
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Side-by-Side Username & PIN Tiles -->
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
                                            <tr>
                                                <td width="48%" valign="top" class="gx-stack-cell" style="padding-right: 7px;">
                                                    <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" class="gx-rounded" style="background-color: #ffffff; border: 1px solid #d5e2f2; border-radius: 12px; border-collapse: separate;">
                                                        <tr>
                                                            <td style="padding: 14px 16px;">
                                                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #64748b;">
                                                                    Username Login
                                                                </div>
                                                                <div style="margin-top: 6px; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; font-size: 21px; font-weight: 800; color: #0a4282; letter-spacing: 0.06em;">
                                                                    {{ $username }}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="48%" valign="top" class="gx-stack-cell gx-stack-gap" style="padding-left: 7px;">
                                                    <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" class="gx-rounded" style="background-color: #ffffff; border: 1px solid #f5d67b; border-radius: 12px; border-collapse: separate;">
                                                        <tr>
                                                            <td style="padding: 14px 16px;">
                                                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #926000;">
                                                                    Password / PIN
                                                                </div>
                                                                <div style="margin-top: 6px; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; font-size: 21px; font-weight: 800; color: #0a4282; letter-spacing: 0.08em;">
                                                                    {{ $plainPassword }}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Login Tip -->
                                        <p style="margin: 14px 0 0 0; font-size: 12.5px; color: #475569; line-height: 1.5;">
                                            Anda juga dapat masuk menggunakan alamat email sekolah yang terdaftar ini beserta PIN di atas.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Primary CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $loginUrl }}" target="_blank" class="gx-cta-btn" style="display: inline-block; background-color: #0a4282; color: #ffffff; font-size: 15px; font-weight: 700; text-decoration: none; padding: 14px 30px; border-radius: 999px; letter-spacing: 0.01em; box-shadow: 0 8px 20px -6px rgba(10, 66, 130, 0.45);">
                                            Masuk Portal Sekolah &amp; Cetak Kartu
                                        </a>
                                        <div style="margin-top: 10px; font-size: 12px; color: #64748b;">
                                            Atau buka langsung: <a href="{{ $loginUrl }}" target="_blank" style="color: #0a4282; font-weight: 600; text-decoration: underline;">{{ $loginUrl }}</a>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Participants Roster Table -->
                            @if($participantCount > 0)
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin-bottom: 12px;">
                                    <tr>
                                        <td align="left" valign="middle">
                                            <h2 style="margin: 0; font-size: 16px; font-weight: 800; color: #0f172a; letter-spacing: -0.015em;">
                                                Daftar Peserta Delegasi
                                            </h2>
                                        </td>
                                        <td align="right" valign="middle">
                                            <span style="font-size: 12px; font-weight: 700; color: #0a4282;">
                                                Total {{ $participantCount }} Siswa
                                            </span>
                                        </td>
                                    </tr>
                                </table>

                                <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" class="gx-table-sm gx-rounded" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 14px; border-collapse: separate; overflow: hidden; margin-bottom: 26px;">
                                    <thead>
                                        <tr style="background-color: #f1f5f9;">
                                            <th align="left" style="padding: 11px 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #475569; border-bottom: 1px solid #e2e8f0;">
                                                Kode
                                            </th>
                                            <th align="left" style="padding: 11px 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #475569; border-bottom: 1px solid #e2e8f0;">
                                                Nama Siswa
                                            </th>
                                            <th align="left" class="gx-hide-sm" style="padding: 11px 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #475569; border-bottom: 1px solid #e2e8f0;">
                                                NISN
                                            </th>
                                            <th align="center" style="padding: 11px 10px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #475569; border-bottom: 1px solid #e2e8f0;">
                                                Kelas
                                            </th>
                                            <th align="center" style="padding: 11px 10px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #475569; border-bottom: 1px solid #e2e8f0;">
                                                L/P
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($participants as $idx => $p)
                                            @php
                                                $rowBg = ($idx % 2 === 1) ? '#fafcff' : '#ffffff';
                                                $isFemale = strtoupper((string) ($p['gender'] ?? 'L')) === 'P';
                                                $genderBg = $isFemale ? '#fdf2f8' : '#eff6ff';
                                                $genderColor = $isFemale ? '#be185d' : '#0a4282';
                                                $codeVal = $p['competition_code'] ?? ($p['registration_number'] ?? '-');
                                            @endphp
                                            <tr style="background-color: {{ $rowBg }};">
                                                <td style="padding: 11px 12px; border-bottom: 1px solid #edf2f7; white-space: nowrap;">
                                                    <span style="display: inline-block; background-color: #eff6ff; color: #0a4282; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; font-size: 11.5px; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                                                        {{ $codeVal }}
                                                    </span>
                                                </td>
                                                <td style="padding: 11px 12px; border-bottom: 1px solid #edf2f7; font-size: 13.5px; font-weight: 700; color: #0f172a;">
                                                    {{ $p['name'] ?? '-' }}
                                                </td>
                                                <td class="gx-hide-sm" style="padding: 11px 12px; border-bottom: 1px solid #edf2f7; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; font-size: 12px; color: #475569;">
                                                    {{ $p['nisn'] ?? '-' }}
                                                </td>
                                                <td align="center" style="padding: 11px 10px; border-bottom: 1px solid #edf2f7; font-size: 12.5px; font-weight: 600; color: #334155; white-space: nowrap;">
                                                    {{ $p['class_name'] ?? '-' }}
                                                </td>
                                                <td align="center" style="padding: 11px 10px; border-bottom: 1px solid #edf2f7;">
                                                    <span style="display: inline-block; background-color: {{ $genderBg }}; color: {{ $genderColor }}; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 999px;">
                                                        {{ $p['gender'] ?? 'L' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            <!-- Next Steps Notice -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" class="gx-rounded" style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 14px; border-collapse: separate;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <div style="font-size: 13px; font-weight: 800; color: #78350f; margin-bottom: 6px;">
                                            Langkah Selanjutnya untuk Koordinator Sekolah
                                        </div>
                                        <p style="margin: 0; font-size: 13px; line-height: 1.65; color: #713f12;">
                                            1. Masuk ke Portal Sekolah menggunakan Username &amp; PIN di atas.<br>
                                            2. Periksa kembali biodata peserta dan cetak <strong style="color: #78350f;">Kartu Peserta (QR Check-in)</strong>.<br>
                                            3. Bawa Kartu Peserta saat daftar ulang di <strong style="color: #78350f;">Gedung Genza English Center (Tanjung PHM Noor)</strong> minimal 30 menit sebelum lomba dimulai.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Institutional Footer -->
                    <tr>
                        <td class="gx-pad" style="background-color: #0b192c; padding: 24px 36px; text-align: center;">
                            <div style="font-size: 14px; font-weight: 800; color: #ffffff; letter-spacing: 0.01em;">
                                Genza English Center <span style="color: #fbb900;">&middot;</span> Tanjung PHM Noor
                            </div>
                            <p style="margin: 6px 0 0 0; font-size: 12px; line-height: 1.6; color: #94a3b8;">
                                Jl. Ir. PHM Noor RT.4 No. 119, Pembataan, Kec. Murung Pudak, Kab. Tabalong<br>
                                <a href="{{ $appBaseUrl }}" target="_blank" style="color: #fbb900; text-decoration: none; font-weight: 600;">genzaenglish.my.id</a>
                            </p>
                            <p style="margin: 12px 0 0 0; font-size: 11px; color: #64748b;">
                                &copy; {{ date('Y') }} Genza English Center. Email ini dikirim otomatis oleh sistem, mohon tidak membalas ke alamat ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
