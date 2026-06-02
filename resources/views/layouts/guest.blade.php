<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'KeySaku' }} — Catat pengeluaran secepat bicara</title>
    @include('layouts.partials.pwa-meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#f0f2f8] antialiased">
    <div class="min-h-screen relative overflow-hidden">
        <div class="fixed inset-0 bg-[#f0f2f8] -z-10"></div>
        <div class="fixed inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(255,255,255,0.18)_0%,_transparent_60%)] -z-10"></div>

        <main class="max-w-md mx-auto min-h-screen flex flex-col justify-center px-5 py-10">
            <div class="text-center mb-8">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-white border border-[#dde3f5] text-[#0f0f5e] text-3xl shadow-xl shadow-black/10">
                    💰
                </a>
                <h1 class="mt-4 text-3xl font-bold text-[#0f0f5e]">KeySaku</h1>
                <p class="mt-2 text-sm font-medium text-[#0f0f5e]/75">Catat pengeluaran secepat bicara.</p>
            </div>

            <section class="bg-white5 rounded-3xl p-5 shadow-2xl shadow-black/10 border border-[#dde3f5]">
                {{ $slot }}
            </section>
        </main>
    </div>
</body>
</html>
