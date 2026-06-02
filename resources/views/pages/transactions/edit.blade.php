<x-app-layout>
    <x-slot:title>Edit Transaksi</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-28" style="background:#0A1628 !important;">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-white">Edit Transaksi</h2>
                <p class="mt-1 text-xs text-[#64748B]">Perbarui catatan transaksi yang sudah tersimpan.</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="shrink-0 rounded-full px-3 py-2 text-xs font-bold shadow-sm" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.08) !important;color:#A3E635 !important;">
                Riwayat
            </a>
        </div>

        <livewire:transactions.edit-transaction :transaction="$transaction" />
    </div>
</x-app-layout>
