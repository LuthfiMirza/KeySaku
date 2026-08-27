<x-app-layout>
    <x-slot:title>Zona Waktu</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Pengaturan</p>
                <h2 class="mt-1 text-base font-bold text-slate-800">Zona Waktu</h2>
            </div>
            <a href="{{ route('settings') }}" class="inline-flex min-h-10 items-center rounded-2xl bg-white/80 px-3 py-2 text-xs font-bold text-slate-500 shadow-sm border border-white/60">Kembali</a>
        </div>

        @if (session('settings_status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700 shadow-sm">
                {{ session('settings_status') }}
            </div>
        @endif

        <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60">
            <p class="text-sm font-semibold text-slate-700">Pilih zona waktu tempatmu berada.</p>
            <p class="mt-2 text-xs leading-relaxed text-slate-400">Menentukan "hari ini" untuk transaksi dan laporan bulananmu — bukan berdasarkan server, tapi jam di tempatmu.</p>

            <form method="POST" action="{{ route('settings.timezone.update') }}" class="mt-5 space-y-3">
                @csrf
                @method('PATCH')

                @foreach ([
                    'Asia/Jakarta' => 'WIB — Jakarta, Sumatra, Jawa, Kalimantan Barat/Tengah',
                    'Asia/Makassar' => 'WITA — Bali, NTB, NTT, Kalimantan Selatan/Timur/Utara, Sulawesi',
                    'Asia/Jayapura' => 'WIT — Maluku, Papua',
                ] as $value => $label)
                    <label class="flex min-h-14 items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                        <input type="radio" name="timezone" value="{{ $value }}" @checked(old('timezone', auth()->user()->timezone) === $value) class="border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span>
                            <span class="block text-sm font-bold text-slate-800">{{ $value }}</span>
                            <span class="block text-xs text-slate-400">{{ $label }}</span>
                        </span>
                    </label>
                @endforeach

                @error('timezone')
                    <p class="text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror

                <button type="submit" class="min-h-12 w-full rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30">Simpan Zona Waktu</button>
            </form>
        </section>
    </div>
</x-app-layout>
