<div class="space-y-5 lg:max-w-5xl lg:mx-auto">
    @if (session('savings_status'))
        <div class="rounded-2xl border border-emerald-100 bg-[#A3E635]/10 px-4 py-3 text-sm font-bold text-emerald-700 shadow-sm">
            {{ session('savings_status') }}
        </div>
    @endif

    <section class="rounded-3xl p-5" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
        <div>
            <h3 class="text-sm font-bold" style="color:#FFFFFF !important;">Buat Target</h3>
            <p class="mt-1 text-xs leading-relaxed" style="color:#64748B !important;">Tentukan tujuan tabungan dan nominal yang ingin dicapai.</p>
        </div>

        <form wire:submit="save" class="mt-5 space-y-5">
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Nama Target</label>
                <input id="name" type="text" maxlength="100" wire:model="name" class="mt-2 block min-h-12 w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;" placeholder="Contoh: Dana Darurat">
                @error('name')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="target_amount" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Nominal Target</label>
                <input id="target_amount" type="number" min="0" step="1000" inputmode="numeric" wire:model="target_amount" class="mt-2 block min-h-12 w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;" placeholder="Contoh: 10000000">
                @error('target_amount')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="deadline" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Deadline</label>
                <input id="deadline" type="date" wire:model="deadline" class="mt-2 block min-h-12 w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;">
                @error('deadline')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="min-h-12 w-full rounded-2xl px-4 py-3 text-sm font-bold shadow-lg shadow-lime-500/20" style="background:#A3E635 !important;color:#0A1628 !important;">
                Simpan Target
            </button>
        </form>
    </section>

    <section class="space-y-3">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold" style="color:#FFFFFF !important;">Daftar Target</h3>
                <p class="mt-1 text-xs" style="color:#64748B !important;">Pantau target tabungan yang sedang direncanakan.</p>
            </div>
            <span class="rounded-full px-3 py-1 text-xs font-bold" style="background:rgba(163,230,53,0.15) !important;color:#A3E635 !important;border-radius:999px !important;padding:4px 12px !important;font-size:11px !important;">{{ $goals->count() }}</span>
        </div>

        @if ($goals->isEmpty())
            <div class="rounded-3xl p-6 text-center" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
                <p class="text-4xl mb-3">🏦</p>
                <h3 class="text-sm font-bold" style="color:#FFFFFF !important;">Belum ada target tabungan.</h3>
                <p class="mt-2 text-xs leading-relaxed text-[#64748B]">Buat target untuk memantau progres dana darurat, liburan, atau kebutuhan keluarga.</p>
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

                    <article wire:key="savings-goal-{{ $goal->id }}" class="rounded-2xl p-4" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-base font-extrabold text-white">{{ $goal->name }}</p>
                                <p class="mt-1 text-xs" style="color:#64748B !important;">{{ $goal->deadline ? 'Deadline: '.$goal->deadline->translatedFormat('d F Y') : 'Tanpa deadline' }}</p>
                            </div>
                            <div class="flex shrink-0 flex-col items-end gap-2">
                                <span class="rounded-full px-3 py-1 text-[10px] font-bold" style="background:rgba(163,230,53,0.15) !important;color:#A3E635 !important;border-radius:999px !important;">{{ $currentAmount >= $targetAmount ? 'Target tercapai' : $statusLabel }}</span>
                                @if ($contributingGoalId !== $goal->id && $editingGoalId !== $goal->id)
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <button type="button" wire:click="startEditGoal({{ $goal->id }})" class="inline-flex min-h-9 items-center rounded-2xl px-3 py-1.5 text-xs font-bold" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;color:#FFFFFF !important;">Edit</button>
                                        <button type="button" wire:click="startContribution({{ $goal->id }})" class="inline-flex min-h-9 items-center rounded-2xl px-3 py-1.5 text-xs font-bold" style="background:#1A2B52 !important;border:1px solid rgba(163,230,53,0.2) !important;color:#A3E635 !important;">Tambah Dana</button>
                                        <button type="button" wire:click="archiveGoal({{ $goal->id }})" wire:confirm="Yakin ingin mengarsipkan target ini? Riwayat dana tetap tersimpan." class="inline-flex min-h-9 items-center rounded-2xl px-3 py-1.5 text-xs font-bold" style="background:#1A2B52 !important;border:1px solid rgba(245,158,11,0.2) !important;color:#F59E0B !important;">Arsipkan</button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($editingGoalId === $goal->id)
                            <form wire:submit="updateGoal" class="mt-4 rounded-2xl px-4 py-3" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:20px !important;">
                                <h4 class="text-sm font-bold" style="color:#FFFFFF !important;">Edit Target</h4>

                                <div class="mt-3 space-y-3">
                                    <div>
                                        <label for="edit-name-{{ $goal->id }}" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Nama Target</label>
                                        <input id="edit-name-{{ $goal->id }}" type="text" maxlength="100" wire:model="editName" class="mt-2 block min-h-12 w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;">
                                        @error('editName')
                                            <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="edit-target-{{ $goal->id }}" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Nominal Target</label>
                                        <input id="edit-target-{{ $goal->id }}" type="number" min="0" step="1000" inputmode="numeric" wire:model="editTargetAmount" class="mt-2 block min-h-12 w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;">
                                        @error('editTargetAmount')
                                            <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="edit-deadline-{{ $goal->id }}" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Deadline</label>
                                        <input id="edit-deadline-{{ $goal->id }}" type="date" wire:model="editDeadline" class="mt-2 block min-h-12 w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;">
                                        @error('editDeadline')
                                            <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-3 flex gap-2">
                                    <button type="submit" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-2xl px-4 py-2 text-xs font-bold shadow-lg shadow-lime-500/20" style="background:#A3E635 !important;color:#0A1628 !important;">Simpan</button>
                                    <button type="button" wire:click="cancelEditGoal" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-2xl px-4 py-2 text-xs font-bold shadow-sm" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;color:#FFFFFF !important;">Batal</button>
                                </div>
                            </form>
                        @endif

                        @if ($contributingGoalId === $goal->id)
                            <form wire:submit="addContribution" class="mt-4 rounded-2xl px-4 py-3" style="background:#1A2B52 !important;border:1px solid rgba(163,230,53,0.12) !important;border-radius:20px !important;">
                                <h4 class="text-sm font-bold" style="color:#FFFFFF !important;">Tambah Dana</h4>

                                <div class="mt-3 space-y-3">
                                    <div>
                                        <label for="contribution-amount-{{ $goal->id }}" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Nominal</label>
                                        <input id="contribution-amount-{{ $goal->id }}" type="number" min="0" step="1000" inputmode="numeric" wire:model="contributionAmount" class="mt-2 block min-h-12 w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;" placeholder="Contoh: 500000">
                                        @error('contributionAmount')
                                            <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="contribution-date-{{ $goal->id }}" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Tanggal</label>
                                        <input id="contribution-date-{{ $goal->id }}" type="date" wire:model="contributionDate" class="mt-2 block min-h-12 w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;">
                                        @error('contributionDate')
                                            <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="contribution-note-{{ $goal->id }}" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Catatan</label>
                                        <textarea id="contribution-note-{{ $goal->id }}" rows="3" wire:model="contributionNote" class="mt-2 block min-h-12 w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;" placeholder="Opsional"></textarea>
                                        @error('contributionNote')
                                            <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-3 flex gap-2">
                                    <button type="submit" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-2xl px-4 py-2 text-xs font-bold shadow-lg shadow-lime-500/20" style="background:#A3E635 !important;color:#0A1628 !important;">Simpan Dana</button>
                                    <button type="button" wire:click="cancelContribution" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-2xl px-4 py-2 text-xs font-bold shadow-sm" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;color:#FFFFFF !important;">Batal</button>
                                </div>
                            </form>
                        @endif

                        <div class="mt-4 grid grid-cols-3 gap-2 text-xs">
                            <div class="rounded-2xl px-3 py-2" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;border-radius:14px !important;">
                                <p class="text-[#64748B]">Target</p>
                                <p class="mt-1 font-extrabold leading-tight text-white tabular-nums">Rp {{ number_format($targetAmount, 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-2xl px-3 py-2" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;border-radius:14px !important;">
                                <p class="text-[#64748B]">Terkumpul</p>
                                <p class="mt-1 font-extrabold leading-tight text-[#A3E635] tabular-nums">Rp {{ number_format($currentAmount, 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-2xl px-3 py-2" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;border-radius:14px !important;">
                                <p class="text-[#64748B]">Sisa</p>
                                <p class="mt-1 font-extrabold leading-tight {{ $currentAmount >= $targetAmount ? 'text-[#A3E635]' : 'text-white' }} tabular-nums">{{ $currentAmount >= $targetAmount ? 'Tercapai' : 'Rp '.number_format($remainingAmount, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="mb-2 flex items-center justify-between text-xs font-bold">
                                <span class="text-[#64748B]">{{ $percentage }}% terkumpul</span>
                                <span class="text-[#64748B]">{{ $currentAmount >= $targetAmount ? 'Target tercapai' : 'Dalam proses' }}</span>
                            </div>
                            <div class="overflow-hidden rounded-full" style="background:#1A2B52 !important;height:5px !important;border-radius:999px !important;" role="progressbar" aria-label="Progres target tabungan {{ $goal->name }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $barPercentage }}">
                                <div class="rounded-full" style="background:linear-gradient(90deg,#A3E635,#4ADE80) !important;height:5px !important;border-radius:999px !important;width: {{ $barPercentage }}%;"></div>
                            </div>
                        </div>

                        <div class="mt-4 rounded-2xl px-4 py-3" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:20px !important;">
                            <h4 class="text-sm font-bold" style="color:#FFFFFF !important;">Riwayat Dana</h4>

                            @if ($goal->contributions->isEmpty())
                                <div class="mt-3 rounded-2xl px-3 py-3" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.06) !important;">
                                    <p class="text-sm font-semibold text-[#64748B]">Belum ada dana masuk.</p>
                                    <p class="mt-1 text-xs leading-relaxed" style="color:#64748B !important;">Tambahkan dana untuk melihat riwayat kontribusi.</p>
                                </div>
                            @else
                                <div class="mt-3 space-y-2">
                                    @foreach ($goal->contributions->take(3) as $contribution)
                                        <div class="rounded-2xl px-3 py-3" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.06) !important;">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-extrabold text-[#A3E635] tabular-nums">Rp {{ number_format((float) $contribution->amount, 0, ',', '.') }}</p>
                                                    <p class="mt-1 text-xs" style="color:#64748B !important;">{{ $contribution->contribution_date->translatedFormat('d F Y') }}</p>
                                                </div>
                                            </div>
                                            @if ($contribution->note)
                                                <p class="mt-2 text-xs leading-relaxed text-[#64748B]"><span class="font-bold text-white">Catatan:</span> {{ $contribution->note }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
