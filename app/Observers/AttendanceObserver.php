<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Notifications\ChildAttendanceNotification;

class AttendanceObserver
{
    /**
     * Handle the Attendance "created" event.
     */
    public function created(Attendance $attendance): void
    {
        $this->notifyParents($attendance, 'in');
    }

    /**
     * Handle the Attendance "updated" event.
     */
    public function updated(Attendance $attendance): void
    {
        if ($attendance->wasChanged('time_out') && $attendance->time_out !== null) {
            $this->notifyParents($attendance, 'out');
        }
    }

    protected function notifyParents(Attendance $attendance, $type)
    {
        // Get the student related to this attendance
        $student = $attendance->student ?? $attendance->user?->student;
        if (!$student) return;

        // Get parents (assuming parents are users linked through the pivot)
        $parents = $student->parents;

        foreach ($parents as $parent) {
            $parent->notify((new ChildAttendanceNotification($attendance, $type))->onConnection('database'));
        }
    }
}
