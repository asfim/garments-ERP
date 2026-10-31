<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TBG - Turkey Bangla Garments Ltd | ERP Solution V6.0')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '"Noto Sans Bengali"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            dark: '#070c1e',
                            card: '#0d1630',
                            cardBorder: 'rgba(255, 255, 255, 0.08)',
                            neonBlue: '#00f2fe',
                            neonPurple: '#9d4edd',
                            neonGreen: '#10b981',
                            neonAmber: '#f59e0b',
                            neonRose: '#f43f5e'
                        }
                    }
                }
            }
        }
    </script>
    <!-- Separate Custom ERP CSS File -->
    <link rel="stylesheet" href="{{ asset('css/erp-style.css') }}?v=1.2">
    @stack('styles')
</head>
<body class="min-h-screen text-slate-200 antialiased flex flex-col justify-between">

    <!-- Global Header -->
    @include('layouts.header')

    <!-- Main Wrapper (Sidebar + Dynamic Content) -->
    <div class="flex flex-1 overflow-hidden relative">
        <!-- Global Sidebar -->
        @include('layouts.sidebar')

        <!-- Page Specific Content -->
        <main class="flex-1 overflow-y-auto p-4 lg:p-6 space-y-5">
            @yield('content')
        </main>
    </div>

    <!-- Global Footer -->
    @include('layouts.footer')

    <!-- Separate Custom ERP JS File -->
    <script src="{{ asset('js/erp-app.js') }}"></script>
    @stack('scripts')
</body>
</html>

