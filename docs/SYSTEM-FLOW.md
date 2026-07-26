# System Flow & Architecture — KeySaku

> **Basis dokumen: branch `main` (per 26 Juli 2026).**
> Versi awal dokumen ini dibuat di branch `ui/bento-dark-dashboard` dan
> memuat detail UI (Alpine counter, Chart.js, landing pages) yang tidak
> ada di `main`. Dokumen sudah dikoreksi agar sesuai `main`.
> Rangkuman perbedaan ada di section terakhir.

Dokumen ini memetakan alur sistem, model data, otorisasi, dan logika perhitungan uang di KeySaku.
Dibuat melalui analisis read-only kode.

---

## A. Model & Skema Data

### 1. Daftar Model & Relasi

| Model | Relasi | File |
|-------|--------|------|
| **User** | `hasMany(Category)`, `hasMany(Transaction)`, `hasMany(Budget)`, `hasMany(SavingsGoal)` | `app/Models/User.php:12-47` |
| **Category** | `belongsTo(User)`, `hasMany(Transaction)`, `hasMany(Budget)` | `app/Models/Category.php:8-34` |
| **Transaction** | `belongsTo(User)`, `belongsTo(Category)` | `app/Models/Transaction.php:8-49` |
| **Budget** | `belongsTo(User)`, `belongsTo(Category)` | `app/Models/Budget.php:8-28` |
| **SavingsGoal** | `belongsTo(User)`, `hasMany(SavingsContribution)` | `app/Models/SavingsGoal.php:8-43` |
| **SavingsContribution** | `belongsTo(SavingsGoal)` | `app/Models/SavingsContribution.php:8-22` |

Semua model mempunyai `user_id` (foreign key ke User) kecuali `SavingsContribution` (punya `savings_goal_id`).

### 2. Tipe Kolom Uang di Migrations

| Tabel | Kolom | Tipe | File |
|-------|-------|------|------|
| transactions | amount | `decimal(15, 2)` | `database/migrations/2026_05_05_000003_create_transactions_table.php:18` |
| budgets | amount | `decimal(15, 2)` | `database/migrations/2026_05_06_000004_create_budgets_table.php:15` |
| savings_goals | target_amount | `decimal(15, 2)` | `database/migrations/2026_05_06_000005_create_savings_goals_table.php:15` |
| savings_goals | current_amount | `decimal(15, 2)` | `database/migrations/2026_05_06_000005_create_savings_goals_table.php:16` |
| savings_contributions | amount | `decimal(15, 2)` | `database/migrations/2026_05_06_000006_create_savings_contributions_table.php:12` |

**Semua kolom uang menggunakan `decimal(15, 2)` untuk presisi finansial.**

### 3. Model dengan SoftDeletes

- **Transaction**: `use SoftDeletes` (`app/Models/Transaction.php:8`)

Tidak ada model lain yang pakai soft delete. Kategori, Budget, SavingsGoal, SavingsContribution dihapus hard.

### 4. Casting untuk Kolom Uang & Tanggal

**User** (`app/Models/User.php:34-38`):
- `email_verified_at` → `datetime`
- `password` → `hashed`

**Transaction** (`app/Models/Transaction.php:37-41`):
- `amount` → `decimal:2`
- `transaction_date` → `date`

**Budget** (`app/Models/Budget.php:20-26`):
- `amount` → `decimal:2`
- `month` → `integer`
- `year` → `integer`

**SavingsGoal** (`app/Models/SavingsGoal.php:31-38`):
- `target_amount` → `decimal:2`
- `current_amount` → `decimal:2`
- `deadline` → `date`

**SavingsContribution** (`app/Models/SavingsContribution.php:16-22`):
- `amount` → `decimal:2`
- `contribution_date` → `date`

---

## B. Scoping & Otorisasi

### 5. Pembatasan Data Per User

**Metode: Manual `where('user_id', ...)` di tiap query + Route Model Binding check di Controller/Livewire.**

Tidak ada Global Scope. Tidak ada Policy. Pembatasan dilakukan secara eksplisit:

- **Livewire Components**: Gunakan `auth()->user()->categories()`, `auth()->user()->transactions()`, dll.
  - Contoh: `app/Livewire/Transactions/CreateTransaction.php:99` → `auth()->user()->categories()->where('type', ...)`
  
