<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->classOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Bulan</label>
                <select wire:model="month">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($num); ?>"><?php echo e($name); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Tahun</label>
                <select wire:model="year">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($y = now()->year - 2; $y <= now()->year + 1; $y++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>
            <div class="filter-group" style="display: flex; gap: 0.5rem;">
                <button wire:click="generateReport" class="px-6 py-2 bg-genza-blue hover:bg-blue-800 text-white font-bold rounded-full shadow-md hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300 text-sm">📊 Tampilkan</button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($reportData)): ?>
                    <button wire:click="exportPdf" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-full shadow-md hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300 text-sm">📄 PDF</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($reportData)): ?>
        <div class="summary-cards">
            <div class="summary-card summary-present">
                <div class="number"><?php echo e($summary['present'] ?? 0); ?></div>
                <div class="label">✅ Hadir</div>
            </div>
            <div class="summary-card summary-late">
                <div class="number"><?php echo e($summary['late'] ?? 0); ?></div>
                <div class="label">⏰ Terlambat</div>
            </div>
            <div class="summary-card summary-absent">
                <div class="number"><?php echo e($summary['absent'] ?? 0); ?></div>
                <div class="label">❌ Tidak Hadir</div>
            </div>
            <div class="summary-card summary-excused">
                <div class="number"><?php echo e($summary['excused'] ?? 0); ?></div>
                <div class="label">📋 Izin</div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Report Table -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($reportData)): ?>
        <div class="report-table-wrapper" style="margin-top: 1rem;">
            <table class="report-table">
                <thead>
                    <tr>
                        <th class="name-col">Nama Siswa</th>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <th><?php echo e($date->format('d')); ?></th>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <th style="background: #065f46;">H</th>
                        <th style="background: #92400e;">T</th>
                        <th style="background: #991b1b;">A</th>
                        <th style="background: #1e40af;">I</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr>
                            <td class="name-cell"><?php echo e($row['student']->name); ?></td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $row['days']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($status === 'present'): ?>
                                        <span class="status-h">H</span>
                                    <?php elseif($status === 'late'): ?>
                                        <span class="status-t">T</span>
                                    <?php elseif($status === 'absent'): ?>
                                        <span class="status-a">A</span>
                                    <?php elseif($status === 'excused'): ?>
                                        <span class="status-i">I</span>
                                    <?php else: ?>
                                        <span class="status-none">-</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <td><strong class="status-h"><?php echo e($row['totals']['present']); ?></strong></td>
                            <td><strong class="status-t"><?php echo e($row['totals']['late']); ?></strong></td>
                            <td><strong class="status-a"><?php echo e($row['totals']['absent']); ?></strong></td>
                            <td><strong class="status-i"><?php echo e($row['totals']['excused']); ?></strong></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php elseif($classId): ?>
        <div style="text-align: center; padding: 3rem; color: #9ca3af;">
            <p style="font-size: 1rem;">Klik <strong>Tampilkan</strong> untuk melihat data.</p>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 3rem; color: #9ca3af;">
            <p style="font-size: 1rem;">Pilih kelas dan klik <strong>Tampilkan</strong> untuk melihat rekap absensi.</p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH C:\Users\ahmad\Downloads\Laravel\g-english\resources\views/filament/pages/attendance-report.blade.php ENDPATH**/ ?>