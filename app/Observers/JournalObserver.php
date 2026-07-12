<?php

namespace App\Observers;

use App\Models\Journal;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class JournalObserver
{
    /**
     * Handle the Journal "created" event.
     */
    public function created(Journal $journal): void
    {
        if ($journal->status === 'submitted') {
            $this->notifyAdmins($journal);
        }
    }

    /**
     * Handle the Journal "updated" event.
     */
    public function updated(Journal $journal): void
    {
        // Check if status changed to 'submitted'
        if ($journal->isDirty('status') && $journal->status === 'submitted') {
            $this->notifyAdmins($journal);
        }
        
        // Check if status changed to 'validated' or 'rejected'
        if ($journal->isDirty('status') && in_array($journal->status, ['validated', 'rejected'])) {
            $this->notifyTutor($journal);
        }
    }

    private function notifyTutor(Journal $journal)
    {
        if (!$journal->tutor || !$journal->tutor->user) {
            return;
        }

        $statusText = $journal->status === 'validated' ? 'telah divalidasi' : 'ditolak';
        $icon = $journal->status === 'validated' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';
        
        Notification::make()
            ->title('Status Jurnal Diperbarui')
            ->body("Jurnal Anda untuk topik '{$journal->topic}' {$statusText}.")
            ->icon($icon)
            ->success()
            ->sendToDatabase($journal->tutor->user);
    }

    private function notifyAdmins(Journal $journal)
    {
        $admins = User::where('role', 'admin')->get();
        
        $tutorName = $journal->tutor ? $journal->tutor->name : 'Seorang Tutor';
        $className = $journal->classRoom ? $journal->classRoom->name : 'Kelas';

        foreach ($admins as $admin) {
            Notification::make()
                ->title('Jurnal Baru untuk Divalidasi')
                ->body("{$tutorName} baru saja mengajukan jurnal untuk kelas {$className}.")
                ->icon('heroicon-o-document-text')
                ->success()
                ->sendToDatabase($admin);
        }
    }

    /**
     * Handle the Journal "deleted" event.
     */
    public function deleted(Journal $journal): void
    {
        //
    }

    /**
     * Handle the Journal "restored" event.
     */
    public function restored(Journal $journal): void
    {
        //
    }

    /**
     * Handle the Journal "force deleted" event.
     */
    public function forceDeleted(Journal $journal): void
    {
        //
    }
}
