<div
    class="space-y-4"
    x-data="{
        supported: false,
        listening: false,
        errorMessage: '',
        statusMessage: 'Mikrofon siap digunakan',
        recognition: null,
        init() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.supported = Boolean(SpeechRecognition);

            if (! this.supported) {
                this.statusMessage = 'Browser belum mendukung Web Speech API. Kamu bisa ketik teks langsung di bawah.';
                return;
            }

            try {
                this.recognition = new SpeechRecognition();
                this.recognition.lang = 'id-ID';
                this.recognition.interimResults = false;
                this.recognition.continuous = false;
                this.recognition.maxAlternatives = 1;

                this.recognition.onstart = () => {
                    this.listening = true;
                    this.errorMessage = '';
                    this.statusMessage = 'Mendengarkan ucapanmu...';
                };

                this.recognition.onresult = (event) => {
                    const text = event.results[0][0].transcript;
                    this.$wire.set('originalTranscript', text);
                    this.$wire.set('transcript', text);
                    this.statusMessage = 'Suara berhasil dibaca!';
                    this.$wire.parse();
                };

                this.recognition.onerror = (event) => {
                    this.listening = false;
                    this.errorMessage = this.errorText(event.error);
                    this.statusMessage = 'Suara belum berhasil dibaca';
                };

                this.recognition.onend = () => {
                    this.listening = false;
                };
            } catch (err) {
                this.supported = false;
                this.statusMessage = 'Input suara tidak tersedia. Silakan gunakan input teks manual di bawah.';
            }
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
        errorText(error) {
            if (error === 'not-allowed' || error === 'service-not-allowed') {
                return 'Izin mikrofon belum aktif. Pastikan izin mikrofon diizinkan di browser Anda.';
            }

            if (error === 'no-speech') {
                return 'Suara tidak terdengar. Coba bicara lebih dekat ke mikrofon.';
            }

            if (error === 'audio-capture') {
                return 'Mikrofon tidak ditemukan. Periksa perangkat mikrofon kamu.';
            }

            if (error === 'network') {
                return 'Koneksi ke speech recognition terputus. Pastikan perangkat terhubung internet.';
            }

            return 'Terjadi kendala saat membaca suara (' + error + '). Silakan coba lagi.';
        }
    }"
