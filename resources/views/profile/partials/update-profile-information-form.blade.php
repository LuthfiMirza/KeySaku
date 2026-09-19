<section>
    <div class="mb-4">
        <h3 class="text-sm font-bold text-slate-800">Informasi Profil</h3>
        <p class="mt-1 text-xs text-slate-400">Perbarui nama dan alamat email akun kamu.</p>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        {{-- Name --}}
        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">
                Nama Lengkap
            </label>
            <input id="name" name="name" type="text" autocomplete="name" required
                   value="{{ old('name', $user->name) }}"
                   class="block w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('name')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">
                Email
            </label>
            <input id="email" name="email" type="email" autocomplete="username" required
                   value="{{ old('email', $user->email) }}"
                   class="block w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('email')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 rounded-2xl bg-amber-50 border border-amber-200 px-4 py-3">
                    <p class="text-xs text-amber-700">Email belum diverifikasi.
                        <button form="send-verification" class="underline font-bold">
                            Kirim ulang verifikasi
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 text-xs font-medium text-emerald-600">Link verifikasi sudah dikirim!</p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Save --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                    class="flex-1 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-md shadow-indigo-500/20 active:scale-[0.98] transition-transform">
                Simpan Perubahan
            </button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-xs font-medium text-emerald-600">
                    Tersimpan ✓
                </p>
            @endif
        </div>
    </form>
</section>
