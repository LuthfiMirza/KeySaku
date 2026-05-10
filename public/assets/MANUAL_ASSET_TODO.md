# Manual Asset TODO — KeySaku Landing-v2

Landing-v2 sekarang sudah memakai placeholder struktur untuk asset manual supaya visual tidak terasa generic/AI. Designer bisa replace placeholder dengan file berikut.

## Prioritas P0

- [ ] `public/assets/decorations/underline-squiggle.svg` — underline hand-drawn di headline.
- [ ] `public/assets/decorations/coin-1.svg` — flat coin, bukan emoji/generic plus.
- [ ] `public/assets/decorations/star-deco.svg` — 4-point star flat.
- [ ] `public/assets/decorations/sparkle.svg` — sparkle 6-point kecil.
- [ ] `public/assets/mascot/keysaku-mascot.svg` — karakter brand KeySaku, celengan/dompet dengan personality.
- [ ] `public/assets/icons/marquee-star.svg` — divider marquee kecil jika ingin mengganti karakter `✦`.
- [ ] `public/assets/mockup/phone-screen.png` — screenshot app asli untuk isi phone frame.

## Prioritas P1

- [ ] `public/assets/decorations/coin-2.svg` — variasi coin dengan angle berbeda.
- [ ] `public/assets/decorations/arrow-doodle.svg` — arrow hand-drawn untuk CTA.

## Catatan Implementasi

- Placeholder HTML memakai `data-asset="assets/..."` agar path target jelas.
- Setelah file final tersedia, ganti `<div class="asset-placeholder ...">` menjadi `<img src="{{ asset('assets/...') }}" ...>`.
- Pertahankan ukuran/class existing agar layout tidak berubah besar.
- Hindari gradient/blob generic; pakai outline tebal, flat color, hard shadow, dan sedikit rotasi.

## LESA-Inspired Visual Update

Landing-v2 sekarang memakai palet dan pola dekorasi yang terinspirasi dari LESA, tetapi tetap diadaptasi untuk KeySaku:

- Dominan KeySaku: warm cream `#FFF9F0` dan coral `#FF6B35`.
- Aksen LESA-style: green `#3FDA7C`, pink `#FD64B6`, yellow `#FFDF49`, lavender `#E8E6F2`.
- Dekorasi hero memakai inline SVG micro-shapes: star burst, diamond, swoosh, parallelogram, circle, arrow swoosh.
- Blob cair/generic tidak dipakai lagi.
- Font display/body diarahkan ke `Nunito` + `Plus Jakarta Sans`.

Asset manual tetap disarankan untuk membuat visual makin natural:

- [ ] Replace inline micro-shapes dengan SVG custom jika designer sudah punya style final.
- [ ] Buat badge custom tanpa emoji untuk `Streak`, `Hemat Pro`, dan `Ditabung`.
- [ ] Buat screenshot asli `public/assets/mockup/phone-screen.png` untuk mengganti UI mockup HTML.
