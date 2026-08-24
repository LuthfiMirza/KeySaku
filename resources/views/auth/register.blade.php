<x-guest-layout>
    <x-slot:title>Daftar KeySaku</x-slot:title>

    <div class="mb-5">
        <h2 class="text-xl font-bold text-slate-900">Buat akun baru</h2>
        <p class="mt-1 text-sm text-slate-500">Mulai kelola pemasukan, pengeluaran, dan target keluarga.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-semibold text-slate-700">Nama</label>
            <input id="name" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama lengkap">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700">Email</label>
            <input id="email" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-slate-700">Kata sandi</label>
            <input id="password" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Konfirmasi kata sandi</label>
            <input id="password_confirmation" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div>
            <label for="initial_balance" class="block text-sm font-semibold text-slate-700">Saldo awal <span class="font-normal text-slate-400">(opsional)</span></label>
            <div class="mt-2 flex rounded-2xl border border-slate-200 bg-white/80 shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">
                <span class="flex items-center px-4 text-sm font-bold text-slate-400">Rp</span>
                <input id="initial_balance" class="block w-full rounded-r-2xl border-0 bg-transparent px-2 py-3 text-sm focus:ring-0" type="text" inputmode="numeric" name="initial_balance" value="{{ old('initial_balance') }}" placeholder="0">
            </div>
            <p class="mt-2 text-xs text-slate-400">Uang yang sudah kamu punya sebelum mulai mencatat di KeySaku. Bisa diisi nanti lewat Pengaturan.</p>
            <x-input-error :messages="$errors->get('initial_balance')" class="mt-2" />
        </div>

        <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30 transition active:scale-[0.99]">
            Daftar
        </button>
    </form>

    <p class="mt-5 text-center text-sm text-slate-500">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-700">Masuk</a>
    </p>
</x-guest-layout>
