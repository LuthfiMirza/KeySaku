<x-app-layout>
    <x-slot:title>Integrasi Telegram</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Pengaturan</p>
                <h2 class="mt-1 text-base font-bold text-slate-800">Integrasi Telegram Bot</h2>
            </div>
            <a href="{{ route('settings') }}" class="inline-flex min-h-10 items-center rounded-2xl bg-white/80 px-3 py-2 text-xs font-bold text-slate-500 shadow-sm border border-white/60">Kembali</a>
        </div>

        @if (session('settings_status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700 shadow-sm">
                {{ session('settings_status') }}
            </div>
        @endif

        <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-sky-100 flex items-center justify-center text-xl text-sky-600">
                        ✈️
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Bot @<span class="text-sky-600">{{ $botUsername }}</span></h3>
                        <p class="text-xs text-slate-400">Catat transaksi secepat kirim pesan</p>
                    </div>
                </div>

                @if ($user->isTelegramLinked())
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Terhubung
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Belum Terhubung
                    </span>
                @endif
            </div>

            @if ($user->isTelegramLinked())
                <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100 text-xs text-slate-600 space-y-2">
                    <p class="font-medium text-slate-700">Akun Anda sudah terhubung!</p>
                    <p class="text-slate-500">Chat ID Telegram Anda: <code class="rounded bg-slate-200 px-1 py-0.5 font-mono text-slate-800">{{ $user->telegram_chat_id }}</code></p>
                    <p class="text-slate-500">Silakan buka Telegram Anda dan coba kirim pesan seperti: <code class="font-bold text-indigo-600">Makan siang 35.000</code> atau <code class="font-bold text-indigo-600">/saldo</code>.</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="https://t.me/{{ $botUsername }}" target="_blank" rel="noopener" class="flex-1 inline-flex min-h-12 items-center justify-center rounded-2xl bg-sky-500 px-4 py-3 text-sm font-bold text-white shadow-md shadow-sky-500/20 hover:bg-sky-600 transition">
                        Buka @<span class="ml-0.5">{{ $botUsername }}</span>
                    </a>

                    <form method="POST" action="{{ route('settings.telegram.unlink') }}" onsubmit="return confirm('Yakin ingin memutuskan koneksi bot Telegram?');">
                        @csrf
                        <button type="submit" class="min-h-12 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600 hover:bg-rose-100 transition">
                            Putus Koneksi
                        </button>
                    </form>
                </div>
            @else
                <div class="space-y-4">
                    <p class="text-xs leading-relaxed text-slate-500">
                        Hubungkan akun Telegram Anda untuk mencatat pengeluaran atau pemasukan kapan saja.
                    </p>

                    <a href="{{ $linkUrl }}" target="_blank" rel="noopener" class="min-h-12 w-full flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-sky-500 to-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-sky-500/30 hover:opacity-95 transition">
                        <span>Hubungkan ke Telegram</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>

                    <div class="pt-2 border-t border-slate-100">
                        <p class="text-xs font-semibold text-slate-600 mb-1.5">Atau hubungkan secara manual:</p>
                        <p class="text-xs text-slate-400 mb-2">Buka bot <a href="https://t.me/{{ $botUsername }}" target="_blank" class="text-sky-600 font-medium">@<span class="underline">{{ $botUsername }}</span></a> di Telegram dan kirimkan teks berikut:</p>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly value="/start {{ $token }}" class="w-full text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 select-all focus:outline-none">
                        </div>
                    </div>
                </div>
            @endif
        </section>

        <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60 space-y-3">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <span>💡</span> Panduan Chat Bot
            </h3>

            <div class="space-y-2.5 text-xs text-slate-600">
                <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                    <p class="font-bold text-slate-700">Contoh Pengeluaran:</p>
                    <p class="text-slate-500 mt-1">• <code>makan siang 35000</code></p>
                    <p class="text-slate-500">• <code>kopi susu 25rb kategori hiburan</code></p>
                    <p class="text-slate-500">• <code>bensin 50.000 kemarin</code></p>
                </div>

                <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                    <p class="font-bold text-slate-700">Contoh Pemasukan:</p>
                    <p class="text-slate-500 mt-1">• <code>gaji bulanan 8.000.000</code></p>
                    <p class="text-slate-500">• <code>terima transfer bonus 500rb</code></p>
                </div>

                <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                    <p class="font-bold text-slate-700">Perintah Praktis:</p>
                    <p class="text-slate-500 mt-1">• <code>/saldo</code> — Menampilkan sisa saldo semua akun</p>
                    <p class="text-slate-500">• <code>/batal</code> — Menghapus transaksi terakhir jika salah ketik</p>
                    <p class="text-slate-500">• <code>/bantuan</code> — Menampilkan panduan format</p>
                </div>

                <p class="text-slate-400 text-[11px] leading-relaxed pt-1">
                    ⚡ <strong>Auto-Fetching Offline:</strong> Anda tetap bisa mengirim chat ke bot meski laptop/server lokal sedang nonaktif. Saat server kembali berjalan, seluruh pesan otomatis terproses!
                </p>
            </div>
        </section>
    </div>
</x-app-layout>
