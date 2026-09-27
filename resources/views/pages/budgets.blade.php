<x-app-layout>
    <x-slot:title>Anggaran & Tabungan</x-slot:title>

    <div class="px-5 pt-3 space-y-5 pb-6">
        {{-- Top Navigation Bar --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-[#121316] tracking-tight">Anggaran</h1>
                <p class="text-xs text-[#8E8E93] mt-0.5">Atur batas pengeluaran per kategori.</p>
            </div>
            <a href="#buat-anggaran"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#121316] text-white text-xs font-bold shadow-md hover:bg-black active:scale-95 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>+ Buat Baru</span>
            </a>
        </div>

        {{-- Tab Selector Pill --}}
        <div class="flex p-1 rounded-full bg-white border border-[#F0EBEB] shadow-[0_2px_12px_rgba(0,0,0,0.03)]">
            <a href="{{ route('budgets.index') }}"
               class="flex-1 py-2 text-center text-xs font-bold rounded-full bg-[#121316] text-white shadow-sm transition">
                Anggaran Bulanan
            </a>
            <a href="{{ route('savings.index') }}"
               class="flex-1 py-2 text-center text-xs font-bold rounded-full text-[#8E8E93] hover:text-[#121316] transition">
                Target Tabungan
            </a>
        </div>

        <livewire:budgets.manage-budgets />
    </div>
</x-app-layout>

