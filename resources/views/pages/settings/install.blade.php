<x-app-layout>
    <x-slot:title>Pasang di Layar Utama</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Pengaturan</p>
                <h2 class="mt-1 text-base font-bold text-slate-800">Pasang di Layar Utama</h2>
            </div>
            <a href="{{ route('settings') }}" class="inline-flex min-h-10 items-center rounded-2xl bg-white/80 px-3 py-2 text-xs font-bold text-slate-500 shadow-sm border border-white/60">Kembali</a>
        </div>

        <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60 space-y-5">
            <p class="text-sm leading-relaxed text-slate-500">Kamu bisa memasang KeySaku di layar utama agar terasa seperti aplikasi. Fitur ini bergantung pada dukungan browser dan mode PWA belum memakai service worker di versi ini.</p>

            <div class="space-y-3">
                <h3 class="text-sm font-bold text-slate-800">Android Chrome</h3>
                <ol class="list-decimal space-y-2 pl-5 text-sm leading-relaxed text-slate-500">
                    <li>Buka KeySaku di Chrome.</li>
                    <li>Ketuk menu tiga titik di kanan atas.</li>
                    <li>Pilih <span class="font-bold text-slate-700">Tambahkan ke layar utama</span>.</li>
                    <li>Ikuti konfirmasi yang muncul.</li>
                </ol>
            </div>

            <div class="space-y-3">
                <h3 class="text-sm font-bold text-slate-800">iPhone Safari</h3>
                <ol class="list-decimal space-y-2 pl-5 text-sm leading-relaxed text-slate-500">
                    <li>Buka KeySaku di Safari.</li>
                    <li>Ketuk tombol bagikan.</li>
                    <li>Pilih <span class="font-bold text-slate-700">Add to Home Screen</span>.</li>
                    <li>Ketuk tambah untuk menyimpan ikon.</li>
                </ol>
            </div>

            <div class="rounded-2xl bg-indigo-50 px-4 py-3">
                <p class="text-sm font-bold text-indigo-700">Desktop</p>
                <p class="mt-1 text-xs leading-relaxed text-indigo-600">Beberapa browser desktop menampilkan ikon install di address bar. Jika ikon tidak muncul, gunakan bookmark sebagai pintasan sementara.</p>
            </div>
        </section>
    </div>
</x-app-layout>
