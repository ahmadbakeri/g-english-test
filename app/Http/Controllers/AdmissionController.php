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
            'student_type' => 'nullable|in:internal,external',
            'name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:L,P',
            'religion' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'social_media' => 'nullable|string|max:255',
            'address' => 'required|string',
            'education_level' => 'required|string|max:50',
            'course_level' => 'required|string|max:50',
            'class_name' => 'required|string|max:50',
            'school_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'mother_phone' => 'required|string|max:20',
            'mother_email' => 'nullable|email|max:255',
            'mother_job' => 'nullable|string|max:255',
            'father_name' => 'required|string|max:255',
            'father_phone' => 'nullable|string|max:20',
            'father_email' => 'nullable|email|max:255',
            'father_job' => 'nullable|string|max:255',
        ]);

        $validated['student_type'] = $validated['student_type'] ?? 'internal';
        $validated['status'] = 'pending';

        Admission::create($validated);

        return redirect()->route('landing')->with('success', 'Pendaftaran berhasil! Tim kami akan segera menghubungi Anda melalui WhatsApp.');
    }
}
