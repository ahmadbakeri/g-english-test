<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\Tutor;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class FormAbsensi extends Component
{
    public $token;
    public $roleType = 'student';
    public $personId;

    public $successMessage = '';
    public $errorMessage = '';

    public function mount()
    {
        $this->token = request()->query('token');
        $this->validateToken();
    }

    private function validateToken()
    {
        // Accept current minute AND previous minute tokens (QR refreshes every minute)
        $now = Carbon::now();
        $currentToken = md5($now->format('Y-m-d-H-i') . config('app.key'));
        $prevToken = md5($now->subMinute()->format('Y-m-d-H-i') . config('app.key'));

        if ($this->token !== $currentToken && $this->token !== $prevToken) {
            $this->errorMessage = __('Token is invalid or expired. Please scan the latest QR Code.');
        }
    }

    public function submit()
    {
        $this->successMessage = '';
        $this->errorMessage = '';
        
        $this->validateToken();
        if ($this->errorMessage) {
            return;
        }

        if (empty($this->personId)) {
            $this->errorMessage = __('Please select your name.');
            return;
        }

        $date = Carbon::today()->format('Y-m-d');
        $studentId = null;
        $tutorId = null;
        $userId = null;
        $classId = null;
        $classSchedule = null;
        $name = '';

        if ($this->roleType === 'student') {
            $student = Student::find($this->personId);
            if (!$student) return;
            $name = $student->name;
            $studentId = $student->id;
            
            $dayOfWeek = $this->getIndonesianDay();
            $classSchedule = \App\Models\ClassSchedule::whereHas('classRoom.students', function ($q) use ($studentId) {
                $q->where('students.id', $studentId);
            })->where('day_of_week', $dayOfWeek)->first();

            if ($classSchedule) $classId = $classSchedule->class_id;

        } elseif ($this->roleType === 'tutor') {
            $tutor = Tutor::find($this->personId);
            if (!$tutor) return;
            $name = $tutor->name;
            $tutorId = $tutor->id;

            $dayOfWeek = $this->getIndonesianDay();
            $classSchedule = \App\Models\ClassSchedule::where('tutor_id', $tutorId)
                ->where('day_of_week', $dayOfWeek)
                ->first();

            if ($classSchedule) $classId = $classSchedule->class_id;

        } elseif ($this->roleType === 'employee') {
            $user = User::find($this->personId);
            if (!$user) return;
            $name = $user->name;
            $userId = $user->id;
        }

        // Check if already attended
        $query = Attendance::where('role_type', $this->roleType)->where('date', $date);
        if ($studentId) $query->where('student_id', $studentId);
        if ($tutorId) $query->where('tutor_id', $tutorId);
        if ($userId) $query->where('user_id', $userId);

        if ($query->exists()) {
            $this->errorMessage = __("Hello :name, you have already submitted your attendance today.", ['name' => $name]);
            return;
        }

        // Determine status (Late or Present)
        $status = 'present';
        $currentTime = now()->format('H:i:s');
        if ($classSchedule && $currentTime > $classSchedule->start_time) {
            $status = 'late';
        }

        Attendance::create([
            'role_type' => $this->roleType,
            'student_id' => $studentId,
            'tutor_id' => $tutorId,
            'user_id' => $userId,
            'class_id' => $classId,
            'date' => $date,
            'status' => $status,
            'notes' => __('Attended via Public Mobile Form at :time', ['time' => now()->format('H:i')]),
        ]);

        $this->successMessage = __("Thank you :name, your attendance has been recorded successfully!", ['name' => $name]);
        $this->personId = null; // reset
    }

    private function getIndonesianDay()
    {
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        return $days[Carbon::now()->format('l')];
    }

    public function render()
    {
        $options = [];
        if ($this->roleType === 'student') {
            $options = Student::orderBy('name')->pluck('name', 'id')->toArray();
        } elseif ($this->roleType === 'tutor') {
            $options = Tutor::orderBy('name')->pluck('name', 'id')->toArray();
        } elseif ($this->roleType === 'employee') {
            $options = User::orderBy('name')->pluck('name', 'id')->toArray();
        }

        return view('livewire.form-absensi', [
            'options' => $options
        ])->layout('components.layouts.app');
    }
}
