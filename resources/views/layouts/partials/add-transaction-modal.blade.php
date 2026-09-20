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
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
         x-on:click="showAddMenu = false"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0 max-w-md mx-auto relative pb-24">
            <div x-show="showAddMenu"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                 class="relative transform overflow-hidden rounded-3xl bg-white/90 backdrop-blur-xl text-left shadow-2xl transition-all w-full border border-white/50 p-6">
                
                <h3 class="text-lg font-bold text-slate-800 text-center mb-6">Pilih Metode Input</h3>

                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('transactions.create') }}" class="flex flex-col items-center justify-center p-6 bg-slate-50 hover:bg-slate-100 rounded-2xl border border-slate-200 transition">
                        <span class="text-4xl mb-3">📝</span>
                        <span class="text-sm font-bold text-slate-800">Manual</span>
                    </a>
                    <button type="button" x-on:click="showAddMenu = false; showVoiceModal = true" class="flex flex-col items-center justify-center p-6 bg-indigo-50 hover:bg-indigo-100 rounded-2xl border border-indigo-200 transition">
                        <span class="text-4xl mb-3">🎙️</span>
                        <span class="text-sm font-bold text-indigo-800">Suara</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Voice Input Modal -->
<div x-show="showVoiceModal" style="display: none;" class="relative z-50">
    <!-- Backdrop -->
    <div x-show="showVoiceModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
         x-on:click="showVoiceModal = false"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 z-10 overflow-hidden pointer-events-none">
        <div class="flex min-h-full items-end sm:items-center justify-center p-0 sm:p-4 text-center max-w-md mx-auto relative pointer-events-none pb-[88px]">
            <div x-show="showVoiceModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                 class="relative flex flex-col transform overflow-hidden bg-slate-50 text-left shadow-2xl transition-all w-full rounded-t-3xl sm:rounded-3xl max-h-[85vh] pointer-events-auto">
                
                <div class="flex items-center justify-between p-4 bg-white border-b border-slate-100 sticky top-0 z-10">
                    <h3 class="text-base font-bold text-slate-800">Input Suara</h3>
                    <button type="button" x-on:click="showVoiceModal = false" class="p-2 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-2">
                    <livewire:transactions.voice-transaction />
                </div>
            </div>
        </div>
    </div>
</div>
