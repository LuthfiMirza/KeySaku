<div
    class="px-4 pt-6 space-y-5 pb-4"
    x-data="{
        supported: false,
        listening: false,
        originalTranscript: @entangle('originalTranscript').live,
        transcript: @entangle('transcript').live,
        errorMessage: '',
        statusMessage: 'Mikrofon siap digunakan',
        recognition: null,
        init() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.supported = Boolean(SpeechRecognition);

            if (! this.supported) {
                this.statusMessage = 'Browser ini belum mendukung input suara. Gunakan Chrome atau input manual.';
                return;
            }

            this.recognition = new SpeechRecognition();
            this.recognition.lang = 'id-ID';
            this.recognition.interimResults = false;
            this.recognition.continuous = false;
            this.recognition.maxAlternatives = 1;

            this.recognition.onstart = () => {
                this.listening = true;
                this.errorMessage = '';
                this.statusMessage = 'Mendengarkan...';
            };

            this.recognition.onresult = (event) => {
                this.originalTranscript = event.results[0][0].transcript;
                this.transcript = this.originalTranscript;
                this.statusMessage = 'Suara berhasil dibaca';
                this.$wire.clearParsedDraft();
            };

            this.recognition.onerror = (event) => {
                this.errorMessage = this.errorText(event.error);
                this.statusMessage = 'Suara belum berhasil dibaca';
            };

            this.recognition.onend = () => {
                this.listening = false;
            };
        },
        startListening() {
            if (! this.supported || ! this.recognition || this.listening) {
                return;
            }

            this.errorMessage = '';

            try {
                this.recognition.start();
            } catch (error) {
                this.errorMessage = 'Mikrofon belum siap. Coba beberapa detik lagi.';
                this.statusMessage = 'Suara belum berhasil dibaca';
                this.listening = false;
            }
        },
        resetToOriginal() {
            this.transcript = this.originalTranscript;
            this.$wire.clearParsedDraft();
        },
        clearTranscript() {
            this.originalTranscript = '';
            this.transcript = '';
            this.errorMessage = '';
            this.statusMessage = this.supported ? 'Mikrofon siap digunakan' : 'Browser ini belum mendukung input suara. Gunakan Chrome atau input manual.';
            this.$wire.clearParsedDraft();
        },
        errorText(error) {
            if (error === 'not-allowed' || error === 'service-not-allowed') {
                return 'Izin mikrofon ditolak. Aktifkan izin mikrofon di browser atau gunakan input manual.';
            }

            if (error === 'no-speech') {
                return 'Suara tidak terdengar. Coba bicara lebih dekat ke mikrofon.';
            }

            if (error === 'audio-capture') {
                return 'Mikrofon tidak ditemukan. Periksa perangkat mikrofon kamu.';
            }

            return 'Terjadi kendala saat membaca suara. Silakan coba lagi.';
        }
    }"
