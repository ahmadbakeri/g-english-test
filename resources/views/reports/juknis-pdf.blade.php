<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 15mm 14mm 16mm 14mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5pt;
            line-height: 1.48;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #0a4282;
            color: #ffffff;
            padding: 14px 16px;
            border-radius: 8px;
            border-bottom: 4px solid #fbb900;
            margin-bottom: 12px;
        }
        .brand-badge {
            display: inline-block;
            background-color: #fbb900;
            color: #0a4282;
            font-size: 8pt;
            font-weight: bold;
            padding: 2px 9px;
            border-radius: 4px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        h1 {
            color: #ffffff;
            font-size: 15.5pt;
            margin: 2px 0 4px 0;
            line-height: 1.25;
        }
        .subtitle {
            color: #dbeafe;
            font-size: 9pt;
            margin: 0;
        }
        .meta-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin: 0 -6px 12px -6px;
        }
        .meta-card {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-top: 3px solid #0a4282;
            border-radius: 5px;
            padding: 6px 9px;
            font-size: 8.5pt;
            vertical-align: top;
        }
        .meta-label {
            color: #64748b;
            font-size: 7.5pt;
            text-transform: uppercase;
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
        }
        .meta-val {
            color: #0a4282;
            font-weight: bold;
        }
        .flow-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px 0;
            margin: 0 -4px 12px -4px;
        }
        .flow-step {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 7px 6px;
            text-align: center;
            vertical-align: top;
        }
        .flow-num {
            display: inline-block;
            background-color: #0a4282;
            color: #fbb900;
            font-weight: bold;
            font-size: 8pt;
            width: 18px;
            height: 18px;
            line-height: 18px;
            border-radius: 9px;
            margin-bottom: 3px;
        }
        .flow-title {
            font-weight: bold;
            font-size: 8.5pt;
            color: #0a4282;
            display: block;
        }
        .flow-desc {
            font-size: 7.5pt;
            color: #475569;
        }
        .flow-arrow {
            width: 14px;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            color: #fbb900;
            font-size: 12pt;
        }
        .section {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #0a4282;
            color: #ffffff;
            font-size: 10pt;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            margin: 0 0 7px 0;
        }
        .section-title span {
            color: #fbb900;
            margin-right: 4px;
        }
        ol, ul {
            margin: 3px 0 6px 0;
            padding-left: 18px;
        }
        li {
            margin-bottom: 3.5px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 8.8pt;
        }
        table.data-table th {
            background-color: #e2e8f0;
            color: #0a4282;
            text-align: left;
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            font-weight: bold;
        }
        table.data-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }
        .shot-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background-color: #f8fafc;
            padding: 6px;
            margin-top: 7px;
            margin-bottom: 6px;
            page-break-inside: avoid;
        }
        .shot-topbar {
            font-size: 7.5pt;
            color: #475569;
            background-color: #e2e8f0;
            padding: 3px 8px;
            border-radius: 4px 4px 0 0;
            margin-bottom: 4px;
            font-weight: bold;
        }
        .shot-img {
            width: 100%;
            height: auto;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            display: block;
        }
        .shot-caption {
            font-size: 8pt;
            color: #334155;
            margin-top: 4px;
            font-style: italic;
        }
        .note-box {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-left: 4px solid #fbb900;
            padding: 7px 10px;
            border-radius: 4px;
            font-size: 8.8pt;
            margin-top: 7px;
        }
        .footer {
            position: fixed;
            bottom: -10mm;
            left: 0;
            right: 0;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            font-size: 7.8pt;
            color: #64748b;
            text-align: center;
        }
        code {
            background-color: #f1f5f9;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 8.3pt;
            color: #0a4282;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="footer">
        G-English Course &amp; Brain Champion • Portal Resmi: https://genzaenglish.my.id • Dokumen Petunjuk Teknis Bergambar
    </div>

    <div class="header">
        <div class="brand-badge">G-ENGLISH • {{ $badge }}</div>
        <h1>{{ $title }}</h1>
        <p class="subtitle">{{ $subtitle }}</p>
    </div>

    <table class="meta-grid">
        <tr>
            <td class="meta-card" style="width: 38%;">
                <span class="meta-label">Akses Portal</span>
                <span class="meta-val">{{ $portalUrl }}</span>
            </td>
            <td class="meta-card" style="width: 38%;">
                <span class="meta-label">Sasaran Pengguna</span>
                <span class="meta-val">{{ $audience }}</span>
            </td>
            <td class="meta-card" style="width: 24%;">
                <span class="meta-label">Versi Dokumen</span>
                <span class="meta-val">{{ $version ?? 'v2.0 (2026)' }}</span>
            </td>
        </tr>
    </table>

    @if (!empty($flow))
        <table class="flow-table">
            <tr>
                @foreach ($flow as $i => $step)
                    <td class="flow-step">
                        <span class="flow-num">{{ $i + 1 }}</span>
                        <span class="flow-title">{{ $step['title'] }}</span>
                        <span class="flow-desc">{{ $step['desc'] }}</span>
                    </td>
                    @if ($i < count($flow) - 1)
                        <td class="flow-arrow">&rarr;</td>
                    @endif
                @endforeach
            </tr>
        </table>
    @endif

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

            @if (!empty($section['screenshots']))
                @foreach ($section['screenshots'] as $shot)
                    @if (!empty($shot['src']))
                        <div class="shot-card">
                            <div class="shot-topbar">&#128247; Tangkapan Layar: {{ $shot['label'] }}</div>
                            <img class="shot-img" src="{{ $shot['src'] }}" alt="{{ $shot['label'] }}">
                            @if (!empty($shot['caption']))
                                <div class="shot-caption">{{ $shot['caption'] }}</div>
                            @endif
                        </div>
                    @endif
                @endforeach
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
