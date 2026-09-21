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
  - `🗑️ Batalkan Transaksi` ➔ Membatalkan transaksi terakhir (dalam 60 menit) dan memulihkan saldo akun.
  - `❓ Bantuan` ➔ Menampilkan panduan dan contoh format transaksi.

### C. Natural Language Transaction Recording
- Format pengeluaran: `makan siang 35.000` atau `kopi susu 25rb kategori hiburan`.
- Format pemasukan: `gaji bulanan 8.000.000` atau `terima transfer 500rb`.
- Deteksi tanggal: "hari ini", "kemarin".
- Otomatis memotong/menambah saldo akun dompet/rekening via `TransactionObserver`.

### D. Voice Input (Input Suara)
- **Keyboard Dictation:** Menggunakan tombol mic keyboard HP (0 setup).
- **Voice Note (.oga):** Transkripsi otomatis pesan suara Telegram via Google Gemini Flash API (`GEMINI_API_KEY`).
