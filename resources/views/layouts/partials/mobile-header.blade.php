<header class="sticky top-0 z-30 lg:hidden px-4 pt-8 pb-3 bg-transparent">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-white/70">
                Selamat datang{{ auth()->check() ? ', '.auth()->user()->name : '' }} 👋
            </p>
            <h1 class="text-lg font-bold text-white">KeySaku</h1>
        </div>

        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-9 h-9 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white border border-white/30" aria-label="Keluar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                    </svg>
                </button>
            </form>
        @endauth
    </div>
</header>
