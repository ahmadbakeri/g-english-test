<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackUserAccessActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            if (!$request->isMethod('GET') || $request->expectsJson() || $request->ajax()) {
                return $response;
            }

            $user = Auth::user();
            if (!$user) {
                return $response;
            }

            $path = ltrim($request->path(), '/');
            if (
                $path === ''
                || str_starts_with($path, 'livewire')
                || str_starts_with($path, 'filament/assets')
                || str_starts_with($path, 'storage/')
                || str_starts_with($path, 'build/')
                || str_starts_with($path, 'images/')
                || str_starts_with($path, 'juknis/')
                || str_starts_with($path, 'up')
                || str_contains($path, 'notifications')
            ) {
                return $response;
            }

            $pageLabel = self::resolvePageLabel($path);

            // Throttle duplicate DB writes per user+page (15s via Redis/Cache)
            $throttleKey = 'access_log:' . $user->id . ':' . md5($pageLabel);
            if (!Cache::add($throttleKey, 1, 15)) {
                return $response;
            }

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'access',
                'page' => $pageLabel,
                'description' => '/' . $path,
                'ip' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 240),
            ]);
        } catch (\Throwable) {
            // Never disrupt user requests if logging fails
        }

        return $response;
    }

    public static function resolvePageLabel(string $path): string
    {
        $map = [
            'admin/h-r/students' => 'Kelola Siswa Internal',
            'admin/h-r/external-students' => 'Kelola Siswa Eksternal',
            'admin/h-r/schools' => 'Kelola Sekolah',
            'admin/h-r/tutors' => 'Kelola Tutor',
            'admin/h-r/users' => 'Kelola Pegawai',
            'admin/h-r/attendances' => 'Absensi',
            'admin/h-r/evaluations' => 'Evaluasi & Nilai',
            'admin/academic/cbt-exams' => 'Kelola Ujian CBT',
            'admin/academic/class-rooms' => 'Kelola Kelas',
            'admin/academic/class-schedules' => 'Jadwal Kelas',
            'admin/academic/study-materials' => 'Materi Belajar',
            'admin/evaluation/cbt-attempts' => 'Exam Monitoring',
            'admin/evaluation/tests' => 'Kelola Tes',
            'admin/evaluation/test-sessions' => 'Sesi & Server Tes',
            'admin/evaluation/test-schedule-requests' => 'Kelola Jadwal Tes',
            'admin/finance/student-payments' => 'Pembayaran Siswa',
            'admin/finance/tutor-salaries' => 'Gaji Tutor',
            'admin/finance/expenses' => 'Pengeluaran',
            'admin/operations/journals' => 'Jurnal Mengajar',
            'admin/operations/journal-validations' => 'Validasi Jurnal',
            'admin/operations/admissions' => 'Pendaftaran Siswa',
            'admin/operations/schedules' => 'Jadwal Harian',
            'admin/access-logs' => 'Detail Log Akses',
            'admin' => 'Dashboard Admin',
            'school/school-participants' => 'Kelola Peserta Sekolah',
            'school/test-schedule-requests' => 'Request Jadwal Tes',
            'school' => 'Dashboard Sekolah',
            'student/cbt' => 'Kerjakan Ujian CBT',
            'student/test-schedule-requests' => 'Request Jadwal Tes',
            'student/study-materials' => 'Materi Belajar',
            'student/attendances' => 'Rekap Kehadiran',
            'student/student-payments' => 'Pembayaran Kursus',
            'student' => 'Dashboard Siswa',
            'cbt' => 'Kerjakan Ujian CBT',
            'tutor/journals' => 'Jurnal Mengajar',
            'tutor/cbt-exams' => 'Kelola Ujian CBT',
            'tutor/tests' => 'Kelola Tes',
            'tutor/test-schedule-requests' => 'Kelola Jadwal Tes',
            'tutor/schedules' => 'Jadwal Mengajar',
            'tutor/evaluations' => 'Evaluasi Siswa',
            'tutor/tutor-salaries' => 'Slip Gaji Tutor',
            'tutor' => 'Dashboard Tutor',
            'parent/student-payments' => 'Pembayaran Anak',
            'parent/journals' => 'Jurnal Belajar Anak',
            'parent' => 'Dashboard Orang Tua',
            'agency/participating-schools' => 'Sekolah Peserta',
            'agency/agency-participants' => 'Daftar Peserta Dinas',
            'agency' => 'Dashboard Monitoring Dinas',
        ];

        foreach ($map as $prefix => $label) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return $label;
            }
        }

        return ucwords(str_replace(['-', '_', '/'], ' ', $path));
    }
}
