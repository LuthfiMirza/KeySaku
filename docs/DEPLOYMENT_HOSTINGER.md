# KeySaku Hostinger Deployment Checklist

Target: **Hostinger Premium shared hosting**, **PHP 8.4**, **MySQL**, Laravel 12.

This checklist assumes KeySaku is built locally, then uploaded to Hostinger. Production must run on PHP shared hosting and must not require a running Node.js server.

---

## 1. Local Preparation

Run these commands locally before uploading:

```bash
composer install
npm install
npm run build
php artisan route:list --except-vendor
php artisan test
```

Remove Vite dev server marker if it exists:

```bash
rm -f public/hot
```

For a production-ready vendor folder, run:

```bash
composer install --no-dev --optimize-autoloader
```

Use this when Hostinger terminal cannot reliably run Composer or cannot access Packagist.

---

## 2. Files To Upload

Upload the Laravel application outside the public web root whenever possible.

Recommended project folder:

```text
/home/USER/domains/YOUR_DOMAIN/keysaku/
```

Upload these folders/files into `keysaku/`:

```text
app/
bootstrap/
config/
database/
public/
resources/
routes/
storage/
vendor/
artisan
composer.json
composer.lock
.env
```

Optional but useful for maintenance:

```text
docs/
AGENTS.md
CLAUDE.md
```

Do not upload local-only folders unless needed:

```text
node_modules/
tests/
```

---

## 3. Hostinger Folder Structure

### Best Option: Domain points to Laravel `public/`

If Hostinger allows changing the document root, point the domain to:

```text
/home/USER/domains/YOUR_DOMAIN/keysaku/public
```

Final structure:

```text
/home/USER/domains/YOUR_DOMAIN/
├── keysaku/
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── artisan
└── public_html/  # points to or redirects to keysaku/public if supported
```

### Shared Hosting Fallback: Copy `public/` contents into `public_html/`

If Hostinger cannot point the domain to `keysaku/public`, keep the app outside `public_html` and copy only public assets to `public_html`.

```text
/home/USER/domains/YOUR_DOMAIN/
├── keysaku/
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── artisan
└── public_html/
    ├── index.php
    ├── .htaccess
    ├── build/
    ├── manifest.json
    └── images/
```

When using this fallback, update `public_html/index.php` paths:

```php
require __DIR__.'/../keysaku/vendor/autoload.php';

(require_once __DIR__.'/../keysaku/bootstrap/app.php')
    ->handleRequest(Request::capture());
```

---

## 4. Production `.env` Checklist

Create `.env` on Hostinger in the Laravel project root, not inside `public_html`.

Template:

```env
APP_NAME=KeySaku
APP_ENV=production
APP_KEY=base64:PASTE_GENERATED_KEY_HERE
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://your-domain.com

APP_LOCALE=id
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=id_ID

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=HOSTINGER_DB_HOST
DB_PORT=3306
DB_DATABASE=HOSTINGER_DB_NAME
DB_USERNAME=HOSTINGER_DB_USER
DB_PASSWORD=HOSTINGER_DB_PASSWORD

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
```

Production requirements:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com`
- `APP_KEY` must be set
- MySQL credentials must match Hostinger database panel
- `.env` must not be inside `public_html`

Generate app key on Hostinger if needed:

```bash
php artisan key:generate --force
```

---

## 5. MySQL Setup Notes

Create a MySQL database in Hostinger panel:

- Database name: from Hostinger, e.g. `u123456789_keysaku`
- Username: from Hostinger, e.g. `u123456789_keysaku_user`
- Password: strong generated password
- Host: Hostinger-provided MySQL host, often `localhost` or a specific hostname
- Port: `3306`

After `.env` is set, run:

```bash
php artisan migrate --force
```

This creates the auth, cache, categories, and transactions tables.

---

## 6. Cache And Optimize Commands

Run from the Laravel project root:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Use after each deployment or `.env` change.

If routes behave unexpectedly, clear route cache:

```bash
php artisan route:clear
```

---

## 7. Storage And Cache Permissions

These directories must be writable by the web server:

```text
storage/
bootstrap/cache/
```

Typical permission command if SSH access allows it:

```bash
chmod -R 775 storage bootstrap/cache
```

If Hostinger file manager is used, ensure write permissions are enabled for:

```text
storage/framework/cache/
storage/framework/sessions/
storage/framework/views/
storage/logs/
bootstrap/cache/
```

`php artisan storage:link` is not required yet because KeySaku does not use file uploads in the current scope.

Run later only when uploads are introduced:

```bash
php artisan storage:link
```

---

## 8. PWA Notes

Current PWA readiness includes:

```text
public/manifest.json
resources/views/layouts/partials/pwa-meta.blade.php
```

Before public launch, add icon files referenced by the manifest:

```text
public/images/icons/icon-192.png
public/images/icons/icon-512.png
```

If icons are missing, the app still loads, but browser install prompts may show incomplete branding.

---

## 9. Security Checklist

Never expose these inside `public_html`:

```text
.env
app/
bootstrap/
config/
database/
resources/
routes/
storage/
vendor/
composer.json
composer.lock
package.json
package-lock.json
node_modules/
tests/
phpunit.xml
AGENTS.md
CLAUDE.md
```

Only public web files belong in `public_html`:

```text
index.php
.htaccess
build/
manifest.json
images/
```

Production safety checks:

```bash
php artisan about
php artisan route:list --except-vendor
```

Confirm:

- Debug mode is disabled
- Environment is production
- Database connection is MySQL
- Routes are cached only after deployment is stable

---

## 10. Rollback Notes

Before uploading a new version:

1. Download or copy current working project folder.
2. Export MySQL database from Hostinger panel.
3. Keep previous `public_html` and app folder backup.

Rollback options:

- Restore previous app folder.
- Restore previous `public_html` contents.
- Import previous database backup if migrations changed data.
- Run cache clears:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

For current KeySaku scope, migrations are structural and simple, but always back up the database before production migration.

---

## 11. Common Troubleshooting

### Blank page or 500 error

Check:

```bash
cat storage/logs/laravel.log
php artisan about
```

Common causes:

- `.env` missing
- `APP_KEY` missing
- wrong database credentials
- `storage/` not writable
- `bootstrap/cache/` not writable

### CSS/JS not loading

Check:

```text
public/build/manifest.json
public/build/assets/
```

Run locally before upload:

```bash
npm run build
```

Do not upload `public/hot` to production.

### Routes return 404

Check `.htaccess` exists in web root:

```text
public/.htaccess
```

Clear cached routes if needed:

```bash
php artisan route:clear
php artisan route:cache
```

### Login fails or sessions reset

Check:

- `APP_KEY` is stable and not regenerated repeatedly
- `SESSION_DRIVER=database`
- migrations have created `sessions` table
- `APP_URL` matches production domain

### Database connection fails

Check Hostinger database values:

```env
DB_HOST=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Then run:

```bash
php artisan migrate:status
```

---

## 12. Deployment Quick Runbook

Local:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
rm -f public/hot
php artisan test
```

Upload files to Hostinger.

Hostinger project root:

```bash
php artisan key:generate --force
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan about
```

Final browser checks:

- Open homepage
- Register/login
- Create category if needed
- Add transaction
- View transaction list
- Edit transaction
- Soft delete transaction
- Confirm dashboard totals update
