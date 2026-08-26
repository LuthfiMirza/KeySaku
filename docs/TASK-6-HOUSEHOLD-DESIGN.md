# Task 6 — Household / Family Sharing: Draf Desain

Status: **DRAF — menunggu keputusan sebelum kode ditulis**
Terkait: `docs/EXECUTION-PLAN.md` Task 6 (dependensi: Task 5 merged ✅)

Dokumen ini dibuat karena dua alasan:
1. Task 6 secara eksplisit ditandai plan sebagai **"PALING BESAR"**.
2. Saat verifikasi awal, premis kunci di plan ternyata tidak berlaku lagi
   (dijelaskan di §1). Itu mengubah ukuran dan pendekatan task ini cukup
   signifikan, jadi perlu disepakati dulu sebelum eksekusi.

---

## 1. Kenapa perlu didesain ulang: premis "satu file" gugur

Teks asli plan:
> Ganti isi global scope dari user_id ke household_id (satu file, berkat Task 4)

Ini mengasumsikan Task 4 mengaktifkan *global scope* Eloquent yang otomatis
memfilter semua query berdasarkan `user_id` — sehingga mengganti household
tinggal ganti satu kondisi di satu file.

**Kenyataannya:** Task 4 (dengan persetujuan Anda, opsi "a-revisi") **tidak**
mengaktifkan global scope itu di mana pun. Alasannya waktu itu terbukti
empiris dua kali — global scope literal merusak test otorisasi Task 3 karena
test itu memverifikasi data user lain lewat query mentah sambil masih login
sebagai user penyerang. Isolasi data sekarang murni lewat scoping manual
`auth()->user()->relasi()` di tiap komponen.

Akibatnya, scoping tersebar di **9 file, ~30 call site**:

| File | Jumlah call site |
|---|---|
| `app/Livewire/Budgets/ManageBudgets.php` | 10 |
| `app/Livewire/Savings/ManageSavingsGoals.php` | 6 |
| `app/Livewire/Transactions/EditTransaction.php` | 5 |
| `app/Livewire/Transactions/CreateTransaction.php` | 2 |
| `app/Livewire/Transactions/TransactionList.php` | 2 |
| `app/Livewire/Transactions/VoiceTransaction.php` | 2 |
| `app/Http/Controllers/DashboardController.php` | 1 |
| `app/Http/Controllers/PageController.php` | 1 |
| `app/Livewire/CategoryManager.php` | 1 |

Semua ini perlu diubah dari "punya user ini" jadi "punya household user ini"
— bukan satu baris, tapi mekanis dan berpola sama di tiap tempat (lihat §7).

---

## 2. Fitur — apa saja yang bisa dilakukan setelah Task 6

- **Household otomatis saat daftar.** User baru langsung punya household
  sendiri (dirinya jadi `owner`), persis seperti sekarang punya kategori
  otomatis — tidak ada langkah tambahan.
- **Gabung household lewat kode undangan.** Saat registrasi, ada field
  opsional "Kode undangan" (pola sama seperti field "Saldo awal" di Task 5).
  Kalau diisi dan valid, user langsung jadi anggota household itu (role
  `member`) alih-alih dapat household baru.
- **Lihat & bagikan kode undangan.** Di halaman Pengaturan, ada menu baru
  "Anggota Keluarga" yang menampilkan kode undangan household (untuk
  disalin dan dibagikan lewat kanal apa pun — WhatsApp, dll — di luar
  aplikasi, karena Task 6 eksplisit tidak mencakup notifikasi/email).
- **Data dipakai bersama.** Semua anggota household melihat kategori,
  transaksi, anggaran, dan target tabungan yang **sama persis** — bukan
  masing-masing salinan sendiri.
- **Atribusi tetap ada.** Tiap transaksi/kontribusi tetap mencatat siapa
  yang menulisnya (`user_id` dipertahankan persis seperti instruksi plan:
  *"JANGAN hapus user_id — maknanya berubah jadi siapa yang mencatat"*).
  UI bisa menampilkan "dicatat oleh Budi" di masa depan (visual ditunda ke
  Task 9, tapi datanya sudah tersedia dari task ini).
- **Kelola anggota.** Owner bisa melihat daftar anggota dan mengeluarkan
  anggota. Anggota (bukan owner) bisa keluar dari household sendiri.
