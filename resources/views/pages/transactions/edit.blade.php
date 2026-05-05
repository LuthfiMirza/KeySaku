<x-app-layout>
    <x-slot:title>Edit Transaksi</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Edit Transaksi</h2>
                <p class="mt-1 text-xs text-slate-400">Perbarui catatan transaksi yang sudah tersimpan.</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="shrink-0 rounded-full bg-white/80 px-3 py-2 text-xs font-bold text-indigo-600 shadow-sm border border-white/60">
                Riwayat
            </a>
        </div>

        <livewire:transactions.edit-transaction :transaction="$transaction" />
    </div>
</x-app-layout>
