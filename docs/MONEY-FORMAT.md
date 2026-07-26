# Money Format — PHP ↔ JS Parity

`App\Support\Money::format()` (PHP) dan `formatMoney()` (JS di `resources/js/money.js`) harus menghasilkan string identik untuk currency & value yang sama.

## Sumber kebenaran
- `config/currencies.php` — daftar currency + rule format.
- `window.appCurrency` — di-inject di `resources/views/layouts/app.blade.php` (hanya layout `main`/`app`, bukan guest) berdasarkan `Money::config()` untuk user yang login.

## Tabel referensi

### IDR (symbol=`Rp`, position=before, decimals=0, dec=`,`, thou=`.`, space=true)

| Value      | Output          |
|------------|-----------------|
| 0          | `Rp 0`          |
| 1000       | `Rp 1.000`      |
| 25000      | `Rp 25.000`     |
| 5000000    | `Rp 5.000.000`  |
| 1234567.89 | `Rp 1.234.568`  |
| -25000     | `-Rp 25.000`    |
| null       | `Rp 0`          |

### USD (symbol=`$`, position=before, decimals=2, dec=`.`, thou=`,`, space=false)

| Value      | Output          |
|------------|-----------------|
| 0          | `$0.00`         |
| 1000       | `$1,000.00`     |
| 25000      | `$25,000.00`    |
| 5000000    | `$5,000,000.00` |
| 1234567.89 | `$1,234,567.89` |
| -25000     | `-$25,000.00`   |
| null       | `$0.00`         |

## Verifikasi manual JS

Buka DevTools console setelah login lalu jalankan:

```js
[0, 1000, 25000, 5000000, 1234567.89, -25000, null].forEach(v => console.log(v, '→', formatMoney(v)));
```

Bandingkan dengan tabel currency aktif di atas.

## Catatan
- `formatMoney(v, { symbol: false })` mengembalikan angka saja (dipakai untuk Alpine counter dashboard).
- Jika `window.appCurrency` tidak ada (halaman guest), JS fallback ke IDR default.
- Rounding: `Math.round(abs * 10^decimals) / 10^decimals` untuk match perilaku `number_format()` PHP (half away from zero).

## Cakupan migrasi (basis: `main`)

Total call site di branch `main`: 36 baris grep di 6 file Blade:
- `resources/views/pages/dashboard.blade.php` (15)
- `resources/views/livewire/budgets/manage-budgets.blade.php` (8)
- `resources/views/pages/reports/index.blade.php` (7)
- `resources/views/livewire/savings/manage-savings-goals.blade.php` (4)
- `resources/views/livewire/transactions/voice-transaction.blade.php` (1)
- `resources/views/livewire/transactions/transaction-list.blade.php` (1)

**Tidak berlaku di main** (hanya ada di branch `ui/bento-dark-dashboard`):
- Alpine counter dengan `x-text=toLocaleString(...)` (B1)
- Chart.js tooltip dengan `toLocaleString(...)` (B2)
- File landing marketing (`pages/landing.blade.php`, `pages/landing-playful.blade.php`) (B3)
- `auth/onboarding.blade.php`
