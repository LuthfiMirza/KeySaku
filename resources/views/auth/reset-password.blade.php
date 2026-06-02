<x-guest-layout>
    <x-slot:title>Reset Kata Sandi</x-slot:title>

    <div class="mb-5">
        <h2 class="text-xl font-bold text-slate-900">Reset kata sandi</h2>
        <p class="mt-1 text-sm text-[#8890b0]">Buat kata sandi baru untuk akun KeySaku kamu.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="block text-sm font-semibold text-[#0f0f5e]">Email</label>
            <input id="email" class="mt-2 block w-full rounded-2xl border-[#dde3f5] bg-white px-4 py-3 text-sm shadow-sm focus:border-[#1a1aff] focus:ring-[#1a1aff]/20" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-[#0f0f5e]">Kata sandi baru</label>
            <input id="password" class="mt-2 block w-full rounded-2xl border-[#dde3f5] bg-white px-4 py-3 text-sm shadow-sm focus:border-[#1a1aff] focus:ring-[#1a1aff]/20" type="password" name="password" required autocomplete="new-password">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-[#0f0f5e]">Konfirmasi kata sandi</label>
            <input id="password_confirmation" class="mt-2 block w-full rounded-2xl border-[#dde3f5] bg-white px-4 py-3 text-sm shadow-sm focus:border-[#1a1aff] focus:ring-[#1a1aff]/20" type="password" name="password_confirmation" required autocomplete="new-password">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="w-full rounded-2xl bg-[#ff3d8b] px-4 py-3 text-sm font-bold text-[#0f0f5e] shadow-lg shadow-pink-500/20">
            Simpan kata sandi baru
        </button>
    </form>
</x-guest-layout>
