<x-guest-layout>
    <x-slot:title>Lupa Kata Sandi</x-slot:title>

    <div class="mb-5">
        <h2 class="text-xl font-bold text-slate-900">Lupa kata sandi?</h2>
        <p class="mt-1 text-sm text-[#8890b0]">Masukkan email, kami akan kirim tautan untuk mengatur ulang kata sandi.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-[#0f0f5e]">Email</label>
            <input id="email" class="mt-2 block w-full rounded-2xl border-[#dde3f5] bg-white px-4 py-3 text-sm shadow-sm focus:border-[#1a1aff] focus:ring-[#1a1aff]/20" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="w-full rounded-2xl bg-[#ff3d8b] px-4 py-3 text-sm font-bold text-[#0f0f5e] shadow-lg shadow-pink-500/20">
            Kirim tautan reset
        </button>
    </form>

    <p class="mt-5 text-center text-sm text-[#8890b0]">
        Ingat kata sandi?
        <a href="{{ route('login') }}" class="font-bold text-[#1a1aff] hover:text-indigo-700">Masuk</a>
    </p>
</x-guest-layout>
