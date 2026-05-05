<x-app-layout>
    <x-slot:title>Input Suara</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Input Suara</h2>
                <p class="mt-1 text-xs text-slate-400">Catat pengeluaran secepat bicara.</p>
            </div>
            <span class="rounded-full bg-purple-50 px-3 py-2 text-xs font-bold text-purple-600">Prototype</span>
        </div>

        <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60 text-center overflow-hidden relative">
            <div class="absolute inset-x-0 -top-20 mx-auto h-40 w-40 rounded-full bg-purple-200/40 blur-3xl"></div>

            <div class="relative">
                <button type="button" disabled class="mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-2xl shadow-indigo-500/30 ring-8 ring-indigo-100/80 disabled:cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18.75a6 6 0 006-6v-1.5m-12 0v1.5a6 6 0 006 6zm0 0v3m-3 0h6M12 15a3 3 0 003-3V6a3 3 0 10-6 0v6a3 3 0 003 3z" />
                    </svg>
                </button>

                <p class="mt-5 text-sm font-bold text-slate-800">Mikrofon belum aktif</p>
                <p class="mt-1 text-xs text-slate-400">Fitur rekam suara akan diaktifkan pada fase berikutnya tanpa mengubah data yang sudah ada.</p>

                <button type="button" disabled class="mt-5 w-full rounded-2xl bg-slate-200 px-4 py-3 text-sm font-bold text-slate-500 disabled:cursor-not-allowed">
                    Mulai Bicara
                </button>
            </div>
        </section>

        <section class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60">
            <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Contoh ucapan</p>
            <p class="mt-2 text-sm font-semibold leading-relaxed text-slate-700">“Beli nasi goreng dua puluh lima ribu kategori makanan.”</p>
            <p class="mt-2 text-xs text-slate-400">Nanti KeySaku akan menampilkan hasil suara untuk dicek sebelum disimpan.</p>
        </section>

        <section class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-xl">📝</span>
                <div>
                    <p class="text-sm font-bold text-slate-800">Preview Transkrip</p>
                    <p class="mt-1 text-xs text-slate-400">Hasil suara akan muncul di sini sebelum disimpan.</p>
                </div>
            </div>
        </section>

        <section class="bg-indigo-50/80 rounded-2xl p-4 border border-indigo-100">
            <p class="text-sm font-bold text-indigo-800">Belum bisa pakai suara?</p>
            <p class="mt-1 text-xs text-indigo-600">Input manual tetap tersedia sebagai fallback utama.</p>
            <a href="{{ route('transactions.create') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-2xl bg-indigo-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30">
                Input Manual
            </a>
        </section>
    </div>
</x-app-layout>
