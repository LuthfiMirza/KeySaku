# Salvage Audit — `ui/bento-dark-dashboard` → `main`

Analisis read-only atas perubahan **backend** di branch arsip `ui/bento-dark-dashboard` yang tidak ada di `main`. Tujuan: memilih mana yang layak dibawa balik.

Tanggal audit: 26 Juli 2026.
Basis: `main` (HEAD saat audit = `3e6d0b9`).

---

## Ruang lingkup perubahan backend

| File | Baris | Sifat |
|------|------:|-------|
| `app/Services/VoiceTransactionParser.php` | +111 / −3 | **[LOGIKA]** fitur baru |
| `app/Livewire/Transactions/VoiceTransaction.php` | +5 / −3 | **[LOGIKA]** dependen parser baru |
| `app/Livewire/Transactions/CreateTransaction.php` | +48 / −2 | **[LOGIKA]** seed kategori + normalize + redirect |
| `app/Http/Controllers/TransactionController.php` | +101 (new) | **[LOGIKA]** form fallback — **tidak diambil** |
| `routes/web.php` | +4 | **[LOGIKA]** 3 route baru — bergantung file UI-only |
| `tests/Unit/VoiceTransactionParserTest.php` | +76 / −2 | **[TEST]** 5 case baru |
| `tests/Feature/Transactions/VoiceTransactionTest.php` | +29 | **[TEST]** 1 case baru |

Nol perubahan di `app/Models/`, `database/migrations/`, `config/`.

---

## Rekomendasi

| # | Item | Alasan | Risiko | Sudah punya test? | Layak? |
|---|------|--------|:------:|:-----------------:|:------:|
| 1 | `app/Services/VoiceTransactionParser.php` (upgrade penuh) + 5 test unit baru + 1 test feature baru + patch `VoiceTransaction.php` (dukungan `type` dari draft) | Menambah parsing tanggal ("hari ini", "kemarin"), tipe transaksi dari keyword & kategori, pattern nominal `X juta`, `X juta Y ribu`. Nol perubahan kontrak (signature/return keys). Berdiri sendiri, tidak menarik view. | **Sedang** | ✅ 6 test | **LAYAK** — satu kelompok bundle |
| 2 | `CreateTransaction::normalizeAmount()` (helper strip non-digit sebelum validate) | Izinkan user ketik "50.000" atau "Rp 50.000" di input amount tanpa validation error. Tidak menyentuh view. | **Rendah** | ❌ tidak ada test yang secara spesifik menguji ini | **LAYAK** — tapi tulis 1 test kecil |
| 3 | `CreateTransaction::seedDefaultsIfEmpty()` + `defaultCategories()` (seed 13 kategori saat mount) | Menghindari empty state buruk untuk user baru. Namun sudah ada `CategoryManager::seedDefaultsIfEmpty()` yang melakukan hal sama saat user buka `/kategori`. Duplikasi logika. Kalau diambil, sebaiknya extract ke Service atau Observer, bukan copy-paste. | **Sedang** (duplikasi kode) | ❌ tidak ada test | **LAYAK TAPI PERLU REFACTOR** — jangan copy mentah; consolidate ke satu tempat |
| 4 | `CreateTransaction::save()` return `redirect()->route(...)->with(...)` (bukan void + session flash) | UX lebih baik: user kembali ke halaman list dengan flash message, alih-alih tetap di form. Konsisten dengan `EditTransaction::save()` yang sudah pakai flash saja. | **Rendah** | ❌ tidak ada test yang assert redirect | **LAYAK** — 1 test kecil untuk assertRedirect |
| 5 | `app/Http/Controllers/TransactionController.php` | Duplikasi CreateTransaction untuk form POST fallback. Route `POST /transaksi` hanya dipakai oleh view UI (attribute `action=` sebagai graceful degradation kalau Livewire mati). Di `main`, single write path via Livewire lebih bersih dan sesuai target arsitektur. | — | Test ada (?, cek: tidak ditemukan file test TransactionControllerTest) | **TIDAK LAYAK** — bertentangan dengan "single write path" yang jadi target `main` |
| 6 | Route `POST /transaksi` (`transactions.store`) | Butuh item 5. | — | — | **TIDAK LAYAK** (dependen item 5) |
| 7 | Route `GET /landing-v2` + view `pages.landing-playful.blade.php` | Landing marketing UI baru. **Terikat markup**. | — | — | **TIDAK LAYAK** (bukan backend murni; UI content) |
| 8 | Route `GET /auth/onboarding` + view `auth.onboarding.blade.php` | Onboarding page UI baru. **Terikat markup**. | — | — | **TIDAK LAYAK** (bukan backend murni; UI content) |

