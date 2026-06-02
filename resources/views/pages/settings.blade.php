<x-app-layout>
    <x-slot:title>Pengaturan</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-28 lg:px-8 lg:pt-8 lg:pb-8 lg:max-w-5xl lg:mx-auto" style="background:#0A1628 !important;">
        <div>
            <h2 class="text-base font-bold text-white">Pengaturan</h2>
            <p class="mt-1 text-xs leading-relaxed text-[#64748B]">Kelola akun, kategori, dan preferensi aplikasi yang tersedia.</p>
        </div>

        <div>
            @foreach([
                ['icon' => 'user', 'label' => 'Profil Pengguna', 'href' => route('profile.edit')],
                ['icon' => 'tag', 'label' => 'Kategori Transaksi', 'href' => route('categories')],
                ['icon' => 'bell', 'label' => 'Notifikasi', 'href' => route('settings.notifications')],
                ['icon' => 'coins', 'label' => 'Mata Uang', 'href' => route('settings.currency')],
                ['icon' => 'globe', 'label' => 'Bahasa', 'href' => route('settings.language')],
                ['icon' => 'lock', 'label' => 'Keamanan', 'href' => route('settings.security')],
                ['icon' => 'smartphone', 'label' => 'Pasang di Layar Utama', 'href' => route('settings.install')],
            ] as $item)
                <a href="{{ $item['href'] }}" style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:#132044;border-radius:14px;border:1px solid rgba(255,255,255,0.08);margin-bottom:8px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:38px;height:38px;background:#1A2B52;border-radius:10px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(163,230,53,0.15);">
                            <i data-lucide="{{ $item['icon'] }}" style="width:18px;height:18px;color:#A3E635;"></i>
                        </div>
                        <span style="font-size:14px;color:#FFFFFF;">{{ $item['label'] }}</span>
                    </div>
                    <i data-lucide="chevron-right" style="width:16px;height:16px;color:#64748B;"></i>
                </a>
            @endforeach
        </div>

        <p class="text-center text-xs text-[#64748B]">KeySaku v0.1.0 — Kelola keuangan dengan lebih bijak</p>
    </div>
</x-app-layout>
