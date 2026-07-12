<?php
namespace App\Filament\Resources\Operations\JournalValidationResource\Pages;
use App\Filament\Resources\Operations\JournalValidationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListJournalValidations extends ListRecords
{
    protected static string $resource = JournalValidationResource::class;
    protected function getHeaderActions(): array 
    { 
        return [
            Actions\Action::make('validate_all')
                ->label(__('Approve All Pending Journals'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Setujui Semua Jurnal')
                ->modalDescription('Tindakan ini akan menyetujui semua jurnal yang saat ini berstatus "Diajukan" (Submitted).')
                ->action(function () {
                    $pendingJournals = \App\Models\Journal::where('status', 'submitted')->get();
                    $count = 0;
                    $userId = \Illuminate\Support\Facades\Auth::id();
                    
                    foreach ($pendingJournals as $journal) {
                        \App\Models\JournalValidation::create([
                            'journal_id' => $journal->id,
                            'validated_by' => $userId,
                            'status' => 'approved',
                            'notes' => 'Disetujui secara massal otomatis.',
                            'validated_at' => now(),
                        ]);
                        $journal->update(['status' => 'validated']);
                        $count++;
                    }
                    
                    \Filament\Notifications\Notification::make()
                        ->title(__('Successfully approved :count journals!', ['count' => $count]))
                        ->success()
                        ->send();
                })
        ]; 
    }
}
