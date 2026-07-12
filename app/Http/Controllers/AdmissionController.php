<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use Illuminate\Http\Request;

class AdmissionController extends Controller
{
    public function create()
    {
        return view('admission');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'level' => 'required|string|in:SD,SMP,SMA',
            'mother_name' => 'required|string|max:255',
            'mother_phone' => 'required|string|max:20',
            'mother_email' => 'nullable|email|max:255',
            'mother_job' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|max:20',
            'father_email' => 'nullable|email|max:255',
            'father_job' => 'nullable|string|max:255',
        ]);

        $validated['status'] = 'pending';

        Admission::create($validated);

        return redirect()->route('landing')->with('success', 'Pendaftaran berhasil! Tim kami akan segera menghubungi Anda melalui WhatsApp.');
    }
}
