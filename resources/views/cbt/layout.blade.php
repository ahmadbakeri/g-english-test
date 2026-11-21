<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    
    <title>CBT Engine - @yield('title', 'Exam')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    @vite(['resources/css/exam.css'])

    @stack('styles')
</head>
<body class="bg-gray-50 text-slate-800 antialiased overflow-hidden selection:bg-indigo-500 selection:text-white">
    
    <!-- Exam Container -->
    <div class="h-screen w-screen flex flex-col overflow-hidden bg-slate-50 relative">
        @yield('content')
    </div>

    <!-- Modals & Overlays -->
    @stack('modals')


    @stack('scripts')
</body>
</html>
