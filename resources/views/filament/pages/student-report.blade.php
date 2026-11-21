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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        .filter-group select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            color: #111827;
        }
        .filter-group select:focus {
            outline: none;
            border-color: #0284c7;
            box-shadow: 0 0 0 2px rgba(2,132,199,0.15);
        }


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
            font-size: 0.875rem;
        }
        .report-table th {
            background: #0369a1;
            color: white;
            padding: 0.75rem;
            text-align: center;
            font-weight: 600;
            white-space: nowrap;
        }
        .report-table th.name-col { text-align: left; }
        .report-table td {
            padding: 0.75rem;
            text-align: center;
            border-bottom: 1px solid #f3f4f6;
        }
        .report-table td.name-cell {
            text-align: left;
            font-weight: 600;
            color: #1f2937;
        }
        .report-table tbody tr:hover td { background: #f0f9ff; }
        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-red { background: #fee2e2; color: #991b1b; }
    </style>

    <!-- Filters -->
    <div class="report-filters">
        <h3>🔍 Filter Laporan Raport</h3>
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
            <div class="filter-group" style="display: flex; gap: 0.5rem;">
                <button wire:click="generateReport" class="px-6 py-2 bg-genza-blue hover:bg-blue-800 text-white font-bold rounded-full shadow-md hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300 text-sm">📊 Tampilkan</button>
                @if(!empty($reportData))
                    <button wire:click="exportPdf" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-full shadow-md hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300 text-sm">📄 Cetak Raport PDF</button>
                @endif
            </div>
        </div>
    </div>

    <!-- Report Table -->
    @if(!empty($reportData))
        <div class="report-table-wrapper">
            <table class="report-table">
                <thead>
                    <tr>
                        <th class="name-col" rowspan="2">Nama Siswa</th>
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
                                    <td>
                                        <span class="{{ $test['score'] < ($test['max_score'] * 0.6) ? 'badge badge-red' : 'badge badge-green' }}">
                                            {{ $test['score'] }}
                                        </span>
                                    </td>
                                @endforeach
                            @else
                                <td>-</td>
                            @endif
                            <td><strong>{{ $row['avg_score'] }}</strong></td>
                            <td style="color: #16a34a;"><strong>{{ $row['attendance']['present'] }}</strong></td>
                            <td style="color: #f59e0b;"><strong>{{ $row['attendance']['late'] }}</strong></td>
                            <td style="color: #3b82f6;"><strong>{{ $row['attendance']['excused'] }}</strong></td>
                            <td style="color: #ef4444;"><strong>{{ $row['attendance']['absent'] }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($classId)
        <div style="text-align: center; padding: 3rem; color: #9ca3af;">
            <p style="font-size: 1rem;">Klik <strong>Tampilkan</strong> untuk melihat data raport.</p>
        </div>
    @endif

</x-filament-panels::page>
