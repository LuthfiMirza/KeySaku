# SCHEMA.md — KeySaku Database Design

> **Terakhir diperbarui:** 2026-09-20
> Status dokumen ini mencerminkan skema **sesungguhnya** pada database setelah semua migrasi dijalankan.

## Engine
MySQL 8.x via Hostinger Premium managed hosting.
Charset: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.

---

## Tables

### `users`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | auto increment |
| name | varchar(255) | display name |
| email | varchar(255) unique | |
| password | varchar(255) | bcrypt |
| preferred_currency | varchar(10) | default: IDR |
| preferred_language | varchar(10) | default: id |
| remember_token | varchar(100) nullable | |
| email_verified_at | timestamp nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

> **Catatan:** Kolom `timezone` dan `initial_balance` sudah **dihapus**. Saldo awal kini ada di tabel `households`.

---

### `households`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| name | varchar(255) | nama keluarga |
| initial_balance | decimal(15,2) | default: 0.00 |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### `household_members`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| household_id | bigint unsigned FK | → households.id |
| user_id | bigint unsigned FK | → users.id |
| role | varchar(20) | `owner` atau `member` |
| created_at | timestamp | |
| updated_at | timestamp | |
| Unique | | (household_id, user_id) |

---

### `accounts`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| household_id | bigint unsigned FK nullable | → households.id |
| user_id | bigint unsigned FK | → users.id (pemilik/pembuat) |
| name | varchar(100) | e.g. BCA, Dana, Tunai |
| type | enum | cash, bank, ewallet, investment |
| balance | decimal(15,2) | default: 0.00 |
| icon | varchar(50) nullable | emoji atau icon name |
| color | varchar(20) nullable | hex color untuk UI |
| is_active | boolean | default: true |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### `categories`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| household_id | bigint unsigned FK | → households.id |
| user_id | bigint unsigned FK | → users.id (pencatat) |
| name | varchar(100) | e.g. Makanan, Transportasi |
| type | varchar(20) | `income` atau `expense` |
| color | varchar(20) nullable | hex color |
| icon | varchar(50) nullable | emoji atau icon name |
| is_active | boolean | default: true |
| created_at | timestamp | |
| updated_at | timestamp | |
| Unique | | (household_id, name, type) |

Default expense (13 kategori): Makanan, Transportasi, Belanja Dapur, Tagihan & Utilitas, Kesehatan, Pendidikan, Hiburan, Pakaian, Perawatan Pribadi, Sosial & Donasi, Tabungan, Investasi, Lainnya
Default income: Gaji, Bisnis / Freelance, Investasi, Hadiah, Lainnya

---

### `transactions`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| household_id | bigint unsigned FK | → households.id |
| user_id | bigint unsigned FK | → users.id (pencatat) |
| category_id | bigint unsigned FK nullable | → categories.id |
| type | varchar(20) | `income` atau `expense` |
| amount | bigint | selalu positif, dalam satuan terkecil |
| note | varchar(255) nullable | catatan singkat |
| transaction_date | date | |
| source | varchar(20) | `manual` atau `voice` |
| raw_voice_text | text nullable | input suara mentah |
| deleted_at | timestamp nullable | soft delete |
| created_at | timestamp | |
| updated_at | timestamp | |

> **Catatan:** `account_id` **tidak ada** di schema saat ini. `amount` disimpan sebagai integer (bukan decimal).

---

### `budgets`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| household_id | bigint unsigned FK | → households.id |
| user_id | bigint unsigned FK | → users.id |
| category_id | bigint unsigned FK | → categories.id |
| month | tinyint | 1–12 |
| year | smallint | e.g. 2026 |
| amount | bigint | plafon anggaran |
| created_at | timestamp | |
| updated_at | timestamp | |
| Unique | | (household_id, category_id, month, year) |

---

### `savings_goals`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| household_id | bigint unsigned FK | → households.id |
| user_id | bigint unsigned FK | → users.id |
| name | varchar(255) | e.g. Dana Darurat, Liburan |
| icon | varchar(50) nullable | emoji |
| target_amount | bigint | target tabungan |
| current_amount | bigint | default: 0 |
| deadline | date nullable | target tanggal selesai |
| status | varchar(20) | `active` atau `archived` |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### `savings_contributions`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| household_id | bigint unsigned FK | → households.id |
| user_id | bigint unsigned FK | → users.id (pencatat kontribusi) |
| savings_goal_id | bigint unsigned FK | → savings_goals.id |
| amount | bigint | jumlah kontribusi |
| note | varchar(255) nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## Indexes
- `transactions`: index on `(user_id, type)`
- `transactions`: index on `(user_id, transaction_date)`
- `transactions`: soft delete via `deleted_at`
- `budgets`: unique on `(household_id, category_id, month, year)`
- `categories`: unique on `(household_id, name, type)`

## Notes
- Semua nilai moneter disimpan sebagai **integer** (bukan decimal), mewakili unit terkecil (sen/poin).
- Soft delete hanya diterapkan di `transactions`.
- `user_id` di tabel bertenant berarti "siapa yang mencatat", bukan "siapa pemilik data".
- Kepemilikan data ditentukan oleh `household_id`.
