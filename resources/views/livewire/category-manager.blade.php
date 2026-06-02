<div class="space-y-5">
    @if (session('category_status'))
        <div class="rounded-2xl bg-[#A3E635]/10 border border-emerald-100 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('category_status') }}
        </div>
    @endif

    <section class="rounded-3xl p-4" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
        <div class="mb-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-[#A3E635]">
                {{ $editingCategory ? 'Edit Kategori' : 'Kategori Baru' }}
            </p>
            <h2 class="text-lg font-bold text-white">Atur kategori transaksi</h2>
            <p class="mt-1 text-xs text-[#64748B]">Kategori ini akan dipakai untuk input manual dan suara nanti.</p>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-semibold" style="color:#FFFFFF !important;">Nama kategori</label>
                <input id="name" type="text" wire:model="name" class="mt-2 block w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;" placeholder="Contoh: Makanan">
                @error('name')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold" style="color:#FFFFFF !important;">Jenis</label>
                <div class="mt-2 grid grid-cols-2 gap-2 rounded-2xl bg-[#1A2B52] p-1">
                    <button type="button" wire:click="$set('type', 'expense')" class="rounded-xl px-3 py-2 text-sm font-bold transition {{ $type === 'expense' ? 'bg-[#F87171] text-[#0A1628] shadow-sm' : 'text-[#64748B]' }}">
                        Pengeluaran
                    </button>
                    <button type="button" wire:click="$set('type', 'income')" class="rounded-xl px-3 py-2 text-sm font-bold transition {{ $type === 'income' ? 'bg-[#A3E635] text-[#0A1628] shadow-sm' : 'text-[#64748B]' }}">
                        Pemasukan
                    </button>
                </div>
                @error('type')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="icon" class="block text-sm font-semibold" style="color:#FFFFFF !important;">Ikon</label>
                    <input id="icon" type="text" wire:model="icon" class="mt-2 block w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;" placeholder="utensils">
                    @error('icon')
                        <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="color" class="block text-sm font-semibold" style="color:#FFFFFF !important;">Warna</label>
                    <input id="color" type="text" wire:model="color" class="mt-2 block w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;" placeholder="#6366f1">
                    @error('color')
                        <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-2xl px-4 py-3 text-sm font-bold shadow-lg shadow-lime-500/20 transition active:scale-[0.99]" style="background:#A3E635 !important;color:#0A1628 !important;">
                    {{ $editingCategory ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                </button>

                @if ($editingCategory)
                    <button type="button" wire:click="resetForm" class="rounded-2xl px-4 py-3 text-sm font-bold" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;color:#64748B !important;">
                        Batal
                    </button>
                @endif
            </div>
        </form>
    </section>

    @php
        $categoryIconMap = [
            'makanan' => ['icon' => 'utensils', 'color' => '#F87171'],
            'transportasi' => ['icon' => 'car', 'color' => '#60A5FA'],
            'belanja dapur' => ['icon' => 'shopping-cart', 'color' => '#FCD34D'],
            'tagihan' => ['icon' => 'receipt', 'color' => '#FB923C'],
            'kesehatan' => ['icon' => 'heart-pulse', 'color' => '#F472B6'],
            'pendidikan' => ['icon' => 'graduation-cap', 'color' => '#A3E635'],
            'hiburan' => ['icon' => 'gamepad-2', 'color' => '#C084FC'],
            'gaji' => ['icon' => 'banknote', 'color' => '#A3E635'],
            'bonus' => ['icon' => 'gift', 'color' => '#FCD34D'],
            'lainnya' => ['icon' => 'more-horizontal', 'color' => '#94A3B8'],
        ];
    @endphp

    <section class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-white">Kategori Pengeluaran</h2>
            <span class="rounded-full bg-[#e24b4a]/10 px-3 py-1 text-xs font-bold text-[#F87171]">{{ $expenseCategories->count() }}</span>
        </div>

        <div class="space-y-2">
            @foreach ($expenseCategories as $category)
                @php
                    $categoryIcon = $categoryIconMap[strtolower($category->name)] ?? ['icon' => 'more-horizontal', 'color' => '#94A3B8'];
                @endphp
                <div wire:key="expense-{{ $category->id }}" class="rounded-2xl p-3 flex items-center justify-between gap-3" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:20px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
                    <div class="flex items-center gap-3 min-w-0">
                        <div style="width:38px;height:38px;background:#1A2B52;border-radius:10px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(163,230,53,0.15);flex-shrink:0;">
                            <i data-lucide="{{ $categoryIcon['icon'] }}" style="width:18px;height:18px;color:{{ $categoryIcon['color'] }};"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-white">{{ $category->name }}</p>
                            <p class="text-xs text-[#64748B]">Pengeluaran</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="edit({{ $category->id }})" class="text-xs font-bold text-[#A3E635]">Edit</button>
                        <button type="button" wire:click="delete({{ $category->id }})" wire:confirm="Hapus kategori ini?" class="text-xs font-bold text-[#F87171]">Hapus</button>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-white">Kategori Pemasukan</h2>
            <span class="rounded-full bg-[#A3E635]/10 px-3 py-1 text-xs font-bold text-[#A3E635]">{{ $incomeCategories->count() }}</span>
        </div>

        <div class="space-y-2">
            @foreach ($incomeCategories as $category)
                @php
                    $categoryIcon = $categoryIconMap[strtolower($category->name)] ?? ['icon' => 'more-horizontal', 'color' => '#94A3B8'];
                @endphp
                <div wire:key="income-{{ $category->id }}" class="rounded-2xl p-3 flex items-center justify-between gap-3" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:20px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
                    <div class="flex items-center gap-3 min-w-0">
                        <div style="width:38px;height:38px;background:#1A2B52;border-radius:10px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(163,230,53,0.15);flex-shrink:0;">
                            <i data-lucide="{{ $categoryIcon['icon'] }}" style="width:18px;height:18px;color:{{ $categoryIcon['color'] }};"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-white">{{ $category->name }}</p>
                            <p class="text-xs text-[#64748B]">Pemasukan</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="edit({{ $category->id }})" class="text-xs font-bold text-[#A3E635]">Edit</button>
                        <button type="button" wire:click="delete({{ $category->id }})" wire:confirm="Hapus kategori ini?" class="text-xs font-bold text-[#F87171]">Hapus</button>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
