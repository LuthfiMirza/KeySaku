<x-app-layout>
    <x-slot:title>Pengaturan</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div>
            <h2 class="text-base font-bold text-slate-800">Pengaturan</h2>
            <p class="mt-1 text-xs leading-relaxed text-slate-400">Kelola akun, kategori, dan preferensi aplikasi yang tersedia.</p>
        </div>

        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-white/60 divide-y divide-slate-100">
            @foreach([
                ['icon' => '👤', 'label' => 'Profil Pengguna', 'href' => route('profile.edit')],
                ['icon' => '🏷️', 'label' => 'Kategori Transaksi', 'href' => route('categories')],
                ['icon' => '🔔', 'label' => 'Notifikasi', 'href' => route('settings.notifications')],
                ['icon' => '💱', 'label' => 'Mata Uang', 'href' => route('settings.currency')],
                ['icon' => '🌐', 'label' => 'Bahasa', 'href' => route('settings.language')],
                ['icon' => '🔒', 'label' => 'Keamanan', 'href' => route('settings.security')],
                ['icon' => '📱', 'label' => 'Pasang di Layar Utama', 'href' => route('settings.install')],
            ] as $item)
                <a href="{{ $item['href'] }}" class="flex min-h-14 items-center justify-between px-4 py-3.5">
                    <div class="flex items-center gap-3">
                        <span class="text-lg">{{ $item['icon'] }}</span>
                        <span class="text-sm font-medium text-slate-700">{{ $item['label'] }}</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </a>
            @endforeach
        </div>

        <p class="text-center text-xs text-slate-400">KeySaku v0.1.0 — Kelola keuangan dengan lebih bijak</p>
    </div>
</x-app-layout>
