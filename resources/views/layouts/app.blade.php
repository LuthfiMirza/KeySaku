<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'KeySaku' }} — Kelola Keuangan Keluarga</title>
    @include('layouts.partials.pwa-meta')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-[#FCF8F8] text-[#121316] antialiased selection:bg-[#FF8E53]/20 selection:text-[#121316]"
      x-data="{ showAddMenu: false, showVoiceModal: false }"
      x-on:keydown.escape="showAddMenu = false; showVoiceModal = false"
      @open-voice-modal.window="showVoiceModal = true; showAddMenu = false"
      @open-add-menu.window="showAddMenu = true; showVoiceModal = false">
    <div class="min-h-screen relative bg-[#FCF8F8]">
        <!-- Ambient delicate porcelain luxury glows -->
        <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-lg h-80 bg-[radial-gradient(ellipse_at_top,_rgba(255,142,83,0.08)_0%,_transparent_70%)] pointer-events-none -z-10"></div>
        <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-lg h-72 bg-[radial-gradient(ellipse_at_bottom,_rgba(255,107,107,0.05)_0%,_transparent_70%)] pointer-events-none -z-10"></div>

        <div class="max-w-md mx-auto min-h-screen flex flex-col relative bg-[#FCF8F8] shadow-[0_0_50px_rgba(18,19,22,0.03)] border-x border-[#F0EBEB]/50">
            @include('layouts.partials.mobile-header')

            <main class="flex-1 overflow-y-auto safe-bottom">
                {{ $slot }}
            </main>
        </div>
    </div>

    @include('layouts.partials.add-transaction-modal')
    @include('layouts.partials.bottom-nav')

    <script>window.appCurrency = @json(\App\Support\Money::config());</script>
    @livewireScripts
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        }
    </script>
    @stack('scripts')
</body>
</html>
