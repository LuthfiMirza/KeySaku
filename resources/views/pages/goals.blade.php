<x-app-layout>
    <x-slot:title>Target Tabungan</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60">
            <div class="flex items-start gap-3">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-2xl">🎯</span>
                <div>
                    <h2 class="text-base font-extrabold text-slate-800">Target Tabungan</h2>
                    <p class="mt-1 text-xs leading-relaxed text-slate-400">Rencanakan dana untuk tujuan penting.</p>
                </div>
            </div>
        </section>

        <livewire:savings.manage-savings-goals />
    </div>
</x-app-layout>