- **Controllers**: Gunakan `$request->user()->...` atau `auth()->user()->...`
  - Contoh: `app/Http/Controllers/DashboardController.php:10` → `$user = auth()->user()`

- **Model Route Binding**: Verify ownership di method (abort jika beda user)
  - Contoh: `app/Livewire/Transactions/EditTransaction.php:29` → `abort_unless($transaction->user_id === auth()->id(), 404)`

### 6. Jumlah Tempat Memanggil auth()->id() / auth()->user() / Auth::id()

**Total: 37 call sites** (berdasarkan grep)

File-file yang menggunakan auth():
1. `app/Livewire/Savings/ManageSavingsGoals.php`
2. `app/Livewire/Transactions/CreateTransaction.php`
3. `app/Livewire/Transactions/TransactionList.php`
4. `app/Livewire/Transactions/EditTransaction.php`
5. `app/Livewire/Transactions/VoiceTransaction.php`
6. `app/Livewire/Budgets/ManageBudgets.php`
7. `app/Livewire/CategoryManager.php`
8. `app/Http/Controllers/DashboardController.php`
9. `app/Http/Controllers/PageController.php`

### 7. Policy — Status

**TIDAK ADA Policy** di `app/Policies/`. Otorisasi dilakukan inline di Livewire component atau Controller:
- `abort_unless($transaction->user_id === auth()->id(), 404)` di `EditTransaction::mount()` dan `save()`
- `abort_unless($goal, 404)` setelah query dengan `auth()->user()->savingsGoals()->whereKey($goalId)`
- `abort_if(!$budget, 404)` setelah query dengan `auth()->user()->budgets()->whereKey($budgetId)`

**Model yang tidak ter-cover oleh Policy (karena tidak ada Policy sama sekali)**: User, Category, Transaction, Budget, SavingsGoal, SavingsContribution.

### 8. Route dengan Parameter ID & Verifikasi Kepemilikan

**Web routes** (`routes/web.php`):
```
GET  /transaksi/{transaction}/edit           → PageController::editTransaction(Transaction)
```

**Verifikasi** di `app/Http/Controllers/PageController.php:95-97`:
```php
public function editTransaction(Transaction $transaction): View
{
    abort_unless($transaction->user_id === auth()->id(), 404);
    return view('pages.transactions.edit', compact('transaction'));
}
```

Semua route lain tidak punya parameter ID model; mereka melalui Livewire component yang handle query scoped.

---

## C. Alur Perhitungan Uang

### 9. SALDO Dashboard

**Query**: `app/Http/Controllers/DashboardController.php:11-34`

```php
$allTimeIncome = $user->transactions()
    ->where('type', Transaction::TYPE_INCOME)
    ->sum('amount');

$allTimeExpense = $user->transactions()
    ->where('type', Transaction::TYPE_EXPENSE)
    ->sum('amount');

// Net balance
$netBalance = $allTimeIncome - $allTimeExpense;
```

**Karakteristik:**
- Dihitung dari **semua transaksi sepanjang waktu** (tidak ada periode filter).
- **Tidak ada kolom cache**. Dihitung on-the-fly setiap render.
- Transaksi soft-deleted **TIDAK termasuk** (karena query di atas tidak menggunakan `withTrashed()`).
- Tersimpan di view variable `$netBalance` di dashboard.

### 10. PROGRESS ANGGARAN

**On-the-fly calculation** di `app/Livewire/Budgets/ManageBudgets.php:263-290`:

```php
private function budgetsWithProgress()
{
    $budgets = $this->budgets()->get();
    $expenseTotals = $this->expenseTotalsByCategory();  // Query yang terpisah

    return $budgets->map(function (Budget $budget) use ($expenseTotals) {
        $budgetAmount = (float) $budget->amount;
        $usedAmount = (float) ($expenseTotals[$budget->category_id] ?? 0);
        $remainingAmount = $budgetAmount - $usedAmount;
        $percentage = $budgetAmount > 0 ? round(($usedAmount / $budgetAmount) * 100) : 0;
        // ...
    });
}

private function expenseTotalsByCategory()
{
    // Query transaksi pengeluaran bulan tertentu, grouped by category
    return auth()->user()
        ->transactions()
        ->selectRaw('category_id, SUM(amount) as total')
        ->where('type', Transaction::TYPE_EXPENSE)
        ->whereIn('category_id', $budgetCategoryIds)
        ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
        ->groupBy('category_id')
        ->pluck('total', 'category_id');
}
```

