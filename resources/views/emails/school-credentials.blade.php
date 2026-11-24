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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body, table, td, th, p, a, div, span, h1, h2, h3 {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #eef2f7 !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table {
            border-collapse: collapse;
        }
        table.gx-card {
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
            .gx-outer {
                padding: 12px 8px !important;
            }
            .gx-pad {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
            .gx-kop-meta {
                display: none !important;
            }
            .gx-logo-genza {
                width: 92px !important;
                height: 40px !important;
            }
            .gx-logo-english {
                width: 97px !important;
                height: 40px !important;
            }
            .gx-hero-title {
                font-size: 19px !important;
                line-height: 1.3 !important;
            }
            .gx-stack {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            .gx-stack-mt {
                margin-top: 10px !important;
            }
            .gx-hide-sm {
                display: none !important;
            }
            .gx-roster th,
            .gx-roster td {
                padding: 9px 6px !important;
                font-size: 11.5px !important;
            }
            .gx-cta {
                padding: 13px 22px !important;
                font-size: 14px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #eef2f7; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #1e293b;">
    @php
        $appBaseUrl = rtrim((string) config('app.url', 'https://genzaenglish.my.id'), '/');
        $genzaLogoUrl = $appBaseUrl . '/images/logo-genza-education.png';
        $englishLogoUrl = $appBaseUrl . '/images/logo.png';
        $participantCount = count($participants);
    @endphp

    <!-- Hidden Inbox Preview Text -->
    <div style="display: none; font-size: 1px; color: #eef2f7; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
        Bukti Registrasi Resmi {{ $schoolName }} ({{ $participantCount }} siswa) — Username Portal: {{ $username }} &bull; G-English Brain Champion.
    </div>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="background-color: #eef2f7;">
        <tr>
            <td align="center" class="gx-outer" style="padding: 32px 16px;">
                <!-- Main Formal Sheet -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" class="gx-card" style="max-width: 660px; background-color: #ffffff; border: 1px solid #d5e0ee; border-radius: 16px; border-collapse: separate; overflow: hidden; box-shadow: 0 14px 36px -14px rgba(10, 66, 130, 0.14);">

                    <!-- Dual Institutional Top Stripe (Genza Blue + Gold) -->
                    <tr>
                        <td style="height: 4px; background-color: #0a4282; font-size: 0; line-height: 0;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="height: 3px; background-color: #fbb900; font-size: 0; line-height: 0;">&nbsp;</td>
                    </tr>

                    <!-- Formal Dual-Logo Letterhead (Kop Surat) -->
                    <tr>
                        <td class="gx-pad" style="background-color: #ffffff; padding: 20px 36px; border-bottom: 1px solid #e2e8f0;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
                                <tr>
                                    <td align="left" valign="middle">
                                        <table cellpadding="0" cellspacing="0" border="0" role="presentation">
                                            <tr>
                                                <td valign="middle">
                                                    <a href="{{ $appBaseUrl }}" target="_blank" style="text-decoration: none; display: block;">
                                                        <img src="{{ $genzaLogoUrl }}" alt="Genza Education" width="102" height="44" class="gx-logo-genza" style="display: block; width: 102px; height: 44px; border: 0;" />
                                                    </a>
                                                </td>
                                                <td valign="middle" style="padding: 0 14px;">
                                                    <div style="width: 1px; height: 32px; background-color: #cbd5e1; font-size: 0; line-height: 0;">&nbsp;</div>
                                                </td>
                                                <td valign="middle">
                                                    <a href="{{ $appBaseUrl }}" target="_blank" style="text-decoration: none; display: block;">
                                                        <img src="{{ $englishLogoUrl }}" alt="Genza English" width="107" height="44" class="gx-logo-english" style="display: block; width: 107px; height: 44px; border: 0;" />
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td align="right" valign="middle" class="gx-kop-meta">
                                        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: #0a4282; line-height: 1.35;">
                                            Bukti Registrasi Resmi
                                        </div>
                                        <div style="font-size: 11.5px; font-weight: 500; color: #64748b; margin-top: 2px;">
                                            Cabang Tanjung PHM Noor
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Deep Navy Event Title Header -->
                    <tr>
                        <td class="gx-pad" style="background-color: #0a4282; background-image: linear-gradient(135deg, #0a4282 0%, #072f5f 100%); padding: 26px 36px; text-align: center;">
                            <h1 class="gx-hero-title" style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 800; line-height: 1.3; letter-spacing: 0.01em;">
                                Akses Portal Koordinator Sekolah
                            </h1>
                            <p style="margin: 7px 0 0 0; color: #fbb900; font-size: 13px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">
                                G-English Brain Champion &bull; &ldquo;One Mind, One Champion&rdquo;
                            </p>
                            <p style="margin: 6px 0 0 0; color: #d5e5f9; font-size: 12px; font-weight: 400; line-height: 1.5;">
                                Kompetisi Offline di Gedung &middot; Sistem Gugur (Whiteboard &amp; Kertas &mdash; Soal Dibacakan Langsung)
                            </p>
                        </td>
                    </tr>

                    <!-- Formal Letter Body -->
                    <tr>
                        <td class="gx-pad" style="padding: 32px 36px 34px 36px;">

                            <!-- Formal Salutation -->
                            <p style="margin: 0 0 14px 0; font-size: 14.5px; line-height: 1.6; color: #0f172a;">
                                Yth. Bapak/Ibu <strong>{{ $coordinatorName }}</strong>,<br>
                                <span style="color: #475569; font-size: 13.5px;">Koordinator Delegasi <strong style="color: #0f172a;">{{ $schoolName }}</strong></span>
                            </p>

                            <!-- Justified Opening Paragraph -->
                            <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 1.75; color: #334155; text-align: justify;">
                                Terima kasih telah mendaftarkan delegasi peserta dari <strong style="color: #0f172a;">{{ $schoolName }}</strong> pada ajang <strong style="color: #0a4282;">G-English Brain Champion</strong> yang diselenggarakan oleh Genza Education &amp; Genza English Center. Pendaftaran sekolah Anda telah tercatat secara resmi di sistem kami. Berikut adalah rincian akun Portal Koordinator Sekolah untuk memeriksa biodata peserta serta mencetak Kartu Peserta (QR Check-in):
                            </p>

                            <!-- Structured Credential Sheet -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" class="gx-card" style="background-color: #f8fafd; border: 1px solid #d5e2f2; border-radius: 14px; border-collapse: separate; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px 22px 22px 22px;">
                                        <!-- Aligned Key-Value Summary -->
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin-bottom: 18px;">
                                            <tr>
                                                <td width="38%" valign="top" style="padding: 7px 12px 7px 0; font-size: 12.5px; font-weight: 600; color: #64748b; border-bottom: 1px solid #e6eef8;">
                                                    Nama Sekolah
                                                </td>
                                                <td width="62%" valign="top" style="padding: 7px 0; font-size: 13.5px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #e6eef8;">
                                                    {{ $schoolName }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top" style="padding: 7px 12px 7px 0; font-size: 12.5px; font-weight: 600; color: #64748b; border-bottom: 1px solid #e6eef8;">
                                                    Guru Pendamping
                                                </td>
                                                <td valign="top" style="padding: 7px 0; font-size: 13.5px; font-weight: 600; color: #1e293b; border-bottom: 1px solid #e6eef8;">
                                                    {{ $coordinatorName }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top" style="padding: 7px 12px 7px 0; font-size: 12.5px; font-weight: 600; color: #64748b; border-bottom: 1px solid #e6eef8;">
                                                    Jumlah Delegasi
                                                </td>
                                                <td valign="top" style="padding: 7px 0; font-size: 13.5px; font-weight: 700; color: #0a4282; border-bottom: 1px solid #e6eef8;">
                                                    {{ $participantCount }} Siswa Terdaftar
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top" style="padding: 7px 12px 4px 0; font-size: 12.5px; font-weight: 600; color: #64748b;">
                                                    Alamat Portal Login
                                                </td>
                                                <td valign="top" style="padding: 7px 0 4px 0; font-size: 12.5px; font-weight: 600; word-break: break-all;">
                                                    <a href="{{ $loginUrl }}" target="_blank" style="color: #0a4282; text-decoration: underline;">{{ $loginUrl }}</a>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Side-by-Side Username & PIN Pass -->
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
                                            <tr>
                                                <td width="49%" valign="top" class="gx-stack" style="padding-right: 6px;">
                                                    <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" class="gx-card" style="background-color: #ffffff; border: 1px solid #cbd9ec; border-radius: 10px; border-collapse: separate;">
                                                        <tr>
                                                            <td align="center" style="padding: 14px 12px;">
                                                                <div style="font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b;">
                                                                    Username Login
                                                                </div>
                                                                <div style="margin-top: 6px; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; font-size: 21px; font-weight: 800; color: #0a4282; letter-spacing: 0.08em;">
                                                                    {{ $username }}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="49%" valign="top" class="gx-stack gx-stack-mt" style="padding-left: 6px;">
                                                    <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" class="gx-card" style="background-color: #ffffff; border: 1px solid #ecc968; border-radius: 10px; border-collapse: separate;">
                                                        <tr>
                                                            <td align="center" style="padding: 14px 12px;">
                                                                <div style="font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #926000;">
                                                                    Password / PIN
                                                                </div>
                                                                <div style="margin-top: 6px; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; font-size: 21px; font-weight: 800; color: #0a4282; letter-spacing: 0.1em;">
                                                                    {{ $plainPassword }}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <p style="margin: 12px 0 0 0; font-size: 12px; color: #64748b; text-align: center; line-height: 1.5;">
                                            Anda juga dapat masuk menggunakan alamat email sekolah yang terdaftar beserta PIN di atas.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Primary CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $loginUrl }}" target="_blank" class="gx-cta" style="display: inline-block; background-color: #0a4282; color: #ffffff; font-size: 14.5px; font-weight: 700; text-decoration: none; padding: 14px 32px; border-radius: 999px; letter-spacing: 0.01em;">
                                            Masuk Portal Sekolah &amp; Cetak Kartu
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Participants Roster Table -->
                            @if($participantCount > 0)
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin-bottom: 10px;">
                                    <tr>
                                        <td align="left" valign="middle">
                                            <h2 style="margin: 0; font-size: 15px; font-weight: 800; color: #0f172a; letter-spacing: -0.01em;">
                                                Daftar Peserta Delegasi
                                            </h2>
                                        </td>
                                        <td align="right" valign="middle">
                                            <span style="display: inline-block; background-color: #e8f0fc; color: #0a4282; font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px;">
                                                Total {{ $participantCount }} Siswa
                                            </span>
                                        </td>
                                    </tr>
                                </table>

                                <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" class="gx-roster gx-card" style="width: 100%; border: 1px solid #d8e2ef; border-radius: 12px; border-collapse: separate; overflow: hidden; margin-bottom: 26px;">
                                    <thead>
                                        <tr style="background-color: #f1f5f9;">
                                            <th width="7%" align="center" style="padding: 10px 8px; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; border-bottom: 1px solid #d8e2ef;">
                                                No
                                            </th>
                                            <th width="21%" align="left" style="padding: 10px 10px; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; border-bottom: 1px solid #d8e2ef;">
                                                Kode Peserta
                                            </th>
                                            <th width="32%" align="left" style="padding: 10px 10px; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; border-bottom: 1px solid #d8e2ef;">
                                                Nama Siswa
                                            </th>
                                            <th width="19%" align="left" class="gx-hide-sm" style="padding: 10px 10px; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; border-bottom: 1px solid #d8e2ef;">
                                                NISN
                                            </th>
                                            <th width="13%" align="center" style="padding: 10px 8px; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; border-bottom: 1px solid #d8e2ef;">
                                                Kelas
                                            </th>
                                            <th width="8%" align="center" style="padding: 10px 8px; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; border-bottom: 1px solid #d8e2ef;">
                                                L/P
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($participants as $idx => $p)
                                            @php
                                                $isLast = ($idx === $participantCount - 1);
                                                $cellBorder = $isLast ? 'none' : '1px solid #edf2f7';
                                                $rowBg = ($idx % 2 === 1) ? '#fafcff' : '#ffffff';
                                                $isFemale = strtoupper((string) ($p['gender'] ?? 'L')) === 'P';
                                                $genderBg = $isFemale ? '#fdf2f8' : '#eff6ff';
                                                $genderColor = $isFemale ? '#be185d' : '#0a4282';
                                                $codeVal = $p['competition_code'] ?? ($p['registration_number'] ?? '-');
                                            @endphp
                                            <tr style="background-color: {{ $rowBg }};">
                                                <td align="center" style="padding: 10px 8px; border-bottom: {{ $cellBorder }}; font-size: 12px; font-weight: 600; color: #64748b;">
                                                    {{ $idx + 1 }}
                                                </td>
                                                <td align="left" style="padding: 10px 10px; border-bottom: {{ $cellBorder }}; white-space: nowrap;">
                                                    <span style="display: inline-block; background-color: #eff6ff; color: #0a4282; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; font-size: 11.5px; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                                                        {{ $codeVal }}
                                                    </span>
                                                </td>
                                                <td align="left" style="padding: 10px 10px; border-bottom: {{ $cellBorder }}; font-size: 13px; font-weight: 700; color: #0f172a;">
                                                    {{ $p['name'] ?? '-' }}
                                                </td>
                                                <td align="left" class="gx-hide-sm" style="padding: 10px 10px; border-bottom: {{ $cellBorder }}; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; font-size: 12px; color: #475569;">
                                                    {{ $p['nisn'] ?? '-' }}
                                                </td>
                                                <td align="center" style="padding: 10px 8px; border-bottom: {{ $cellBorder }}; font-size: 12.5px; font-weight: 600; color: #334155; white-space: nowrap;">
                                                    {{ $p['class_name'] ?? '-' }}
                                                </td>
                                                <td align="center" style="padding: 10px 8px; border-bottom: {{ $cellBorder }};">
                                                    <span style="display: inline-block; background-color: {{ $genderBg }}; color: {{ $genderColor }}; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 999px;">
                                                        {{ $p['gender'] ?? 'L' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            <!-- Hanging-Indent Numbered Next Steps -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" class="gx-card" style="background-color: #fffbeb; border: 1px solid #f5dfa0; border-radius: 12px; border-collapse: separate; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 18px 20px;">
                                        <div style="font-size: 13px; font-weight: 800; color: #78350f; margin-bottom: 12px;">
                                            Petunjuk Pelaksanaan bagi Koordinator Sekolah
                                        </div>
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
                                            <tr>
                                                <td width="28" valign="top" style="padding-bottom: 8px;">
                                                    <div style="width: 20px; height: 20px; line-height: 20px; text-align: center; border-radius: 999px; background-color: #0a4282; color: #ffffff; font-size: 11px; font-weight: 800;">1</div>
                                                </td>
                                                <td valign="top" style="padding-bottom: 8px; font-size: 13px; line-height: 1.65; color: #713f12; text-align: justify;">
                                                    Masuk ke Portal Koordinator Sekolah menggunakan <strong>Username</strong> dan <strong>Password / PIN</strong> resmi yang tertera di atas.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="28" valign="top" style="padding-bottom: 8px;">
                                                    <div style="width: 20px; height: 20px; line-height: 20px; text-align: center; border-radius: 999px; background-color: #0a4282; color: #ffffff; font-size: 11px; font-weight: 800;">2</div>
                                                </td>
                                                <td valign="top" style="padding-bottom: 8px; font-size: 13px; line-height: 1.65; color: #713f12; text-align: justify;">
                                                    Periksa kelengkapan biodata seluruh peserta dan cetak <strong>Kartu Peserta (QR Check-in)</strong> sebelum hari pelaksanaan.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="28" valign="top">
                                                    <div style="width: 20px; height: 20px; line-height: 20px; text-align: center; border-radius: 999px; background-color: #0a4282; color: #ffffff; font-size: 11px; font-weight: 800;">3</div>
                                                </td>
                                                <td valign="top" style="font-size: 13px; line-height: 1.65; color: #713f12; text-align: justify;">
                                                    Wajib membawa Kartu Peserta saat daftar ulang di <strong>Gedung Genza English Center (Tanjung PHM Noor)</strong> minimal 30 menit sebelum perlombaan dimulai.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Formal Justified Closing -->
                            <p style="margin: 0 0 18px 0; font-size: 13.5px; line-height: 1.75; color: #475569; text-align: justify;">
                                Demikian informasi resmi ini kami sampaikan. Mohon simpan email ini sebagai bukti registrasi delegasi sekolah Anda. Atas partisipasi dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.
                            </p>
                            <p style="margin: 0; font-size: 13.5px; line-height: 1.6; color: #0f172a;">
                                Hormat kami,<br>
                                <strong>Panitia G-English Brain Champion</strong><br>
                                <span style="font-size: 12.5px; color: #64748b;">Genza Education &middot; Genza English Center Tanjung PHM Noor</span>
                            </p>
                        </td>
                    </tr>

                    <!-- Institutional Footer -->
                    <tr>
                        <td class="gx-pad" style="background-color: #0b1e36; padding: 22px 36px; text-align: center;">
                            <div style="font-size: 13.5px; font-weight: 800; color: #ffffff; letter-spacing: 0.01em;">
                                Genza Education <span style="color: #fbb900;">&bull;</span> Genza English Center
                            </div>
                            <p style="margin: 6px 0 0 0; font-size: 12px; line-height: 1.6; color: #9fb3c8;">
                                Jl. Ir. PHM Noor RT.4 No. 119, Pembataan, Kec. Murung Pudak, Kab. Tabalong<br>
                                <a href="{{ $appBaseUrl }}" target="_blank" style="color: #fbb900; text-decoration: none; font-weight: 600;">genzaenglish.my.id</a>
                            </p>
                            <p style="margin: 12px 0 0 0; font-size: 11px; color: #6c829c;">
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