>
    <!-- Warning if Household Has No Categories -->
    @if (! $hasCategories)
        <div class="rounded-2xl border border-amber-200/80 bg-amber-50/90 p-4 shadow-xs">
            <div class="flex items-center gap-2">
                <span class="text-xl">🏷️</span>
                <p class="text-xs font-bold text-amber-900">Belum ada kategori.</p>
            </div>
            <p class="mt-1 text-xs text-amber-800 leading-relaxed">Input suara mencocokkan ucapanmu ke kategori yang ada. Buat minimal satu kategori dulu supaya transaksi bisa disimpan.</p>
            <a href="{{ route('categories') }}" class="mt-3 inline-flex min-h-[44px] items-center rounded-xl bg-amber-500 hover:bg-amber-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition active:scale-95">
                Buat Kategori
            </a>
        </div>
    @endif

    <!-- AI Smart Listener Hero Card -->
    <section class="bg-white rounded-[28px] p-6 shadow-sm border border-black/5 text-center relative overflow-hidden">
        <!-- Ambient Sunset Glow Blur -->
        <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-48 h-48 bg-gradient-to-br from-[#FF6B6B]/20 via-[#FF8E53]/15 to-transparent rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col items-center">
            <!-- AI Smart Listener Badge -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold tracking-wider uppercase bg-amber-500/10 text-amber-600 border border-amber-500/20 mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                AI SMART LISTENER
            </div>

            <!-- Dynamic Headline -->
            <h2 class="text-base font-extrabold text-[#121316] tracking-tight" x-text="listening ? 'Mendengarkan ucapanmu...' : 'Sentuh mikrofon untuk bicara'"></h2>
            <p class="mt-1 text-xs text-[#8E8E93]" x-text="statusMessage"></p>

            <!-- Voice Audio Waveform Glow -->
            <div class="flex items-center justify-center gap-1.5 h-10 my-3">
                <span class="w-1.5 rounded-full bg-gradient-to-t from-[#FF6B6B] to-[#FFA07A] transition-all" :class="listening ? 'animate-wave-1' : 'h-2 opacity-40'"></span>
                <span class="w-1.5 rounded-full bg-gradient-to-t from-[#FF6B6B] to-[#FFA07A] transition-all" :class="listening ? 'animate-wave-2' : 'h-4 opacity-50'"></span>
                <span class="w-1.5 rounded-full bg-gradient-to-t from-[#FF6B6B] to-[#FFA07A] transition-all" :class="listening ? 'animate-wave-3' : 'h-6 opacity-60'"></span>
                <span class="w-1.5 rounded-full bg-gradient-to-t from-[#FF6B6B] to-[#FFA07A] transition-all" :class="listening ? 'animate-wave-4' : 'h-5 opacity-60'"></span>
                <span class="w-1.5 rounded-full bg-gradient-to-t from-[#FF6B6B] to-[#FFA07A] transition-all" :class="listening ? 'animate-wave-5' : 'h-3 opacity-50'"></span>
                <span class="w-1.5 rounded-full bg-gradient-to-t from-[#FF6B6B] to-[#FFA07A] transition-all" :class="listening ? 'animate-wave-6' : 'h-2 opacity-40'"></span>
            </div>

            <!-- Central Hero Mic Button -->
            <button
                type="button"
                x-on:click="startListening"
                x-bind:disabled="! supported || listening"
                class="flex h-24 w-24 items-center justify-center rounded-full text-white shadow-xl transition-all active:scale-95 disabled:cursor-not-allowed disabled:opacity-60 my-2"
                x-bind:class="listening ? 'bg-gradient-to-br from-[#FF6B6B] to-[#EF4444] shadow-[#FF6B6B]/40 ring-8 ring-[#FF6B6B]/20 scale-105 animate-pulse' : 'bg-gradient-to-br from-[#FF6B6B] via-[#FF8E53] to-[#FFA07A] shadow-[0_10px_28px_rgba(255,107,107,0.35)] ring-8 ring-[#FF6B6B]/15 hover:scale-105'"
                aria-label="Mulai bicara"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18.75a6 6 0 006-6v-1.5m-12 0v1.5a6 6 0 006 6zm0 0v3m-3 0h6M12 15a3 3 0 003-3V6a3 3 0 10-6 0v6a3 3 0 003 3z" />
                </svg>
            </button>

            <!-- Error Notification -->
            <template x-if="errorMessage">
                <div class="mt-3 rounded-2xl bg-rose-50 border border-rose-100 px-4 py-2.5 text-left text-xs font-medium text-rose-600 w-full" x-text="errorMessage"></div>
            </template>

            <!-- Bottom Action Trigger -->
            <button
                type="button"
                x-on:click="startListening"
                x-bind:disabled="! supported || listening"
                class="mt-4 w-full min-h-[44px] rounded-2xl px-4 py-3 text-xs font-bold transition disabled:cursor-not-allowed disabled:opacity-60"
                x-bind:class="supported ? 'bg-[#121316] text-white hover:bg-black shadow-md shadow-black/10' : 'bg-slate-200 text-slate-500'"
                x-text="listening ? 'Mendengarkan...' : 'Mulai Bicara'"
            ></button>
        </div>
    </section>

    <!-- Real-time Speech Transcription Bubble -->
    <section class="bg-white rounded-2xl p-4 shadow-sm border border-black/5">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-1.5">
                <span class="text-sm">💬</span>
                <span class="text-xs font-bold text-[#121316]">Transkrip Ucapan</span>
            </div>
            @if ($transcript !== '')
                <button type="button" wire:click="clearTranscript" class="text-[11px] font-bold text-rose-500 hover:text-rose-600 transition">
                    Hapus
                </button>
            @endif
        </div>

        <div class="relative">
            <textarea
                wire:model.live.debounce.300ms="transcript"
                rows="2"
                class="block w-full rounded-xl border border-black/10 bg-[#FCF8F8] px-3.5 py-2.5 text-xs font-semibold leading-relaxed text-[#121316] placeholder:text-[#8E8E93]/70 focus:border-[#FF6B6B] focus:ring-1 focus:ring-[#FF6B6B]"
                placeholder="Beli bensin motor empat puluh ribu pakai GoPay"
            ></textarea>

            @error('transcript')
                <p class="mt-1 text-[11px] font-medium text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-2.5 flex items-center gap-2">
            @if ($originalTranscript && $transcript !== $originalTranscript)
                <button type="button" wire:click="resetToOriginal" class="flex-1 min-h-[40px] rounded-xl bg-slate-100 hover:bg-slate-200 px-3 py-2 text-[11px] font-bold text-[#8E8E93] transition">
                    Reset Suara
                </button>
            @endif
            <button type="button" wire:click="parse" class="flex-1 min-h-[40px] rounded-xl bg-gradient-to-r from-[#FF6B6B] to-[#FF8E53] hover:from-[#FF5A5A] hover:to-[#FF8040] text-white px-4 py-2 text-xs font-bold shadow-sm shadow-[#FF6B6B]/25 transition active:scale-95">
                Proses Teks
            </button>
        </div>
    </section>

    <!-- Parsed Data Confirmation Card -->
    @if ($parsedDraft)
        @php
            $draftIsIncome = ($parsedDraft['type'] ?? \App\Models\Transaction::TYPE_EXPENSE) === \App\Models\Transaction::TYPE_INCOME;
        @endphp
        <section class="bg-white rounded-[28px] p-5 shadow-sm border border-black/5 space-y-4">
            <!-- Header with Type Badge -->
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div>
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#8E8E93]">Hasil Ekstraksi AI</h3>
                    <p class="text-sm font-bold text-[#121316] mt-0.5">Konfirmasi Transaksi</p>
                </div>
                
                @if ($draftIsIncome)
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Pemasukan
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        Pengeluaran
                    </span>
                @endif
            </div>

            <!-- Warnings if any -->
            @if (! empty($parsedDraft['warnings']))
                <div class="rounded-xl bg-amber-50 border border-amber-200/80 px-3.5 py-2.5">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-amber-700">Perlu Dicek</p>
                    <ul class="mt-1 space-y-0.5 text-xs text-amber-800">
                        @foreach ($parsedDraft['warnings'] as $warning)
                            <li>• {{ $warning }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Confirmation Fields -->
            <div class="space-y-3">
                <!-- Nominal Display -->
                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-[#FCF8F8] border border-black/5">
                    <span class="text-xs font-semibold text-[#8E8E93]">Nominal</span>
                    @if ($parsedDraft['amount'])
                        <x-money :value="$parsedDraft['amount']" class="text-xl font-black text-[#121316] font-tabular tracking-tight" />
                    @else
                        <span class="text-xs font-bold text-rose-500">Belum terbaca</span>
                    @endif
                </div>

                <!-- Category & Account Tags -->
                <div class="grid grid-cols-2 gap-2.5">
                    <div class="p-3 rounded-2xl bg-[#FCF8F8] border border-black/5">
                        <span class="text-[10px] font-bold text-[#8E8E93] uppercase tracking-wider block mb-1.5">Kategori</span>
                        @if (!empty($parsedDraft['category_name']))
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-[#FF6B6B]/10 text-[#FF6B6B] border border-[#FF6B6B]/20">
                                🏷️ {{ $parsedDraft['category_name'] }}
                            </span>
                        @else
                            <span class="text-xs font-semibold text-[#8E8E93]">Belum ada</span>
                        @endif
                    </div>

                    <div class="p-3 rounded-2xl bg-[#FCF8F8] border border-black/5">
                        <span class="text-[10px] font-bold text-[#8E8E93] uppercase tracking-wider block mb-1.5">Metode / Akun</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                            GoPay
                        </span>
                    </div>
                </div>

                <!-- Notes & Date -->
                <div class="p-3 rounded-2xl bg-[#FCF8F8] border border-black/5 space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-[#8E8E93]">Catatan:</span>
                        <span class="font-bold text-[#121316] text-right truncate max-w-[200px]">{{ $parsedDraft['note'] ?: 'Belum dikenali' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-1 border-t border-black/5">
                        <span class="font-semibold text-[#8E8E93]">Tanggal:</span>
                        <span class="font-medium text-[#121316] font-tabular">{{ $parsedDraft['transaction_date'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Error message for draft -->
            @error('parsedDraft')
                <div class="rounded-xl bg-rose-50 border border-rose-100 px-3.5 py-2.5 text-xs font-semibold text-rose-600">
                    {{ $message }}
                </div>
            @enderror

            <!-- Action Buttons: Batalkan & Konfirmasi -->
            <div class="grid grid-cols-2 gap-3 pt-2">
                <button
                    type="button"
                    wire:click="clearTranscript"
                    x-on:click="if (typeof showVoiceModal !== 'undefined') showVoiceModal = false;"
                    class="min-h-[44px] rounded-2xl bg-black/5 hover:bg-black/10 text-xs font-bold text-[#8E8E93] hover:text-[#121316] transition flex items-center justify-center active:scale-95"
                >
                    Batalkan
                </button>
                <button
                    type="button"
                    wire:click="save"
                    @disabled(! $this->canSave)
                    class="min-h-[44px] rounded-2xl px-4 py-2.5 text-xs font-bold transition flex items-center justify-center gap-1.5 active:scale-95 {{ $this->canSave ? 'bg-[#121316] hover:bg-black text-white shadow-md shadow-black/10' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}"
                >
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                    <span>Konfirmasi</span>
                </button>
            </div>
        </section>
    @endif

    <!-- Guidance Examples Pill Accordion -->
    <div class="bg-white/80 rounded-2xl p-4 border border-black/5 shadow-2xs">
        <span class="text-[11px] font-bold text-[#FF6B6B] uppercase tracking-wider block mb-1.5">Tips Ucapan AI</span>
        <div class="space-y-1 text-xs text-[#8E8E93]">
            <p>• “Beli bensin motor 40 ribu kategori transportasi”</p>
            <p>• “Beli nasi padang dua puluh lima ribu kategori makanan”</p>
        </div>
    </div>
</div>

