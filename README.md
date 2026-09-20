<div align="center">

# 💰 KeySaku

**Aplikasi Pengelolaan Keuangan Keluarga — Mobile-First PWA**

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Livewire](https://img.shields.io/badge/Livewire-3-FB70A9?style=flat-square&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![TailwindCSS](https://img.shields.io/badge/Tailwind-3-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![PWA](https://img.shields.io/badge/PWA-Ready-5A0FC8?style=flat-square&logo=pwa&logoColor=white)](https://web.dev/progressive-web-apps/)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)

*Catat pemasukan & pengeluaran keluarga dalam hitungan detik — dengan dukungan offline dasar (PWA).*

</div>

---

## 📋 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Memulai (Setup Lokal)](#-memulai-setup-lokal)
- [Struktur Proyek](#-struktur-proyek)
- [Deployment ke Hostinger](#-deployment-ke-hostinger)
- [Roadmap](#-roadmap)
- [Kontribusi](#-kontribusi)

---

## 🏠 Tentang Proyek

**KeySaku** adalah aplikasi web *Progressive Web App* (PWA) yang dirancang untuk membantu keluarga Indonesia mengelola keuangan sehari-hari — mencatat transaksi, memantau anggaran bulanan, dan melacak target tabungan — dengan antarmuka yang sederhana, modern, dan mobile-friendly.

### Masalah yang Diselesaikan

| Masalah | Solusi KeySaku |
|---|---|
| Pencatatan keuangan manual merepotkan | Input cepat + input suara via browser |
| Tidak tahu ke mana uang pergi | Dashboard & laporan visual bulanan |
| Anggaran keluarga tidak terkontrol | Anggaran per kategori + peringatan otomatis |
| Target tabungan sulit dicapai | Progress tracker tabungan real-time |

---

## ✨ Fitur Utama

### 💳 Manajemen Akun
- Kelola beberapa akun keuangan (Kas, Bank, E-Wallet)
- Saldo akun diperbarui otomatis saat transaksi dibuat/diedit/dihapus

### 📒 Transaksi
- Catat pemasukan dan pengeluaran
- Filter riwayat transaksi berdasarkan bulan dan kategori
- Edit dan hapus transaksi dengan konfirmasi

### 🎤 Input Suara *(Milestone 5)*
- Catat transaksi cukup dengan ucapan menggunakan Browser SpeechRecognition
- Parser otomatis: *"Beli makan siang dua puluh ribu"* → transaksi langsung terbuat

### 📊 Anggaran Bulanan
- Tetapkan anggaran per kategori per bulan
- Indikator status: **Aman / Mendekati Batas / Lewat Batas**
- Salin anggaran bulan lalu dengan satu klik
- Ringkasan anggaran keseluruhan di dashboard

### 🎯 Target Tabungan
- Buat goal tabungan dengan nominal target dan tenggat waktu
- Lacak progress dengan progress bar visual
- Tambah kontribusi kapan saja

### 📈 Laporan & Insight
- Grafik batang pemasukan vs. pengeluaran 6 bulan terakhir (Chart.js)
- Grafik donat pengeluaran per kategori
- Insight otomatis di dashboard

### 📱 PWA — Installable & Offline-Ready
- Dapat diinstal di layar utama Android & iOS seperti aplikasi native
- Service worker untuk dukungan offline dasar

---

## 🛠 Tech Stack

| Layer | Teknologi |
|---|---|
| **Backend** | Laravel 12, PHP 8.4 |
| **Frontend** | Blade, Livewire 3, Alpine.js |
| **Styling** | Tailwind CSS 3 |
| **Charts** | Chart.js |
| **Database** | MySQL (production) / SQLite (development) |
| **Build** | Vite |
| **Hosting** | Hostinger Premium Shared Hosting |

---

## 🚀 Memulai (Setup Lokal)

### Prasyarat

- PHP >= 8.4 dengan ekstensi: `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`
- Composer >= 2.x
- Node.js >= 20.x & npm

### Instalasi

```bash
# 1. Clone repository
git clone https://github.com/LuthfiMirza/KeySaku.git
cd KeySaku

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies & build assets
npm install && npm run build

# 4. Salin file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate
```

### Konfigurasi Database

**Opsi A — SQLite (Cepat untuk development):**
```env
# .env
DB_CONNECTION=sqlite
```
```bash
touch database/database.sqlite
php artisan migrate --seed
```

**Opsi B — MySQL:**
```env
# .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=keysaku
DB_USERNAME=root
DB_PASSWORD=your_password
```
```bash
php artisan migrate --seed
```

### Jalankan Aplikasi

```bash
php artisan serve
```

Buka browser dan akses: **http://localhost:8000**

### Jalankan Test

```bash
php artisan test
```

---

## 📁 Struktur Proyek

```
KeySaku/
├── app/
│   ├── Http/Controllers/       # Controller tipis — logika di Livewire/Service
│   ├── Livewire/               # Komponen Livewire (CRUD real-time)
│   │   ├── Transactions/       # CreateTransaction, EditTransaction, VoiceTransaction
│   │   ├── Budgets/            # ManageBudgets
│   │   ├── Goals/              # ManageGoals
│   │   └── Accounts/           # ManageAccounts
│   ├── Models/                 # Eloquent Models
│   ├── Observers/              # TransactionObserver (auto-update saldo)
│   └── Providers/
├── database/
│   └── migrations/             # Skema database bertahap
├── resources/
│   └── views/
│       ├── layouts/            # Layout utama + partials (nav, modal)
│       ├── livewire/           # Blade views untuk komponen Livewire
│       └── pages/              # Halaman statis (dashboard, laporan, dll.)
├── routes/
│   └── web.php                 # Routing dengan named routes
├── docs/                       # Dokumentasi proyek (PRD, SCHEMA, TASKS, dll.)
└── public/
    └── manifest.json           # PWA manifest
```

---

## 🌐 Deployment ke Hostinger

KeySaku dirancang untuk berjalan di **PHP shared hosting** tanpa Node.js server di production.

### Langkah Deployment

```bash
# 1. Build asset untuk production
npm run build

# 2. Install vendor tanpa dev dependencies
composer install --no-dev --optimize-autoloader

# 3. Upload semua file ke server Hostinger
#    (via FTP/SSH/File Manager — lihat docs/DEPLOYMENT_HOSTINGER.md)

# 4. Di Hostinger terminal, jalankan:
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Konfigurasi `.env` Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

> Lihat panduan lengkap di [`docs/DEPLOYMENT_HOSTINGER.md`](docs/DEPLOYMENT_HOSTINGER.md)

---

## 🗺 Roadmap

| Milestone | Status | Keterangan |
|---|---|---|
| **M1** — Foundation | ✅ Selesai | Laravel scaffold, layout mobile, PWA manifest |
| **M2** — Auth & Akun | ✅ Selesai | Register/login (Breeze), manajemen akun keuangan |
| **M3** — Transaksi | ✅ Selesai | CRUD transaksi, filter bulan & kategori |
| **M4** — Anggaran & Tabungan | ✅ Selesai | Anggaran bulanan, target tabungan, progress bar |
| **M5** — Voice & Insight | ✅ Selesai | Input suara, laporan Chart.js, auto-update saldo |
| **M6** — Polish & Deploy | ✅ Selesai | PWA service worker, .htaccess, production caching |

---

## 🤝 Kontribusi

Pull request dan issue sangat diterima! Sebelum berkontribusi, pastikan:

1. Fork repository ini
2. Buat branch fitur baru: `git checkout -b feat/nama-fitur`
3. Commit dengan pesan yang jelas dan atomik
4. Buat Pull Request ke branch `main`

Baca [`AGENTS.md`](AGENTS.md) untuk konvensi kode dan panduan arsitektur proyek.

---

## 📄 Lisensi

Proyek ini menggunakan lisensi [MIT](LICENSE).

---

<div align="center">

Dibuat dengan ❤️ untuk keluarga Indonesia

**[⬆ Kembali ke atas](#-keysaku)**

</div>
