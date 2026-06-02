<x-app-layout>
    <x-slot:title>Riwayat Transaksi</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-28 lg:px-8 lg:pt-8 lg:pb-8 lg:max-w-5xl lg:mx-auto" style="background:#0A1628 !important;">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-white">Riwayat Transaksi</h2>
                <p class="mt-1 text-xs text-[#64748B]">Pantau pemasukan dan pengeluaranmu.</p>
            </div>
            <a href="{{ route('transactions.create') }}" class="shrink-0 rounded-full px-3 py-2 text-xs font-bold shadow-lg shadow-lime-500/20" style="background:#A3E635 !important;color:#0A1628 !important;">
                + Tambah
            </a>
        </div>

        <livewire:transactions.transaction-list />
    </div>
</x-app-layout>
