<x-filament-panels::page>
    <style>
        .report-filters {
            background: #ffffff;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
        }
        .report-filters h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 1rem 0;
        }
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            align-items: end;
        }
        .filter-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 0.375rem;
            text-transform: uppercase;
        }
        .filter-group select, .filter-group input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            color: #111827;
        }
        .filter-group select:focus, .filter-group input:focus {
            outline: none;
            border-color: #0284c7;
            box-shadow: 0 0 0 2px rgba(2,132,199,0.15);
        }
        .btn-generate {
            padding: 0.5rem 1.5rem;
            background: linear-gradient(135deg, #0369a1, #0284c7);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-generate:hover { background: linear-gradient(135deg, #075985, #0369a1); }
        .btn-pdf {
            padding: 0.5rem 1.5rem;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-pdf:hover { background: linear-gradient(135deg, #b91c1c, #dc2626); }

        .report-table-wrapper {
            background: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
            overflow-x: auto;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.75rem;
        }
        .report-table th {
            background: #0369a1;
            color: white;
            padding: 0.5rem 0.375rem;
            text-align: center;
            font-weight: 600;
            white-space: nowrap;
            position: sticky;
            top: 0;
        }
        .report-table th.name-col {
            text-align: left;
            padding-left: 0.75rem;
            min-width: 150px;
            position: sticky;
            left: 0;
            z-index: 10;
            background: #0369a1;
        }
        .report-table td {
            padding: 0.375rem;
            text-align: center;
            border-bottom: 1px solid #f3f4f6;
            white-space: nowrap;
        }
        .report-table td.name-cell {
            text-align: left;
            padding-left: 0.75rem;
            font-weight: 600;
            color: #1f2937;
            position: sticky;
            left: 0;
            background: white;
            z-index: 5;
        }
        .report-table tbody tr:hover td { background: #f0f9ff; }
        .report-table tbody tr:hover td.name-cell { background: #f0f9ff; }

        .status-h { color: #16a34a; font-weight: 700; }
        .status-t { color: #f59e0b; font-weight: 700; }
        .status-a { color: #ef4444; font-weight: 700; }
        .status-i { color: #3b82f6; font-weight: 700; }
        .status-none { color: #d1d5db; }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .summary-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
        }
        .summary-card .number { font-size: 1.5rem; font-weight: 800; }
        .summary-card .label { font-size: 0.75rem; color: #6b7280; font-weight: 600; }
        .summary-present .number { color: #16a34a; }
        .summary-late .number { color: #f59e0b; }
        .summary-absent .number { color: #ef4444; }
        .summary-excused .number { color: #3b82f6; }
    </style>

    <!-- Filters -->
    <div class="report-filters">
        <h3>🔍 Filter Laporan</h3>
        <div class="filter-grid">
            <div class="filter-group">
                <label>Kelas</label>
                <select wire:model="classId">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($this->classOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Bulan</label>
                <select wire:model="month">
                    @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $num => $name)
                        <option value="{{ $num }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Tahun</label>
                <select wire:model="year">
                    @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="filter-group" style="display: flex; gap: 0.5rem;">
                <button wire:click="generateReport" class="btn-generate">📊 Tampilkan</button>
                @if(!empty($reportData))
                    <button wire:click="exportPdf" class="btn-pdf">📄 PDF</button>
                @endif
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    @if(!empty($reportData))
        <div class="summary-cards">
            <div class="summary-card summary-present">
                <div class="number">{{ $summary['present'] ?? 0 }}</div>
                <div class="label">✅ Hadir</div>
            </div>
            <div class="summary-card summary-late">
                <div class="number">{{ $summary['late'] ?? 0 }}</div>
                <div class="label">⏰ Terlambat</div>
            </div>
            <div class="summary-card summary-absent">
                <div class="number">{{ $summary['absent'] ?? 0 }}</div>
                <div class="label">❌ Tidak Hadir</div>
            </div>
            <div class="summary-card summary-excused">
                <div class="number">{{ $summary['excused'] ?? 0 }}</div>
                <div class="label">📋 Izin</div>
            </div>
        </div>
    @endif

    <!-- Report Table -->
    @if(!empty($reportData))
        <div class="report-table-wrapper" style="margin-top: 1rem;">
            <table class="report-table">
                <thead>
                    <tr>
                        <th class="name-col">Nama Siswa</th>
                        @foreach($dates as $date)
                            <th>{{ $date->format('d') }}</th>
                        @endforeach
                        <th style="background: #065f46;">H</th>
                        <th style="background: #92400e;">T</th>
                        <th style="background: #991b1b;">A</th>
                        <th style="background: #1e40af;">I</th>
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
                                        <span class="status-none">-</span>
                                    @endif
                                </td>
                            @endforeach
                            <td><strong class="status-h">{{ $row['totals']['present'] }}</strong></td>
                            <td><strong class="status-t">{{ $row['totals']['late'] }}</strong></td>
                            <td><strong class="status-a">{{ $row['totals']['absent'] }}</strong></td>
                            <td><strong class="status-i">{{ $row['totals']['excused'] }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($classId)
        <div style="text-align: center; padding: 3rem; color: #9ca3af;">
            <p style="font-size: 1rem;">Klik <strong>Tampilkan</strong> untuk melihat data.</p>
        </div>
    @else
        <div style="text-align: center; padding: 3rem; color: #9ca3af;">
            <p style="font-size: 1rem;">Pilih kelas dan klik <strong>Tampilkan</strong> untuk melihat rekap absensi.</p>
        </div>
    @endif

</x-filament-panels::page>
