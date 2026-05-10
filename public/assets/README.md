# KeySaku Asset Guideline

Dokumen ini menjelaskan struktur, workflow, dan aturan produksi asset visual KeySaku untuk style **Playful Gamified Fintech**: colorful cartoon, rounded UI, bright palette, floating mobile mockups, serta elemen level, streak, badge, reward, challenge, dan progress.

## Lokasi Asset

- `assets/` adalah **source/design inventory**. Folder ini dipakai untuk daftar produksi, placeholder desain, token, guideline, dan koordinasi designer/developer.
- `public/assets/` adalah **production-ready public assets**. Hanya asset yang aman dibaca langsung oleh browser yang boleh dipakai dari Blade/CSS/JS.
- Landing page harus membaca asset dari `public/assets/`, bukan dari `assets/`.
- Jika asset belum final, gunakan fallback visual yang aman: CSS gradient, inline SVG sederhana, icon existing yang sudah ready, atau sembunyikan elemen dekoratif.

## Struktur Folder

- `brand/`: logo, app icon, favicon, brand pattern, dan guideline brand.
- `tokens/`: design tokens warna, tipografi, radius, dan skala font dalam CSS/JSON/TXT.
- `landing/`: asset khusus landing page seperti hero phone mockup, feature visual, gamification cards, saving goal, trust, dan CTA.
- `app-mockups/`: mockup layar aplikasi untuk onboarding, dashboard, transaksi, budget, report, dan rewards.
- `icons/`: navigation icons, transaction category icons, feature icons, dan utility icons.
- `illustrations/`: ilustrasi finance, gamification, character/mascot, hands, dan objects.
- `ui/`: referensi visual komponen seperti buttons, cards, inputs, tabs, progress, badges, modals, dan toast.
- `states/`: empty states, loading, success, error, dan no-internet fallback.
- `decorations/`: blobs, doodle lines, sparkles, stars, dan stickers untuk polish visual.
- `animations/`: Lottie JSON untuk confetti, coin drop, loading, dan level up.
- `marketing/`: social post, story, app preview, launch banner, dan OG image.

## Prioritas Asset

- `P0`: wajib disiapkan sebelum `landing-v2` dijadikan production. Contoh: logo utama, icon PWA, hero mockup, feature icon utama, navigation icon utama, visual wallet/coins/piggy bank, no-internet fallback.
- `P1`: boleh menyusul setelah MVP visual siap. Contoh: budget/rewards mockup, category icons, feature illustrations, CTA mockup, Lottie reward moments.
- `P2`: polish dan campaign asset. Contoh: dekorasi tambahan, marketing social/story/banner, loading alternatif, modal/toast visual reference.
- `P3`: opsional untuk eksperimen atau ekspansi nanti. Tambahkan hanya jika ada kebutuhan produk yang jelas.

## Naming Convention

- Gunakan lowercase-kebab-case untuk semua file dan folder.
- Prefix harus menjelaskan kategori: `logo-`, `icon-nav-`, `icon-category-`, `illustration-`, `button-`, `card-`, `badge-`, `empty-`, `blob-`, `doodle-`, `sticker-`.
- Hindari spasi, underscore, huruf kapital, dan nama generik seperti `image1.png` atau `final-final.svg`.
- Nama harus stabil karena bisa dipakai di Blade, CSS, manifest, dan cache browser.

## Format File

- SVG: logo, icon, dekorasi sederhana, pattern, badge, dan ilustrasi sederhana yang perlu scalable.
- PNG/WebP: mockup phone, ilustrasi kompleks, mascot, hero visual, social preview, dan OG image.
- JSON: Lottie animation dan data design token.
- CSS/JSON: design tokens yang dipakai developer untuk warna, tipografi, radius, spacing, dan skala font.
- PDF: guideline brand final, bukan asset runtime landing page.

## Ukuran Export

- Logo SVG: gunakan `viewBox` rapi, tanpa fixed background kecuali varian memang membutuhkan background.
- App icon PNG: export minimal `1024x1024`, lalu turunkan ke ukuran PWA/favicon sesuai kebutuhan.
- Favicon PNG: `32x32`, `48x48`, atau `192x192` sesuai manifest PWA.
- Phone mockup PNG/WebP: export `2x` dari ukuran tampil; rekomendasi lebar 640–960 px untuk hero mobile-first.
- Hero visual: rekomendasi maksimal 1600 px sisi terpanjang untuk desktop, dengan versi lebih kecil untuk mobile jika perlu.
- Social post: `1080x1080`.
- Story: `1080x1920`.
- OG image: `1200x630`.
- Lottie JSON: jaga ukuran file sekecil mungkin; hindari embed bitmap besar.

