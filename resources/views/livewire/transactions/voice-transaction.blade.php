<div
    class="px-4 pt-6 space-y-5 pb-4"
    x-data="{
        state: @js($transcript ? 'done' : 'idle'),
        supported: false,
        listening: false,
        originalTranscript: @js($originalTranscript),
        transcript: @js($transcript),
        errorMessage: '',
        statusMessage: 'Mikrofon siap digunakan',
        recognition: null,
        mediaStream: null,
        livewireComponent() {
            const componentId = this.$root.getAttribute('wire:id');

            return componentId && window.Livewire ? window.Livewire.find(componentId) : null;
        },
        wireSet(property, value, defer = false) {
            const component = this.livewireComponent();

            if (component) {
                return component.set(property, value, defer);
            }

            return Promise.resolve();
        },
        wireCall(method) {
            const component = this.livewireComponent();

            if (component) {
                return component.call(method);
            }

            return Promise.resolve();
        },
        init() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.supported = Boolean(SpeechRecognition);

            if (! this.supported) {
                this.statusMessage = 'Browser ini belum mendukung input suara. Gunakan Chrome atau input manual.';
                return;
            }

            this.recognition = new SpeechRecognition();
            this.recognition.lang = 'id-ID';
            this.recognition.interimResults = true;
            this.recognition.continuous = false;
            this.recognition.maxAlternatives = 1;

            this.recognition.onstart = () => {
                this.state = 'listening';
                this.listening = true;
                this.errorMessage = '';
                this.statusMessage = 'Mendengarkan...';
            };

            this.recognition.onresult = (event) => {
                const result = event.results[event.results.length - 1];
                const text = result[0].transcript.trim();

                if (! text) {
                    return;
                }

                this.transcript = text;
                this.wireSet('transcript', text, false);

                if (result.isFinal) {
                    this.state = 'done';
                    this.originalTranscript = text;
                    this.wireSet('originalTranscript', text, false);
                    this.statusMessage = 'Suara berhasil dibaca';
                    this.wireCall('clearParsedDraft');
                } else {
                    this.statusMessage = 'Suara mulai terbaca... lanjut bicara sampai selesai';
                }
            };

            this.recognition.onerror = (event) => {
                this.errorMessage = this.errorText(event.error);
                this.statusMessage = 'Suara belum berhasil dibaca';
            };

            this.recognition.onend = () => {
                this.listening = false;
                this.stopMediaStream();

                if (! this.transcript && ! this.errorMessage) {
                    this.state = 'idle';
                    this.errorMessage = 'Belum ada suara yang terbaca. Pastikan izin mikrofon aktif, lalu bicara setelah status Mendengarkan.';
                    this.statusMessage = 'Suara belum berhasil dibaca';
                } else if (this.transcript) {
                    this.state = 'done';
                } else {
                    this.state = 'idle';
                }
            };
        },
        async startListening() {
            if (! this.supported || ! this.recognition || this.listening) {
                return;
            }

            this.state = 'listening';
            this.errorMessage = '';
            this.statusMessage = 'Memeriksa izin mikrofon...';

            try {
                await this.requestMicrophoneAccess();
                this.recognition.start();
            } catch (error) {
                this.errorMessage = this.microphoneErrorText(error);
                this.state = this.transcript ? 'done' : 'idle';
                this.statusMessage = 'Suara belum berhasil dibaca';
                this.listening = false;
                this.stopMediaStream();
            }
        },
        async requestMicrophoneAccess() {
            if (! navigator.mediaDevices || ! navigator.mediaDevices.getUserMedia) {
                return;
            }

            this.mediaStream = await navigator.mediaDevices.getUserMedia({ audio: true });
        },
        stopMediaStream() {
            if (! this.mediaStream) {
                return;
            }

            this.mediaStream.getTracks().forEach((track) => track.stop());
            this.mediaStream = null;
        },
        resetToOriginal() {
            this.state = this.originalTranscript ? 'done' : 'idle';
            this.transcript = this.originalTranscript;
            this.wireSet('transcript', this.originalTranscript, false);
            this.wireCall('clearParsedDraft');
        },
        async processTranscript() {
            await this.wireSet('transcript', this.transcript || '');
            await this.wireCall('parse');
        },
        clearTranscript() {
            this.state = 'idle';
            this.originalTranscript = '';
            this.transcript = '';
            this.wireSet('originalTranscript', '', false);
            this.wireSet('transcript', '', false);
            this.errorMessage = '';
            this.statusMessage = this.supported ? 'Mikrofon siap digunakan' : 'Browser ini belum mendukung input suara. Gunakan Chrome atau input manual.';
            this.wireCall('clearParsedDraft');
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

            if (error === 'network') {
                return 'Layanan pengenal suara browser sedang gagal terhubung. Periksa internet, lalu coba lagi.';
            }

            if (error === 'aborted') {
                return 'Perekaman suara dibatalkan. Tekan tombol Mulai Bicara lalu ucapkan transaksi sampai selesai.';
            }

            if (error === 'language-not-supported') {
                return 'Bahasa Indonesia belum didukung oleh pengenal suara browser ini. Gunakan Chrome terbaru atau input manual.';
            }

            return `Terjadi kendala saat membaca suara (${error}). Silakan coba lagi atau gunakan input manual.`;
        },
        microphoneErrorText(error) {
            if (error instanceof DOMException && (error.name === 'NotAllowedError' || error.name === 'SecurityError')) {
                return 'Izin mikrofon belum aktif. Klik ikon gembok di address bar, izinkan Mikrofon, lalu muat ulang halaman.';
            }

            if (error instanceof DOMException && error.name === 'NotFoundError') {
                return 'Mikrofon tidak ditemukan. Periksa perangkat/input mikrofon di browser atau sistem.';
            }

            if (error instanceof DOMException && error.name === 'NotReadableError') {
                return 'Mikrofon sedang dipakai aplikasi lain. Tutup aplikasi perekam/meeting, lalu coba lagi.';
            }

            return 'Mikrofon belum siap. Cek izin mikrofon browser, pastikan tidak diblokir, lalu coba lagi.';
        }
    }"
