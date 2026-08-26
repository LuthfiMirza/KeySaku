<x-app-layout>
    <x-slot:title>Riwayat Transaksi</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Riwayat Transaksi</h2>
                <p class="mt-1 text-xs text-slate-400">Pantau pemasukan dan pengeluaranmu.</p>
            </div>
            <a href="{{ route('transactions.create') }}" class="shrink-0 rounded-full bg-indigo-500 px-3 py-2 text-xs font-bold text-white shadow-lg shadow-indigo-500/30">
                + Tambah
            </a>
        </div>

        <div class="flex gap-2 text-xs font-bold">
            <a href="{{ route('transactions.export.csv') }}" class="flex-1 rounded-2xl bg-white/80 px-3 py-2.5 text-center text-slate-600 shadow-sm border border-white/60">Unduh CSV</a>
            <a href="{{ route('transactions.export.xlsx') }}" class="flex-1 rounded-2xl bg-white/80 px-3 py-2.5 text-center text-slate-600 shadow-sm border border-white/60">Unduh Excel</a>
        </div>

        <livewire:transactions.transaction-list />
    </div>
</x-app-layout>
