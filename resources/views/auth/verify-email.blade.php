<x-guest-layout>
    <x-slot:title>Verifikasi Email</x-slot:title>

    <div class="mb-5">
        <h2 class="text-xl font-bold text-slate-900">Verifikasi email</h2>
        <p class="mt-1 text-sm text-[#8890b0]">Terima kasih sudah daftar. Cek email kamu dan klik tautan verifikasi untuk melanjutkan.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-2xl bg-[#1d9e75]/10 p-3 text-sm font-medium text-emerald-700">
            Tautan verifikasi baru sudah dikirim ke email kamu.
        </div>
    @endif

    <div class="space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full rounded-2xl bg-[#ff3d8b] px-4 py-3 text-sm font-bold text-[#0f0f5e] shadow-lg shadow-pink-500/20">
                Kirim ulang email verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full rounded-2xl bg-[#f0f2f8] px-4 py-3 text-sm font-bold text-[#0f0f5e]">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
