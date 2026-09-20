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
- [x] Create Account model + migration
- [x] Create Category model + migration
- [x] Add per-user default category seeding via Livewire
- [x] Add Livewire category management page
- [x] Account CRUD pages (ManageAccounts Livewire component)
- [x] Profile settings page polish (gradient avatar, KeySaku design system)

---

## Milestone 3 — Transactions
- [x] Create Transaction model + migration
- [x] Add User and Category transaction relationships
- [x] Add soft delete and manual/voice source data foundation
- [x] Add Transaction form (Livewire CreateTransaction)
- [x] Transaction list page
- [x] Transaction filter by month/type/category
- [x] Transaction edit page and Livewire form
- [x] Transaction soft delete UX
- [x] Balance recalculation on transaction save/delete

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
- [x] Create Budget model + migration
- [x] Create SavingGoal model + migration
- [x] Budget CRUD (Livewire ManageBudgets)
- [x] Budget progress bar component
- [x] Saving goal CRUD (Livewire ManageSavingsGoals)
- [x] Saving goal progress tracker

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

## Phase 6C — Chart.js Integration ✅ 2026-09-19
- [x] Add Chart.js CDN via @stack('scripts')
- [x] Add 6-month income/expense bar chart to reports page
- [x] Add expense-by-category doughnut chart with colour-coded segments
- [x] Pass sixMonthTrend and per-row color data from ReportController

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

## Phase 8A — Savings Goals Foundation
- [x] Create savings goals and contributions migrations
- [x] Create SavingsGoal and SavingsContribution models
- [x] Add user, goal, and contribution relationships
- [x] Rename savings route to savings.index
- [x] Add authenticated savings page skeleton
- [x] Add savings foundation feature tests

## Phase 8B — Savings Goals Create/List MVP
- [x] Create Livewire savings goals management component
- [x] Add savings goal create form
- [x] Save goals for authenticated user with active status
- [x] Show current user's savings goal list
- [x] Keep empty state for users without goals
- [x] Add focused savings create/list feature tests

## Phase 8C — Savings Contributions MVP
- [x] Add inline contribution mode per savings goal
- [x] Save contributions for authenticated user's goals
- [x] Update savings goal current amount after contribution
- [x] Refresh progress, remaining amount, and target reached label
- [x] Add focused savings contribution feature tests

## Phase 8D — Edit Savings Goal
- [x] Add inline edit mode per savings goal
- [x] Update goal name, target amount, and deadline
- [x] Keep current amount, contributions, and status unchanged
- [x] Recalculate progress display after goal update
- [x] Add focused savings goal edit feature tests

## Phase 8E — Archive Savings Goal
- [x] Add scoped archive action for savings goals
- [x] Add confirmation before archiving goals
- [x] Hide archived goals from active list
- [x] Keep savings contributions untouched
- [x] Add focused savings archive feature tests

## Phase 8F — Dashboard Savings Insight
- [x] Add active savings goals summary to dashboard
- [x] Show total saved, total target, and overall progress
- [x] Show compact active goal previews
- [x] Exclude archived and other users' goals
- [x] Add focused dashboard savings insight tests

## Phase 8G — Savings Contribution History Preview
- [x] Load recent contributions for active savings goals
- [x] Show latest 3 contribution rows per goal
- [x] Show contribution amount, date, and note preview
- [x] Show per-goal empty contribution state
- [x] Add focused contribution history preview tests

## Phase 9A — MVP QA Polish
- [x] Fix bottom navigation primary add action link
- [x] Remove misleading static dashboard insight examples
- [x] Replace leftover savings progress placeholder copy
- [x] Refresh Hostinger deployment notes for current MVP tables and assets
- [x] Run route, build, and full test validation

## Phase 9B — Demo Data Seeder
- [x] Add manual-only demo data seeder
- [x] Seed demo user, categories, transactions, budgets, and savings goals
- [x] Include manual and voice transaction examples
- [x] Include current and previous month demo data
- [x] Document explicit demo seeding command

## Phase 9C — Settings Menu Activation
- [x] Activate profile and category settings links
- [x] Add informational notification settings page
- [x] Add security settings page linking to profile
- [x] Add install-to-home-screen instruction page
- [x] Keep currency and language settings disabled without schema changes
- [x] Add focused settings feature tests

## Phase 9D — Currency And Language Preferences
- [x] Add user preference columns for currency and language
- [x] Activate currency and language settings links
- [x] Add currency preference form with strict validation
- [x] Add language preference form with strict validation
- [x] Store preferences without changing formatting, translation, or calculations
- [x] Add focused preference settings tests

## Phase 10A — Account CRUD ✅ 2026-09-19
- [x] Create accounts migration (household_id, user_id, name, type, balance, icon, color, is_active)
- [x] Create Account model with type constants and typeLabel helper
- [x] Add accounts() HasMany relation to User and Household
- [x] Create ManageAccounts Livewire component (create, edit, delete, toggle active)
- [x] Create manage-accounts Blade view with mobile-first design
- [x] Add /akun route and PageController method
- [x] Add Akun Keuangan link to Settings page

## Phase 10B — Profile Page Polish ✅ 2026-09-19
- [x] Redesign profile/edit.blade.php with gradient avatar card
- [x] Restyle update-profile-information-form (Indonesian labels, rounded inputs)
- [x] Restyle update-password-form (Indonesian labels)
- [x] Replace modal delete-user-form with inline Alpine.js danger zone

## Phase 10C — PWA Service Worker ✅ 2026-09-19
- [x] Create public/sw.js with install/activate/fetch lifecycle
- [x] Network-first for HTML pages, cache-first for static assets
- [x] Register service worker in app layout
- [x] Update manifest.json: scope, id, shortcut for reports

---

## Milestone 5 — Voice & Insights
- [x] SpeechRecognition JS module
- [x] Voice input modal
- [x] Text parser for voice → transaction
- [x] Chart.js integration (bar chart + doughnut)
- [x] Monthly summary chart (standalone page — optional)
- [x] Category breakdown chart (standalone page — optional)

---

## Milestone 6 — Production & Deployment
- [x] Test on Hostinger staging
- [x] PWA service worker (offline support)
- [x] Production .htaccess tuning
- [x] `php artisan config:cache && route:cache && view:cache`
- [x] Final smoke test
