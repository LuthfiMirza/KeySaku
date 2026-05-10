@php
    $assetUrl = fn (string $path): string => asset($path);

    $brand = [
        'logo' => $assetUrl('assets/brand/logo-keysaku-horizontal.svg'),
        'mark' => $assetUrl('assets/brand/logo-keysaku-mark.svg'),
        'white' => $assetUrl('assets/brand/logo-keysaku-white.svg'),
    ];

    $hero = [
        'blob' => $assetUrl('assets/landing/hero/hero-bg-blob.svg'),
        'coins' => $assetUrl('assets/landing/hero/hero-floating-coins.svg'),
        'badges' => $assetUrl('assets/landing/hero/hero-floating-badges.svg'),
    ];

    $icons = [
        'wallet' => $assetUrl('assets/icons/finance/icon-wallet.svg'),
        'coin' => $assetUrl('assets/icons/finance/icon-coin.svg'),
        'receipt' => $assetUrl('assets/icons/finance/icon-receipt.svg'),
        'piggy' => $assetUrl('assets/icons/finance/icon-piggy-bank.svg'),
        'target' => $assetUrl('assets/icons/gamification/icon-target.svg'),
        'trophy' => $assetUrl('assets/icons/gamification/icon-trophy.svg'),
        'badge' => $assetUrl('assets/icons/gamification/icon-badge.svg'),
    ];
