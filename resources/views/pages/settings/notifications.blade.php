<x-app-layout>
    <x-slot:title>Notifikasi</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4 lg:px-8 lg:pt-8 lg:pb-8 lg:max-w-5xl lg:mx-auto">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Pengaturan</p>
                <h2 class="mt-1 text-base font-bold text-slate-800">Notifikasi</h2>
            </div>
            <a href="{{ route('settings') }}" class="inline-flex min-h-10 items-center rounded-2xl bg-white/80 px-3 py-2 text-xs font-bold text-slate-500 shadow-sm border border-white/60">Kembali</a>
        </div>

        <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60">
            <p class="text-sm font-semibold text-slate-700">Pengingat belum aktif di versi ini.</p>
            <p class="mt-2 text-xs leading-relaxed text-slate-400">Pengingat anggaran dan target tabungan masih disiapkan sebagai pengaturan tampilan saja. KeySaku belum mengirim email, SMS, atau push notification.</p>

            <div class="mt-5 space-y-3">
                @foreach ([
                    ['title' => 'Pengingat Anggaran', 'description' => 'Peringatan saat pengeluaran mendekati batas anggaran.'],
                    ['title' => 'Pengingat Target Tabungan', 'description' => 'Pengingat untuk menambah dana ke target tabungan.'],
                ] as $item)
                    <div class="flex items-center justify-between gap-4 rounded-2xl bg-slate-50 px-4 py-3 opacity-70">
                        <div>
                            <p class="text-sm font-bold text-slate-700">{{ $item['title'] }}</p>
                            <p class="mt-1 text-xs leading-relaxed text-slate-400">{{ $item['description'] }}</p>
                        </div>
                        <button type="button" disabled class="relative h-7 w-12 shrink-0 rounded-full bg-slate-200" aria-label="{{ $item['title'] }} belum tersedia">
                            <span class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow-sm"></span>
                        </button>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
