# ROADMAP — KeySaku

## Status legend
- 🟢 Done
- 🔵 In Progress
- ⚪ Planned
- 🔴 Blocked

---

## Milestone 1 — Foundation 🟢
**Goal:** Laravel 12 scaffold + mobile-first dashboard shell

- 🟢 Environment audit (PHP 8.5, Composer 2.9, Node 24)
- 🟢 Laravel 12 project scaffolded
- 🟢 AGENTS.md, CLAUDE.md, .agentsignore
- 🟢 Docs: PRD, SCHEMA, ROADMAP, TASKS, DECISIONS
- 🟢 Mobile-first layout (gradient + glassmorphism)
- 🟢 Bottom navigation (Beranda, Anggaran, Tabungan, Setelan)
- 🟢 Dashboard shell with static placeholder data
- 🟢 Blade components: dashboard-card, quick-action, insight-card
- 🟢 PWA manifest.json (updated with scope, id, shortcuts)
- 🟢 npm install + Vite build
- 🟢 Composer vendor install

---

## Milestone 2 — Auth & Accounts 🟢
**Goal:** User register/login + account management

- 🟢 Register, login, logout (Laravel Breeze)
- 🟢 User profile settings (restyled with KeySaku design)
- 🟢 Account CRUD (kas, bank, e-wallet) — ManageAccounts Livewire
- 🟢 Default categories seeded

---

## Milestone 3 — Transactions 🟢
**Goal:** Core transaction recording

- 🟢 Add income transaction
- 🟢 Add expense transaction
- 🟢 Transaction list with filter (month, category)
- 🟢 Transaction edit
- 🟢 Transaction soft delete
- ⚪ Balance auto-update on transaction (account balance sync)

---

## Milestone 4 — Budgets & Saving Goals 🟢
**Goal:** Budget tracking and savings progress

- 🟢 Monthly budget per category
- 🟢 Budget progress indicator (Aman / Mendekati Batas / Lewat Batas)
- 🟢 Over-budget warning (insight card)
- 🟢 Copy previous month budgets
- 🟢 Saving goal CRUD
- 🟢 Saving goal progress tracker
- 🟢 Manual savings top-up (contributions)

---

## Milestone 5 — Voice Input & Insights 🔵
**Goal:** Voice-first expense input + AI-style insights

- 🟢 Browser SpeechRecognition integration
- 🟢 Voice text → transaction parser (Indonesian keyword-based)
- 🟢 Manual fallback always available
- 🟢 Category breakdown chart (Chart.js doughnut)
- 🟢 Monthly trend chart (Chart.js bar — 6 months)
- ⚪ Voice input modal (full-screen UX)
- ⚪ Monthly spending insights (auto-generated text)

---

## Milestone 6 — PWA & Deployment 🔵
**Goal:** Production-ready on Hostinger Premium

- 🟢 PWA service worker (offline support — network-first strategy)
- 🟢 App install prompt (manifest shortcuts)
- ⚪ Hostinger Premium deployment
- ⚪ .htaccess tuning for Laravel
- ⚪ Production config cache
- ⚪ Error monitoring setup

---

## Post-launch ideas (backlog)
- Family sharing (multi-user per household) — partially done (Household model exists)
- Recurring transactions
- CSV/PDF export — done ✅
- Push notifications (budget warnings)
- Dark mode
- Balance auto-update on transaction save/delete
