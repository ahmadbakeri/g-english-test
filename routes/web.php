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

Route::get('/absen', \App\Livewire\FormAbsensi::class);

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
