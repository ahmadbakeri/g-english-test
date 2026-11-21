<?php

namespace App\Notifications;

use Filament\Notifications\Notification as FilamentNotification;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class ChildAttendanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $attendance;
    protected $type; // 'in' or 'out'

    public function __construct($attendance, $type)
    {
        $this->attendance = $attendance;
        $this->type = $type;
        $this->onConnection('database');
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class, 'database'];
    }

    public function toDatabase($notifiable)
    {
        $studentName = $this->attendance->student?->name ?? $this->attendance->user?->name ?? 'Siswa';
        $time = ($this->type === 'in' ? $this->attendance->time_in : $this->attendance->time_out) ?? now()->format('H:i:s');
        $action = $this->type === 'in' ? 'masuk' : 'pulang';

        return FilamentNotification::make()
            ->title('Notifikasi Presensi')
            ->body("{$studentName} telah {$action} pada pukul {$time}.")
            ->success()
            ->getDatabaseMessage();
    }

    public function toWebPush($notifiable, $notification)
    {
        $studentName = $this->attendance->student?->name ?? $this->attendance->user?->name ?? 'Siswa';
        $time = ($this->type === 'in' ? $this->attendance->time_in : $this->attendance->time_out) ?? now()->format('H:i:s');
        
        $action = $this->type === 'in' ? 'masuk' : 'pulang';

        return (new WebPushMessage)
            ->title('Notifikasi Presensi')
            ->icon('/favicon.ico')
            ->body("{$studentName} telah {$action} pada pukul {$time}.")
            ->data(['url' => '/parent']); // Clicking the notification opens the parent panel
    }
}
