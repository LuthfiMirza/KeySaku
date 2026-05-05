# PRD — KeySaku

## Product Name
KeySaku — Aplikasi Pengelolaan Keuangan Keluarga

## Vision
Membantu keluarga Indonesia mengelola keuangan sehari-hari dengan cara yang mudah, cepat, dan menyenangkan melalui antarmuka mobile-first modern dengan kemampuan input suara.

## Target Users
- Keluarga Indonesia usia 25–45 tahun
- Pengguna personal yang ingin mencatat pemasukan dan pengeluaran
- Pengguna yang terbiasa dengan smartphone dan aplikasi keuangan

## Core Problems
1. Pencatatan keuangan manual terasa merepotkan
2. Tidak ada gambaran jelas tentang ke mana uang pergi setiap bulan
3. Anggaran keluarga sering tidak terkontrol
4. Target tabungan sulit dicapai tanpa pemantauan rutin

## Solution
Aplikasi web PWA yang:
- Bisa diinstal di layar utama smartphone (PWA)
- Mendukung input transaksi via suara (SpeechRecognition)
- Menyediakan dashboard ringkasan bulanan
- Membantu atur anggaran per kategori
- Melacak progres target tabungan
- Memberikan insight otomatis tentang pola pengeluaran

## Key Features (by milestone)

### Milestone 1 — Foundation ✅
- Mobile-first layout shell
- Dashboard dengan data statis
- Bottom navigation
- PWA manifest

### Milestone 2 — Auth & Data
- Register / Login
- User profile
- Akun keuangan (kas, bank, e-wallet)
- Kategori pemasukan & pengeluaran

### Milestone 3 — Transactions
- Tambah transaksi manual (pemasukan/pengeluaran/transfer)
- Riwayat transaksi
- Filter dan pencarian

### Milestone 4 — Budgets & Goals
- Anggaran bulanan per kategori
- Target tabungan dengan progress bar
- Notifikasi mendekati batas anggaran

### Milestone 5 — Voice & Insights
- Input suara via Browser SpeechRecognition
- Parsing teks ke transaksi otomatis
- Insight pengeluaran otomatis
- Chart laporan bulanan (Chart.js)

### Milestone 6 — Polish & Deploy
- PWA service worker (offline support)
- Deployment ke Hostinger Premium
- Performance optimization
- Error monitoring

## Non-Goals
- Tidak terintegrasi langsung dengan rekening bank (open banking)
- Tidak ada fitur investasi
- Tidak ada fitur pinjaman/kredit
- Tidak membutuhkan Node.js server di produksi

## Success Metrics
- User bisa mencatat transaksi dalam < 30 detik
- Dashboard load time < 2 detik pada mobile
- Installable sebagai PWA di Android dan iOS
