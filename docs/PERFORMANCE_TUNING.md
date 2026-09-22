# KeySaku — Performance Tuning & High-Scale Architecture Guide

Target Deployment: **Hostinger Shared Hosting / VPS (PHP 8.4 + MySQL 8.0)**  
Goal: **Sub-20ms response time on Shared Hosting, Sub-5ms in cached production/VPS, low RAM usage, zero N+1 queries.**

---

## 1. Database & SQL Query Architecture

### Composite Indexes
KeySaku transactions table uses composite indexes aligned with the left-to-right query patterns executed by multi-tenant financial reporting:
- `(household_id, type, transaction_date)`: Used for monthly & all-time income/expense calculations and range filters.
- `(household_id, category_id, transaction_date)`: Used for category budget tracking and spending breakdown.
- `(household_id, account_id)`: Used for account-specific ledger and balance tracking.

### Single-Pass Conditional Aggregation
Instead of executing separate `count()` or `sum()` queries for income and expense (which previously caused 4 separate index scans), financial aggregations are consolidated into single SQL queries:

```sql
SELECT 
    COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS total_income,
    COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS total_expense
FROM transactions
WHERE household_id = ? AND transaction_date BETWEEN ? AND ?;
```

### Eliminating PHP RAM Hydration
The 6-month historical financial trend in `ReportController` pre-aggregates monthly totals in MySQL/SQLite using:

```sql
SELECT 
    SUBSTR(transaction_date, 1, 7) AS ym,
    SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) AS total_income,
    SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) AS total_expense
FROM transactions
WHERE household_id = ? AND transaction_date BETWEEN ? AND ?
GROUP BY ym;
```
This replaces retrieving hundreds of raw Eloquent model instances into PHP memory with a single fast aggregation query returning a 6-row summary.

---

## 2. OPcache Tuning (Hostinger / Production PHP 8.4)

In Hostinger hPanel:
1. Navigate to **Advanced > PHP Configuration**.
2. Ensure **PHP 8.4** is selected.
3. Open **PHP Extensions** and confirm `opcache` is checked.
4. Under **PHP Options** (or custom `.user.ini` / `php.ini`):

```ini
; OPcache Production Configuration for KeySaku
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=0
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.fast_shutdown=1
```

> **Note on Shared Hosting:** If your shared hosting prevents setting `opcache.validate_timestamps=0`, set `opcache.revalidate_freq=60` to check file timestamps only once per minute instead of on every incoming HTTP request.
>
> **Important:** `opcache.save_comments=1` is strictly mandatory for Livewire 3 and PHP 8 attributes.

---

## 3. Composer Production Optimizations

When deploying or building artifacts for production:

```bash
composer install --no-dev --optimize-autoloader --classmap-authoritative
```

- `--optimize-autoloader`: Converts PSR-0/4 rules into classmap for faster resolution.
- `--classmap-authoritative`: Bypasses filesystem checks entirely when resolving classes. If a class is not in the generated classmap, PHP won't check the disk, preventing slow disk I/O on shared hosting.

---

## 4. Laravel Production Cache Pipeline

Run these commands during deployment (or in deploy script):

```bash
# 1. Clear stale caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# 2. Re-cache for production (or simply: php artisan optimize)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Verifying Cache Status
To verify that routes and configs are fully cached:
```bash
php artisan route:list
# Should be instantaneous without recompiling routes/web.php
```

---

## 5. Frontend Asset Delivery (Vite & Tree-Shaking)

KeySaku uses Tailwind CSS v4 and tree-shaken Chart.js.

### Tree-Shaken Chart.js (`resources/js/chart.js`)
Instead of importing the entire Chart.js bundle (>400KB), only required components are registered:
- `BarController`, `DoughnutController`
- `BarElement`, `ArcElement`
- `CategoryScale`, `LinearScale`
- `Tooltip`, `Legend`

### Asset Sizes (Gzip)
- CSS (`app.css`): **~9.5 kB** gzip
- JS (`app.js` with Alpine + Chart.js): **~56.5 kB** gzip

Ensure `npm run build` is run before deployment, and assets in `public/build/` are uploaded to Hostinger.
