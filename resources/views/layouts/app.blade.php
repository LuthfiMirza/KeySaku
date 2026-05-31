<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <title>{{ $title ?? 'KeySaku' }} — Kelola Keuangan Keluarga</title>
    @include('layouts.partials.pwa-meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --bento-bg: #0A1628;
            --bento-card: #132044;
            --bento-card-2: #1A2B52;
            --bento-border: rgba(255,255,255,0.08);
            --bento-lime: #A3E635;
            --bento-lime-dim: #84CC16;
            --bento-purple: #A3E635;
            --bento-teal: #A3E635;
            --bento-rose: #F87171;
            --bento-amber: #FCD34D;
            --bento-text: #FFFFFF;
            --bento-text-muted: #94A3B8;
            --bento-text-dim: #64748B;
            --bento-radius: 24px;
            --bento-radius-sm: 16px;
        }

        .font-display {
            font-family: 'Plus Jakarta Sans', var(--font-sans, sans-serif);
            font-weight: 800;
            letter-spacing: -0.03em;
            font-feature-settings: "tnum";
        }

        @keyframes bentoIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .bento-animate {
            animation: bentoIn 0.4s ease-out both;
        }

        .bento-animate:nth-child(1) { animation-delay: 0ms; }
        .bento-animate:nth-child(2) { animation-delay: 60ms; }
        .bento-animate:nth-child(3) { animation-delay: 120ms; }
        .bento-animate:nth-child(4) { animation-delay: 180ms; }
        .bento-animate:nth-child(5) { animation-delay: 240ms; }
        .bento-animate:nth-child(6) { animation-delay: 300ms; }
    </style>
</head>
<body class="h-full bg-[var(--bento-bg)] font-['Plus_Jakarta_Sans'] text-[var(--bento-text)] antialiased" style="background: radial-gradient(ellipse at top, #0F1F45 0%, #0A1628 60%) !important;color:#FFFFFF !important;">
    <div class="min-h-screen relative" x-data="{ sidebarOpen: true }">

        <aside class="hidden lg:flex lg:flex-col lg:fixed lg:inset-y-0 lg:z-50 transition-all duration-300" :class="sidebarOpen ? 'lg:w-60' : 'lg:w-20'">
            @include('layouts.partials.sidebar')
        </aside>

        <div class="min-h-screen transition-all duration-300" :class="sidebarOpen ? 'lg:pl-60' : 'lg:pl-20'">
            <div class="max-w-md mx-auto min-h-screen flex flex-col relative lg:max-w-none">
                @include('layouts.partials.mobile-header')

                <main class="flex-1 rounded-t-3xl bg-[var(--bento-bg)] pb-32 lg:mt-0 lg:rounded-none lg:overflow-visible lg:pb-0" style="padding-top:100px;">
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
