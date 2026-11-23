<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 14mm 12mm 15mm 12mm; }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5pt;
            line-height: 1.4;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #0a4282;
            color: #ffffff;
            padding: 11px 14px;
            border-radius: 6px;
            border-bottom: 3px solid #fbb900;
            margin-bottom: 10px;
        }
        .brand {
            display: inline-block;
            background-color: #fbb900;
            color: #0a4282;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 2px 7px;
            border-radius: 3px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        h1 {
            margin: 0 0 2px 0;
            font-size: 13.5pt;
            color: #ffffff;
        }
        .subtitle {
            margin: 0;
            font-size: 8.2pt;
            color: #dbeafe;
        }
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin: 0 -6px 10px -6px;
        }
        .summary-cell {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-top: 3px solid #0a4282;
            border-radius: 5px;
            padding: 6px 9px;
        }
        .summary-label {
            font-size: 7pt;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            display: block;
        }
        .summary-val {
            font-size: 10pt;
            font-weight: bold;
            color: #0a4282;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        table.data-table thead th {
            background-color: #0a4282;
            color: #ffffff;
            text-align: left;
            padding: 6px 7px;
            border: 1px solid #0a4282;
            font-weight: bold;
            font-size: 7.5pt;
            text-transform: uppercase;
        }
        table.data-table tbody td {
            padding: 5px 7px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }
        table.data-table tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .empty {
            text-align: center;
            color: #64748b;
            padding: 18px;
            font-style: italic;
        }
        .footer {
            position: fixed;
            bottom: -9mm;
            left: 0;
            right: 0;
            border-top: 1px solid #cbd5e1;
            padding-top: 3px;
            font-size: 7.5pt;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="footer">
        G-English Course &amp; Brain Champion • Dicetak pada {{ $printedAt }} • https://genzaenglish.my.id
    </div>

    <div class="header">
        <div class="brand">GENZA ENGLISH • LAPORAN DATA</div>
        <h1>{{ $title }}</h1>
        <p class="subtitle">{{ $subtitle }}</p>
    </div>

    @if (!empty($summary))
        <table class="summary-table">
            <tr>
                @foreach ($summary as $label => $value)
                    <td class="summary-cell">
                        <span class="summary-label">{{ $label }}</span>
                        <span class="summary-val">{{ $value }}</span>
                    </td>
                @endforeach
            </tr>
        </table>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                @foreach ($headers as $th)
                    <th>{{ $th }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="empty">Tidak ada data untuk periode / filter yang dipilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
