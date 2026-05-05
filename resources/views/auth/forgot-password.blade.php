<x-guest-layout>
    <x-slot:title>Lupa Kata Sandi</x-slot:title>

    <div class="mb-5">
        <h2 class="text-xl font-bold text-slate-900">Lupa kata sandi?</h2>
        <p class="mt-1 text-sm text-slate-500">Masukkan email, kami akan kirim tautan untuk mengatur ulang kata sandi.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700">Email</label>
            <input id="email" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30">
            Kirim tautan reset
        </button>
    </form>

    <p class="mt-5 text-center text-sm text-slate-500">
        Ingat kata sandi?
        <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-700">Masuk</a>
    </p>
</x-guest-layout>
