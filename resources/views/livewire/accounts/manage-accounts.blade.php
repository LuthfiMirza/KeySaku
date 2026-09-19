<div class="space-y-4">

    {{-- Status message --}}
    @if ($statusMessage)
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm font-medium text-emerald-700">
            ✅ {{ $statusMessage }}
        </div>
    @endif

    {{-- Add button --}}
    @if (! $showForm)
        <button wire:click="openCreate"
                class="w-full rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/30 active:scale-[0.98] transition-transform">
            + Tambah Akun Baru
        </button>
    @endif

    {{-- Form: Create / Edit --}}
    @if ($showForm)
        <div class="rounded-3xl bg-white/90 border border-white/60 p-5 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-800">
                {{ $editingId ? 'Edit Akun' : 'Tambah Akun Baru' }}
            </h3>

            {{-- Name --}}
            <div>
                <label for="acc-name" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">
                    Nama Akun
                </label>
                <input id="acc-name" wire:model="name" type="text" placeholder="Contoh: BCA, Dana, Tunai"
                       class="block w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>

            {{-- Type --}}
            <div>
                <label for="acc-type" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">
                    Jenis Akun
                </label>
                <select id="acc-type" wire:model="type"
                        class="block w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach ($typeLabels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('type') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>

            {{-- Balance --}}
            <div>
                <label for="acc-balance" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">
                    Saldo Awal (Rp)
                </label>
                <input id="acc-balance" wire:model="balance" type="number" min="0" step="1000" placeholder="0"
                       class="block w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('balance') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>

            {{-- Icon --}}
            <div>
                <label for="acc-icon" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">
                    Ikon (emoji, opsional)
                </label>
                <input id="acc-icon" wire:model="icon" type="text" placeholder="💰"
                       class="block w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            {{-- Color --}}
            <div class="flex items-center gap-3">
                <label for="acc-color" class="text-xs font-bold uppercase tracking-wide text-slate-400">Warna</label>
                <input id="acc-color" wire:model="color" type="color"
                       class="h-10 w-16 cursor-pointer rounded-xl border-slate-200 p-1">
            </div>

            {{-- Actions --}}
            <div class="grid grid-cols-2 gap-3 pt-1">
                <button wire:click="cancelForm"
                        class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-600 active:scale-[0.98] transition-transform">
                    Batal
                </button>
                <button wire:click="save"
                        class="rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-md shadow-indigo-500/20 active:scale-[0.98] transition-transform">
                    Simpan
                </button>
            </div>
        </div>
    @endif

    {{-- Delete confirmation modal --}}
    @if ($confirmDeleteId)
        <div class="rounded-3xl bg-rose-50 border border-rose-200 p-5 shadow-sm space-y-3">
            <p class="text-sm font-bold text-rose-700">Hapus akun ini?</p>
            <p class="text-xs text-rose-500">Tindakan ini tidak dapat dibatalkan. Saldo akun akan dihapus.</p>
            <div class="grid grid-cols-2 gap-3">
                <button wire:click="cancelDelete"
                        class="rounded-2xl border border-rose-200 bg-white px-4 py-3 text-sm font-bold text-slate-600">
                    Batal
                </button>
                <button wire:click="delete"
                        class="rounded-2xl bg-rose-500 px-4 py-3 text-sm font-bold text-white shadow-md shadow-rose-500/20">
                    Ya, Hapus
                </button>
            </div>
        </div>
    @endif

    {{-- Account list --}}
    @if ($accounts->isEmpty())
        <div class="rounded-3xl bg-white/80 border border-white/60 p-8 text-center shadow-sm">
            <p class="text-4xl mb-3">🏦</p>
            <p class="text-sm font-bold text-slate-700">Belum ada akun keuangan.</p>
            <p class="mt-1 text-xs text-slate-400">Tambahkan akun pertamamu untuk mulai mencatat keuangan.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($accounts as $account)
                @php
                    $typeIcons = ['cash'=>'💵','bank'=>'🏦','ewallet'=>'📱','investment'=>'📈'];
                    $typeIcon  = $typeIcons[$account->type] ?? '💰';
                @endphp
                <div class="rounded-3xl bg-white/80 border border-white/60 p-4 shadow-sm {{ $account->is_active ? '' : 'opacity-60' }}">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-xl"
                                  style="background-color: {{ $account->color ?? '#6366f1' }}22; color: {{ $account->color ?? '#6366f1' }};">
                                {{ $account->icon ?: $typeIcon }}
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-slate-800">{{ $account->name }}</p>
                                <p class="text-xs text-slate-400">{{ \App\Models\Account::typeLabel($account->type) }}
                                    @if (! $account->is_active)
                                        · <span class="text-slate-300">Nonaktif</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-sm font-extrabold tabular-nums text-slate-800">
                                <x-money :amount="$account->balance" />
                            </p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-3 flex items-center gap-2">
                        <button wire:click="openEdit({{ $account->id }})"
                                class="flex-1 rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-600 active:bg-slate-200 transition-colors">
                            Edit
                        </button>
                        <button wire:click="toggleActive({{ $account->id }})"
                                class="flex-1 rounded-xl {{ $account->is_active ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' }} px-3 py-2 text-xs font-bold active:scale-[0.98] transition-all">
                            {{ $account->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                        <button wire:click="confirmDelete({{ $account->id }})"
                                class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-500 active:bg-rose-100 transition-colors">
                            Hapus
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
