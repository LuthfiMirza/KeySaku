<x-app-layout>
    <x-slot:title>Anggaran Bulanan</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Anggaran Bulanan</h2>
                <p class="mt-1 text-xs text-slate-400">Atur batas pengeluaran per kategori.</p>
            </div>
            <span class="shrink-0 rounded-full bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-500">Beta</span>
        </div>

        <livewire:budgets.manage-budgets />
    </div>
</x-app-layout>
