# QA Pre-Release Audit Report — KeySaku

**Tanggal Audit:** 2026-09-20  
**Branch Audit:** `qa/pre-release-audit`  
**Auditor Role:** Senior QA Engineer + Laravel Security Reviewer  
**Verdict:** ✅ **LAYAK RILIS DENGAN CATATAN**

---

## 1. Ringkasan Eksekutif

KeySaku adalah aplikasi Laravel 12 + Livewire 3 yang telah melewati 6 milestone pengembangan. Secara keseluruhan, kode sudah terstruktur dengan baik, model-scoping data ke household sudah konsisten, dan test coverage sudah memadai (212 test, 901 assertions).

Audit ini menemukan **3 bug confirmed** yang semuanya telah diperbaiki dalam sesi ini, serta **5 risiko tersisa** yang bersifat medium/low yang harus diselesaikan sebelum atau sesaat setelah deploy production.

---

## 2. Tabel Bug

| ID | Severity | Area | Langkah Reproduksi | Expected | Actual | Bukti | Status |
|---|---|---|---|---|---|---|---|
| BUG-01 | **Critical** | Routing / Views | Akses halaman Dashboard, Laporan, atau Reports saat login | HTTP 200 | HTTP 500 `RouteNotFoundException` | `resources/views/pages/dashboard.blade.php:157`, `reports/index.blade.php:106,159` | ✅ **FIXED** |
| BUG-02 | **High** | Security / PWA | Logout user A, login user B di browser sama (offline) | User B melihat halaman login | User B melihat halaman terautentikasi user A yang ter-cache SW | `public/sw.js` — HTML pages di-cache tanpa session boundary | ✅ **FIXED** |
| BUG-03 | **Medium** | Security / Config | Gunakan `.env.example` sebagai base untuk config production | `APP_DEBUG=false` | `APP_DEBUG=true` (expose stack trace di production) | `.env.example:4` | ✅ **FIXED** |

---

## 3. Hasil Test Suite

### Sebelum Perbaikan (Baseline)

```
Tests:    12 failed, 200 passed (854 assertions)
Duration: ~11s

Failing:
  - DashboardBudgetInsightTest (2 test)
  - DashboardInitialBalancePromptTest (3 test)
  - DashboardInitialBalanceTest (2 test)
  - DashboardSavingsInsightTest (3 test)
  - HouseholdSharingTest (1 test)
  - Reports\ReportPageTest (1 test)

Root Cause: Route [transactions.voice] not defined.
(dipicu oleh template rendering di dashboard.blade.php dan reports/index.blade.php)
```

### Setelah Perbaikan

```
Tests:    212 passed (901 assertions)   ✅ 0 failed
Duration: ~6.5s

Tambahan test passes dari route fix yang tidak langsung — 12 test sebelumnya
gagal karena HTTP 500 saat merender halaman, kini semua kembali hijau.
```

### Route Cache

```
php artisan route:cache → SUCCESS ✅
(Tidak ada closure route yang merusak cache)
```

---

## 4. Audit Struktur

### Migrations — [CONFIRMED] Bersih

Semua 23 migration berjalan clean dengan `migrate:fresh` tanpa error.  
Tidak ada duplicate schema. `savings_goals` (bukan `saving_goals`) konsisten di seluruh codebase.

### Model / Livewire Duplikat — [CONFIRMED] Bersih

| Komponen | File | Status |
|---|---|---|
| ManageBudgets | `app/Livewire/Budgets/ManageBudgets.php` | ✅ Satu komponen, aktif |
| ManageSavingsGoals | `app/Livewire/Savings/ManageSavingsGoals.php` | ✅ Satu komponen, aktif |
| TransactionList | `app/Livewire/Transactions/TransactionList.php` | ✅ Aktif |
| VoiceTransaction | `app/Livewire/Transactions/VoiceTransaction.php` | ✅ Digunakan via modal |

### Household Scoping — [CONFIRMED] Lengkap dan Konsisten

Semua Livewire component melakukan query melalui `auth()->user()->household->...->whereKey($id)`. Tidak ada query yang langsung ke model tanpa household scope.

### Versi PHP — [CONFIRMED] Inkonsistensi Ringan

| Sumber | Versi |
|---|---|
| `composer.json` | `^8.4` |
| `README.md` | `>= 8.4` |
| PHP aktual di lokal | `8.5.5` |

> **[SUSPECTED]** Lokal menggunakan PHP 8.5 yang belum direferensikan di docs. Tidak ada bug aktif, tapi perlu dicatat karena Hostinger mungkin menggunakan PHP 8.3 atau 8.4.

---

## 5. Audit Keamanan

### IDOR (Insecure Direct Object Reference) — [CONFIRMED] Aman

Semua komponen Livewire yang menerima ID luar (budget_id, goal_id, transaction_id, contribution_id) melakukan lookup melalui:

```php
auth()->user()->household->budgets()->whereKey($budgetId)->firstOrFail();
```

User B tidak dapat mengakses data user A karena scope selalu melalui household milik user yang sedang login.

**Evidence:** `ManageBudgets.php:46-47`, `ManageSavingsGoals.php` — setiap action menggunakan household-scoped query.

### Mass Assignment — [CONFIRMED] Aman

Model menggunakan `$fillable` explicit. Tidak ada `$guarded = []` yang terbuka lebar.

### Livewire Public Properties — [CONFIRMED] Aman

