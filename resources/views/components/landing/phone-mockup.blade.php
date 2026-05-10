@props(['title' => 'Dashboard KeySaku'])

<div {{ $attributes->merge(['class' => 'relative mx-auto w-full max-w-[290px]']) }} aria-label="{{ $title }}" role="img">
    <div class="absolute -left-8 top-12 h-20 w-20 rounded-full bg-[#a3e635] blur-xl"></div>
    <div class="absolute -right-8 bottom-16 h-24 w-24 rounded-full bg-[#f472b6] blur-xl"></div>
    <div class="relative rounded-[2.5rem] border-[10px] border-[#201a2e] bg-[#201a2e] shadow-[0_32px_80px_rgba(32,26,46,0.35)]">
        <div class="overflow-hidden rounded-[1.8rem] bg-[#fff8e7]">
            <div class="flex items-center justify-between bg-white px-5 py-4">
                <div><p class="text-xs font-bold text-slate-400">Saldo bulan ini</p><p class="text-xl font-black text-[#201a2e]">Rp2,4 jt</p></div>
                <div class="rounded-2xl bg-[#a3e635] px-3 py-2 text-sm font-black text-[#201a2e]">Lv. 3</div>
            </div>
            <div class="space-y-4 p-5">
                <div class="rounded-3xl bg-[#8b5cf6] p-4 text-white"><p class="text-xs font-bold opacity-80">Challenge hemat</p><p class="mt-1 text-lg font-black">5/7 hari streak 🔥</p><div class="mt-3 h-3 rounded-full bg-white/25"><div class="h-3 w-[72%] rounded-full bg-[#fde047]"></div></div></div>
                <div class="grid grid-cols-2 gap-3"><div class="rounded-2xl bg-white p-3"><p class="text-xs font-bold text-slate-400">Masuk</p><p class="font-black text-[#0f766e]">+850K</p></div><div class="rounded-2xl bg-white p-3"><p class="text-xs font-bold text-slate-400">Keluar</p><p class="font-black text-[#db2777]">-320K</p></div></div>
                <div class="rounded-3xl bg-white p-4"><div class="mb-3 flex items-center justify-between"><p class="text-sm font-black text-[#201a2e]">Target sepatu</p><span class="text-xs font-black text-[#8b5cf6]">64%</span></div><div class="h-3 rounded-full bg-slate-100"><div class="h-3 w-[64%] rounded-full bg-[#2dd4bf]"></div></div></div>
                <div class="flex justify-around rounded-3xl bg-[#201a2e] p-3 text-lg" aria-hidden="true"><span>🏠</span><span>💳</span><span>💰</span><span>📊</span></div>
            </div>
        </div>
    </div>
</div>