- **Keluar = dapat household baru sendirian.** User yang keluar/dikeluarkan
  tidak pernah kehilangan akun — dia otomatis dapat household kosong baru
  miliknya sendiri (owner), supaya tetap bisa pakai aplikasi. Data lama
  **tidak ikut** dia — data itu milik household, bukan milik satu orang
  (lihat §4.4).
- **Saldo dan laporan jadi milik household**, bukan lagi milik user
  individu — dua anggota di household yang sama melihat angka saldo,
  anggaran, dan progres tabungan yang identik.

**Di luar cakupan (tetap, sesuai plan):**
- Tidak ada notifikasi/email undangan — murni kode yang dibagikan manual.
- Tidak ada perubahan visual/UI baru di luar yang benar-benar perlu
  berfungsi (redesign penuh masuk Task 9).
- Tidak ada household ganda / pindah-pindah household — satu user, satu
  household aktif pada satu waktu (lihat §4.5 kenapa ini dibatasi).

---

## 3. Skema data

### Tabel baru: `households`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned PK | |
| name | varchar(100) nullable | opsional, default null (tampil sebagai "Keluarga {nama owner}" di UI kalau kosong) |
| invite_code | varchar(12) unique | dibuat otomatis, contoh: `A7K3-9XPQ` |
| created_at, updated_at | timestamp | |

### Tabel baru: `household_members`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned PK | |
| household_id | bigint unsigned FK → households.id, cascade delete | |
| user_id | bigint unsigned FK → users.id, cascade delete | |
| role | varchar(20) | `owner` \| `member` |
| created_at, updated_at | timestamp | |
| Unique | | `user_id` (satu user = satu household aktif, lihat §4.5) |
| Index | | `household_id` |

### Kolom baru: `household_id` di 5 tabel bertenant
`categories`, `transactions`, `budgets`, `savings_goals`,
`savings_contributions` — masing-masing dapat:
```
$table->foreignId('household_id')->constrained()->cascadeOnDelete();
```
`user_id` di kelima tabel ini **dipertahankan apa adanya** (semantik
berubah jadi atribusi, bukan kepemilikan).

### Kolom yang pindah: `initial_balance`
**Temuan tambahan** (lihat §4.3): kolom ini perlu pindah dari `users` ke
`households`, karena saldo sekarang dihitung per-household bukan per-user.

---

## 4. Keputusan desain — rekomendasi untuk 4 pertanyaan terbuka

### 4.1 Kategori: per-household atau tetap per-user?

**Masalah:** Task 3 bikin `UserObserver` yang auto-seed 13 kategori ke
**setiap user baru**. Kalau 2 orang gabung 1 household, tanpa perubahan
mereka akan punya 26 kategori "kembar" (13 milik A + 13 milik B), bukan
13 yang dipakai bersama. Constraint unique kategori saat ini juga
`(user_id, type, name)` — per-user.

**Rekomendasi: kategori jadi milik household.** Satu set 13 kategori
default per household (bukan per user). Constraint unique pindah jadi
`(household_id, type, name)`.

**Konsekuensi:** `UserObserver::created()` (Task 3) tidak lagi bisa
langsung seed kategori saat `User::created`, karena household milik user
baru belum tentu baru dibuat (bisa jadi dia gabung household yang sudah
ada lewat kode undangan). Logika seeding harus pindah ke titik
"household baru dibuat", bukan "user baru dibuat" — lihat §5.

### 4.2 Alur undang anggota tanpa notifikasi

**Rekomendasi: kode undangan statis per household**, bukan pencarian
email. Alasan: mencari user lain by email dan langsung menambahkannya
tanpa persetujuan adalah risiko privasi (bisa dipakai untuk mengecek email
mana yang terdaftar) dan tidak ada mekanisme approval tanpa notifikasi.
Kode undangan lebih aman: hanya orang yang benar-benar diberi kode oleh
owner yang bisa masuk, dan usernya sendiri yang memasukkan kode saat
registrasi.

### 4.3 Saldo awal ikut household atau tetap per-user?

**Masalah baru yang saya temukan saat menyusun draf ini:** Task 5 menaruh
`initial_balance` di tabel `users`. Definition of done Task 6 bilang *"Dua
user dalam satu household melihat data yang sama"* — termasuk saldo. Tapi
kalau transaksi sudah household-wide sedangkan `initial_balance` tetap
per-user, dua anggota household yang sama bisa melihat **total saldo yang
berbeda** (income/expense sama, tapi komponen saldo awalnya beda-beda per
orang) — melanggar DoD.

