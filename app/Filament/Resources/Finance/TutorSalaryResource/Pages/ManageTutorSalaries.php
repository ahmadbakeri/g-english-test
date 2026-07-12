<?php

namespace App\Filament\Resources\Finance\TutorSalaryResource\Pages;

use App\Filament\Resources\Finance\TutorSalaryResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use App\Models\Tutor;
use App\Models\TutorSalary;
use App\Models\Journal;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class ManageTutorSalaries extends ManageRecords
{
    protected static string $resource = TutorSalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_all')
                ->label(__('Generate Monthly Recap'))
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Generate Penggajian Semua Tutor')
                ->modalDescription('Tindakan ini akan menghitung otomatis JP dan Gaji seluruh tutor aktif untuk periode bulan ini (Tgl 26 bulan lalu s/d 25 bulan ini). Data yang sudah ada di bulan ini tidak akan diganti.')
                ->action(function () {
                    $month = now()->month;
                    $year = now()->year;
                    
                    // The period is from the 26th of the previous month to the 25th of the current month.
                    $currentMonthDate = Carbon::create($year, $month, 1);
                    $startDate = $currentMonthDate->copy()->subMonth()->day(26)->startOfDay();
                    $endDate = $currentMonthDate->copy()->day(25)->endOfDay();

                    $tutors = Tutor::where('is_active', true)->get();
                    $generatedCount = 0;

                    foreach ($tutors as $tutor) {
                        // Check if already exists to prevent duplicate
                        $exists = TutorSalary::where('tutor_id', $tutor->id)
                            ->where('month', $month)
                            ->where('year', $year)
                            ->exists();
                            
                        if (!$exists) {
                            $totalJp = Journal::where('tutor_id', $tutor->id)
                                ->whereBetween('date', [$startDate, $endDate])
                                ->where('status', 'validated')
                                ->sum('jp_count');
                                
                            // Only generate if there is JP, or generate 0? We generate anyway so it shows 0
                            $ratePerJp = 25000; // Default C grade
                            if ($tutor->grade === 'A') $ratePerJp = 50000;
                            if ($tutor->grade === 'B') $ratePerJp = 35000;
                            if ($tutor->grade === 'C') $ratePerJp = 25000;
                            
                            TutorSalary::create([
                                'tutor_id' => $tutor->id,
                                'month' => $month,
                                'year' => $year,
                                'total_jp' => $totalJp,
                                'total_amount' => $totalJp * $ratePerJp,
                                'status' => 'pending',
                            ]);
                            $generatedCount++;
                        }
                    }

                    Notification::make()
                        ->title(__('Successfully generated :count tutor salary data!', ['count' => $generatedCount]))
                        ->success()
                        ->send();
                }),
            CreateAction::make()->label(__('Create Manually')),
        ];
    }
}