**Karakteristik:**
- Dihitung **on-the-fly setiap render** (tidak disimpan).
- **Per-bulan**, berdasarkan `$this->month` dan `$this->year` property di Livewire.
- Transaksi soft-deleted **TIDAK ikut** (no `withTrashed()`).

### 11. TARGET TABUNGAN

**Kontribusi data**:
- Tabel: `savings_contributions`
- Struktur: `savings_goal_id`, `amount`, `contribution_date`, `note`
- File: `app/Models/SavingsContribution.php`

**Penyimpanan current_amount**:
- Disimpan di kolom `current_amount` di tabel `savings_goals` (denormalisasi).
- Update saat `addContribution()` di `app/Livewire/Savings/ManageSavingsGoals.php:130-150`:

```php
DB::transaction(function () use ($goal, $validated) {
    SavingsContribution::create([
        'savings_goal_id' => $goal->id,
        'amount' => $validated['contributionAmount'],
        'contribution_date' => $validated['contributionDate'],
        'note' => $validated['contributionNote'] ?: null,
    ]);

    $goal->update([
        'current_amount' => (float) $goal->current_amount + (float) $validated['contributionAmount'],
    ]);
});
```

**Pengaruh ke Dashboard**:
- Kontribusi **TIDAK membuat Transaction** baru (tabel terpisah).
- **TIDAK mengurangi saldo dashboard** (net balance = income - expense, kontribusi tidak dihitung).
- Ditampilkan sebagai insight terpisah di dashboard (savings goal progress).

### 12. Soft-Deleted Transactions di Kalkulasi

**Status: Soft-deleted transactions TIDAK termasuk** dalam:
- Dashboard saldo (`sum('amount')` tanpa `withTrashed()`)
- Progress anggaran (query tanpa `withTrashed()`)
- Laporan bulanan (query tanpa `withTrashed()`)

**Bukti**:
- `app/Http/Controllers/DashboardController.php:11-34`: `$user->transactions()->sum('amount')` (no withTrashed)
- `app/Livewire/Budgets/ManageBudgets.php:281-290`: `auth()->user()->transactions()...` (no withTrashed)
- `app/Http/Controllers/ReportController.php:15-23`: `$user->transactions()->whereBetween(...)` (no withTrashed)

### 13. Timezone Batas Bulan

**Timezone**: `Asia/Jakarta` (default)
- File: `config/app.php` → `'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta')`

**Penggunaan**:
- `now()` menggunakan timezone config app.
- `$selectedMonth->startOfMonth()` dan `endOfMonth()` juga menggunakan timezone yang sudah set di Carbon.

**Contoh di Report**:
```php
// app/Http/Controllers/ReportController.php:65
return Carbon::createFromFormat('Y-m', $month, config('app.timezone'))->startOfMonth();
```

---

## D. Alur Transaksi

### 14. Alur Lengkap Membuat Transaksi Manual

**Route entry**: `GET /transaksi/tambah` → Livewire full-page. Di `main` tidak ada `POST /transaksi` maupun `TransactionController` — form fallback controller-based hanya ada di branch `ui/bento-dark-dashboard`.

**Via Livewire** (satu-satunya jalur di `main`):
1. User buka `GET /transaksi/tambah` → `resources/views/pages/transactions/create.blade.php`
2. Mount component `<livewire:transactions.create-transaction />` → `app/Livewire/Transactions/CreateTransaction.php`
3. Component `mount()` → `app/Livewire/Transactions/CreateTransaction.php:22-26`:
   - Set default tanggal = hari ini
   - Seed kategori default jika belum ada
   - Set kategori pertama
4. User input amount, type, category, date, note
5. Livewire `save()` → `app/Livewire/Transactions/CreateTransaction.php:34-85`:
   - Validate (amount, type, category, date, note)
   - Query kategori user: `auth()->user()->categories()->whereKey($category_id)`
   - Cek tipe kategori sesuai tipe transaksi
   - `Transaction::create([...])`
   - Redirect ke `transactions.index` dengan success message

**Via Form POST** (fallback): TIDAK ADA di `main`. Rute dan controller-nya (`TransactionController::store`, route `transactions.store`) hanya diperkenalkan di branch `ui/bento-dark-dashboard`.

