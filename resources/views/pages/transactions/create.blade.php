<x-app-layout>
    <x-slot:title>Tambah Transaksi</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4 lg:px-8 lg:pt-8 lg:pb-8 lg:max-w-5xl lg:mx-auto">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-white">Tambah Transaksi</h2>
                <p class="mt-1 text-xs text-white/40">Input manual untuk pemasukan dan pengeluaran.</p>
            </div>
            <span class="rounded-full bg-indigo-500/30 border border-indigo-400/30 px-3 py-2 text-xs font-bold text-indigo-300">Manual</span>
        </div>

        <livewire:transactions.create-transaction />
    </div>
</x-app-layout>
