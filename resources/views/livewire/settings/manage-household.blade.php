<div class="space-y-5">
    @if (session('household_status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-100 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('household_status') }}
        </div>
    @endif

    @error('member')
        <div class="rounded-2xl bg-rose-50 border border-rose-100 px-4 py-3 text-sm font-medium text-rose-700">
            {{ $message }}
        </div>
    @enderror

    @error('leave')
        <div class="rounded-2xl bg-rose-50 border border-rose-100 px-4 py-3 text-sm font-medium text-rose-700">
            {{ $message }}
        </div>
    @enderror

    <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60">
        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-500">Kode Undangan</p>
        <h2 class="mt-1 text-lg font-bold text-slate-800">Ajak anggota keluarga</h2>
        <p class="mt-1 text-xs leading-relaxed text-slate-400">Bagikan kode ini lewat WhatsApp atau kanal lain. Anggota baru memasukkannya saat mendaftar untuk langsung gabung ke keluarga ini.</p>

        <div class="mt-4 flex items-center justify-between rounded-2xl border border-dashed border-indigo-200 bg-indigo-50 px-4 py-3">
            <span class="font-mono text-lg font-bold tracking-widest text-indigo-700">{{ $household->invite_code }}</span>
        </div>
    </section>

    <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60">
        <h2 class="text-lg font-bold text-slate-800">Anggota Keluarga</h2>
        <p class="mt-1 text-xs text-slate-400">{{ $members->count() }} orang berbagi data keuangan yang sama.</p>

        <div class="mt-4 space-y-3">
            @foreach ($members as $member)
                <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-slate-800">
                            {{ $member->user->name }}
                            @if ($member->user_id === auth()->id())
                                <span class="font-normal text-slate-400">(kamu)</span>
                            @endif
                        </p>
                        <p class="text-xs text-slate-400">{{ $member->role === 'owner' ? 'Pemilik' : 'Anggota' }}</p>
                    </div>

                    @if ($isOwner && $member->user_id !== auth()->id())
                        <button
                            type="button"
                            wire:click="removeMember({{ $member->id }})"
                            wire:confirm="Keluarkan {{ $member->user->name }} dari keluarga?"
                            class="shrink-0 rounded-2xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-600"
                        >
                            Keluarkan
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60">
        @if ($isOwner && $members->count() > 1)
            <p class="text-sm font-semibold text-slate-700">Kamu pemilik keluarga ini.</p>
            <p class="mt-1 text-xs leading-relaxed text-slate-400">Keluarkan semua anggota lain dulu kalau ingin keluar dari keluarga ini.</p>
        @else
            <p class="text-sm font-semibold text-slate-700">Keluar dari keluarga ini?</p>
            <p class="mt-1 text-xs leading-relaxed text-slate-400">Kamu akan mendapat keluarga baru untuk dirimu sendiri. Data keuangan keluarga ini tidak ikut pindah.</p>
            <button
                type="button"
                wire:click="leaveHousehold"
                wire:confirm="Yakin ingin keluar dari keluarga ini?"
                class="mt-3 min-h-11 w-full rounded-2xl bg-rose-500 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-rose-500/20"
            >
                Keluar dari Keluarga
            </button>
        @endif
    </section>
</div>