**Events/Observers/Jobs**: TIDAK ADA. Tidak ada event listener atau job yang ke-trigger saat transaksi dibuat.

### 15. Pemilihan Kategori

**Saat Input Manual**:
- Dropdown di view (Livewire) menggunakan `$this->categories()` → `app/Livewire/Transactions/CreateTransaction.php:97-102`:
  ```php
  private function categories()
  {
      return auth()->user()
          ->categories()
          ->where('type', $this->type)
          ->orderBy('name');
  }
  ```
- Kategori **difilter berdasarkan jenis (income/expense)** yang dipilih user.
- Kategori default di-seed otomatis jika belum ada (first-time user).

**Kategori default** di `app/Livewire/Transactions/CreateTransaction.php:103-116`:
```php
private function defaultCategories(): array
{
    return [
        ['name' => 'Makanan', 'type' => Category::TYPE_EXPENSE, ...],
        ['name' => 'Transportasi', 'type' => Category::TYPE_EXPENSE, ...],
        // ... 11 lebih
    ];
}
```

---

## E. Input Suara

### 16. Alur Input Suara Lengkap

**Route entry**: `GET /transaksi/suara` → `PageController::voiceTransaction()` → `resources/views/pages/transactions/voice.blade.php`

**Frontend (Blade/Alpine)**:
1. View load `livewire:transactions.voice-transaction` component
2. Browser API `SpeechRecognition` ditangkap di **JavaScript/Alpine** (TIDAK ditemukan di controller/Livewire)
   - TIDAK DITEMUKAN kode JS/Alpine yang langsung menggunakan `SpeechRecognition`
   - Asumsi: Vue/Alpine directive di view yang belum di-inspect secara detail
3. Result transcript dikirim ke Livewire component via property binding
4. Livewire component `VoiceTransaction` (`app/Livewire/Transactions/VoiceTransaction.php:8-87`):
   - Property `$transcript` (string dari SpeechRecognition)
   - Method `parse(VoiceTransactionParser $parser)` → parse teks
   - Method `save()` → simpan Transaction

**Parse Flow** (`app/Livewire/Transactions/VoiceTransaction.php:18-25`):
```php
public function parse(VoiceTransactionParser $parser): void
{
    $this->validate(['transcript' => ['required', 'string', 'max:1000']]);
    
    $this->parsedDraft = $parser->parse(
        $this->transcript,
        auth()->user()->categories()->get()
    );
}
```

**Service Layer** (`app/Services/VoiceTransactionParser.php`):
- Menerima `string $transcript` dan `Collection $categories`
- Return array: `['type', 'amount', 'category_id', 'transaction_date', 'note', 'warnings', ...]`

**Save** (`app/Livewire/Transactions/VoiceTransaction.php:27-73`):
- Validate `parsedDraft` not null dan valid
- Query kategori user
- Create Transaction dengan `source = Transaction::SOURCE_VOICE` dan `raw_voice_text = $transcript`

### 17. Logika Parsing Teks Suara

**File**: `app/Services/VoiceTransactionParser.php:17-59`

**Tahapan parsing** (dalam urutan):

1. **Normalize Text** (line 51-56):
   - Lowercase
   - Replace smart quotes dengan space
   - Collapse whitespace

2. **Extract Date** (line 60-78):
   - Cari phrase: "hari ini", "kemarin", "kemaren"
   - Default: `now()`
   - Return: tuple `[dateString, remainingText]`

3. **Extract Type** (line 80-106):
   - Cari income keywords: "pemasukan", "pendapatan", "masuk", "terima", "menerima", "dapat", "gaji", "bonus", "usaha", "hadiah"
   - Cari expense keywords: "pengeluaran", "keluar", "beli", "belanja", "bayar", "makan", "parkir"
   - Default: `Transaction::TYPE_EXPENSE`
   - Return: tuple `[typeString, remainingText]`

4. **Extract Category** (line 108-138):
   - Cari pattern regex: `/\bkategori\s+(.+)$/u`
   - Jika ditemukan, ambil text setelah "kategori"
   - Match kategori user via:
     - Direct string match (case-insensitive)
     - Contains check
     - Levenshtein distance ≤ 2 (fuzzy match)
   - Return: tuple `[Category|null, remainingText]`

