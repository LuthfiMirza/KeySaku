# Voice Input — KeySaku

Dokumen ini menjelaskan pola input suara yang saat ini didukung oleh MVP KeySaku.

## Status
- Mendukung input pengeluaran dan pemasukan.
- Mendukung input suara via Web Speech API (PWA / Browser) dan via Telegram Bot (@Keysaku_bot).
- Mendukung kata keterangan tanggal natural seperti "hari ini", "kemarin", dan "kemaren".
- Transaksi yang disimpan memakai `source = voice` dan menyimpan transkrip di `raw_voice_text`.

## Format yang Didukung
Gunakan pola natural sehari-hari:

```text
[keterangan transaksi] [nominal] kategori [nama kategori]
```

Contoh Pengeluaran:
- “Beli nasi goreng dua puluh lima ribu kategori makanan”
- “Bayar parkir lima ribu kemarin”
- “Belanja dapur 150.000 kategori belanja dapur”
- “Beli obat tiga puluh ribu kategori kesehatan”

Contoh Pemasukan:
- “Gaji bulanan delapan juta”
- “Terima bonus proyek 500rb”

## Integrasi Telegram Voice
- **Keyboard Dictation:** Tekan ikon mic pada keyboard HP saat mengetik pesan ke @Keysaku_bot.
- **Voice Note (.oga):** Kirim VN langsung ke bot dengan backend transkripsi Google Gemini Flash API (`GEMINI_API_KEY`).
