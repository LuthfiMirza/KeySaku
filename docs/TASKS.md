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

## Phase 5B — SpeechRecognition Capture
- [x] Browser SpeechRecognition support detection
- [x] Microphone permission requested on click only
- [x] Transcript capture and preview
- [x] Unsupported browser fallback
- [x] Friendly recognition error messages
- [x] Clear transcript action

---

## Phase 5C — Editable Transcript Preview
- [x] Store original voice transcript separately
- [x] Editable transcript textarea
- [x] Reset transcript to original voice result
- [x] Clear original and editable transcript

---

## Phase 5D — Simple Transcript Parser
- [x] VoiceTransactionParser service
- [x] Parse numeric and simple Indonesian word amounts
- [x] Detect and match category phrase
- [x] Parsed transaction preview on voice page
- [x] Parser unit tests
- [x] Save parsed voice transaction after confirmation

---

## Phase 5E — Voice Transaction Confirmation Save
- [x] Save parsed voice transaction after confirmation
- [x] Store source as voice
- [x] Preserve editable transcript as raw_voice_text
- [x] Guard incomplete parsed drafts
- [x] Voice save feature tests

---

## Phase 5F — Voice Input QA Polish
- [x] Show voice save success flash on transaction list
- [x] Improve parser warning copy
- [x] Add clearer supported voice examples
- [x] Document supported voice input patterns

---

## Phase 6A — Basic Monthly Reports
- [x] Authenticated reports page route
- [x] Selected month filter with safe fallback
- [x] Monthly income, expense, and balance totals
- [x] Expense by category summary
- [x] Latest selected-month transactions
- [x] Basic reports feature tests

---

## Phase 6B — Reports Polish
- [x] Keep bottom navigation unchanged
- [x] Improve dashboard report shortcut copy
- [x] Improve empty report states
- [x] Add manual and voice input CTAs for empty reports

---

## Phase 7A — Budgeting Foundation
- [x] Create budgets migration
- [x] Create Budget model
- [x] Add user and category budget relationships
- [x] Rename budget route to budgets.index
- [x] Add mobile-first budget page skeleton
- [x] Budget foundation feature tests

---

## Phase 7B — Create Budget MVP
- [x] Create Livewire budget management component
- [x] Add monthly budget create form
- [x] Limit budget categories to expense categories
- [x] Block duplicate category budget per month
- [x] Show selected-month budget list
- [x] Budget create feature tests

---

## Phase 7C — Budget Progress Read-Only
- [x] Calculate selected-month expense usage per budget category
- [x] Show budget, used, remaining, and percentage values
- [x] Add simple budget progress bars
- [x] Show Aman, Mendekati Batas, and Lewat Batas statuses
- [x] Budget progress feature tests

## Phase 7D — Budget UX Polish
- [x] Calculate selected-month budget summary totals
- [x] Show Ringkasan Anggaran on budget page
- [x] Add compact current-month dashboard budget insight
- [x] Show dashboard empty state when current month has no budgets
- [x] Budget summary and dashboard insight feature tests

## Phase 7E — Budget Empty-State UX Polish
- [x] Improve selected-month no-budget empty state
- [x] Add month/year selector guidance copy
- [x] Add no-expense helper for budgeted categories
- [x] Add compact dashboard no-budget CTA
- [x] Update focused budget and dashboard tests

## Phase 7F — Budget Mobile & Accessibility Micro-Polish
- [x] Improve budget form spacing and tap targets
- [x] Add helper text associations for month/year controls
- [x] Improve progress card visual hierarchy on mobile
- [x] Add basic ARIA labels and progressbar semantics
- [x] Keep budget behavior and calculations unchanged

## Phase 7G — Edit Budget Amount
- [x] Add Livewire edit amount state and actions
- [x] Add inline budget amount edit UI
- [x] Validate edited amount before update
- [x] Scope budget updates to authenticated user
- [x] Add focused edit budget feature tests

## Phase 7H — Delete Budget
- [x] Add scoped budget delete action
- [x] Add confirmation before deleting budgets
- [x] Ensure budget delete does not delete transactions
- [x] Refresh budget list, progress, and summary after delete
- [x] Add focused delete budget feature tests

## Phase 7I — Copy Previous Month Budgets
- [x] Add action to copy budgets from previous calendar month
- [x] Skip categories that already have target-month budgets
- [x] Keep existing target budgets unchanged
- [x] Add copy helper UI and status messages
- [x] Add focused copy budget feature tests

---

## Milestone 5 — Voice & Insights
- [x] SpeechRecognition JS module
- [ ] Voice input modal
- [x] Text parser for voice → transaction
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
