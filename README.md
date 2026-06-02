# KeySaku

**KeySaku** adalah aplikasi web pengelolaan keuangan keluarga berbasis Laravel yang dirancang mobile-first dengan tampilan modern, input transaksi manual dan suara, anggaran bulanan, laporan, serta target tabungan.

Aplikasi ini dibuat untuk membantu pengguna mencatat pemasukan/pengeluaran, memahami arus kas bulanan, mengontrol anggaran, dan memantau progres tujuan tabungan dalam satu tempat.

---

## Ringkasan Produk

KeySaku cocok untuk:

- keluarga Indonesia yang ingin mencatat keuangan harian;
- pengguna personal yang ingin memantau pemasukan dan pengeluaran;
- pengguna mobile yang ingin pengalaman seperti aplikasi, tetapi tetap berjalan sebagai web app;
- kebutuhan demo/staging dengan data contoh yang realistis.

Fokus utama KeySaku adalah **sederhana, cepat, mobile-first, dan kompatibel dengan shared hosting PHP**.

---

## Fitur Utama

### 1. Autentikasi Pengguna

KeySaku sudah memiliki sistem autentikasi berbasis Laravel Breeze Blade:

- register;
- login;
- logout;
- lupa password;
- reset password;
- profil pengguna;
- update nama/email;
- update password;
- hapus akun.

Semua halaman aplikasi utama dilindungi autentikasi. Pengguna yang belum login akan diarahkan ke halaman login.

---

### 2. Dashboard Keuangan

Dashboard menampilkan ringkasan kondisi keuangan pengguna:

- total saldo bersih dari semua transaksi;
- pemasukan bulan ini;
- pengeluaran bulan ini;
- pengeluaran per kategori;
- ringkasan anggaran bulan berjalan;
- insight target tabungan aktif;
- transaksi terakhir;
- shortcut aksi cepat.

Dashboard hanya menampilkan data milik user yang sedang login.

---

### 3. Kategori Transaksi

Pengguna dapat mengelola kategori transaksi sendiri.

Kategori dipisahkan menjadi:

- kategori pemasukan;
- kategori pengeluaran.

Kategori digunakan oleh:

- transaksi manual;
- transaksi suara;
- laporan bulanan;
- anggaran bulanan.

---

### 4. Transaksi Manual

Pengguna dapat mencatat transaksi secara manual dengan field:

- jenis transaksi: pemasukan atau pengeluaran;
- nominal;
- kategori;
- tanggal transaksi;
- catatan opsional.

Transaksi manual mendukung:

- tambah transaksi;
- riwayat transaksi;
- filter transaksi;
- edit transaksi;
- soft delete transaksi.

Transaksi yang dihapus menggunakan soft delete sehingga tidak langsung hilang secara permanen dari database.

---

### 5. Input Transaksi Suara

KeySaku memiliki MVP input suara menggunakan Browser SpeechRecognition.

Alur umumnya:

1. pengguna membuka halaman input suara;
2. browser meminta izin mikrofon;
3. suara pengguna ditranskrip menjadi teks;
4. teks diproses menjadi draft transaksi;
5. pengguna meninjau preview;
6. transaksi disimpan sebagai transaksi bersumber `voice`.

Contoh teks suara:

> beli makan siang lima puluh ribu kategori makanan

Catatan:

- fitur ini bergantung pada dukungan browser;
- Chrome/Android biasanya menjadi target terbaik untuk pengujian;
- transaksi suara tetap disimpan sebagai data transaksi biasa;
- `raw_voice_text` disimpan untuk transaksi bersumber suara.

---

### 6. Laporan Bulanan

Halaman laporan membantu pengguna memahami aktivitas keuangan per bulan.

Fitur laporan:

- pilih bulan laporan;
- total pemasukan;
- total pengeluaran;
- saldo bulan ini;
- perbandingan pemasukan vs pengeluaran;
- pengeluaran per kategori;
- daftar transaksi terbaru pada bulan terpilih.

Laporan memakai data transaksi milik user login dan mengecualikan transaksi soft-deleted.

---

### 7. Anggaran Bulanan

KeySaku memiliki fitur budgeting MVP yang cukup lengkap.

Fitur anggaran:

- membuat anggaran bulanan per kategori pengeluaran;
- melihat daftar anggaran pada bulan/tahun terpilih;
- progress pemakaian anggaran;
- ringkasan total anggaran;
- total terpakai;
- sisa anggaran;
- jumlah kategori lewat batas;
- edit nominal anggaran;
- hapus anggaran;
- salin anggaran dari bulan sebelumnya.

Aturan penting:

- anggaran hanya berlaku untuk kategori pengeluaran;
- user hanya bisa mengelola anggaran miliknya sendiri;
- hapus anggaran tidak menghapus transaksi;
- salin dari bulan lalu tidak menimpa anggaran yang sudah ada;
- perhitungan anggaran hanya menghitung transaksi pengeluaran pada kategori yang memiliki budget.

---

### 8. Target Tabungan

Fitur target tabungan membantu pengguna memantau tujuan finansial.

