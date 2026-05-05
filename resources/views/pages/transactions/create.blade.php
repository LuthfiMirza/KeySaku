<x-app-layout>
    <x-slot:title>Tambah Transaksi</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Tambah Transaksi</h2>
                <p class="mt-1 text-xs text-slate-400">Input manual untuk pemasukan dan pengeluaran.</p>
            </div>
            <span class="rounded-full bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-600">Manual</span>
        </div>

        <livewire:transactions.create-transaction />
    </div>
</x-app-layout>
