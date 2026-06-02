<x-app-layout>
    <x-slot:title>Keamanan</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-28 lg:px-8 lg:pt-8 lg:pb-8 lg:max-w-5xl lg:mx-auto" style="background:#0A1628 !important;">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-[#A3E635]">Pengaturan</p>
                <h2 class="mt-1 text-base font-bold text-white">Keamanan</h2>
            </div>
            <a href="{{ route('settings') }}" class="inline-flex min-h-10 items-center rounded-2xl px-3 py-2 text-xs font-bold shadow-sm" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.08) !important;color:#A3E635 !important;">Kembali</a>
        </div>

        <section class="rounded-3xl p-5" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
            <p class="text-sm font-semibold text-white">Pengaturan akun dan password dikelola dari halaman profil.</p>
            <p class="mt-2 text-xs leading-relaxed text-[#64748B]">Gunakan halaman profil untuk memperbarui nama, email, password, atau menghapus akun. KeySaku tidak menambahkan mekanisme keamanan baru di halaman ini.</p>

            <a href="{{ route('profile.edit') }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-2xl px-4 py-3 text-sm font-bold shadow-lg shadow-lime-500/20" style="background:#A3E635 !important;color:#0A1628 !important;">Buka Profil</a>
        </section>
    </div>
</x-app-layout>
