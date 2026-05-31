<x-app-layout>
    <x-slot:title>Anggaran Bulanan</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-28" style="background:#0A1628 !important;">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-[#A3E635]" style="color:#A3E635 !important;">Anggaran Bulanan</h2>
                <p class="mt-1 text-xs text-[#64748B]" style="color:#64748B !important;">Atur batas pengeluaran per kategori.</p>
            </div>
            <span class="shrink-0 rounded-full px-3 py-2 text-xs font-bold" style="background:rgba(163,230,53,0.15) !important;color:#A3E635 !important;border-radius:999px !important;padding:3px 10px !important;">Beta</span>
        </div>

        <livewire:budgets.manage-budgets />
    </div>
</x-app-layout>
