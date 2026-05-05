# AGENTS.md — KeySaku

## Project
KeySaku — Aplikasi Pengelolaan Keuangan Keluarga (Mobile-First PWA).

## Stack
- Laravel 12
- PHP 8.4
- MySQL
- Blade
- Livewire
- Tailwind CSS
- Alpine.js
- Chart.js for reports later
- PWA-ready frontend
- Browser SpeechRecognition for voice input later
- Hostinger Premium shared hosting deployment target

## Deployment rule
Production must run on PHP shared hosting.
Do not introduce a production Node.js server requirement.
Node/npm may be used only for local development and Vite asset builds.

## Language
- UI copy and user-facing strings: Indonesian (Bahasa Indonesia)
- Code, variable names, comments: English
- Docs in this repo: Indonesian or English (match context)

## Code conventions
- PSR-12 PHP code style enforced via Laravel Pint
- Blade components live in `resources/views/components/`
- Pages live in `resources/views/pages/`
- Layouts live in `resources/views/layouts/`
- Livewire components live in `app/Livewire/`
- Models live in `app/Models/`
- Controllers stay thin — logic belongs in services or Livewire components
- Use named routes in Blade (`route('name')`)
- Do not hardcode URLs

## Design system
- Mobile-first, max-width `max-w-md` centered shell
- Gradient background: indigo → purple → pink (`from-indigo-600 via-purple-600 to-pink-500`)
- Cards: `bg-white/80 backdrop-blur-sm rounded-2xl border border-white/60`
- Primary color: indigo-500 / indigo-600
- Rounded corners: `rounded-2xl` or `rounded-3xl`
- Bottom navigation is fixed and always visible
- Typography: system font stack via Tailwind default

## Scope guards
- Auth is implemented with Laravel Breeze Blade; do not add social login or extra auth features without approval
- Do not build transaction CRUD yet
- Do not build reports yet
- Do not build voice input yet (placeholder only)
- Do not add external API calls
- Do not edit `.env` secrets directly

## Validation commands
```bash
php artisan route:list
php artisan view:clear
php artisan config:clear
npm run build
php artisan serve
```

## Deployment prep (Hostinger)
```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