5. **Extract Amount** (line 140-199):
   - Patterns (dalam urutan prioritas):
     - `/\b([0-9]+)\s*(?:juta|jt)\s+([0-9]{1,3})(?:\s*(?:ribu|rb))?\b/u` (e.g., "1 juta 500 ribu")
     - `/\brp\s*([0-9]+(?:\.[0-9]{3})+|[0-9]+)\b/u` (e.g., "rp 25.000")
     - `/\b([0-9]+(?:\.[0-9]{3})+)\b/u` (e.g., "25.000")
     - `/\b([0-9]+)\s*(?:juta|jt)\b/u` (e.g., "1 juta")
     - `/\b([0-9]+)\s*(?:ribu|rb)\b/u` (e.g., "50 ribu")
     - `/\b([0-9]+)\b/u` (bare number)
   - Word amounts fallback:
     - "dua puluh lima ribu" → 25000
     - "tiga puluh ribu" → 30000
     - ... (6 more patterns)
   - Return: tuple `[float|null, remainingText]`

6. **Clean Note** (line 201-204):
   - Collapse whitespace
   - Trim
   - Sisa text = note

**Contoh**:
```
Input: "beli makan siang lima puluh ribu kategori makanan"
Output: [
  'type' => 'expense',
  'amount' => 50000.0,
  'category_id' => 2,  // ID kategori 'Makanan'
  'category_name' => 'Makanan',
  'transaction_date' => '2026-07-26',
  'note' => 'beli makan siang',
  'warnings' => []
]
```

**File + Baris**:
- Main parser: `app/Services/VoiceTransactionParser.php:17-204`
- Date extraction: `app/Services/VoiceTransactionParser.php:60-78`
- Type extraction: `app/Services/VoiceTransactionParser.php:80-106`
- Category extraction: `app/Services/VoiceTransactionParser.php:108-138`
- Amount extraction: `app/Services/VoiceTransactionParser.php:140-199`

---

## F. Pengaturan & Lokalisasi

### 18. Penyimpanan Mata Uang & Bahasa

**Lokasi penyimpanan**: Kolom di tabel `users`
- `preferred_currency` (default: 'IDR')
- `language` (default: 'id')

**Migration**: `database/migrations/2026_05_06_152045_add_preferences_to_users_table.php:11-12`

**Model**: `app/Models/User.php:15-21`
```php
protected $fillable = [
    'name',
    'email',
    'password',
    'preferred_currency',
    'language',
];
```

**Update via**:
- `PATCH /pengaturan/mata-uang` → `PageController::updateCurrency()` → `$request->user()->update(['preferred_currency' => ...])`
- `PATCH /pengaturan/bahasa` → `PageController::updateLanguage()` → `$request->user()->update(['language' => ...])`

**Dibaca saat render**:
- Via `auth()->user()->preferred_currency` / `auth()->user()->language` di Blade view atau Controller
- **TIDAK ADA helper global atau middleware yang auto-apply setting** (belum fully implemented)

### 19. Format Uang ke Tampilan

**Method: Inline di Blade view** menggunakan `number_format()` function PHP

**Pattern**:
```php
// di resources/views/pages/dashboard.blade.php:2
$formatRupiah = fn ($amount) => 'Rp '.number_format((float) $amount, 0, ',', '.');

// Kemudian digunakan:
{{ $formatRupiah($thisMonthIncome) }}  // Output: "Rp 5.000.000"
```

**Frekuensi**: ~50 tempat di views menggunakan `number_format()` atau `$formatRupiah` closure

**TIDAK ADA**:
- Helper function global
- Blade component khusus untuk format uang
- Accessor di model

---

## G. Struktur Frontend & Testing

### 20. Livewire Versi & Struktur Komponens

**Livewire versi**: `^3.6` (dari `composer.json`)

**Tipe komponen**: Full-page components (dalam Blade pages)
- `<livewire:transactions.create-transaction />` → dimuat di `resources/views/pages/transactions/create.blade.php`
- `<livewire:transactions.voice-transaction />` → dimuat di `resources/views/pages/transactions/voice.blade.php`
- `<livewire:budgets.manage-budgets />` → dimuat di `resources/views/pages/budgets.blade.php`
- etc.

**Struktur**:
- Component class di `app/Livewire/[NameSpace]/[ComponentName].php`
- View di `resources/views/livewire/[namespace]/[component-name].blade.php`

### 20a. Struktur Layout (verifikasi lapangan)