>
    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-slate-800">Input Suara</h2>
            <p class="mt-1 text-xs text-slate-400">Catat pengeluaran secepat bicara.</p>
        </div>
        <span class="rounded-full bg-purple-50 px-3 py-2 text-xs font-bold text-purple-600">Beta</span>
    </div>

    <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60 text-center overflow-hidden relative">
        <div class="absolute inset-x-0 -top-20 mx-auto h-40 w-40 rounded-full bg-purple-200/40 blur-3xl"></div>

        <div class="relative">
            <button
                type="button"
                x-on:click="startListening"
                x-bind:disabled="! supported || listening"
                class="mx-auto flex h-28 w-28 items-center justify-center rounded-full text-white shadow-2xl ring-8 transition active:scale-95 disabled:cursor-not-allowed disabled:opacity-70"
                x-bind:class="listening ? 'bg-gradient-to-br from-rose-500 to-pink-600 shadow-rose-500/30 ring-rose-100/80 animate-pulse' : 'bg-gradient-to-br from-indigo-500 to-purple-600 shadow-indigo-500/30 ring-indigo-100/80'"
                aria-label="Mulai bicara"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18.75a6 6 0 006-6v-1.5m-12 0v1.5a6 6 0 006 6zm0 0v3m-3 0h6M12 15a3 3 0 003-3V6a3 3 0 10-6 0v6a3 3 0 003 3z" />
                </svg>
            </button>

            <p class="mt-5 text-sm font-bold text-slate-800" x-text="statusMessage"></p>
            <p class="mt-1 text-xs text-slate-400" x-show="supported">Tekan tombol saat siap bicara. Izin mikrofon baru diminta setelah tombol ditekan.</p>
            <p class="mt-1 text-xs text-amber-600" x-show="! supported">Browser ini belum mendukung input suara. Gunakan Chrome atau input manual.</p>

            <template x-if="errorMessage">
                <div class="mt-4 rounded-2xl bg-rose-50 border border-rose-100 px-4 py-3 text-left text-xs font-medium text-rose-600" x-text="errorMessage"></div>
            </template>

            <button
                type="button"
                x-on:click="startListening"
                x-bind:disabled="! supported || listening"
                class="mt-5 w-full rounded-2xl px-4 py-3 text-sm font-bold transition disabled:cursor-not-allowed disabled:opacity-60"
                x-bind:class="supported ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-slate-200 text-slate-500'"
                x-text="transcript ? 'Mulai Bicara Lagi' : 'Mulai Bicara'"
            ></button>
        </div>
    </section>

    <section class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60">
        <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Contoh ucapan</p>
        <p class="mt-2 text-xs text-slate-400">Format saat ini: keterangan pengeluaran + nominal + kategori.</p>
        <div class="mt-3 space-y-2 text-sm font-semibold leading-relaxed text-slate-700">
            <p>“Beli nasi goreng dua puluh lima ribu kategori makanan”</p>
            <p>“Bayar parkir lima ribu kategori transportasi”</p>
            <p>“Belanja dapur 150.000 kategori belanja dapur”</p>
            <p>“Beli obat tiga puluh ribu kategori kesehatan”</p>
        </div>
        <p class="mt-3 text-xs text-slate-400">KeySaku akan menampilkan hasil suara untuk dicek sebelum disimpan.</p>
    </section>

    <section class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60">
        <div class="flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-xl">📝</span>
            <div class="min-w-0 flex-1">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-slate-800">Periksa Transkrip</p>
                        <p class="mt-1 text-xs text-slate-400">Perbaiki teks jika ada kata yang salah sebelum diproses.</p>
                    </div>
                    <button type="button" x-show="transcript" x-on:click="clearTranscript" class="shrink-0 text-xs font-bold text-rose-500">
                        Hapus Teks
                    </button>
                </div>

                <p class="mt-3 text-xs text-slate-400" x-show="! transcript">Hasil suara akan muncul di sini sebelum disimpan.</p>

                <div x-show="transcript" class="mt-3 space-y-3">
                    <textarea x-model="transcript" rows="4" class="block w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold leading-relaxed text-slate-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: Beli nasi goreng dua puluh lima ribu kategori makanan"></textarea>

                    @error('transcript')
                        <p class="text-xs font-medium text-rose-500">{{ $message }}</p>
                    @enderror

                    <button type="button" x-show="originalTranscript && transcript !== originalTranscript" x-on:click="resetToOriginal" class="w-full rounded-2xl bg-slate-100 px-4 py-3 text-xs font-bold text-slate-600">
                        Kembalikan ke Hasil Suara
                    </button>

                    <button type="button" wire:click="parse" class="w-full rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30">
                        Proses Teks
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60">
        <div class="flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-xl">🧾</span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-slate-800">Preview Transaksi</p>

                @if (! $parsedDraft)
                    <p class="mt-1 text-xs text-slate-400">Transkrip siap diproses.</p>
                @else
                    @if (! empty($parsedDraft['warnings']))
                        <div class="mt-3 rounded-2xl bg-amber-50 border border-amber-100 px-4 py-3">
                            <p class="text-xs font-bold uppercase tracking-wide text-amber-600">Perlu Dicek</p>
                            <ul class="mt-2 space-y-1 text-xs text-amber-700">
                                @foreach ($parsedDraft['warnings'] as $warning)
                                    <li>• {{ $warning }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-3 grid gap-2 text-sm">
                        <div class="flex items-center justify-between gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                            <span class="text-slate-500">Jenis</span>
                            <span class="font-bold text-rose-600">Pengeluaran</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                            <span class="text-slate-500">Nominal</span>
                            <span class="font-bold text-slate-800">{{ $parsedDraft['amount'] ? 'Rp '.number_format($parsedDraft['amount'], 0, ',', '.') : 'Belum dikenali' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                            <span class="text-slate-500">Kategori</span>
                            <span class="font-bold text-slate-800">{{ $parsedDraft['category_name'] ?? 'Belum ditemukan' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                            <span class="text-slate-500">Tanggal</span>
                            <span class="font-bold text-slate-800">{{ $parsedDraft['transaction_date'] }}</span>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <span class="text-slate-500">Catatan</span>
                            <p class="mt-1 font-bold text-slate-800">{{ $parsedDraft['note'] ?: 'Belum dikenali' }}</p>
                        </div>
                    </div>

                    @error('parsedDraft')
                        <p class="mt-3 rounded-2xl bg-rose-50 px-4 py-3 text-xs font-medium text-rose-600">{{ $message }}</p>
                    @enderror

                    <button type="button" wire:click="save" @disabled(! $this->canSave) class="mt-3 w-full rounded-2xl px-4 py-3 text-sm font-bold transition {{ $this->canSave ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-slate-200 text-slate-500 cursor-not-allowed' }}">
                        Simpan Transaksi
                    </button>

                    @if (! $this->canSave)
                        <p class="mt-2 text-xs text-slate-400">Lengkapi nominal, kategori, dan catatan sebelum menyimpan.</p>
                    @endif
                @endif
            </div>
        </div>
    </section>

    <section class="bg-indigo-50/80 rounded-2xl p-4 border border-indigo-100">
        <p class="text-sm font-bold text-indigo-800">Belum bisa pakai suara?</p>
        <p class="mt-1 text-xs text-indigo-600">Input manual tetap tersedia sebagai fallback utama.</p>
        <a href="{{ route('transactions.create') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-2xl bg-indigo-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30">
            Input Manual
        </a>
    </section>
</div>
