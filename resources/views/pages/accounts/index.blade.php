<x-app-layout>
    <x-slot:title>Akun Keuangan</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Akun Keuangan</h2>
                <p class="mt-1 text-xs text-slate-400">Kelola kas, rekening bank, dan dompet digital.</p>
            </div>
            <a href="{{ route('settings') }}"
               class="shrink-0 rounded-full bg-slate-100 px-3 py-2 text-xs font-bold text-slate-500">
                ← Setelan
            </a>
        </div>

        <livewire:accounts.manage-accounts />
    </div>
</x-app-layout>
