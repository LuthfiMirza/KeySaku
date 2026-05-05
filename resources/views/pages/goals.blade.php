<x-app-layout>
    <x-slot:title>Target Tabungan</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800">Target Tabungan</h2>
            <button class="text-xs text-indigo-500 font-medium bg-indigo-50 px-3 py-1 rounded-full">+ Tambah</button>
        </div>

        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-5 shadow-sm border border-white/60 text-center py-12">
            <p class="text-4xl mb-3">🎯</p>
            <p class="text-sm font-semibold text-slate-700">Belum ada target tabungan</p>
            <p class="text-xs text-slate-400 mt-2">Mulai tentukan tujuan keuanganmu.</p>
        </div>

        <x-insight-card
            title="Tips menabung konsisten"
            description="Sisihkan minimal 10% dari penghasilan setiap bulan untuk tabungan darurat."
            icon="🌟"
            type="success"
        />
    </div>
</x-app-layout>
