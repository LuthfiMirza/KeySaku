<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'KeySaku' }} — Kelola Keuangan Keluarga</title>
    @include('layouts.partials.pwa-meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-slate-100 antialiased">
    <div class="min-h-screen relative" x-data="{ sidebarOpen: true }">
        <div class="fixed inset-0 -z-10" style="background: linear-gradient(160deg, #0a0f2e 0%, #0f1756 25%, #1a1060 50%, #2d0f6e 75%, #3b0f7a 100%);"></div>
        <div class="fixed inset-0 pointer-events-none -z-10" style="background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(99,102,241,0.25), transparent 70%);"></div>

        <aside class="hidden lg:flex lg:flex-col lg:fixed lg:inset-y-0 lg:z-50 transition-all duration-300" :class="sidebarOpen ? 'lg:w-64' : 'lg:w-20'">
            @include('layouts.partials.sidebar')
        </aside>

        <div class="min-h-screen transition-all duration-300" :class="sidebarOpen ? 'lg:pl-64' : 'lg:pl-20'">
            <div class="max-w-md mx-auto min-h-screen flex flex-col relative lg:max-w-none">
                @include('layouts.partials.mobile-header')

                <main class="flex-1 mt-2 bg-slate-50 rounded-t-3xl overflow-y-auto pb-32 shadow-2xl lg:mt-0 lg:bg-transparent lg:rounded-none lg:overflow-visible lg:pb-0 lg:shadow-none lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>

    @include('layouts.partials.bottom-nav')

    @livewireScripts
    <script>
        if ('serviceWorker' in navigator) {
            // Service worker registration will go here in a later milestone.
        }
    </script>
</body>
</html>
