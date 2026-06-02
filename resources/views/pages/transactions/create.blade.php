<x-app-layout>
    <x-slot:title>Tambah Transaksi</x-slot:title>

    <div class="space-y-5 pb-28 lg:px-8 lg:pt-8 lg:pb-8 lg:max-w-5xl lg:mx-auto" style="background:#0A1628 !important;">
        <livewire:transactions.voice-transaction />

        <section id="input-manual" class="px-4 pb-4 lg:px-0">
            <div class="mb-3 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-white">Input Manual</h2>
                    <p class="mt-1 text-xs text-[#64748B]">Gunakan opsi ini jika suara belum terbaca akurat.</p>
                </div>
                <span class="rounded-full border px-3 py-2 text-xs font-bold" style="background:rgba(163,230,53,0.15) !important;border-color:rgba(163,230,53,0.2) !important;color:#A3E635 !important;">Fallback</span>
            </div>

            <livewire:transactions.create-transaction />
        </section>
    </div>
</x-app-layout>
