<x-guest-layout>
    <x-slot:title>Konfirmasi Kata Sandi</x-slot:title>

    <div class="mb-5">
        <h2 class="text-xl font-bold text-slate-900">Konfirmasi kata sandi</h2>
        <p class="mt-1 text-sm text-[#8890b0]">Masukkan kata sandi untuk melanjutkan aksi sensitif ini.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <label for="password" class="block text-sm font-semibold text-[#0f0f5e]">Kata sandi</label>
            <input id="password" class="mt-2 block w-full rounded-2xl border-[#dde3f5] bg-white px-4 py-3 text-sm shadow-sm focus:border-[#1a1aff] focus:ring-[#1a1aff]/20" type="password" name="password" required autocomplete="current-password">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="w-full rounded-2xl bg-[#ff3d8b] px-4 py-3 text-sm font-bold text-[#0f0f5e] shadow-lg shadow-pink-500/20">
            Konfirmasi
        </button>
    </form>
</x-guest-layout>
