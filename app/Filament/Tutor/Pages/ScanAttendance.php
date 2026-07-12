<?php

namespace App\Filament\Tutor\Pages;

use Filament\Pages\Page;
use App\Models\Student;
use App\Models\Tutor;
use App\Models\User;
use App\Models\Attendance;
use App\Models\ClassSchedule;
use Illuminate\Support\Carbon;

class ScanAttendance extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-camera';
    protected static ?string $title = 'Scan Attendance QR Code';
    protected static ?int $navigationSort = 0;
    protected string $view = 'filament.pages.scan-attendance';

    public static function getNavigationLabel(): string
    {
        return __('Scan Attendance');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }

    public function processQrCode($decodedQr)
    {
        $parts = explode('-', $decodedQr);
        if (count($parts) !== 2) {
            $this->dispatch('scan-result', ['type' => 'error', 'message' => __('Invalid QR format.')]);
            return;
        }

        $roleType = strtolower($parts[0]);
        $id = $parts[1];
        
        $studentId = null;
        $tutorId = null;
        $userId = null;
        $classId = null;

        $name = '';

        $dayOfWeek = now()->format('l');

        if ($roleType === 'student') {
            $student = Student::find($id);
            if (!$student) {
                $this->dispatch('scan-result', ['type' => 'error', 'message' => __('Student data not found.')]);
                return;
            }
            $studentId = $student->id;
            $name = $student->name;
            
            $classSchedule = ClassSchedule::whereHas('classRoom.students', function ($q) use ($id) {
                $q->where('students.id', $id);
            })->whereJsonContains('schedule_items', ['day' => $dayOfWeek])->first();
            
            if ($classSchedule) {
                $classId = $classSchedule->class_id;
            }

        } elseif ($roleType === 'tutor') {
            $tutor = Tutor::find($id);
            if (!$tutor) {
                $this->dispatch('scan-result', ['type' => 'error', 'message' => __('Tutor data not found.')]);
                return;
            }
            $tutorId = $tutor->id;
            $name = $tutor->name;
            
            $classSchedule = ClassSchedule::where('tutor_id', $id)
                ->whereJsonContains('schedule_items', ['day' => $dayOfWeek])
                ->first();
            if ($classSchedule) {
                $classId = $classSchedule->class_id;
            }
        } elseif ($roleType === 'employee') {
            $user = User::find($id);
            if (!$user) {
                $this->dispatch('scan-result', ['type' => 'error', 'message' => __('Employee data not found.')]);
                return;
            }
            $userId = $user->id;
            $name = $user->name;
        } else {
            $this->dispatch('scan-result', ['type' => 'error', 'message' => __('Unrecognized QR type.')]);
            return;
        }

        $date = now()->format('Y-m-d');
        
        $query = Attendance::where('role_type', $roleType)->where('date', $date);
        if ($studentId) $query->where('student_id', $studentId);
        if ($tutorId) $query->where('tutor_id', $tutorId);
        if ($userId) $query->where('user_id', $userId);
        
        if ($query->exists()) {
            $this->dispatch('scan-result', ['type' => 'error', 'message' => __(':name has already checked in today.', ['name' => $name])]);
            return;
        }

        $status = 'present';
        
        if ($classId) {
            $scheduleForClass = ClassSchedule::where('class_id', $classId)
                ->whereJsonContains('schedule_items', ['day' => $dayOfWeek])
                ->first();

            $startTime = null;
            if ($scheduleForClass && is_array($scheduleForClass->schedule_items)) {
                foreach ($scheduleForClass->schedule_items as $item) {
                    if (($item['day'] ?? '') === $dayOfWeek) {
                        $startTime = $item['start_time'] ?? null;
                        break;
                    }
                }
            }
            if ($startTime && now()->format('H:i:s') > $startTime) {
                $status = 'late';
            }
        }

        Attendance::create([
            'role_type' => $roleType,
            'class_id' => $classId,
            'student_id' => $studentId,
            'tutor_id' => $tutorId,
            'user_id' => $userId,
            'date' => $date,
            'status' => $status,
        ]);

        $this->dispatch('scan-result', ['type' => 'success', 'message' => __('Check-in successful: :name', ['name' => $name])]);
    }
}