<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('landing');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/pendaftaran-siswa', [\App\Http\Controllers\AdmissionController::class, 'create'])->name('admission');
Route::post('/daftar', [\App\Http\Controllers\AdmissionController::class, 'store'])->name('admission.store');
Route::get('/brain-champion', \App\Livewire\BrainChampionRegistration::class)->name('brain-champion.register');

//Route::get('/absen', \App\Livewire\FormAbsensi::class);

Route::post('/login-action', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        $request->session()->regenerate();
        
        $role = \Illuminate\Support\Facades\Auth::user()->role;
        if ($role === 'admin') {
            return redirect()->to('/admin');
        } elseif ($role === 'tutor') {
            return redirect()->to('/tutor');
        } elseif ($role === 'school') {
            return redirect()->to('/school');
        } elseif ($role === 'parent') {
            return redirect()->to('/parent');
        } else {
            return redirect()->to('/student');
        }
    }

    return back()->withErrors([
        'username' => 'Username atau Password salah.',
    ]);
})->name('login.action');

Route::post('/logout-action', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout.action');

Route::get('/reports/tutor-salary/{id}', [\App\Http\Controllers\ReportController::class, 'printTutorSalary'])
    ->name('report.tutor-salary')
    ->middleware(['auth']);

Route::get('/reports/student-payment/{id}', [\App\Http\Controllers\ReportController::class, 'printStudentPayment'])
    ->name('report.student-payment')
    ->middleware(['auth']);

Route::get('/reports/certificate/{id}', [\App\Http\Controllers\ReportController::class, 'printCertificate'])
    ->name('report.certificate')
    ->middleware(['auth']);

Route::get('/reports/evaluation/{id}', [\App\Http\Controllers\ReportController::class, 'printEvaluation'])
    ->name('report.evaluation')
    ->middleware(['auth']);

Route::get('/reports/brain-champion-card/{student_id}', [\App\Http\Controllers\ReportController::class, 'printBrainChampionCard'])
    ->name('report.brain-champion-card')
    ->middleware(['auth']);

Route::get('/reports/brain-champion-roster/{grade}', [\App\Http\Controllers\ReportController::class, 'printBrainChampionRoster'])
    ->name('report.brain-champion-roster')
    ->middleware(['auth']);

Route::get('/avatar-proxy', function (\Illuminate\Http\Request $request) {
    $path = (string) $request->query('path', '');
    if ($path === '' || str_contains($path, '..') || str_starts_with($path, '/') || str_starts_with($path, '\\')) {
        abort(404);
    }
    if (!\Illuminate\Support\Facades\Storage::disk('r2')->exists($path)) {
        abort(404);
    }
    $content = \Illuminate\Support\Facades\Storage::disk('r2')->get($path);
    $mimeType = \Illuminate\Support\Facades\Storage::disk('r2')->mimeType($path) ?: 'application/octet-stream';
    return response($content)->header('Content-Type', $mimeType);
})->name('avatar.proxy')->middleware(['web']);

Route::post('/api/push/subscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'store'])
    ->name('push.subscribe')
    ->middleware(['web', 'auth']);

Route::post('/api/push/unsubscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'destroy'])
    ->name('push.unsubscribe')
    ->middleware(['web', 'auth']);
