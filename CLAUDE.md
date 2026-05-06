# CLAUDE.md — KeySaku

## What is this project?
KeySaku adalah aplikasi web pengelolaan keuangan keluarga berbasis mobile-first PWA, dibangun dengan Laravel 12 dan ditargetkan untuk Hostinger Premium shared hosting.

## Quick context
- Stack: Laravel 12 + PHP 8.4, Blade, Livewire, Tailwind CSS 4, Alpine.js
- DB: MySQL (Hostinger-managed)
- No Node.js in production — Vite is dev-only
- UI language: Indonesian (Bahasa Indonesia) Dan English (Bahasa Inggris)
- Code language: English

## Key directories
| Path | Purpose |
|------|---------|
| `app/Http/Controllers/` | Thin controllers, route handlers |
| `app/Livewire/` | Livewire components (interactive UI) |
| `app/Models/` | Eloquent models |
| `resources/views/layouts/` | Base layouts and partials |
| `resources/views/pages/` | Page-level Blade views |
| `resources/views/components/` | Reusable Blade components |
| `docs/` | Project documentation |
| `public/` | Web root for Hostinger |

## Current state (Milestone 1)
- [x] Laravel 12 scaffolded
- [x] Mobile-first layout with gradient background
- [x] Dashboard shell with static placeholder data
- [x] Bottom navigation (Beranda, Anggaran, Tabungan, Setelan)
- [x] Blade components: dashboard-card, quick-action, insight-card
- [x] PWA manifest.json
- [ ] Composer vendor install (requires internet)
- [ ] npm install + vite build (requires internet)
- [ ] Database migrations
- [ ] Auth (next milestone)

## Commands
```bash
# Dev
php artisan serve
npm run dev

# Build
npm run build

# Check
php artisan route:list
php artisan view:clear
```

## Do not
- Do not downgrade Laravel below 12
- Do not add a Node.js production server requirement
- Do not build auth, CRUD, or reports until Milestone 2+
- Do not hardcode Rupiah amounts (use placeholder data only for now)
