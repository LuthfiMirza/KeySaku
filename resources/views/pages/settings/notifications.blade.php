<x-app-layout>
    <x-slot:title>Notifikasi</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-28 lg:px-8 lg:pt-8 lg:pb-8 lg:max-w-5xl lg:mx-auto" style="background:#0A1628 !important;">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-[#A3E635]">Pengaturan</p>
                <h2 class="mt-1 text-base font-bold text-white">Notifikasi</h2>
            </div>
            <a href="{{ route('settings') }}" class="inline-flex min-h-10 items-center rounded-2xl px-3 py-2 text-xs font-bold shadow-sm" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.08) !important;color:#A3E635 !important;">Kembali</a>
        </div>

        <section class="rounded-3xl p-5" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
            <p class="text-sm font-semibold text-white">Pengingat belum aktif di versi ini.</p>
            <p class="mt-2 text-xs leading-relaxed text-[#64748B]">Pengingat anggaran dan target tabungan masih disiapkan sebagai pengaturan tampilan saja. KeySaku belum mengirim email, SMS, atau push notification.</p>

            <div class="mt-5 space-y-3">
                @foreach ([
                    ['title' => 'Pengingat Anggaran', 'description' => 'Peringatan saat pengeluaran mendekati batas anggaran.'],
                    ['title' => 'Pengingat Target Tabungan', 'description' => 'Pengingat untuk menambah dana ke target tabungan.'],
                ] as $item)
                    <div class="flex items-center justify-between gap-4 rounded-2xl px-4 py-3 opacity-70" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:14px !important;">
                        <div>
                            <p class="text-sm font-bold text-white">{{ $item['title'] }}</p>
                            <p class="mt-1 text-xs leading-relaxed text-[#64748B]">{{ $item['description'] }}</p>
                        </div>
                        <button type="button" disabled class="relative h-7 w-12 shrink-0 rounded-full bg-[#0A1628]" aria-label="{{ $item['title'] }} belum tersedia">
                            <span class="absolute left-1 top-1 h-5 w-5 rounded-full bg-[#64748B] shadow-sm"></span>
                        </button>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