---

## Detail teknis item 1 — VoiceTransactionParser upgrade

### Kontrak
- **Signature `parse(string, Collection<Category>): array`** — sama.
- **Return keys** — sama: `type`, `amount`, `category_id`, `category_name`, `transaction_date`, `note`, `raw_voice_text`, `warnings`.

### Perubahan perilaku yang berpotensi breaking
1. `type` sebelumnya **hardcoded `EXPENSE`**. Sekarang bisa `INCOME` bila:
   - text mengandung kata kunci income (`pemasukan`, `gaji`, `bonus`, dll.), **atau**
   - kategori match adalah kategori income (`type = income`).
2. `transaction_date` sebelumnya **selalu `now()->toDateString()`**. Sekarang bisa jadi kemarin (dua kata dukungan: `kemarin`, `kemaren`).
3. Kategori match sebelumnya **strict equality (lowercased)**. Sekarang:
   - Pass 1: equality atau `Str::contains` substring.
   - Pass 2 (kalau gagal): normalized (strip kata sambung `dan|untuk|sebesar|senilai`) + Levenshtein ≤ 2.
   - **Risiko false-positive**: mis. "gaji" ↔ "gajih" cocok (Levenshtein=1). Cocok untuk voice yang sering imperfect, tapi bisa salah bind.
4. Nominal:
   - Pattern baru `X juta Y (ribu)?`.
   - Pattern baru `X juta` / `X jt`.
   - Existing pattern `[0-9]+` sekarang mendeteksi trailing `juta` di teks untuk kalikan 10⁶.

### Ketergantungan
Berdiri sendiri. Model & migration tidak berubah. Bisa dicherry-pick tanpa menarik file lain — **selama** `VoiceTransaction.php` di-patch bersamaan supaya bisa membaca `type` dari draft (kalau tidak, income tetap tersimpan sebagai expense).

### Test yang menyertai
Semua **5 unit test + 1 feature test** menguji fitur baru; tidak menguji ulang perilaku lama. Test lama tetap valid tanpa perubahan (kompatibel karena default expense dipertahankan bila tidak ada keyword/kategori match).

---

## Detail teknis item 2 — `normalizeAmount()`

```php
private function normalizeAmount(string $amount): string
{
    return preg_replace('/\D+/', '', $amount) ?: '';
}
```
Dipanggil di awal `save()` sebelum `$this->validate()`. Dampak: input `"50.000"` → `"50000"` sebelum divalidasi `numeric`. **Aman**, satu-satunya efek user: bisa ketik pemisah ribuan.

---

## Detail teknis item 3 — `seedDefaultsIfEmpty()` (peringatan duplikasi)

Sudah ada di `app/Livewire/CategoryManager.php` (`seedDefaultsIfEmpty()` + `$defaults` array dengan 13 item identik). Membawa `CreateTransaction::seedDefaultsIfEmpty()` = duplikasi array 13 item + method di dua tempat.

**Saran**: kalau diambil, extract ke helper/Service (`App\Support\DefaultCategorySeeder` atau observer di `User::created`) supaya satu sumber kebenaran. Kalau tidak sempat, skip item ini — user tetap dapat kategori default via `CategoryManager` di halaman `/kategori`.

---

## Detail teknis item 4 — redirect di `save()`

Sekarang di `main`:
```php
session()->flash('transaction_status', 'Transaksi berhasil disimpan.');
```
User tetap di form (Livewire re-render). Di UI:
```php
return redirect()->route('transactions.index')->with('transaction_status', '...');
```
User dilempar ke list. Konsisten dengan pola redirect di UI, tapi juga konsisten dengan `save()` di `VoiceTransaction.php` **main** yang sudah `return redirect()->route(...)->with(...)`. Jadi ambil item 4 = menyeragamkan alur.

---

## Ringkasan usulan

Bundle yang direkomendasikan (satu commit atau satu PR terpisah):
- **Bundle A (Voice parser upgrade)**: item 1 lengkap. 6 test baru. Berdiri sendiri.
- **Bundle B (Create transaction QoL)**: item 2 (normalize) + item 4 (redirect). Perlu 2 test kecil baru. Tidak menyentuh view.
- **Bundle C (Seed default consolidation)**: skip mentah, atau kerjakan sebagai refactor terpisah kalau prioritas.

Semua di luar itu (`TransactionController`, route `POST /transaksi`, landing, onboarding) **tidak layak** karena terikat markup UI atau bertentangan dengan arsitektur single-write-path di `main`.

