<x-app-layout>
    <x-slot:title>Anggaran</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800">Anggaran Bulan Ini</h2>
            <span class="text-xs text-indigo-500 font-medium bg-indigo-50 px-3 py-1 rounded-full">Mei 2026</span>
        </div>

        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-5 shadow-sm border border-white/60 text-center py-12">
            <p class="text-4xl mb-3">📊</p>
            <p class="text-sm font-semibold text-slate-700">Anggaran Segera Hadir</p>
            <p class="text-xs text-slate-400 mt-2">Fitur anggaran bulanan sedang dalam pengembangan.</p>
        </div>

        <x-insight-card
            title="Kelola anggaran lebih mudah"
            description="Kamu bisa atur batas pengeluaran per kategori setiap bulan."
            icon="💰"
            type="info"
        />
    </div>
</x-app-layout>
