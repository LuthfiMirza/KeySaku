<x-app-layout>
    <x-slot:title>Kategori</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Kategori</h2>
                <p class="mt-1 text-xs text-slate-400">Siapkan kategori untuk catatan manual dan input suara.</p>
            </div>
            <a href="{{ route('settings') }}" class="rounded-full bg-white/80 px-3 py-2 text-xs font-bold text-indigo-600 shadow-sm border border-white/60">
                Setelan
            </a>
        </div>

        <livewire:category-manager />
    </div>
</x-app-layout>
