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
<body class="h-full bg-slate-100 antialiased">
    <div class="min-h-screen relative overflow-hidden">
        <div class="fixed inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 -z-10"></div>
        <div class="fixed inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(255,255,255,0.18)_0%,_transparent_60%)] -z-10"></div>

        <main class="max-w-md mx-auto min-h-screen flex flex-col justify-center px-5 py-10">
            <div class="text-center mb-8">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-white/20 backdrop-blur-xl border border-white/30 text-white text-3xl shadow-xl shadow-black/10">
                    💰
                </a>
                <h1 class="mt-4 text-3xl font-bold text-white">KeySaku</h1>
                <p class="mt-2 text-sm font-medium text-white/75">Catat pengeluaran secepat bicara.</p>
            </div>

            <section class="bg-white/85 backdrop-blur-xl rounded-3xl p-5 shadow-2xl shadow-black/10 border border-white/60">
                {{ $slot }}
            </section>
        </main>
    </div>
</body>
</html>
