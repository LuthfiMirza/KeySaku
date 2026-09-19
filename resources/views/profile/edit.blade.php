<x-app-layout>
    <x-slot:title>Profil Saya</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Profil Saya</h2>
                <p class="mt-1 text-xs text-slate-400">Kelola informasi akun dan keamanan.</p>
            </div>
            <a href="{{ route('settings') }}"
               class="shrink-0 rounded-full bg-slate-100 px-3 py-2 text-xs font-bold text-slate-500">
                ← Setelan
            </a>
        </div>

        {{-- Avatar card --}}
        <div class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-3xl p-5 text-white shadow-xl shadow-indigo-500/30 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-8 translate-x-8 blur-2xl"></div>
            <div class="relative flex items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 text-2xl font-extrabold shadow-inner">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-base font-extrabold">{{ auth()->user()->name }}</p>
                    <p class="mt-0.5 text-sm text-white/70">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>

        {{-- Update profile --}}
        <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60">
            @include('profile.partials.update-profile-information-form')
        </section>

        {{-- Update password --}}
        <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60">
            @include('profile.partials.update-password-form')
        </section>

        {{-- Danger zone --}}
        <section class="bg-rose-50 rounded-3xl p-5 shadow-sm border border-rose-100">
            @include('profile.partials.delete-user-form')
        </section>
    </div>
</x-app-layout>
