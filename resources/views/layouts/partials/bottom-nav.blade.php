<nav class="fixed bottom-5 left-1/2 -translate-x-1/2 z-50 w-[calc(100%-40px)] max-w-[360px]" aria-label="Navigasi Utama">
    <div class="bg-white/85 backdrop-blur-xl rounded-full p-2 flex items-center justify-between border border-white/60 shadow-[0_12px_32px_rgba(0,0,0,0.08)]">
        {{-- 1. Home (Dashboard / Beranda) --}}
        @php
            $isHome = request()->routeIs('dashboard');
        @endphp
        <a href="{{ route('dashboard') }}"
           class="flex items-center justify-center transition-all duration-300 {{ $isHome ? 'bg-[#121316] text-white rounded-full p-3 shadow-md' : 'text-[#8E8E93] hover:text-[#121316] p-3 hover:bg-slate-100/50 rounded-full' }}"
           aria-label="Beranda"
           title="Beranda">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            <span class="sr-only">Beranda</span>
        </a>

        {{-- 2. Analytics (Tren & Grafik Keuangan) --}}
        @php
            $isAnalytics = request()->routeIs('reports.*');
        @endphp
        <a href="{{ route('reports.index') }}"
           class="flex items-center justify-center transition-all duration-300 {{ $isAnalytics ? 'bg-[#121316] text-white rounded-full p-3 shadow-md' : 'text-[#8E8E93] hover:text-[#121316] p-3 hover:bg-slate-100/50 rounded-full' }}"
           aria-label="Analitik Keuangan"
           title="Analitik Keuangan">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
            </svg>
            <span class="sr-only">Analitik</span>
        </a>

        {{-- 3. Budget (Dompet / Target Anggaran) --}}
        @php
            $isBudget = request()->routeIs('budgets.*') || request()->routeIs('savings.*');
        @endphp
        <a href="{{ route('budgets.index') }}"
           class="flex items-center justify-center transition-all duration-300 {{ $isBudget ? 'bg-[#121316] text-white rounded-full p-3 shadow-md' : 'text-[#8E8E93] hover:text-[#121316] p-3 hover:bg-slate-100/50 rounded-full' }}"
           aria-label="Anggaran & Tabungan"
           title="Anggaran & Tabungan">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3" />
            </svg>
            <span class="sr-only">Anggaran</span>
        </a>

        {{-- 4. Voice / Add (Perekam Transaksi Suara AI & Catat Transaksi) --}}
        <button type="button"
                x-on:click="showVoiceModal = true"
                class="flex items-center justify-center text-[#8E8E93] hover:text-white p-3 hover:bg-[#121316] rounded-full transition-all duration-300 relative group active:scale-95"
                aria-label="Rekam Suara AI / Catat Transaksi"
                title="Perekam Suara AI">
            <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FF8E53] opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#FF6B6B]"></span>
            </span>
            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-12 0v1.5a6 6 0 0 0 6 6ZM12 18.75v3.75m-3.75 0h7.5M12 15a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3Z" />
            </svg>
            <span class="sr-only">Input Suara AI</span>
        </button>
    </div>
</nav>

