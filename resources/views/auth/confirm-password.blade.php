<x-guest-layout>
    <x-slot:title>Konfirmasi Kata Sandi</x-slot:title>

    <div class="mb-5">
        <h2 class="text-xl font-bold text-slate-900">Konfirmasi kata sandi</h2>
        <p class="mt-1 text-sm text-slate-500">Masukkan kata sandi untuk melanjutkan aksi sensitif ini.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <label for="password" class="block text-sm font-semibold text-slate-700">Kata sandi</label>
            <input id="password" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password" required autocomplete="current-password">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30">
            Konfirmasi
        </button>
    </form>
</x-guest-layout>