Fitur target tabungan:

- membuat target tabungan;
- melihat daftar target aktif;
- menambahkan dana/kontribusi;
- progress target;
- sisa target;
- label target tercapai jika dana sudah memenuhi target;
- riwayat dana terbaru;
- edit nama, nominal target, dan deadline;
- arsipkan target tabungan.

Catatan:

- kontribusi tabungan tidak otomatis membuat transaksi keuangan;
- arsip tidak menghapus kontribusi;
- target arsip disembunyikan dari daftar aktif;
- belum ada restore arsip di versi ini.

---

### 9. Pengaturan

Halaman pengaturan sudah berisi beberapa menu aktif.

Menu aktif:

- profil pengguna;
- kategori transaksi;
- notifikasi;
- mata uang;
- bahasa;
- keamanan;
- pasang di layar utama.

Detail pengaturan:

- **Notifikasi**: halaman informasional dengan toggle disabled; belum mengirim email/SMS/push notification.
- **Mata Uang**: menyimpan preferensi `IDR` atau `USD` pada user.
- **Bahasa**: menyimpan preferensi `id` atau `en` pada user.
- **Keamanan**: mengarahkan pengguna ke halaman profil untuk update password.
- **Pasang di Layar Utama**: instruksi instalasi PWA untuk Android Chrome, iPhone Safari, dan desktop browser.

Batasan saat ini:

- preferensi mata uang belum mengubah format nominal di seluruh UI;
- belum ada konversi kurs;
- preferensi bahasa belum menerjemahkan seluruh aplikasi;
- belum ada service worker/offline mode.

---

## Halaman dan Route Utama

### Aplikasi

| Path | Nama Route | Fungsi |
|---|---|---|
| `/` | `dashboard` | Beranda/dashboard |
| `/transaksi` | `transactions.index` | Riwayat transaksi |
| `/transaksi/tambah` | `transactions.create` | Tambah transaksi manual |
| `/transaksi/suara` | `transactions.voice` | Input transaksi suara |
| `/transaksi/{transaction}/edit` | `transactions.edit` | Edit transaksi |
| `/kategori` | `categories` | Manajemen kategori |
| `/laporan` | `reports.index` | Laporan bulanan |
| `/anggaran` | `budgets.index` | Anggaran bulanan |
| `/tabungan` | `savings.index` | Target tabungan |
| `/pengaturan` | `settings` | Pengaturan |

### Submenu Pengaturan

| Path | Nama Route | Fungsi |
|---|---|---|
| `/pengaturan/notifikasi` | `settings.notifications` | Info pengaturan notifikasi |
| `/pengaturan/mata-uang` | `settings.currency` | Preferensi mata uang |
| `/pengaturan/bahasa` | `settings.language` | Preferensi bahasa |
| `/pengaturan/keamanan` | `settings.security` | Info keamanan dan link profil |
| `/pengaturan/pasang` | `settings.install` | Instruksi pasang di layar utama |

### Profil dan Auth

| Path | Fungsi |
|---|---|
| `/profile` | Profil pengguna |
| `/login` | Login |
| `/register` | Register |
| `/forgot-password` | Lupa password |
| `/reset-password/{token}` | Reset password |
| `/confirm-password` | Konfirmasi password |
| `/verify-email` | Notifikasi verifikasi email |

---

## Teknologi

KeySaku dibangun menggunakan:

- **Laravel 12**;
- **PHP 8.4+**;
- **MySQL**;
- **Blade**;
- **Livewire**;
- **Tailwind CSS**;
- **Alpine.js**;
- **Vite** untuk build asset lokal;
- **Browser SpeechRecognition API** untuk input suara;
- **PWA-ready meta/manifest**.

Target deployment utama adalah **Hostinger Premium shared hosting**. Aplikasi tidak membutuhkan Node.js server di production.

---

## Struktur Folder Penting

```text
app/
├── Http/Controllers/
├── Livewire/
└── Models/

resources/views/
├── layouts/
├── layouts/partials/
├── pages/
├── livewire/
└── components/

database/
├── migrations/
└── seeders/

docs/
├── PRD.md
├── ROADMAP.md
├── TASKS.md
├── DECISIONS.md
├── DEPLOYMENT_HOSTINGER.md
└── VOICE_INPUT.md
```

Beberapa file penting:

- `routes/web.php` — daftar route aplikasi;
- `resources/views/layouts/app.blade.php` — layout utama aplikasi;
- `resources/views/layouts/partials/bottom-nav.blade.php` — navigasi bawah mobile;
- `resources/views/layouts/partials/sidebar.blade.php` — sidebar desktop;
- `database/seeders/DemoSeeder.php` — data demo lokal/staging.

---

## Desain UI

KeySaku menggunakan pendekatan:

- mobile-first;
- tampilan glassmorphism;
- warna premium navy-indigo;
- bottom navigation untuk mobile;
- sidebar desktop untuk layar besar;
- kartu ringkasan yang ringkas dan mudah dibaca;
- UI copy Bahasa Indonesia.