## Optimasi File

- SVG harus dibersihkan dari metadata editor, hidden layers, dan ID acak yang tidak diperlukan.
- SVG dekoratif sebaiknya menggunakan path sederhana dan warna token KeySaku.
- PNG/WebP harus dikompresi sebelum masuk `public/assets/`.
- Gunakan WebP untuk visual besar jika kualitas tetap baik; simpan PNG hanya jika transparency atau workflow membutuhkan.
- Hindari asset lebih dari 300 KB untuk visual landing biasa; hero/mockup besar boleh lebih, tetapi harus dievaluasi lazy loading.
- Jangan commit placeholder bitmap palsu. Placeholder PNG/WebP/JPG harus berupa image valid atau diganti SVG sementara.
- Asset public yang diganti harus tetap mempertahankan nama file jika sudah direferensikan landing page.

## Alt Text dan Accessibility

- Logo informatif: gunakan alt text singkat seperti `Logo KeySaku`.
- Ilustrasi bermakna: jelaskan fungsi, misalnya `Ilustrasi target tabungan tercapai`.
- Mockup produk: jelaskan layar, misalnya `Mockup dashboard KeySaku dengan ringkasan saldo dan progres tantangan`.
- Dekorasi murni: gunakan `alt=""` dan `aria-hidden="true"`.
- Icon button harus punya accessible label melalui teks terlihat, `aria-label`, atau `sr-only` text.
- Jangan menyampaikan informasi penting hanya lewat warna; sertakan teks, angka, label, atau icon pendukung.

## Aturan Penggunaan di Landing Page

- Gunakan hanya asset dari `public/assets/` untuk Blade, CSS, dan JavaScript runtime.
- P0 wajib siap sebelum `landing-v2` dipromosikan ke production.
- P1 boleh menyusul setelah MVP visual stabil.
- P2 hanya untuk polish, campaign, atau variasi visual.
- Jangan pakai asset `placeholder` atau `needs-production` di production tanpa fallback aman.
- Jika asset belum ready, gunakan fallback visual berbasis CSS, inline SVG sederhana, atau komponen existing agar landing page tidak broken.
- Untuk asset besar seperti hero mockup, app preview, dan OG image, gunakan ukuran yang sesuai, lazy loading jika tidak above-the-fold, dan alt text yang jelas.
- Dekorasi background boleh dihapus atau disembunyikan bila belum ready; jangan mengorbankan readability, performance, atau accessibility.

## Manifest

- `assets/asset-manifest.json` adalah inventory source dan prioritas produksi.
- `public/assets/asset-manifest.json` adalah salinan metadata untuk asset public.
- Setiap item manifest mencatat `sourcePath`, `publicPath`, `priority`, `type`, `formatTarget`, `currentFormat`, `status`, dan `usageLocation`.
- Status yang digunakan:
  - `ready`: aman dipakai.
  - `placeholder`: hanya untuk inventory/desain, jangan production.
  - `needs-production`: file valid tersedia, tetapi masih perlu diganti asset final sebelum production.

## Workflow Menambahkan Asset Final

1. Export asset final dari Figma/Illustrator/tool desain sesuai format target.
2. Simpan source/design inventory di path yang sama dalam `assets/`.
3. Simpan versi browser-ready di path yang sama dalam `public/assets/`.
4. Pastikan nama tetap lowercase-kebab-case dan tidak mengubah path yang sudah dipakai landing page.
5. Optimasi file sebelum commit.
6. Update `assets/asset-manifest.json` dan `public/assets/asset-manifest.json`: ubah `status` menjadi `ready`, perbaiki `currentFormat`, dan cek `usageLocation`.
7. Pastikan Blade/CSS memakai `asset('assets/...')` atau helper named asset lain yang mengarah ke `public/assets/`.
8. Cek landing page di browser untuk memastikan tidak ada broken image, layout shift besar, atau alt text kosong pada asset informatif.

## Rollback

- Jika asset final bermasalah, kembalikan file terkait dari Git ke versi sebelumnya.
- Jika masalah terjadi di browser, rollback hanya file dalam `public/assets/` dan manifest terkait.
- Jika belum yakin asset aman, set status manifest kembali ke `needs-production` dan gunakan fallback visual di landing page.
