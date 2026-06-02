<x-app-layout>
    <x-slot:title>Target Tabungan</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-28" style="background:#0A1628 !important;">
        <section class="rounded-3xl p-5" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
            <div class="flex items-start gap-3">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-2xl" style="background:rgba(163,230,53,0.15) !important;color:#A3E635 !important;">🎯</span>
                <div>
                    <h2 class="text-base font-extrabold" style="color:#A3E635 !important;">Target Tabungan</h2>
                    <p class="mt-1 text-xs leading-relaxed" style="color:#64748B !important;">Rencanakan dana untuk tujuan penting.</p>
                </div>
            </div>
        </section>

        <livewire:savings.manage-savings-goals />
    </div>
</x-app-layout>
