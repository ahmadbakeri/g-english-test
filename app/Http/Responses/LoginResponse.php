<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return redirect()->to('/admin');
        } elseif ($user->role === 'tutor') {
            return redirect()->to('/tutor');
        } elseif ($user->role === 'siswa') {
            return redirect()->to('/student');
        } elseif ($user->role === 'parent') {
            return redirect()->to('/parent');
        }

        return redirect()->to('/');
    }
}
