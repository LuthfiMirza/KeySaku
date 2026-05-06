<x-app-layout>
    <x-slot:title>Keamanan</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Pengaturan</p>
                <h2 class="mt-1 text-base font-bold text-slate-800">Keamanan</h2>
            </div>
            <a href="{{ route('settings') }}" class="inline-flex min-h-10 items-center rounded-2xl bg-white/80 px-3 py-2 text-xs font-bold text-slate-500 shadow-sm border border-white/60">Kembali</a>
        </div>

        <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60">
            <p class="text-sm font-semibold text-slate-700">Pengaturan akun dan password dikelola dari halaman profil.</p>
            <p class="mt-2 text-xs leading-relaxed text-slate-400">Gunakan halaman profil untuk memperbarui nama, email, password, atau menghapus akun. KeySaku tidak menambahkan mekanisme keamanan baru di halaman ini.</p>

            <a href="{{ route('profile.edit') }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30">Buka Profil</a>
        </section>
    </div>
</x-app-layout>
