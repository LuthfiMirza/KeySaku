<div class="space-y-5">
    @if (session('savings_status'))
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700 shadow-sm">
            {{ session('savings_status') }}
        </div>
    @endif

    <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60">
        <div>
            <h3 class="text-sm font-bold text-slate-800">Buat Target</h3>
            <p class="mt-1 text-xs leading-relaxed text-slate-400">Tentukan tujuan tabungan dan nominal yang ingin dicapai.</p>
        </div>

        <form wire:submit="save" class="mt-5 space-y-5">
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wide text-slate-400">Nama Target</label>
                <input id="name" type="text" maxlength="100" wire:model="name" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: Dana Darurat">
                @error('name')
                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="target_amount" class="block text-xs font-bold uppercase tracking-wide text-slate-400">Nominal Target</label>
                <input id="target_amount" type="number" min="0" step="1000" inputmode="numeric" wire:model="target_amount" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: 10000000">
                @error('target_amount')
                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="deadline" class="block text-xs font-bold uppercase tracking-wide text-slate-400">Deadline</label>
                <input id="deadline" type="date" wire:model="deadline" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('deadline')
                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="min-h-12 w-full rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30">
                Simpan Target
            </button>
        </form>
    </section>

    <section class="space-y-3">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Daftar Target</h3>
                <p class="mt-1 text-xs text-slate-400">Pantau target tabungan yang sedang direncanakan.</p>
            </div>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-500">{{ $goals->count() }}</span>
        </div>

        @if ($goals->isEmpty())
            <div class="bg-white/80 backdrop-blur-sm rounded-3xl p-6 shadow-sm border border-white/60 text-center">
                <p class="text-4xl mb-3">🏦</p>
                <h3 class="text-sm font-bold text-slate-700">Belum ada target tabungan.</h3>
                <p class="mt-2 text-xs leading-relaxed text-slate-400">Buat target untuk memantau progres dana darurat, liburan, atau kebutuhan keluarga.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($goals as $goal)
                    @php
                        $targetAmount = (float) $goal->target_amount;
                        $currentAmount = (float) $goal->current_amount;
                        $remainingAmount = max(0, $targetAmount - $currentAmount);
                        $percentage = $targetAmount > 0 ? round(($currentAmount / $targetAmount) * 100) : 0;
                        $barPercentage = min(100, max(0, $percentage));
                        $statusLabel = match ($goal->status) {
                            \App\Models\SavingsGoal::STATUS_COMPLETED => 'Selesai',
                            \App\Models\SavingsGoal::STATUS_ARCHIVED => 'Diarsipkan',
                            default => 'Aktif',
                        };
                    @endphp

                    <article wire:key="savings-goal-{{ $goal->id }}" class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-base font-extrabold text-slate-800">{{ $goal->name }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $goal->deadline ? 'Deadline: '.$goal->deadline->translatedFormat('d F Y') : 'Tanpa deadline' }}</p>
                            </div>
                            <div class="flex shrink-0 flex-col items-end gap-2">
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-bold text-emerald-600">{{ $currentAmount >= $targetAmount ? 'Target tercapai' : $statusLabel }}</span>
                                @if ($contributingGoalId !== $goal->id && $editingGoalId !== $goal->id)
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <button type="button" wire:click="startEditGoal({{ $goal->id }})" class="inline-flex min-h-9 items-center rounded-2xl bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600">Edit</button>
                                        <button type="button" wire:click="startContribution({{ $goal->id }})" class="inline-flex min-h-9 items-center rounded-2xl bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-600">Tambah Dana</button>
                                        <button type="button" wire:click="archiveGoal({{ $goal->id }})" wire:confirm="Yakin ingin mengarsipkan target ini? Riwayat dana tetap tersimpan." class="inline-flex min-h-9 items-center rounded-2xl bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-600">Arsipkan</button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($editingGoalId === $goal->id)
                            <form wire:submit="updateGoal" class="mt-4 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                                <h4 class="text-sm font-bold text-slate-800">Edit Target</h4>

                                <div class="mt-3 space-y-3">
                                    <div>
                                        <label for="edit-name-{{ $goal->id }}" class="block text-xs font-bold uppercase tracking-wide text-slate-400">Nama Target</label>
                                        <input id="edit-name-{{ $goal->id }}" type="text" maxlength="100" wire:model="editName" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('editName')
                                            <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="edit-target-{{ $goal->id }}" class="block text-xs font-bold uppercase tracking-wide text-slate-400">Nominal Target</label>
                                        <input id="edit-target-{{ $goal->id }}" type="number" min="0" step="1000" inputmode="numeric" wire:model="editTargetAmount" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('editTargetAmount')
                                            <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="edit-deadline-{{ $goal->id }}" class="block text-xs font-bold uppercase tracking-wide text-slate-400">Deadline</label>
                                        <input id="edit-deadline-{{ $goal->id }}" type="date" wire:model="editDeadline" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('editDeadline')
                                            <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-3 flex gap-2">
                                    <button type="submit" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-2 text-xs font-bold text-white shadow-lg shadow-indigo-500/30">Simpan</button>
                                    <button type="button" wire:click="cancelEditGoal" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-2xl bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm">Batal</button>
                                </div>
                            </form>
                        @endif

                        @if ($contributingGoalId === $goal->id)
                            <form wire:submit="addContribution" class="mt-4 rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                                <h4 class="text-sm font-bold text-slate-800">Tambah Dana</h4>

                                <div class="mt-3 space-y-3">
                                    <div>
                                        <label for="contribution-amount-{{ $goal->id }}" class="block text-xs font-bold uppercase tracking-wide text-indigo-500">Nominal</label>
                                        <input id="contribution-amount-{{ $goal->id }}" type="number" min="0" step="1000" inputmode="numeric" wire:model="contributionAmount" class="mt-2 block min-h-12 w-full rounded-2xl border-indigo-100 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: 500000">
                                        @error('contributionAmount')
                                            <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="contribution-date-{{ $goal->id }}" class="block text-xs font-bold uppercase tracking-wide text-indigo-500">Tanggal</label>
                                        <input id="contribution-date-{{ $goal->id }}" type="date" wire:model="contributionDate" class="mt-2 block min-h-12 w-full rounded-2xl border-indigo-100 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('contributionDate')
                                            <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="contribution-note-{{ $goal->id }}" class="block text-xs font-bold uppercase tracking-wide text-indigo-500">Catatan</label>
                                        <textarea id="contribution-note-{{ $goal->id }}" rows="3" wire:model="contributionNote" class="mt-2 block w-full rounded-2xl border-indigo-100 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Opsional"></textarea>
                                        @error('contributionNote')
                                            <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-3 flex gap-2">
                                    <button type="submit" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-2 text-xs font-bold text-white shadow-lg shadow-indigo-500/30">Simpan Dana</button>
                                    <button type="button" wire:click="cancelContribution" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-2xl bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm">Batal</button>
                                </div>
                            </form>
                        @endif

                        <div class="mt-4 grid grid-cols-3 gap-2 text-xs">
                            <div class="rounded-2xl bg-slate-50 px-3 py-2">
                                <p class="text-slate-400">Target</p>
                                <p class="mt-1 font-extrabold leading-tight text-slate-800 tabular-nums">Rp {{ number_format($targetAmount, 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-3 py-2">
                                <p class="text-slate-400">Terkumpul</p>
                                <p class="mt-1 font-extrabold leading-tight text-indigo-600 tabular-nums">Rp {{ number_format($currentAmount, 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-3 py-2">
                                <p class="text-slate-400">Sisa</p>
                                <p class="mt-1 font-extrabold leading-tight {{ $currentAmount >= $targetAmount ? 'text-emerald-600' : 'text-slate-800' }} tabular-nums">{{ $currentAmount >= $targetAmount ? 'Tercapai' : 'Rp '.number_format($remainingAmount, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="mb-2 flex items-center justify-between text-xs font-bold">
                                <span class="text-slate-500">{{ $percentage }}% terkumpul</span>
                                <span class="text-slate-400">Placeholder</span>
                            </div>
                            <div class="h-3 overflow-hidden rounded-full bg-slate-100" role="progressbar" aria-label="Progres target tabungan {{ $goal->name }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $barPercentage }}">
                                <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-purple-600" style="width: {{ $barPercentage }}%"></div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
