<div class="space-y-5 lg:max-w-5xl lg:mx-auto">
    @if (session('transaction_status'))
        <div class="rounded-2xl bg-[#1d9e75]/10 border border-[#1d9e75]/25 px-4 py-3 text-sm font-medium text-[#1d9e75]">
            {{ session('transaction_status') }}
        </div>
    @endif

    <section class="rounded-3xl p-4" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
        <div class="mb-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-[#A3E635]">Input Manual</p>
            <h2 class="text-lg font-bold text-white">Tambah transaksi</h2>
            <p class="mt-1 text-xs text-[#64748B]">Catat pemasukan atau pengeluaran dengan cepat.</p>
        </div>

        <form method="POST" action="{{ route('transactions.store') }}" wire:submit="save" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-white mb-1">Jenis transaksi</label>
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="mt-2 grid grid-cols-2 gap-2 rounded-2xl p-1" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;">
                    <button type="button" wire:click="$set('type', 'expense')" class="rounded-xl px-3 py-2 text-sm font-bold transition {{ $type === 'expense' ? 'bg-[#F87171] text-[#0A1628] shadow-md shadow-rose-900/20' : 'text-[#64748B] hover:text-white' }}">
                        Pengeluaran
                    </button>
                    <button type="button" wire:click="$set('type', 'income')" class="rounded-xl px-3 py-2 text-sm font-bold transition {{ $type === 'income' ? 'bg-[#A3E635] text-[#0A1628] shadow-md shadow-lime-900/20' : 'text-[#64748B] hover:text-white' }}">
                        Pemasukan
                    </button>
                </div>
                @error('type')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="amount" class="block text-xs font-semibold text-white mb-1">Nominal</label>
                <div class="mt-2 flex rounded-xl border border-[rgba(255,255,255,0.1)] bg-[#1A2B52] shadow-sm focus-within:border-[#A3E635] focus-within:ring-2 focus-within:ring-[#A3E635]/20">
                    <span class="flex items-center px-4 text-sm font-bold text-[#64748B]">Rp</span>
                    <input id="amount" name="amount" type="text" inputmode="numeric" wire:model="amount" class="block w-full rounded-r-xl border-0 bg-transparent px-2 py-3 text-sm placeholder-[#64748B] focus:ring-0 outline-none" style="color:#FFFFFF !important;" placeholder="50.000">
                </div>
                @error('amount')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category_id" class="block text-xs font-semibold text-white mb-1">Kategori</label>
                @if ($categories->isEmpty())
                    <div class="mt-2 rounded-2xl border border-[#F59E0B]/25 bg-[#F59E0B]/10 p-4">
                        <p class="text-sm font-bold text-[#F59E0B]">Belum ada kategori {{ $type === 'expense' ? 'pengeluaran' : 'pemasukan' }}.</p>
                        <p class="mt-1 text-xs text-[#F59E0B]">Buat kategori dulu agar transaksi bisa dicatat rapi.</p>
                        <a href="{{ route('categories') }}" class="mt-3 inline-flex rounded-full bg-[#1A2B52] border border-[#A3E635]/20 px-4 py-2 text-xs font-bold text-[#A3E635]">
                            Atur kategori
                        </a>
                    </div>
                @else
                    <select id="category_id" name="category_id" wire:model="category_id" class="mt-2 block w-full rounded-xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:bg-[#1A2B52] focus:ring-2 focus:ring-[#A3E635]/20 [color-scheme:dark]" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;color:#FFFFFF !important;">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                        @endforeach
                    </select>
                @endif
                @error('category_id')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="transaction_date" class="block text-xs font-semibold text-white mb-1">Tanggal</label>
                <input id="transaction_date" name="transaction_date" type="date" wire:model="transaction_date" class="mt-2 block w-full rounded-xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:bg-[#1A2B52] focus:ring-2 focus:ring-[#A3E635]/20 [color-scheme:dark]" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;color:#FFFFFF !important;">
                @error('transaction_date')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="note" class="block text-xs font-semibold text-white mb-1">Catatan <span class="font-normal text-[#64748B]">opsional</span></label>
                <textarea id="note" name="note" wire:model="note" rows="3" class="mt-2 block w-full rounded-xl px-4 py-3 text-sm placeholder-[#64748B] shadow-sm focus:border-[#A3E635] focus:bg-[#1A2B52] focus:ring-2 focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;color:#FFFFFF !important;" placeholder="Contoh: makan siang keluarga"></textarea>
                @error('note')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full rounded-2xl px-4 py-3 text-sm font-bold shadow-lg shadow-lime-500/20 transition active:scale-[0.99] disabled:opacity-60" style="background:#A3E635 !important;color:#0A1628 !important;" @disabled($categories->isEmpty())>
                Simpan Transaksi
            </button>
        </form>
    </section>

    <a href="{{ route('dashboard') }}" class="block text-center text-sm font-bold text-[#A3E635] hover:text-[#4d4dff]">
        Kembali ke Beranda
    </a>
</div>
