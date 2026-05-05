# TASKS — KeySaku

Active task board. Update this file as tasks are picked up and completed.

## Legend
- [ ] Pending
- [x] Done
- [~] In Progress
- [!] Blocked

---

## Milestone 1 — Foundation

### Done
- [x] Audit: PHP 8.5.5, Composer 2.9.5, Node v24.9.0, npm 11.6.0
- [x] All required PHP extensions verified (pdo_mysql, mbstring, openssl, etc.)
- [x] Laravel 12 scaffold (manual — no network in sandbox)
- [x] AGENTS.md created
- [x] CLAUDE.md created
- [x] .agentsignore created
- [x] docs/PRD.md
- [x] docs/SCHEMA.md
- [x] docs/ROADMAP.md
- [x] docs/TASKS.md (this file)
- [x] docs/DECISIONS.md
- [x] resources/views/layouts/app.blade.php
- [x] resources/views/layouts/partials/pwa-meta.blade.php
- [x] resources/views/layouts/partials/mobile-header.blade.php
- [x] resources/views/layouts/partials/bottom-nav.blade.php
- [x] resources/views/pages/dashboard.blade.php
- [x] resources/views/pages/budgets.blade.php
- [x] resources/views/pages/goals.blade.php
- [x] resources/views/pages/settings.blade.php
- [x] resources/views/components/dashboard-card.blade.php
- [x] resources/views/components/quick-action.blade.php
- [x] resources/views/components/insight-card.blade.php
- [x] public/manifest.json
- [x] routes/web.php (4 routes)
- [x] app/Http/Controllers/DashboardController.php
- [x] app/Http/Controllers/PageController.php

### Pending (needs internet)
- [x] Run `composer install` to install vendor dependencies
- [x] Run `npm install` to install node_modules
- [x] Run `npm run build` to compile Tailwind + Alpine assets
- [ ] Run `php artisan key:generate` if `APP_KEY` is empty
- [ ] Run `php artisan migrate` after MySQL/SQLite is configured

---

## Milestone 2 — Auth & Accounts
- [x] Install Laravel Breeze Blade
- [x] Create User model + default auth migration
- [x] Add register, login, logout routes
- [x] Protect dashboard and app pages with `auth` middleware
- [x] Restyle auth screens with KeySaku mobile-first UI
- [ ] Run `php artisan migrate` against the configured local database
- [ ] Create Account model + migration
- [x] Create Category model + migration
- [x] Add per-user default category seeding via Livewire
- [x] Add Livewire category management page
- [ ] Account CRUD pages
- [ ] Profile settings page polish

---

## Milestone 3 — Transactions
- [x] Create Transaction model + migration
- [x] Add User and Category transaction relationships
- [x] Add soft delete and manual/voice source data foundation
- [ ] Add Transaction form (Livewire)
- [x] Transaction list page
- [x] Transaction filter by month/type/category
- [x] Transaction edit page and Livewire form
- [x] Transaction soft delete UX
- [ ] Balance recalculation on transaction save/delete

---

## Phase 4A — Dashboard Real Data
- [x] Current month income total
- [x] Current month expense total
- [x] Current month balance
- [x] All-time net balance
- [x] Latest 3 transactions preview
- [x] Empty state for no transactions

---

## Phase 4B — Monthly Category Summary
- [x] Current month expense totals by category
- [x] Top 5 spending categories
- [x] Category percentage calculation
- [x] CSS progress bars on dashboard
- [x] Empty state for zero monthly expense

---

## Milestone 4 — Budgets & Goals
- [ ] Create Budget model + migration
- [ ] Create SavingGoal model + migration
- [ ] Budget CRUD (Livewire)
- [ ] Budget progress bar component
- [ ] Saving goal CRUD (Livewire)
- [ ] Saving goal progress tracker

---

## Phase 5A — Voice Input UI Shell
- [x] Authenticated voice input page shell
- [x] Microphone placeholder UI
- [x] Transcript preview placeholder
- [x] Example voice phrase
- [x] Manual input fallback link

---

## Milestone 5 — Voice & Insights
- [ ] SpeechRecognition JS module
- [ ] Voice input modal
- [ ] Text parser for voice → transaction
- [ ] Chart.js integration
- [ ] Monthly summary chart
- [ ] Category breakdown chart

---

## Milestone 6 — Deploy
- [ ] Test on Hostinger staging
- [ ] PWA service worker
- [ ] Production .htaccess tuning
- [ ] `php artisan config:cache && route:cache && view:cache`
- [ ] Final smoke test
