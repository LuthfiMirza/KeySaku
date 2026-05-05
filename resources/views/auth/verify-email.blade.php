<x-guest-layout>
    <x-slot:title>Verifikasi Email</x-slot:title>

    <div class="mb-5">
        <h2 class="text-xl font-bold text-slate-900">Verifikasi email</h2>
        <p class="mt-1 text-sm text-slate-500">Terima kasih sudah daftar. Cek email kamu dan klik tautan verifikasi untuk melanjutkan.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-2xl bg-emerald-50 p-3 text-sm font-medium text-emerald-700">
            Tautan verifikasi baru sudah dikirim ke email kamu.
        </div>
    @endif

    <div class="space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30">
                Kirim ulang email verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full rounded-2xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-600">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
