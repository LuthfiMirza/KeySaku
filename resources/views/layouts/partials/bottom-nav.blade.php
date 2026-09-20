<nav class="fixed bottom-0 left-0 right-0 z-40 max-w-md mx-auto">
    <div class="mx-4 mb-4 bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl shadow-black/10 border border-white/50 px-2 py-2">
        <div class="flex items-center justify-around">
            {{-- Beranda --}}
            <a href="{{ route('dashboard') }}"
               class="flex flex-col items-center gap-1 px-4 py-2 rounded-2xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/40' : 'text-slate-400 hover:text-indigo-500' }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                <span class="text-[10px] font-semibold">Beranda</span>
            </a>

            {{-- Anggaran --}}
            <a href="{{ route('budgets.index') }}"
               class="flex flex-col items-center gap-1 px-4 py-2 rounded-2xl transition-all duration-200 {{ request()->routeIs('budgets.index') ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/40' : 'text-slate-400 hover:text-indigo-500' }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                </svg>
                <span class="text-[10px] font-semibold">Anggaran</span>
            </a>

            {{-- Tambah (FAB) --}}
            <div class="flex flex-col items-center -mt-6">
                <a href="#" x-on:click.prevent="showAddMenu = true" aria-label="Tambah transaksi" class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 shadow-xl shadow-indigo-500/40 flex items-center justify-center text-white border-4 border-white transition-transform active:scale-95">
                    <svg class="w-7 h-7 transition-transform duration-300" x-bind:class="showAddMenu ? 'rotate-45' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </a>
                <span class="text-[10px] font-semibold text-indigo-500 mt-1">Tambah</span>
            </div>

            {{-- Tabungan --}}
            <a href="{{ route('savings.index') }}"
               class="flex flex-col items-center gap-1 px-4 py-2 rounded-2xl transition-all duration-200 {{ request()->routeIs('savings.index') ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/40' : 'text-slate-400 hover:text-indigo-500' }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-[10px] font-semibold">Tabungan</span>
            </a>

            {{-- Pengaturan --}}
            <a href="{{ route('settings') }}"
               class="flex flex-col items-center gap-1 px-4 py-2 rounded-2xl transition-all duration-200 {{ request()->routeIs('settings') ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/40' : 'text-slate-400 hover:text-indigo-500' }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                </svg>
                <span class="text-[10px] font-semibold">Setelan</span>
            </a>
        </div>
    </div>
</nav>
