<x-guest-layout>
    <x-slot:title>Daftar KeySaku</x-slot:title>

    <div class="mb-5">
        <h2 class="text-xl font-bold text-slate-900">Buat akun baru</h2>
        <p class="mt-1 text-sm text-[#8890b0]">Mulai kelola pemasukan, pengeluaran, dan target keluarga.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-semibold text-[#0f0f5e]">Nama</label>
            <input id="name" class="mt-2 block w-full rounded-2xl border-[#dde3f5] bg-white px-4 py-3 text-sm shadow-sm focus:border-[#1a1aff] focus:ring-[#1a1aff]/20" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama lengkap">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-[#0f0f5e]">Email</label>
            <input id="email" class="mt-2 block w-full rounded-2xl border-[#dde3f5] bg-white px-4 py-3 text-sm shadow-sm focus:border-[#1a1aff] focus:ring-[#1a1aff]/20" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-[#0f0f5e]">Kata sandi</label>
            <input id="password" class="mt-2 block w-full rounded-2xl border-[#dde3f5] bg-white px-4 py-3 text-sm shadow-sm focus:border-[#1a1aff] focus:ring-[#1a1aff]/20" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-[#0f0f5e]">Konfirmasi kata sandi</label>
            <input id="password_confirmation" class="mt-2 block w-full rounded-2xl border-[#dde3f5] bg-white px-4 py-3 text-sm shadow-sm focus:border-[#1a1aff] focus:ring-[#1a1aff]/20" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="w-full rounded-2xl bg-[#ff3d8b] px-4 py-3 text-sm font-bold text-[#0f0f5e] shadow-lg shadow-pink-500/20 transition active:scale-[0.99]">
            Daftar
        </button>
    </form>

    <p class="mt-5 text-center text-sm text-[#8890b0]">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-bold text-[#1a1aff] hover:text-indigo-700">Masuk</a>
    </p>
</x-guest-layout>