**Rekomendasi:** pindahkan `initial_balance` dari `users` ke `households`
(satu saldo awal per household, bukan per anggota). Migration backfill:
`households.initial_balance` = jumlah `initial_balance` semua member yang
di-backfill ke household itu (lihat §6) — supaya total uang yang sudah
tercatat user tidak hilang saat migrasi.

**Dampak ikutan:** halaman Pengaturan → Saldo Awal (baru selesai di Task 5)
perlu diubah supaya baca/tulis ke household, bukan ke user yang sedang
login. Ini mengubah kode yang baru saja di-merge — saya tandai eksplisit
karena ini "menyentuh kembali" pekerjaan yang baru selesai.

### 4.4 Data ikut orang atau ikut household saat seseorang keluar?

**Rekomendasi: data tetap di household.** `household_id` adalah pemilik
data yang sebenarnya sekarang; `user_id` cuma atribusi historis ("siapa
yang mencatat baris ini"). Kalau user keluar/dikeluarkan, transaksi yang
pernah dia catat **tetap ada** di household lama (baris `user_id` itu
tetap menunjuk ke dia untuk keperluan atribusi/audit), dan dia mulai
household baru yang kosong.

### 4.5 Kenapa dibatasi "satu household per user" (bukan multi-household)?

Plan tidak minta UI pindah-household atau multi-household, dan constraint
unique `user_id` di `household_members` (§3) menjaga model tetap
sederhana — cocok dengan positioning "keuangan keluarga" (satu keluarga,
bukan gabung banyak grup sekaligus). Kalau nanti dibutuhkan multi-household,
itu perubahan terpisah, bukan bagian Task 6.

---

## 5. Alur registrasi & pembuatan household (revisi atas Task 3)

**Sebelum (Task 3):** `UserObserver::created()` otomatis seed 13 kategori
ke user baru, tidak peduli konteks apa pun.

**Sesudah (Task 6):**
1. `RegisteredUserController::store()` divalidasi seperti sekarang, plus
   field opsional baru `invite_code`.
2. Setelah `User::create(...)`:
   - **Kalau `invite_code` diisi dan valid** → cari household dengan
     `invite_code` itu, buat baris `household_members` (`role = member`).
     Household yang sudah ada TIDAK di-reseed kategori (sudah ada).
   - **Kalau kosong / tidak valid** → buat household baru (`invite_code`
     digenerate acak), buat baris `household_members`
     (`role = owner`), lalu seed 13 kategori default ke household baru
     itu (logika yang sama seperti `UserObserver` sekarang, hanya target
     kolomnya `household_id` bukan `user_id`).
3. Logika ini dipindah ke satu Action class baru, misal
   `app/Actions/ProvisionHouseholdForUser.php`, dipanggil dari
   `RegisteredUserController` — supaya bisa dipanggil juga dari alur
   "keluar dari household" (§4.4) tanpa duplikasi.
4. `UserObserver` **dihapus total** — sudah dicek isinya, satu-satunya
   tanggung jawabnya adalah seed 13 kategori ke `user_id` baru (`app/
   Observers/UserObserver.php:9`), dan itu pindah seluruhnya ke Action
   di atas dengan target `household_id`. Tidak ada fungsi lain di
   observer ini yang perlu dipertahankan.

Invalid invite code → error validasi biasa ("Kode undangan tidak
ditemukan"), bukan diam-diam bikin household baru — supaya user sadar
kalau salah ketik kode.

---

## 6. Migration & backfill (data existing tidak boleh berubah perilaku)

Urutan migration:
1. `create_households_table`
2. `create_household_members_table`
3. `add_household_id_to_categories_table` (nullable dulu)
4. `add_household_id_to_transactions_table` (nullable dulu)
5. `add_household_id_to_budgets_table` (nullable dulu)
6. `add_household_id_to_savings_goals_table` (nullable dulu)
7. `add_household_id_to_savings_contributions_table` (nullable dulu)
8. `add_initial_balance_to_households_table`
9. **Data migration** (bukan schema-only): untuk tiap user existing —
   - Buat 1 household baru untuknya, `role = owner`
   - `households.initial_balance` = `users.initial_balance` milik dia
   - Set `household_id` di semua baris kategori/transaksi/anggaran/
     goal/kontribusi miliknya (`where user_id = ...`) ke household baru
     itu
   - Ganti constraint unique kategori dari `(user_id,type,name)` ke
     `(household_id,type,name)`
10. `drop_initial_balance_from_users_table`

Pola backfill lewat `DB::statement()` correlated subquery/UPDATE, sama
seperti yang sudah terbukti jalan di migration Task 4
(`savings_contributions.user_id`) — akan diverifikasi lagi terhadap data
dev asli sebelum di-commit, seperti task-task sebelumnya.

---

## 7. Perubahan scoping — pola mekanis di 9 file

Pola penggantinya seragam. Contoh (`ManageBudgets.php`):
```php
// sebelum
$budget = auth()->user()->budgets()->whereKey($budgetId)->first();

// sesudah
$budget = auth()->user()->household->budgets()->whereKey($budgetId)->first();
```
Relasi `categories()`, `transactions()`, `budgets()`, `savingsGoals()`
pindah dari `User` ke `Household` model. `User` dapat relasi baru
`household(): HasOneThrough` (lewat `household_members`) atau accessor
`currentHousehold()` — detail teknis diputuskan saat implementasi, tapi
intinya satu pola pengganti yang sama diterapkan ke seluruh 30 call site
di §1, per file, dengan test dijalankan di antara tiap file supaya kalau
ada yang pecah langsung ketahuan filenya.

Pola `whereKey()->first()` + `abort_if(!$model, 404)` yang sudah ada
(dari Task 3/4) **dipertahankan strukturnya** — cuma sumber query-nya yang
berubah dari `auth()->user()->X()` ke `auth()->user()->household->X()`.
Ini artinya isolasi antar-household otomatis terjaga oleh pola yang sudah
teruji, tanpa perlu global scope.

---

## 8. Role & permission

- **owner**: akses penuh ke data (CRUD transaksi/anggaran/kategori/
  tabungan) + kelola anggota (lihat daftar, keluarkan anggota, lihat kode
  undangan).
- **member**: akses penuh yang sama ke data (tidak dibatasi secara
  finansial — semua anggota keluarga dianggap setara untuk urusan
  pencatatan), TAPI tidak bisa mengeluarkan anggota lain atau melihat/
  mengubah kode undangan. Bisa keluar dari household sendiri kapan saja.
- Household dengan owner yang keluar: owner **tidak bisa keluar** kalau
  masih ada anggota lain (harus transfer kepemilikan dulu — di luar
  cakupan Task 6, dicatat sebagai utang perbaikan) atau household otomatis
  dibubarkan kalau owner adalah satu-satunya anggota (sama seperti create
  household baru untuknya).

---

## 9. Test plan (peta ke Definition of Done)

| DoD | Cara verifikasi |
|---|---|
| User existing tidak merasakan perubahan apa pun | Jalankan seluruh 162 test lama, harus tetap hijau tanpa dimodifikasi (standar yang sama sejak Task 2) |
| Dua user dalam household yang sama melihat data yang sama | Test baru: 2 user gabung 1 household (lewat invite code), salah satu bikin transaksi, assert user lain melihatnya di dashboard/list |
| Test otorisasi diperluas: user dari household lain → 404 | Perluas pola `AuthorizationTest` (Task 3) jadi household-aware: user dari household lain mengakses record household lain → 404, mirip pola yang sudah ada tapi actor & target beda household, bukan cuma beda user |

---

## 10. Urutan eksekusi yang diusulkan (kalau draf ini disetujui)

Supaya tidak jadi satu commit raksasa, dipecah bertahap layaknya
task-task sebelumnya:
1. `feat(households)`: tabel `households` + `household_members` + model
2. `feat(households)`: migration backfill + pindah `initial_balance`
3. `feat(households)`: Action `ProvisionHouseholdForUser` + ubah alur
   registrasi (field kode undangan)
4. `refactor(households)`: pindahkan relasi data (categories, transactions,
   budgets, savingsGoals) dari User ke Household + ubah 9 file call site
   satu per satu, test dijalankan di antaranya
5. `feat(households)`: halaman "Anggota Keluarga" (lihat kode undangan,
   daftar anggota, keluarkan anggota, keluar household)
6. `test(households)`: perluas test otorisasi household-aware
7. `docs`: update `SCHEMA.md` (sudah usang, akan disegarkan sekalian) +
   tandai Task 6 selesai

---

## 11. Yang perlu Anda putuskan

Semua di §4 sudah saya beri rekomendasi dengan alasan. Kalau Anda setuju
semuanya, saya mulai dari urutan §10. Kalau ada yang mau diubah, tandai
poin mana (4.1 / 4.2 / 4.3 / 4.4 / 4.5) dan saya sesuaikan draf ini dulu
sebelum kode.