>
    <div style="text-align:center;padding:28px 20px 16px;">
        <p style="color:#64748B;font-size:11px;letter-spacing:0.08em;margin:0 0 6px 0;">INPUT SUARA</p>
        <p style="color:#fff;font-size:18px;font-weight:700;margin:0 0 4px 0;">Catat dengan Bicara</p>
        <p style="color:#64748B;font-size:13px;margin:0;">Tekan tombol dan ucapkan transaksimu</p>
    </div>

    <section class="rounded-3xl text-center overflow-hidden relative" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
        <div class="pointer-events-none absolute inset-x-0 -top-20 z-0 mx-auto h-40 w-40 rounded-full bg-purple-200/40 blur-3xl" aria-hidden="true"></div>

        <div class="relative z-10" style="display:flex;flex-direction:column;align-items:center;gap:20px;padding:16px 20px;">
            <button
                type="button"
                x-on:click.prevent="startListening()"
                x-bind:disabled="listening"
                class="transition active:scale-95 disabled:cursor-wait disabled:opacity-80"
                style="position:relative;z-index:20;width:130px;height:130px;display:flex;align-items:center;justify-content:center;background:transparent;border:none;padding:0;cursor:pointer;pointer-events:auto;"
                x-bind:class="listening ? 'animate-pulse' : ''"
                aria-label="Mulai bicara"
            >
                <div style="position:absolute;width:130px;height:130px;border-radius:50%;background:rgba(163,230,53,0.05);border:1px solid rgba(163,230,53,0.1);pointer-events:none;"></div>
                <div style="position:absolute;width:98px;height:98px;border-radius:50%;background:rgba(163,230,53,0.08);border:1px solid rgba(163,230,53,0.15);pointer-events:none;"></div>
                <div style="position:relative;z-index:10;cursor:pointer;pointer-events:auto;width:68px;height:68px;border-radius:50%;background:#A3E635;display:flex;align-items:center;justify-content:center;box-shadow:0 0 28px rgba(163,230,53,0.35);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#0A1628" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
                        <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                        <line x1="12" y1="19" x2="12" y2="22"/>
                        <line x1="8" y1="22" x2="16" y2="22"/>
                    </svg>
                </div>
            </button>

            <div style="text-align:center;">
                <p style="color:#fff;font-size:15px;font-weight:600;margin:0 0 4px 0;" x-text="statusMessage">Mikrofon siap digunakan</p>
                <p style="color:#64748B;font-size:12px;margin:0;" x-show="supported">Tekan tombol untuk mulai merekam</p>
            </div>
            <p class="mt-1 text-xs text-[#F59E0B]" x-show="! supported">Browser ini belum mendukung input suara. Gunakan Chrome atau input manual.</p>

            <template x-if="errorMessage">
                <div class="w-full rounded-2xl bg-[#F87171]/10 border border-[#F87171]/20 px-4 py-3 text-left text-xs font-medium text-[#F87171]">
                    <p x-text="errorMessage"></p>
                    <p class="mt-2 text-[#64748B]">Tips: gunakan Chrome, izinkan mikrofon, bicara setelah status “Mendengarkan...”, dan pastikan koneksi internet aktif.</p>
                </div>
            </template>

            <button
                type="button"
                x-on:click.prevent="startListening()"
                x-bind:disabled="listening"
                class="transition disabled:cursor-not-allowed disabled:opacity-60"
                style="position:relative;z-index:20;width:100%;background:#A3E635 !important;border-radius:14px !important;padding:16px !important;display:flex !important;align-items:center !important;justify-content:center !important;gap:10px !important;border:none !important;cursor:pointer !important;color:#0A1628 !important;font-size:15px !important;font-weight:700 !important;box-shadow:0 4px 16px rgba(163,230,53,0.35) !important;pointer-events:auto;"
            >
                <svg xmlns="http://www.w3.org/2000/svg"
                     width="18" height="18"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="#0A1628"
                     stroke-width="2.5"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     aria-hidden="true">
                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                    <line x1="12" y1="19" x2="12" y2="22"/>
                    <line x1="8" y1="22" x2="16" y2="22"/>
                </svg>
                <span style="color:#0A1628;font-size:15px;font-weight:700;" x-text="transcript ? 'Mulai Bicara Lagi' : 'Mulai Bicara'">Mulai Bicara</span>
            </button>
        </div>
    </section>

    <div style="margin:0 20px 20px;position:relative;">
        <section x-show="state !== 'done'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-8" style="background:#132044;border:1px solid rgba(255,255,255,0.08);border-radius:20px;padding:16px 16px 16px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                <div style="width:6px;height:6px;border-radius:50%;background:#A3E635;"></div>
                <p style="color:#A3E635;font-size:11px;font-weight:700;letter-spacing:0.08em;margin:0;">CONTOH UCAPAN</p>
            </div>
            <p style="color:#64748B;font-size:11px;margin:0 0 10px 0;">Format: jenis + keterangan + nominal + kategori</p>
            @foreach ([
                'Beli nasi goreng dua puluh lima ribu kategori makanan',
                'Bayar parkir lima ribu kategori transportasi',
                'Terima gaji lima juta kategori gaji',
                'Pemasukan bonus lima ratus ribu kategori bonus',
                'Belanja dapur 150.000 kategori belanja dapur',
                'Beli obat tiga puluh ribu kategori kesehatan',
            ] as $voiceExample)
                <div style="background:#1A2B52;border-radius:10px;padding:10px 12px;margin-bottom:6px;display:flex;align-items:flex-start;gap:10px;">
                    <svg style="width:14px;height:14px;flex-shrink:0;margin-top:2px;" viewBox="0 0 24 24" fill="none" stroke="#A3E635" stroke-width="2">
                        <path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/>
                        <path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/>
                    </svg>
                    <p style="color:#CBD5E1;font-size:12px;margin:0;font-style:italic;line-height:1.5;">"{{ $voiceExample }}"</p>
                </div>
            @endforeach
        </section>

        <section x-show="state === 'done'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-8" style="background:#132044;border:1px solid rgba(255,255,255,0.08);border-radius:20px;padding:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                    <div style="width:34px;height:34px;background:#1A2B52;border-radius:9px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(163,230,53,0.15);flex-shrink:0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A3E635" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <path d="M14 2v6h6"/>
                            <path d="M16 13H8"/>
                            <path d="M16 17H8"/>
                            <path d="M10 9H8"/>
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <p style="color:#fff;font-size:14px;font-weight:700;margin:0;">Periksa transkrip</p>
                        <p style="color:#64748B;font-size:12px;margin:4px 0 0 0;line-height:1.4;">Koreksi sebelum diproses</p>
                    </div>
                </div>
                <button type="button" x-on:click="clearTranscript" style="background:rgba(248,113,113,0.12);border:1px solid rgba(248,113,113,0.18);border-radius:999px;color:#F87171;font-size:11px;font-weight:700;padding:6px 10px;white-space:nowrap;cursor:pointer;">Hapus</button>
            </div>

            <p style="color:#64748B;font-size:12px;font-style:italic;margin:14px 0 0 0;" x-show="! transcript">Hasil suara akan muncul di sini sebelum disimpan.</p>

            <div x-show="transcript" style="margin-top:14px;display:flex;flex-direction:column;gap:12px;">
                <textarea x-model="transcript" x-on:input.debounce.400ms="state = transcript ? 'done' : 'idle'; wireSet('transcript', transcript || '', false)" rows="4" class="block w-full rounded-2xl border-[rgba(255,255,255,0.1)] bg-[#1A2B52] px-4 py-3 text-sm font-semibold leading-relaxed text-white shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" placeholder="Contoh: Terima gaji lima juta kategori gaji"></textarea>

                @error('transcript')
                    <p class="text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror

                <button type="button" x-show="originalTranscript && transcript !== originalTranscript" x-on:click="resetToOriginal" class="w-full rounded-2xl bg-[#1A2B52] px-4 py-3 text-xs font-bold text-white">
                    Kembalikan ke Hasil Suara
                </button>

                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:10px 0 12px;">
                    <span style="display:inline-flex;align-items:center;gap:6px;font-size:11px;padding:4px 11px;border-radius:20px;font-weight:500;background:rgba(163,230,53,0.1);color:#a3e635;border:0.5px solid rgba(163,230,53,0.25);">
                        <span style="width:6px;height:6px;border-radius:999px;background:#A3E635;"></span>
                        Terdeteksi
                    </span>
                    <span style="display:inline-flex;align-items:center;gap:6px;font-size:11px;padding:4px 11px;border-radius:20px;font-weight:500;background:rgba(255,255,255,0.06);color:#CBD5E1;border:0.5px solid rgba(255,255,255,0.12);">
                        Siap diproses
                    </span>
                </div>

                <button type="button" x-on:click.prevent="processTranscript()" class="w-full rounded-2xl px-4 py-3 text-sm font-bold shadow-lg shadow-lime-500/20" style="background:#A3E635 !important;color:#0A1628 !important;cursor:pointer !important;display:flex;align-items:center;justify-content:center;gap:8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0A1628" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>
                    Proses Teks
                </button>
            </div>
        </section>
    </div>

    <section class="rounded-2xl p-4" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:20px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
        <div class="flex items-start gap-3">
            <div style="width:34px;height:34px;background:#1A2B52;border-radius:9px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(163,230,53,0.15);flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A3E635" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 11 12 14 22 4"/>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                    <div>
                        <p class="text-sm font-bold text-white" style="margin:0;">Preview transaksi</p>
                        <p style="color:#64748B;font-size:12px;margin:4px 0 0 0;">Hasil parsing otomatis</p>
                    </div>
                    @if ($parsedDraft)
                        <span style="background:rgba(163,230,53,0.12);border:1px solid rgba(163,230,53,0.18);border-radius:999px;color:#A3E635;font-size:11px;font-weight:700;padding:6px 10px;white-space:nowrap;">Siap simpan</span>
                    @endif
                </div>
                <div style="height:1px;background:rgba(255,255,255,0.08);margin:14px 0;"></div>

                @if (! $parsedDraft)
                    <p class="mt-1 text-xs text-[#64748B]">Transkrip siap diproses.</p>
                @else
                    @if (! empty($parsedDraft['warnings']))
                        <div class="mt-3 rounded-2xl bg-[#F59E0B]/10 border border-[#F59E0B]/20 px-4 py-3">
                            <p class="text-xs font-bold uppercase tracking-wide text-[#F59E0B]">Perlu Dicek</p>
                            <ul class="mt-2 space-y-1 text-xs text-[#F59E0B]">
                                @foreach ($parsedDraft['warnings'] as $warning)
                                    <li>• {{ $warning }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-3 grid gap-2 text-sm">
                        <div class="flex items-center justify-between gap-3 rounded-2xl px-4 py-3" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;margin-bottom:6px;">
                            <span class="text-[#64748B]">Jenis</span>
                            <span class="font-bold {{ $parsedDraft['type'] === \App\Models\Transaction::TYPE_INCOME ? 'text-[#1d9e75]' : 'text-[#F87171]' }}">
                                {{ $parsedDraft['type'] === \App\Models\Transaction::TYPE_INCOME ? 'Pemasukan' : 'Pengeluaran' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-2xl px-4 py-3" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;margin-bottom:6px;">
                            <span class="text-[#64748B]">Nominal</span>
                            <span class="font-bold text-white">{{ $parsedDraft['amount'] ? 'Rp '.number_format($parsedDraft['amount'], 0, ',', '.') : 'Belum dikenali' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-2xl px-4 py-3" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;margin-bottom:6px;">
                            <span class="text-[#64748B]">Kategori</span>
                            <span class="font-bold text-white">{{ $parsedDraft['category_name'] ?? 'Belum ditemukan' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-2xl px-4 py-3" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;margin-bottom:6px;">
                            <span class="text-[#64748B]">Tanggal</span>
                            <span class="font-bold text-white">{{ \Carbon\Carbon::parse($parsedDraft['transaction_date'])->translatedFormat('j M Y') }}</span>
                        </div>
                        <div class="rounded-2xl px-4 py-3" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;margin-bottom:6px;">
                            <span class="text-[#64748B]">Catatan</span>
                            <div style="margin-top:8px;background:#132044;border:1px solid rgba(255,255,255,0.08);border-radius:14px;color:#FFFFFF;font-size:13px;font-weight:700;line-height:1.5;padding:12px 14px;">
                                {{ $parsedDraft['note'] ?: 'Belum dikenali' }}
                            </div>
                        </div>
                    </div>

                    @error('parsedDraft')
                        <p class="mt-3 rounded-2xl bg-[#F87171]/10 px-4 py-3 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                    @enderror

                    <button type="button" wire:click="save" @disabled(! $this->canSave) class="mt-4 w-full rounded-2xl px-4 py-3 text-sm font-bold transition {{ $this->canSave ? 'bg-[#A3E635] text-[#0A1628] shadow-lg shadow-lime-500/20' : 'bg-[#1A2B52] text-[#64748B] cursor-not-allowed' }}">
                        Simpan Transaksi
                    </button>

                    @if (! $this->canSave)
                        <p class="mt-2 text-xs text-[#64748B]">Lengkapi nominal, kategori, dan catatan sebelum menyimpan.</p>
                    @endif
                @endif
            </div>
        </div>
    </section>

    <section class="rounded-2xl p-4" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:20px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
        <p class="text-sm font-bold" style="color:#FFFFFF !important;">Belum bisa pakai suara?</p>
        <p class="mt-1 text-xs text-[#A3E635]">Input manual tetap tersedia sebagai fallback utama.</p>
        <a href="{{ request()->routeIs('transactions.create') ? '#input-manual' : route('transactions.create') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-2xl px-4 py-3 text-sm font-bold shadow-lg shadow-lime-500/20" style="background:#A3E635 !important;color:#0A1628 !important;">
            Input Manual
        </a>
    </section>
</div>
