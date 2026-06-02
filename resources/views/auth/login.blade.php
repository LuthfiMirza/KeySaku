<x-guest-layout>
    <x-slot:title>Masuk ke KeySaku</x-slot:title>

    <div class="mb-5">
        <h2 class="text-xl font-bold text-slate-900">Masuk ke akunmu</h2>
        <p class="mt-1 text-sm text-[#8890b0]">Pantau keuangan keluarga dari satu tempat.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-[#0f0f5e]">Email</label>
            <input id="email" class="mt-2 block w-full rounded-2xl border-[#dde3f5] bg-white px-4 py-3 text-sm shadow-sm focus:border-[#1a1aff] focus:ring-[#1a1aff]/20" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-[#0f0f5e]">Kata sandi</label>
            <input id="password" class="mt-2 block w-full rounded-2xl border-[#dde3f5] bg-white px-4 py-3 text-sm shadow-sm focus:border-[#1a1aff] focus:ring-[#1a1aff]/20" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center text-sm text-[#8890b0]">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-[#1a1aff] shadow-sm focus:ring-[#1a1aff]/20" name="remember">
                <span class="ms-2">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-[#1a1aff] hover:text-indigo-700" href="{{ route('password.request') }}">
                    Lupa sandi?
                </a>
            @endif
        </div>

        <button type="submit" class="w-full rounded-2xl bg-[#ff3d8b] px-4 py-3 text-sm font-bold text-[#0f0f5e] shadow-lg shadow-pink-500/20 transition active:scale-[0.99]">
            Masuk
        </button>
    </form>

    <p class="mt-5 text-center text-sm text-[#8890b0]">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-bold text-[#1a1aff] hover:text-indigo-700">Daftar gratis</a>
    </p>
</x-guest-layout>
