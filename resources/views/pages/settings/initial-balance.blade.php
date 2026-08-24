<x-app-layout>
    <x-slot:title>Saldo Awal</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Pengaturan</p>
                <h2 class="mt-1 text-base font-bold text-slate-800">Saldo Awal</h2>
            </div>
            <a href="{{ route('settings') }}" class="inline-flex min-h-10 items-center rounded-2xl bg-white/80 px-3 py-2 text-xs font-bold text-slate-500 shadow-sm border border-white/60">Kembali</a>
        </div>

        @if (session('settings_status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700 shadow-sm">
                {{ session('settings_status') }}
            </div>
        @endif

        <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60">
            <p class="text-sm font-semibold text-slate-700">Uang yang sudah kamu punya sebelum mulai mencatat di KeySaku.</p>
            <p class="mt-2 text-xs leading-relaxed text-slate-400">Nilai ini ditambahkan ke saldo bersih di beranda, di luar transaksi pemasukan dan pengeluaran yang kamu catat.</p>

            <form method="POST" action="{{ route('settings.initial-balance.update') }}" class="mt-5 space-y-3">
                @csrf
                @method('PATCH')

                <div>
                    <label for="initial_balance" class="block text-sm font-semibold text-slate-700">Saldo Awal</label>
                    <div class="mt-2 flex rounded-2xl border border-slate-200 bg-white/80 shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">
                        <span class="flex items-center px-4 text-sm font-bold text-slate-400">Rp</span>
                        <input id="initial_balance" class="block w-full rounded-r-2xl border-0 bg-transparent px-2 py-3 text-sm focus:ring-0" type="text" inputmode="numeric" name="initial_balance" value="{{ old('initial_balance', number_format((float) auth()->user()->initial_balance, 0, ',', '.')) }}" placeholder="0">
                    </div>
                    @error('initial_balance')
                        <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="min-h-12 w-full rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30">Simpan Saldo Awal</button>
            </form>
        </section>
    </div>
</x-app-layout>
