<x-app-layout>
    <x-slot:title>Bahasa</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-28 lg:px-8 lg:pt-8 lg:pb-8 lg:max-w-5xl lg:mx-auto" style="background:#0A1628 !important;">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-[#A3E635]">Pengaturan</p>
                <h2 class="mt-1 text-base font-bold text-white">Bahasa</h2>
            </div>
            <a href="{{ route('settings') }}" class="inline-flex min-h-10 items-center rounded-2xl px-3 py-2 text-xs font-bold shadow-sm" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.08) !important;color:#A3E635 !important;">Kembali</a>
        </div>

        @if (session('settings_status'))
            <div class="rounded-2xl border border-emerald-100 bg-[#A3E635]/10 px-4 py-3 text-sm font-bold text-[#A3E635] shadow-sm">
                {{ session('settings_status') }}
            </div>
        @endif

        <section class="rounded-3xl p-5" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
            <p class="text-sm font-semibold text-white">Pilih bahasa tampilan aplikasi.</p>
            <p class="mt-2 text-xs leading-relaxed text-[#64748B]">Terjemahan penuh aplikasi dapat diterapkan bertahap setelah preferensi bahasa aktif.</p>

            <form method="POST" action="{{ route('settings.language.update') }}" class="mt-5 space-y-3">
                @csrf
                @method('PATCH')

                @foreach ([
                    'id' => 'Indonesia',
                    'en' => 'English',
                ] as $value => $label)
                    <label class="flex min-h-14 items-center gap-3 rounded-2xl px-4 py-3" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:14px !important;">
                        <input type="radio" name="language" value="{{ $value }}" @checked(old('language', auth()->user()->language ?? 'id') === $value) class="border-[#64748B] bg-[#1A2B52] text-[#A3E635] focus:ring-[#A3E635]/20">
                        <span>
                            <span class="block text-sm font-bold text-white">{{ $value }}</span>
                            <span class="block text-xs text-[#64748B]">{{ $label }}</span>
                        </span>
                    </label>
                @endforeach

                @error('language')
                    <p class="text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror

                <button type="submit" class="min-h-12 w-full rounded-2xl px-4 py-3 text-sm font-bold shadow-lg shadow-lime-500/20" style="background:#A3E635 !important;color:#0A1628 !important;">Simpan Bahasa</button>
            </form>
        </section>
    </div>
</x-app-layout>