- `resources/views/layouts/app.blade.php` — layout halaman ter-autentikasi, dipakai via `<x-app-layout>` (Blade component `app/View/Components/AppLayout.php`).
- `resources/views/layouts/guest.blade.php` — layout auth Breeze (login, register, reset password, dll), dipakai via `<x-guest-layout>`.
- `resources/views/dashboard.blade.php` — **tidak dirujuk route mana pun** (dashboard aktif = `resources/views/pages/dashboard.blade.php`). Kandidat penghapusan; di luar scope tugas terkait.

**Tidak ada di `main`** (hanya di `ui/bento-dark-dashboard`):
- `resources/views/pages/landing.blade.php`, `resources/views/pages/landing-playful.blade.php`, `resources/views/auth/onboarding.blade.php` — standalone HTML dengan `<!DOCTYPE html>` sendiri.

### 21. Testing Framework & Coverage

**Framework**: PHPUnit (`^11.0`, `tests/TestCase.php` extends `Illuminate\Foundation\Testing\TestCase`). Dokumen sebelumnya keliru menyebut Pest.

**File test**: 18 files
```
tests/
├── Feature/
│   ├── Auth/PasswordConfirmationTest.php
│   ├── Auth/EmailVerificationTest.php
│   ├── Auth/PasswordUpdateTest.php
│   ├── Auth/RegistrationTest.php
│   ├── Auth/AuthenticationTest.php
│   ├── Auth/PasswordResetTest.php
│   ├── DashboardBudgetInsightTest.php
│   ├── DashboardSavingsInsightTest.php
│   ├── Savings/ManageSavingsGoalsTest.php
│   ├── Savings/SavingsFoundationTest.php
│   ├── Budgets/ManageBudgetsTest.php
│   ├── Budgets/BudgetFoundationTest.php
│   ├── Transactions/VoiceTransactionTest.php
│   ├── Reports/ReportPageTest.php
│   ├── ProfileTest.php
│   ├── SettingsPageTest.php
│   └── [... more]
├── Unit/
│   └── VoiceTransactionParserTest.php
└── TestCase.php
```

**Coverage per fitur** (observed):
- ✅ **Auth**: Lengkap (registration, login, password reset, etc.)
- ✅ **Dashboard**: Budget insight & savings insight test
- ✅ **Budgets**: CRUD, copy from prev month
- ✅ **Savings**: CRUD, add contribution
- ✅ **Transactions/Voice**: Parse & save voice transaction
- ✅ **Reports**: Page rendering
- ⚠️ **Transactions/Manual**: TIDAK DITEMUKAN test file khusus `Transactions/CreateTransaction` atau `TransactionList`
- ⚠️ **Categories**: TIDAK DITEMUKAN test file khusus `CategoryManager`

**Total test di `main`**: 99 tests passing (bukan 105 seperti di branch `ui/bento-dark-dashboard`, yang menambah 6 test case di `VoiceTransactionParserTest` & `VoiceTransactionTest`).

---

## Temuan & Risiko

### 1. **Tidak Ada Policy untuk Otorisasi Model**
   - Otorisasi dilakukan manual dengan `abort_unless()` di controller/component
   - Rawan lupa saat menambah endpoint baru
   - **Solusi**: Tambahkan Policy class & gunakan `authorize()` method

### 2. **SavingsContribution Tidak Ada user_id**
   - Hanya ada `savings_goal_id`
   - Jika multi-user household, kontribusi bisa tercampur antar keluarga
   - **Risiko**: Query perlu selalu melalui `SavingsGoal` untuk memastikan ownership

### 3. **Soft-Delete Hanya di Transaction, Bukan Model Lain**
   - Kategori, Budget, SavingsGoal hard-delete
   - Jika ada referential integrity, bisa terjadi orphaned data
   - **Solusi**: Pertimbangkan soft-delete global atau strict foreign key constraint

### 4. **Timezone Fixed di config/app.php, Tidak Per-User**
   - Semua user menggunakan timezone Asia/Jakarta
   - Jika ada user di zona lain, bulan/hari akan berbeda
   - **Risiko**: Kurang cocok untuk multi-region

### 5. **Format Uang Hardcoded di View (Inline Closure)**
   - ~50 tempat menggunakan `number_format()` manual
   - Inconsistency jika format berubah
   - **Solusi**: Buat helper global atau Blade component reusable

