<section>
    <div class="mb-4">
        <h3 class="text-sm font-bold text-slate-800">Ganti Password</h3>
        <p class="mt-1 text-xs text-slate-400">Pastikan password kamu panjang dan unik agar akun tetap aman.</p>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password"
                   class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">
                Password Saat Ini
            </label>
            <input id="update_password_current_password" name="current_password" type="password"
                   autocomplete="current-password"
                   class="block w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('current_password', 'updatePassword')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password"
                   class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">
                Password Baru
            </label>
            <input id="update_password_password" name="password" type="password"
                   autocomplete="new-password"
                   class="block w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('password', 'updatePassword')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation"
                   class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">
                Konfirmasi Password Baru
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                   autocomplete="new-password"
                   class="block w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('password_confirmation', 'updatePassword')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="flex-1 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-md shadow-indigo-500/20 active:scale-[0.98] transition-transform">
                Ganti Password
            </button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-xs font-medium text-emerald-600">
                    Tersimpan ✓
                </p>
            @endif
        </div>
    </form>
</section>
