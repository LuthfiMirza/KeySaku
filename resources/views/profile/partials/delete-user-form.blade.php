<section class="space-y-4" x-data="{ showConfirm: false }">
    <div>
        <h3 class="text-sm font-bold text-rose-700">Zona Berbahaya</h3>
        <p class="mt-1 text-xs text-rose-500">
            Setelah akun dihapus, semua data akan hilang secara permanen dan tidak dapat dipulihkan.
        </p>
    </div>

    <button type="button"
            x-on:click="showConfirm = true"
            class="w-full rounded-2xl border border-rose-300 bg-rose-100 px-4 py-3 text-sm font-bold text-rose-600 active:scale-[0.98] transition-transform">
        🗑️ Hapus Akun Saya
    </button>

    {{-- Confirmation panel --}}
    <div x-show="showConfirm" x-transition class="rounded-2xl bg-white border border-rose-200 p-4 space-y-4">
        <p class="text-sm font-bold text-slate-800">Konfirmasi Hapus Akun</p>
        <p class="text-xs text-slate-500">
            Masukkan password kamu untuk memastikan penghapusan akun. Tindakan ini <span class="font-bold text-rose-500">tidak dapat dibatalkan</span>.
        </p>

        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-3">
            @csrf
            @method('delete')

            <div>
                <label for="delete-password"
                       class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">
                    Password
                </label>
                <input id="delete-password" name="password" type="password" placeholder="Masukkan password"
                       class="block w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-rose-500 focus:ring-rose-500">
                @error('password', 'userDeletion')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button type="button"
                        x-on:click="showConfirm = false"
                        class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-600 active:scale-[0.98] transition-transform">
                    Batal
                </button>
                <button type="submit"
                        class="rounded-2xl bg-rose-500 px-4 py-3 text-sm font-bold text-white shadow-md shadow-rose-500/20 active:scale-[0.98] transition-transform">
                    Ya, Hapus Akun
                </button>
            </div>
        </form>
    </div>
</section>
