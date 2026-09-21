# Integrasi Telegram Bot — KeySaku (@Keysaku_bot)

Dokumentasi arsitektur, panduan konfigurasi, dan fitur integrasi Telegram Bot untuk KeySaku.

---

## 1. Arsitektur Integrasi

Integrasi Telegram Bot di KeySaku dirancang mendukung 2 mode eksekusi:

1. **Local Development (Auto-Fetching / Long Polling):**
   - Menggunakan command `php artisan telegram:poll`.
   - Mengatasi kendala server laptop yang tidak menyala 24 jam.
   - Pesan yang dikirim saat server offline disimpan di cloud queue Telegram (hingga 24 jam) dan langsung diproses begitu server menyala.
2. **Production Deployment (Hostinger Shared Hosting / Webhook):**
   - Menggunakan webhook `POST /api/telegram/webhook`.
   - Diatur via command: `php artisan telegram:webhook set --url=https://domain.com/api/telegram/webhook`.
   - Endpoint di-exempt dari CSRF verification di `bootstrap/app.php`.

---

## 2. Konfigurasi Lingkungan (.env)

```env
TELEGRAM_BOT_TOKEN=your_bot_token_from_botfather
TELEGRAM_BOT_USERNAME=Keysaku_bot
TELEGRAM_WEBHOOK_SECRET=optional_secret_token
GEMINI_API_KEY=optional_key_for_voice_note_transcription
GEMINI_MODEL=gemini-3.5-flash-lite
GROQ_API_KEY=optional_key_for_groq_whisper
```

---

## 3. Fitur yang Didukung

### A. Pairing Akun
- Format: `/start <token>`
- Token didapatkan dari menu Web: **Pengaturan > Integrasi Telegram**.
- Mengaitkan `telegram_chat_id` secara aman ke akun `User`.

### B. Interactive Menu Keyboard
- Tombol keyboard interaktif di bagian bawah layar:
  - `💰 Cek Saldo` ➔ Menampilkan saldo akun dan total saldo keluarga.
  - `📊 Rekap Bulan Ini` ➔ Ringkasan finansial bulanan dengan visual progress bar.
  - `📋 Riwayat` ➔ Menampilkan 5 transaksi terakhir lengkap dengan ID dan tombol aksi hapus cepat.
  - `🗑️ Batalkan Transaksi` ➔ Membatalkan transaksi terakhir (dalam 60 menit) dan memulihkan saldo akun.
  - `❓ Bantuan` ➔ Menampilkan panduan dan contoh format transaksi.

### C. Natural Language Transaction Recording
- Format pengeluaran: `makan siang 35.000` atau `kopi susu 25rb kategori hiburan`.
- Format pemasukan: `gaji bulanan 8.000.000` atau `terima transfer 500rb`.
- Deteksi tanggal: "hari ini", "kemarin".
- Otomatis memotong/menambah saldo akun dompet/rekening via `TransactionObserver`.
- Disertai **Inline Action Button** `[🗑️ Batalkan Transaksi Ini]` untuk menghapus transaksi seketika dengan 1 ketukan.

### D. Koreksi, Edit, dan Pembatalan Transaksi
- **Batalkan Terakhir:** `/batal`
- **Hapus Spesifik berdasarkan ID:** `/hapus <ID>` atau `/batal <ID>` (misal: `/hapus 9`).
- **Edit Transaksi via Chat:** `/edit <ID> <catatan baru> <nominal baru>` (misal: `/edit 9 beli kopi 37.000`).
- **Riwayat & Tombol Hapus Cepat:** `/riwayat` (menampilkan 5 transaksi terakhir beserta tombol inline untuk hapus).
- **Edit di Aplikasi Web PWA:** Buka menu Riwayat Transaksi di web app KeySaku untuk pengeditan visual lengkap.

### E. Voice Input (Input Suara)
- **Keyboard Dictation:** Menggunakan tombol mic keyboard HP (0 setup).
- **Voice Note (.oga):** Transkripsi otomatis pesan suara Telegram via Google Gemini Flash API (`GEMINI_API_KEY`) dengan domain priming kosakata belanja harian Indonesia.