@endphp

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KeySaku — Atur Uang Jadi Lebih Seru</title>
    <meta name="description" content="KeySaku membantu anak muda mencatat transaksi, mengejar target tabungan, dan membangun kebiasaan finansial lewat pengalaman gamified yang playful.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#fff8e7] text-[#201a2e] antialiased">
    <div class="relative min-h-screen overflow-hidden bg-[var(--ks-gradient-playful)]">
        <header class="sticky top-0 z-50 border-b border-white/60 bg-white/75 backdrop-blur-xl">
            <nav class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4" aria-label="Navigasi utama">
                <a href="#hero" class="keysaku-focus rounded-2xl" aria-label="KeySaku beranda">
                    <img src="{{ $brand['logo'] }}" alt="Logo KeySaku" class="h-10 w-auto">
                </a>
                <div class="hidden items-center gap-6 text-sm font-extrabold text-slate-600 sm:flex">
                    <a href="#fitur" class="hover:text-[#201a2e]">Fitur</a>
                    <a href="#game" class="hover:text-[#201a2e]">Gamifikasi</a>
                    <a href="#tabungan" class="hover:text-[#201a2e]">Tabungan</a>
                </div>
                <x-landing.button href="{{ route('login') }}" variant="secondary" class="hidden sm:inline-flex">Masuk</x-landing.button>
            </nav>
        </header>

        <main>
            <section id="hero" class="relative mx-auto grid max-w-6xl items-center gap-10 px-5 pb-20 pt-14 lg:grid-cols-[1.05fr_.95fr] lg:pb-28 lg:pt-20">
                <img src="{{ $hero['blob'] }}" alt="" aria-hidden="true" class="pointer-events-none absolute right-[-240px] top-0 -z-0 w-[760px] max-w-none opacity-25">
                <div class="relative z-10">
                    <span class="inline-flex rounded-full bg-white/80 px-4 py-2 text-xs font-black uppercase tracking-[0.18em] text-[#6d28d9] shadow-sm">Playful Gamified Fintech</span>
                    <h1 class="mt-6 max-w-2xl text-5xl font-black tracking-[-0.06em] text-[#201a2e] sm:text-6xl lg:text-7xl">Atur uang jadi lebih seru, ringan, dan terasa seperti game.</h1>
                    <p class="mt-5 max-w-xl text-lg leading-8 text-slate-600">KeySaku bantu kamu mencatat pengeluaran, mengejar target tabungan, dan menjaga streak finansial tanpa rasa kaku.</p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <x-landing.button href="{{ route('register') }}">Mulai Gratis</x-landing.button>
                        <x-landing.button href="#fitur" variant="secondary">Lihat Fitur</x-landing.button>
                    </div>
                    <div class="mt-8 grid max-w-xl grid-cols-3 gap-3 text-center">
                        <div class="keysaku-card p-4"><p class="text-2xl font-black">7🔥</p><p class="text-xs font-bold text-slate-500">Streak hemat</p></div>
                        <div class="keysaku-card p-4"><p class="text-2xl font-black">64%</p><p class="text-xs font-bold text-slate-500">Target nabung</p></div>
                        <div class="keysaku-card p-4"><p class="text-2xl font-black">Lv.3</p><p class="text-xs font-bold text-slate-500">Progress kamu</p></div>
                    </div>
                </div>
                <div class="relative z-10">
                    <img src="{{ $hero['coins'] }}" alt="" aria-hidden="true" class="absolute -left-10 top-4 z-20 w-36 animate-pulse sm:w-48">
                    <img src="{{ $hero['badges'] }}" alt="" aria-hidden="true" class="absolute -right-4 bottom-8 z-20 w-44 sm:w-56">
                    <x-landing.phone-mockup />
                </div>
            </section>

            <section id="fitur" class="mx-auto max-w-6xl px-5 py-16">
                <div class="max-w-2xl">
                    <p class="text-sm font-black uppercase tracking-[0.18em] text-[#8b5cf6]">Fitur MVP</p>
                    <h2 class="mt-3 text-4xl font-black tracking-[-0.05em]">Semua yang penting untuk mulai rapiin uang.</h2>
                </div>
                <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                    <x-landing.feature-card :icon="$icons['wallet']" title="Catat transaksi" description="Pengeluaran dan pemasukan harian bisa dicatat cepat dengan kategori yang jelas." tone="lime" />
                    <x-landing.feature-card :icon="$icons['receipt']" title="Riwayat rapi" description="Lihat daftar transaksi tanpa ribet supaya kebiasaan belanja makin kebaca." tone="pink" />
                    <x-landing.feature-card :icon="$icons['piggy']" title="Target tabungan" description="Pantau progres tujuan finansial dengan visual yang mudah dipahami." tone="teal" />
                    <x-landing.feature-card :icon="$icons['coin']" title="Ringkasan uang" description="Cek kondisi uang masuk dan keluar dari satu tampilan yang clean." tone="purple" />
                </div>
            </section>

            <section id="game" class="mx-auto grid max-w-6xl gap-8 px-5 py-16 lg:grid-cols-2 lg:items-center">
                <div class="keysaku-card bg-[#201a2e] p-6 text-white sm:p-8">
                    <img src="{{ $icons['trophy'] }}" alt="" aria-hidden="true" class="h-20 w-20 rounded-3xl">
                    <h2 class="mt-6 text-4xl font-black tracking-[-0.05em]">Streak, badge, dan challenge bikin konsisten.</h2>
                    <p class="mt-4 text-white/70">KeySaku mengubah kebiasaan finansial kecil menjadi progress yang terlihat, menyenangkan, dan memotivasi.</p>
                </div>
                <div class="grid gap-4">
                    <div class="keysaku-card flex items-center gap-4 p-5"><img src="{{ $icons['target'] }}" alt="" class="h-16 w-16"><div><h3 class="font-black">Challenge mingguan</h3><p class="text-sm text-slate-600">Contoh: tahan jajan impulsif selama 5 hari.</p></div></div>
                    <div class="keysaku-card flex items-center gap-4 p-5"><img src="{{ $icons['badge'] }}" alt="" class="h-16 w-16"><div><h3 class="font-black">Badge pencapaian</h3><p class="text-sm text-slate-600">Rayakan kebiasaan baik tanpa mengganggu fungsi utama.</p></div></div>
                </div>
            </section>

            <section id="tabungan" class="mx-auto max-w-6xl px-5 py-16">
                <div class="keysaku-card grid gap-8 overflow-hidden bg-white/85 p-6 sm:p-8 lg:grid-cols-[.9fr_1.1fr] lg:items-center">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.18em] text-[#0f766e]">Savings Goal</p>
                        <h2 class="mt-3 text-4xl font-black tracking-[-0.05em]">Target terasa dekat saat progresnya kelihatan.</h2>
                        <p class="mt-4 text-slate-600">Gunakan target visual untuk bantu kamu menabung pelan-pelan, dari dana darurat sampai impian kecil.</p>
                    </div>
                    <div class="rounded-[2rem] bg-[#ecfeff] p-6">
                        <div class="flex items-center justify-between"><div><p class="text-sm font-bold text-slate-500">Target laptop</p><p class="text-3xl font-black">Rp3,2 jt</p></div><span class="rounded-full bg-[#a3e635] px-4 py-2 text-sm font-black">64%</span></div>
                        <div class="mt-6 h-5 rounded-full bg-white"><div class="h-5 w-[64%] rounded-full bg-[#2dd4bf]"></div></div>
                        <p class="mt-4 text-sm font-bold text-slate-500">Tinggal Rp1,8 jt lagi. Gas pelan-pelan 🚀</p>
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-6xl px-5 py-16 pb-24">
                <div class="relative overflow-hidden rounded-[2rem] bg-[var(--ks-gradient-brand)] p-8 text-white shadow-[0_32px_90px_rgba(109,40,217,0.32)] sm:p-12">
                    <img src="{{ $brand['white'] }}" alt="Logo KeySaku" class="h-11 w-auto">
                    <h2 class="mt-8 max-w-2xl text-4xl font-black tracking-[-0.05em]">Siap bikin kebiasaan finansialmu naik level?</h2>
                    <p class="mt-4 max-w-xl text-white/75">Mulai dari catatan kecil hari ini. KeySaku bantu perjalananmu jadi lebih ringan dan menyenangkan.</p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <x-landing.button href="{{ route('register') }}" variant="light">Buat Akun</x-landing.button>
                        <x-landing.button href="{{ route('login') }}" variant="secondary" class="border-white/80 bg-white/10 text-white hover:bg-white/20">Masuk</x-landing.button>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
