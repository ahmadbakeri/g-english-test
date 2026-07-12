<?php

namespace App\Filament\Resources\Finance\StudentPayments\Pages;

use App\Filament\Resources\Finance\StudentPayments\StudentPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageStudentPayments extends ManageRecords
{
    protected static string $resource = StudentPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('generate_bills')
                ->label(__('Generate Monthly Bill'))
                ->icon('heroicon-o-document-plus')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    $month = now()->month;
                    $year = now()->year;
                    
                    $students = \App\Models\Student::where('is_active', true)->get();
                    $count = 0;

                    foreach ($students as $student) {
                        $exists = \App\Models\StudentPayment::where('student_id', $student->id)
                            ->where('month', $month)
                            ->where('year', $year)
                            ->exists();

                        if (!$exists) {
                            \App\Models\StudentPayment::create([
                                'student_id' => $student->id,
                                'month' => $month,
                                'year' => $year,
                                'amount' => 250000,
                                'status' => 'unpaid',
                            ]);
                            $count++;
                        }
                    }

                    \Filament\Notifications\Notification::make()
                        ->title(__('Successfully created :count new bills', ['count' => $count]))
                        ->success()
                        ->send();
                }),
            \Filament\Actions\ExportAction::make()->exporter(\App\Filament\Exports\StudentPaymentExporter::class),
            CreateAction::make(),
        ];
    }
}
