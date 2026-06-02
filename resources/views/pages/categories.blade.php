<x-app-layout>
    <x-slot:title>Kategori</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-28" style="background:#0A1628 !important;">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-white">Kategori</h2>
                <p class="mt-1 text-xs text-[#64748B]">Siapkan kategori untuk catatan manual dan input suara.</p>
            </div>
            <a href="{{ route('settings') }}" class="rounded-full px-3 py-2 text-xs font-bold shadow-sm" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.08) !important;color:#A3E635 !important;">
                Setelan
            </a>
        </div>

        <livewire:category-manager />
    </div>
</x-app-layout>
