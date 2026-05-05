# SCHEMA.md — KeySaku Database Design

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
| timezone | varchar(50) | default: Asia/Jakarta |
| currency | varchar(10) | default: IDR |
| remember_token | varchar(100) nullable | |
| email_verified_at | timestamp nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### `accounts`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| user_id | bigint unsigned FK | → users.id |
| name | varchar(100) | e.g. BCA, Dana, Tunai |
| type | enum | cash, bank, ewallet, investment |
| balance | decimal(15,2) | default: 0.00 |
| color | varchar(20) nullable | hex color for UI |
| icon | varchar(50) nullable | emoji or icon name |
| is_active | boolean | default: true |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### `categories`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| user_id | bigint unsigned FK | → users.id |
| name | varchar(100) | e.g. Makanan, Transportasi |
| type | varchar(20) | income, expense |
| color | varchar(20) nullable | hex color |
| icon | varchar(50) nullable | emoji or icon name |
| is_active | boolean | default: true |
| created_at | timestamp | |
| updated_at | timestamp | |

Default expense categories: Makanan, Transportasi, Belanja Dapur, Tagihan, Kesehatan, Pendidikan, Hiburan, Lainnya
Default income categories: Gaji, Bonus, Usaha, Hadiah, Lainnya

---

### `transactions`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| user_id | bigint unsigned FK | → users.id |
| account_id | bigint unsigned FK | → accounts.id |
| category_id | bigint unsigned FK nullable | → categories.id |
| type | enum | income, expense, transfer |
| amount | decimal(15,2) | always positive |
| description | varchar(255) nullable | |
| notes | text nullable | longer notes |
| transaction_date | date | |
| is_voice_input | boolean | default: false |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### `budgets`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| user_id | bigint unsigned FK | → users.id |
| category_id | bigint unsigned FK | → categories.id |
| month | tinyint | 1–12 |
| year | smallint | e.g. 2026 |
| limit_amount | decimal(15,2) | budget ceiling |
| created_at | timestamp | |
| updated_at | timestamp | |
| Unique | | (user_id, category_id, month, year) |

---

### `saving_goals`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| user_id | bigint unsigned FK | → users.id |
| name | varchar(100) | e.g. Dana Darurat, Liburan |
| icon | varchar(50) nullable | emoji |
| target_amount | decimal(15,2) | |
| current_amount | decimal(15,2) | default: 0.00 |
| target_date | date nullable | |
| is_completed | boolean | default: false |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## Indexes
- `transactions`: index on (user_id, transaction_date)
- `transactions`: index on (user_id, type)
- `budgets`: unique on (user_id, category_id, month, year)
- `accounts`: index on (user_id, is_active)

## Notes
- All monetary values stored as `decimal(15,2)` in IDR
- Soft deletes not used in v1 (keep it simple)
- `voice_input_logs` table planned for Milestone 5 (debug voice parsing)
