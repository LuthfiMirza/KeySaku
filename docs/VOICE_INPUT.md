# Voice Input — KeySaku

Dokumen ini menjelaskan pola input suara yang saat ini didukung oleh MVP KeySaku.

## Status
- Input suara saat ini hanya untuk transaksi pengeluaran.
- Pengguna tetap harus mengecek preview sebelum menyimpan transaksi.
- Transkrip bisa diedit manual sebelum diproses.
- Transaksi yang disimpan dari halaman ini memakai `source = voice` dan menyimpan transkrip di `raw_voice_text`.

## Format yang Didukung
Gunakan pola sederhana:

```text
[keterangan pengeluaran] [nominal] kategori [nama kategori]
```

Contoh:
- “Beli nasi goreng dua puluh lima ribu kategori makanan”
- “Bayar parkir lima ribu kategori transportasi”
- “Belanja dapur 150.000 kategori belanja dapur”
- “Beli obat tiga puluh ribu kategori kesehatan”

## Nominal yang Didukung
Parser MVP mendukung:
- Angka biasa, misalnya `25000`
- Angka ribuan dengan titik, misalnya `25.000` atau `150.000`
- Bentuk `rb` atau `ribu`, misalnya `25rb` atau `25 ribu`
- Beberapa nominal kata sederhana, misalnya `lima ribu`, `dua puluh lima ribu`, dan `tiga puluh ribu`

## Kategori
Kategori harus disebut setelah kata `kategori`, misalnya:

```text
kategori makanan
```

Nama kategori harus cocok dengan kategori pengeluaran milik pengguna.

## Batasan MVP
- Belum mendukung input pemasukan via suara.
- Belum mendukung transfer via suara.
- Belum mendukung tanggal natural seperti “kemarin” atau “minggu lalu”.
- Belum mendukung parsing kalimat bebas yang kompleks.
- Jika hasil parser belum tepat, gunakan edit transkrip atau input manual.
