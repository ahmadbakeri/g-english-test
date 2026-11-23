<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 18mm 16mm 18mm 16mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 3px solid #0a4282;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .header-top {
            width: 100%;
            border-collapse: collapse;
        }
        .brand-badge {
            display: inline-block;
            background-color: #0a4282;
            color: #fbb900;
            font-size: 8.5pt;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 4px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        h1 {
            color: #0a4282;
            font-size: 16pt;
            margin: 2px 0 4px 0;
            line-height: 1.25;
        }
        .subtitle {
            color: #475569;
            font-size: 9.5pt;
            margin: 0;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #fbb900;
            border-radius: 4px;
            padding: 9px 12px;
            margin-bottom: 14px;
            font-size: 9pt;
        }
        .meta-box strong {
            color: #0a4282;
        }
        .section {
            margin-bottom: 13px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #0a4282;
            color: #ffffff;
            font-size: 10.5pt;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 4px;
            margin: 0 0 8px 0;
        }
        .section-title span {
            color: #fbb900;
            margin-right: 4px;
        }
        ol, ul {
            margin: 4px 0 6px 0;
            padding-left: 18px;
        }
        li {
            margin-bottom: 4px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 9pt;
        }
        table.data-table th {
            background-color: #e2e8f0;
            color: #0a4282;
            text-align: left;
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            font-weight: bold;
        }
        table.data-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }
        .note-box {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-left: 4px solid #fbb900;
            padding: 8px 11px;
            border-radius: 4px;
            font-size: 9pt;
            margin-top: 8px;
        }
        .footer {
            position: fixed;
            bottom: -10mm;
            left: 0;
            right: 0;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            font-size: 8pt;
            color: #64748b;
            text-align: center;
        }
        code {
            background-color: #f1f5f9;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 8.5pt;
            color: #0f172a;
        }
    </style>
</head>
<body>
    <div class="footer">
        G-English Course &amp; Brain Champion • Portal Resmi: https://genzaenglish.my.id • Dokumen Petunjuk Teknis
    </div>

    <div class="header">
        <div class="brand-badge">G-ENGLISH • {{ $badge }}</div>
        <h1>{{ $title }}</h1>
        <p class="subtitle">{{ $subtitle }}</p>
    </div>

    <div class="meta-box">
        <strong>Akses Portal:</strong> <code>{{ $portalUrl }}</code> &nbsp;|&nbsp;
        <strong>Sasaran Pengguna:</strong> {{ $audience }} &nbsp;|&nbsp;
        <strong>Versi:</strong> {{ $version ?? 'v2.0 (2026)' }}
    </div>

    @foreach ($sections as $idx => $section)
        <div class="section">
            <div class="section-title"><span>{{ $idx + 1 }}.</span> {{ $section['title'] }}</div>

            @if (!empty($section['intro']))
                <p style="margin: 0 0 6px 0;">{{ $section['intro'] }}</p>
            @endif

            @if (!empty($section['steps']))
                <ol>
                    @foreach ($section['steps'] as $step)
                        <li>{!! $step !!}</li>
                    @endforeach
                </ol>
            @endif

            @if (!empty($section['bullets']))
                <ul>
                    @foreach ($section['bullets'] as $bullet)
                        <li>{!! $bullet !!}</li>
                    @endforeach
                </ul>
            @endif

            @if (!empty($section['table']))
                <table class="data-table">
                    <thead>
                        <tr>
                            @foreach ($section['table']['headers'] as $th)
                                <th>{{ $th }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($section['table']['rows'] as $row)
                            <tr>
                                @foreach ($row as $cell)
                                    <td>{!! $cell !!}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if (!empty($section['note']))
                <div class="note-box">
                    <strong>Catatan Penting:</strong> {!! $section['note'] !!}
                </div>
            @endif
        </div>
    @endforeach
</body>
</html>
