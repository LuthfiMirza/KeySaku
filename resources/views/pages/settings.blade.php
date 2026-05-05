<x-app-layout>
    <x-slot:title>Pengaturan</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <h2 class="text-base font-bold text-slate-800">Pengaturan</h2>

        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-white/60 divide-y divide-slate-100">
            <a href="{{ route('profile.edit') }}" class="flex items-center justify-between px-4 py-3.5">
                <div class="flex items-center gap-3">
                    <span class="text-lg">👤</span>
                    <span class="text-sm font-medium text-slate-700">Profil Pengguna</span>
                </div>
                <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
            </a>

            <a href="{{ route('categories') }}" class="flex items-center justify-between px-4 py-3.5">
                <div class="flex items-center gap-3">
                    <span class="text-lg">🏷️</span>
                    <span class="text-sm font-medium text-slate-700">Kategori Transaksi</span>
                </div>
                <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
            </a>

            @foreach([
                ['icon' => '🔔', 'label' => 'Notifikasi'],
                ['icon' => '💱', 'label' => 'Mata Uang'],
                ['icon' => '🌐', 'label' => 'Bahasa'],
                ['icon' => '🔒', 'label' => 'Keamanan'],
                ['icon' => '📱', 'label' => 'Pasang di Layar Utama'],
            ] as $item)
                <div class="flex items-center justify-between px-4 py-3.5 opacity-60">
                    <div class="flex items-center gap-3">
                        <span class="text-lg">{{ $item['icon'] }}</span>
                        <span class="text-sm font-medium text-slate-700">{{ $item['label'] }}</span>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wide text-slate-300">Nanti</span>
                </div>
            @endforeach
        </div>

        <p class="text-center text-xs text-slate-400">KeySaku v0.1.0 — Kelola keuangan dengan lebih bijak</p>
    </div>
</x-app-layout>
