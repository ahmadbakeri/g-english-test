@extends('cbt.layout')

@section('title', 'Ujian Selesai - ' . $exam->title)

@section('content')
<div class="h-screen w-screen bg-slate-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden animate-fade-in text-center p-10">
        
        <div class="w-24 h-24 bg-emerald-100 rounded-full mx-auto flex items-center justify-center mb-6">
            <i class="ph ph-check-circle text-5xl text-emerald-500"></i>
        </div>

        <h1 class="text-3xl font-black text-slate-800 mb-4">Berhasil Dikumpulkan!</h1>
        <p class="text-slate-500 mb-8 leading-relaxed">
            Jawaban Anda untuk ujian <span class="font-bold text-slate-700">"{{ $exam->title }}"</span> telah berhasil disimpan ke dalam sistem. <br><br>
            Hasil ujian akan diumumkan oleh guru atau admin kelas Anda nantinya.
        </p>

        <a href="{{ url('/student') }}" class="block w-full py-4 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors shadow-md shadow-indigo-200">
            Kembali ke Dashboard
        </a>

    </div>
</div>
@endsection