### 6. **Kategori Default Di-Seed Saat CreateTransaction Mount**
   - Kategori dibuat otomatis setiap kali user membuka form (jika belum ada)
   - Jika seeding logic berubah, existing user tidak mendapat kategori baru
   - **Risiko**: Data inconsistency across users

### 7. **Savings Goal current_amount Denormalisasi Manual**
   - Update `current_amount` di SavingsGoal melalui `DB::transaction()` di Livewire
   - Tidak ada trigger database
   - **Risiko**: Jika aplikasi crash saat update, data bisa inconsistent

### 8. **Levenshtein Distance untuk Fuzzy Match Kategori**
   - Threshold ≤ 2 character difference
   - Bisa match kategori salah (mis "Makanan" ≈ "Makan")
   - **Risiko**: User voice input salah parsing kategori, perlu preview/confirmation

### 9. **Tidak Ada Audit Log / Transaction History untuk Budget/Goal Updates**
   - Saat edit budget/goal, tidak tercatat siapa/kapan
   - Tidak ada restore point
   - **Risiko**: Sulit debug jika ada perubahan yang tidak terduga

### 10. **Multi-User Household Belum Siap**
   - Semua query scoped to `auth()->user()` individual
   - Tidak ada household/family grouping
   - Tidak ada shared budget atau collaborative savings goal
   - **Risiko**: Perlu refactor besar untuk add family mode (soft-delete model relationships, perubahan otorisasi, dll)

---

**Dokumen ini dibuat**: 26 Juli 2026
**Status**: Read-only analysis, no code changes


---

## Perbedaan dengan branch arsip `ui/bento-dark-dashboard`

Branch UI adalah arsip perombakan tampilan dark navy. **Tidak akan di-merge**. Ringkasan hal yang ada di UI tapi TIDAK di `main`:

### Kode
- `app/Http/Controllers/TransactionController.php` — form fallback `POST /transaksi`. Di `main` semua create transaksi lewat Livewire `CreateTransaction`.
- Modifikasi berat: `app/Livewire/Transactions/*`, `app/Services/VoiceTransactionParser.php` (parser diperkaya keyword & word amounts). Model & migration **identik** dengan `main`.

### Routes
- `POST /transaksi` (`transactions.store`).
- `GET /landing-v2` → `pages.landing-playful`.
- `GET /auth/onboarding` (guest middleware).

### Views
- Perombakan masif: `pages/dashboard.blade.php` (+chart doughnut, Alpine animated counter, tabs mobile/desktop), `pages/reports/index.blade.php`, semua `livewire/*.blade.php`, `layouts/app.blade.php` (sidebar desktop + dark theme), semua `layouts/partials/*`, semua `pages/settings/*`.
- File baru: `pages/landing.blade.php`, `pages/landing-playful.blade.php`, `auth/onboarding.blade.php` (standalone HTML), `layouts/partials/sidebar.blade.php`, `components/landing/*`, `components/dashboard-card.blade.php`, `components/insight-card.blade.php`, `components/quick-action.blade.php`.

### Frontend
- `resources/js/app.js` diganti (lucide icons, bukan Alpine).
- `package.json` menambah dependency (lucide).
- `resources/css/keysaku-tokens.css` baru.

### Assets
- Puluhan file di `assets/` dan `public/assets/` (brand, illustrations, landing mockups).

### Tests
- Daftar file test **identik** dengan `main` (18 file). Perbedaan 6 test = penambahan test case di dalam `tests/Unit/VoiceTransactionParserTest.php` dan `tests/Feature/Transactions/VoiceTransactionTest.php` (commit `de5fdbc`).

### Konsekuensi bagi Section C (Alur Perhitungan Uang) — TETAP AKURAT
Query saldo, progress anggaran, kalkulasi savings, soft-delete behavior, dan penggunaan timezone **sama persis** di `main` maupun UI. Yang berubah di UI hanyalah lapisan presentasi.

### Konsekuensi bagi rendering
- **Chart.js**: tidak ada di `main`. Baik dashboard maupun laporan bulanan di `main` merender category breakdown sebagai daftar `<p>/<div>` tabular biasa, bukan chart. UI adds doughnut chart di dashboard dengan tooltip `toLocaleString('id-ID')`.
- **Alpine animated counter** untuk saldo: tidak ada di `main`. UI adds counter di dashboard baris 66 & 266 dengan `x-text="displayed.toLocaleString('id-ID')"`.