Pada mobile, aplikasi tetap terasa seperti app dengan shell `max-w-md` dan bottom nav.

Pada desktop, aplikasi menggunakan sidebar kiri dan area konten lebih luas.

---

## Instalasi Lokal

### 1. Clone repository

```bash
git clone https://github.com/LuthfiMirza/KeySaku.git
cd KeySaku
```

### 2. Install dependency PHP

```bash
composer install
```

### 3. Install dependency frontend

```bash
npm install
```

### 4. Siapkan environment

```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan konfigurasi database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=keysaku
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan migrasi

```bash
php artisan migrate
```

### 6. Build asset frontend

```bash
npm run build
```

### 7. Jalankan server lokal

```bash
php artisan serve
```

Akses aplikasi di:

```text
http://127.0.0.1:8000
```

---

## Data Demo

KeySaku memiliki demo seeder manual.

Jalankan:

```bash
php artisan db:seed --class=DemoSeeder
```

Akun demo:

```text
Email: demo@keysaku.test
Password: password
```

Demo seeder membuat:

- user demo;
- kategori pemasukan dan pengeluaran;
- transaksi bulan ini dan bulan sebelumnya;
- transaksi manual dan suara;
- anggaran bulan ini dan bulan sebelumnya;
- target tabungan;
- kontribusi tabungan.

Seeder ini **tidak dipanggil otomatis** dari default seeder agar aman untuk production.

Untuk reset penuh lokal:

```bash
php artisan migrate:fresh
php artisan db:seed --class=DemoSeeder
```

---

## Testing dan Validasi

Perintah utama validasi:

```bash
php artisan route:list --except-vendor
npm run build
php artisan test
```

Perintah tambahan yang sering dipakai:

```bash
php artisan view:clear
php artisan config:clear
```

Status terakhir saat README ini dibuat:

```text
99 tests passed, 533 assertions
```

---

## Manual QA Checklist Singkat

Gunakan akun demo lalu cek alur berikut:

- login demo berhasil;
- dashboard menampilkan data demo;
- bottom nav mobile berjalan;
- sidebar desktop berjalan;
- tambah transaksi manual berhasil;
- riwayat transaksi tampil dan filter bekerja;
- edit transaksi berhasil;
- soft delete transaksi berhasil;
- input suara terbuka;
- proses teks voice menghasilkan preview;
- simpan transaksi suara berhasil;
- laporan bulanan tampil;
- ganti bulan laporan berhasil;
- anggaran tampil dengan progress;
- edit budget berhasil;
- hapus budget tidak menghapus transaksi;
- salin budget bulan lalu berhasil;
- target tabungan tampil;
- tambah dana tabungan berhasil;
- edit target tabungan berhasil;
- arsipkan target tabungan berhasil;
- pengaturan mata uang/bahasa bisa disimpan;
- logout berhasil;
- route protected redirect ke login.

---

## Deployment Hostinger

Deployment production ditargetkan untuk **Hostinger Premium shared hosting** dengan PHP 8.4 dan MySQL.

Ringkasan langkah:

1. build asset lokal:

```bash
npm run build
```

2. siapkan vendor production:

```bash
composer install --no-dev --optimize-autoloader
```

3. upload file Laravel ke server;
4. arahkan domain ke folder `public/` jika memungkinkan;
5. atur `.env` production;
6. jalankan migrasi:

```bash
php artisan migrate --force
```

7. cache konfigurasi production:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Panduan lengkap ada di:

```text
docs/DEPLOYMENT_HOSTINGER.md
```

---

## Batasan Saat Ini

Beberapa hal yang belum menjadi fitur penuh:

- belum ada service worker/offline mode penuh;
- belum ada push notification/email/SMS notification;
- belum ada exchange rate conversion;
- preferensi bahasa belum menerjemahkan seluruh UI;
- belum ada export PDF/CSV;
- belum ada family sharing multi-user household;
- belum ada restore archived savings goal;
- belum ada full contribution history page.

---

## Prinsip Pengembangan

Saat mengembangkan KeySaku:

- pertahankan kompatibilitas Hostinger shared hosting;
- jangan menambah kebutuhan production Node.js server;
- gunakan route name, jangan hardcode URL;
- UI copy harus Bahasa Indonesia;
- perubahan fitur harus kecil, fokus, dan teruji;
- jangan mengubah schema tanpa rencana yang jelas;
- jalankan test dan build setelah perubahan penting.

---

## Referensi Dokumentasi

Dokumen pendukung:

- `docs/PRD.md` — visi dan kebutuhan produk;
- `docs/ROADMAP.md` — roadmap pengembangan;
- `docs/TASKS.md` — daftar task per fase;
- `docs/DECISIONS.md` — catatan keputusan teknis;
- `docs/SCHEMA.md` — dokumentasi skema database;
- `docs/VOICE_INPUT.md` — catatan fitur input suara;
- `docs/DEPLOYMENT_HOSTINGER.md` — panduan deploy Hostinger.

---

## Lisensi

Belum ada lisensi khusus yang ditetapkan untuk project ini.
