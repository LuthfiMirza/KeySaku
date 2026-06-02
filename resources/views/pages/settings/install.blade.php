<x-app-layout>
    <x-slot:title>Pasang di Layar Utama</x-slot:title>

    <div class="px-4 pt-6 space-y-5 pb-28 lg:px-8 lg:pt-8 lg:pb-8 lg:max-w-5xl lg:mx-auto" style="background:#0A1628 !important;">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-[#A3E635]">Pengaturan</p>
                <h2 class="mt-1 text-base font-bold text-white">Pasang di Layar Utama</h2>
            </div>
            <a href="{{ route('settings') }}" class="inline-flex min-h-10 items-center rounded-2xl px-3 py-2 text-xs font-bold shadow-sm" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.08) !important;color:#A3E635 !important;">Kembali</a>
        </div>

        <section class="rounded-3xl p-5 space-y-5" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
            <p class="text-sm leading-relaxed text-[#64748B]">Kamu bisa memasang KeySaku di layar utama agar terasa seperti aplikasi. Fitur ini bergantung pada dukungan browser dan mode PWA belum memakai service worker di versi ini.</p>

            <div class="space-y-3">
                <h3 class="text-sm font-bold text-white">Android Chrome</h3>
                <ol class="list-decimal space-y-2 pl-5 text-sm leading-relaxed text-[#64748B]">
                    <li>Buka KeySaku di Chrome.</li>
                    <li>Ketuk menu tiga titik di kanan atas.</li>
                    <li>Pilih <span class="font-bold text-white">Tambahkan ke layar utama</span>.</li>
                    <li>Ikuti konfirmasi yang muncul.</li>
                </ol>
            </div>

            <div class="space-y-3">
                <h3 class="text-sm font-bold text-white">iPhone Safari</h3>
                <ol class="list-decimal space-y-2 pl-5 text-sm leading-relaxed text-[#64748B]">
                    <li>Buka KeySaku di Safari.</li>
                    <li>Ketuk tombol bagikan.</li>
                    <li>Pilih <span class="font-bold text-white">Add to Home Screen</span>.</li>
                    <li>Ketuk tambah untuk menyimpan ikon.</li>
                </ol>
            </div>

            <div class="rounded-2xl bg-[#A3E635]/10 px-4 py-3">
                <p class="text-sm font-bold text-[#A3E635]">Desktop</p>
                <p class="mt-1 text-xs leading-relaxed text-[#A3E635]">Beberapa browser desktop menampilkan ikon install di address bar. Jika ikon tidak muncul, gunakan bookmark sebagai pintasan sementara.</p>
            </div>
        </section>
    </div>
</x-app-layout>
