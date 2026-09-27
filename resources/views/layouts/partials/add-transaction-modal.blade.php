<!-- Add Transaction Menu (Bottom Sheet) -->
<div x-show="showAddMenu" style="display: none;" class="relative z-50">
    <!-- Backdrop -->
    <div x-show="showAddMenu"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-[#121316]/50 backdrop-blur-md"
         x-on:click="showAddMenu = false"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-0 sm:p-4 text-center max-w-md mx-auto relative pb-[90px] sm:pb-4">
            <div x-show="showAddMenu"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                 class="relative transform overflow-hidden rounded-t-[36px] sm:rounded-[32px] bg-[#FCF8F8] text-left shadow-[0_-12px_40px_rgba(0,0,0,0.1)] transition-all w-full border-t sm:border border-white/80 p-6">
                
                <!-- Grab Handle -->
                <div class="w-12 h-1.5 bg-black/10 rounded-full mx-auto mb-5"></div>

                <div class="text-center mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-[#121316]/5 text-[#8E8E93] mb-2">
                        Pencatatan Keuangan
                    </span>
                    <h3 class="text-lg font-extrabold text-[#121316] tracking-tight">Pilih Metode Input</h3>
                    <p class="text-xs text-[#8E8E93] mt-1">Catat transaksi dengan cepat dan akurat</p>
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <a href="{{ route('transactions.create') }}" class="group flex flex-col items-center justify-center p-5 bg-white hover:bg-slate-50 rounded-2xl border border-black/5 shadow-sm transition active:scale-[0.98]">
                        <div class="w-12 h-12 rounded-2xl bg-[#FCF8F8] border border-black/5 flex items-center justify-center text-xl mb-3 shadow-inner group-hover:scale-105 transition">
                            ✍️
                        </div>
                        <span class="text-sm font-bold text-[#121316]">Manual Form</span>
                        <span class="text-[11px] text-[#8E8E93] mt-0.5">Ketik detail lengkap</span>
                    </a>
                    
                    <button type="button" x-on:click="showAddMenu = false; showVoiceModal = true" class="group flex flex-col items-center justify-center p-5 bg-gradient-to-b from-white to-[#FFF5F2] hover:to-[#FFEBE5] rounded-2xl border border-[#FF6B6B]/20 shadow-sm transition active:scale-[0.98]">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#FF6B6B] to-[#FFA07A] text-white flex items-center justify-center text-xl mb-3 shadow-md shadow-[#FF6B6B]/20 group-hover:scale-105 transition">
                            🎙️
                        </div>
                        <span class="text-sm font-bold text-[#121316]">Voice AI</span>
                        <span class="text-[11px] text-[#FF6B6B] font-semibold mt-0.5">Bicara instan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Voice Input Modal (Smart Listener Sheet) -->
<div x-show="showVoiceModal" style="display: none;" class="relative z-50">
    <!-- Backdrop -->
    <div x-show="showVoiceModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-[#121316]/50 backdrop-blur-md"
         x-on:click="showVoiceModal = false"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 z-10 overflow-hidden pointer-events-none">
        <div class="flex min-h-full items-end sm:items-center justify-center p-0 sm:p-4 text-center max-w-md mx-auto relative pointer-events-none pb-0 sm:pb-4">
            <div x-show="showVoiceModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                 class="relative flex flex-col transform overflow-hidden bg-[#FCF8F8] text-left shadow-[0_-16px_48px_rgba(0,0,0,0.12)] transition-all w-full rounded-t-[36px] sm:rounded-[32px] border-t sm:border border-white/80 max-h-[92vh] pointer-events-auto">
                
                <!-- Grab Handle -->
                <div class="w-12 h-1.5 bg-black/10 rounded-full mx-auto mt-3 mb-1 shrink-0"></div>

                <div class="flex items-center justify-between px-6 py-3 bg-[#FCF8F8]/90 backdrop-blur-md border-b border-black/5 sticky top-0 z-10">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-[#FF6B6B] animate-pulse"></div>
                        <h3 class="text-sm font-bold text-[#121316] tracking-tight">Catat Transaksi Suara</h3>
                    </div>
                    <button type="button" x-on:click="showVoiceModal = false" class="w-8 h-8 rounded-full bg-black/5 hover:bg-black/10 flex items-center justify-center text-[#8E8E93] hover:text-[#121316] transition" aria-label="Tutup modal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4 pb-[calc(env(safe-area-inset-bottom)+6rem)]">
                    <livewire:transactions.voice-transaction />
                </div>
            </div>
        </div>
    </div>
</div>