Properties publik yang diekspos (month, year, editingBudgetId) tidak pernah langsung dipakai dalam query tanpa validasi ulang dari dalam action method.

### Service Worker Auth Caching — [CONFIRMED] FIXED (BUG-02)

SW sebelumnya meng-cache semua HTML response termasuk halaman terautentikasi. Jika user A login dan user B membuka browser yang sama dalam kondisi offline, SW bisa mengembalikan halaman dashboard user A.

**Fix:** HTML pages tidak lagi di-cache. Hanya static assets (CSS/JS/images) yang di-cache. Fallback offline sekarang mengarah ke `/login`.

---

## 6. Audit Logika Bisnis

### Balance Observer — [CONFIRMED] Benar

`TransactionObserver` menangani 4 skenario:
- `created` → increment/decrement saldo akun
- `updated` → revert saldo lama, apply saldo baru (termasuk ganti akun)  
- `deleted` (soft delete) → revert saldo
- `restored` → delegate ke `created`

**Risiko:** Observer menggunakan `increment/decrement` (atomic SQL update). Tidak ada race condition yang obvious, tapi jika ada banyak transaksi bersamaan (tidak realistis untuk household app), bisa terjadi double-apply jika observer dipanggil lebih dari sekali. [SUSPECTED - Low Risk]

### Anggaran — [CONFIRMED] Aman

- Copy budget bersifat idempoten: ada pengecekan `!$existing` sebelum insert.
- Status Aman/Mendekati Batas/Lewat Batas dihitung di PHP, bukan raw SQL.

### Tabungan — [CONFIRMED] Aman

- Kontribusi negatif/0 dicegah via validasi `min:1`.
- Goals yang diarsip dikecualikan dari dashboard lewat `where('status', STATUS_ACTIVE)`.

---

## 7. Audit Frontend / PWA

### Chart.js CDN — [SUSPECTED] Risiko Offline

`resources/views/pages/reports/index.blade.php:203` memuat Chart.js dari CDN:

```html
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/..."></script>
```

**Dampak Offline:** Laporan akan tampil tanpa grafik jika CDN tidak tersedia.  
**Rekomendasi:** Bundel Chart.js via npm dan Vite (`npm install chart.js`, import di `app.js`).  
**Priority:** Medium — tidak menyebabkan crash, hanya degradasi UI.

### SpeechRecognition — [CONFIRMED] Memiliki Guard

`VoiceTransaction.php` dan view sudah ada pengecekan browser support via Alpine.js. Tidak crash di browser tanpa dukungan.

### PWA Icons — [SUSPECTED] Icon Files Missing

```
public/images/icons/ direktori ada tapi kosong
```

File `icon-192.png` dan `icon-512.png` yang direferensikan di `manifest.json` tidak ada.  
**Dampak:** PWA tidak dapat diinstall dengan icon yang benar. Browser akan menampilkan default favicon.  
**Fix:** Generate dan upload kedua file icon PNG tersebut.

### Settings Currency/Language — [CONFIRMED] Hanya Tersimpan, Tidak Berdampak

Pengaturan mata uang dan bahasa tersimpan ke database tapi tidak digunakan sebagai locale aktif atau format angka. Ini berpotensi menyesatkan pengguna.  
**Rekomendasi:** Tandai di UI sebagai "Segera Hadir" atau implementasikan efek nyata.

---

## 8. Konsistensi Dokumen

### README vs Kode

| Klaim di README | Kenyataan |
|---|---|
| "Transfer antar akun" | ✅ Ada form transfer di CreateTransaction |
| "Tanpa perlu internet" | ⚠️ Chart.js dari CDN → tidak bisa tampil offline |
| "Peringatan otomatis mendekati batas" | ✅ Ada status Mendekati Batas/Lewat Batas |
| "Input suara" | ✅ Ada via global modal |

---

## 9. Daftar Test Baru yang Ditambahkan

Tidak ada test baru yang ditambahkan dalam sesi ini. Semua 12 test yang sebelumnya gagal kini **pass** karena perbaikan root cause (route `transactions.voice` yang dihapus tapi masih direferensikan di views).

---

## 10. Risiko Tersisa & Rekomendasi Sebelum Deploy

| # | Risiko | Severity | Rekomendasi |
|---|---|---|---|
| R-01 | PHP 8.5 lokal vs Hostinger mungkin 8.3/8.4 | Medium | Verifikasi PHP version di Hostinger panel, pastikan ≥ 8.4 |
| R-02 | Chart.js dari CDN — tidak berfungsi offline | Medium | Bundel via `npm install chart.js` + import di Vite |
| R-03 | PWA icons tidak ada (`icon-192.png`, `icon-512.png`) | Medium | Generate icon dan upload ke `public/images/icons/` |
| R-04 | Settings currency/language tidak berdampak | Low | Tandai "Segera Hadir" di UI atau implementasikan |
| R-05 | `SESSION_SECURE_COOKIE` tidak di-set `true` untuk production | Low | Set `SESSION_SECURE_COOKIE=true` di Hostinger .env setelah HTTPS aktif |

---

## 11. Perintah Validasi Final

```bash
# Pastikan semua test hijau
php artisan test

# Build production assets
npm run build

# Cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verifikasi tidak ada closure route yang menyebabkan cache gagal
php artisan route:cache  # harus SUCCESS
```

---

*Laporan ini dibuat berdasarkan hasil eksekusi nyata. Semua temuan [CONFIRMED] telah diverifikasi dengan output perintah dan stack trace.*
