<x-app-layout>
    <x-slot:title>Dompet & Kartu</x-slot:title>

    <div class="px-4 pt-3 space-y-4 pb-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
                <span>Beranda</span>
            </a>

            <h2 class="text-sm font-extrabold text-slate-800 tracking-tight">
                Dompet Saya
            </h2>

            <a href="{{ route('settings') }}"
               class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors"
               title="Setelan Akun">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
            </a>
        </div>

        <livewire:accounts.manage-accounts />
    </div>
</x-app-layout>
