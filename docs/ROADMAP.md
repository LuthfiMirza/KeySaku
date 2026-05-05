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
- 🟢 PWA manifest.json
- ⚪ npm install + Vite build (needs internet)
- ⚪ Composer vendor install (needs internet)

---

## Milestone 2 — Auth & Accounts ⚪
**Goal:** User register/login + account management

- Register, login, logout
- Email verification (optional)
- User profile settings
- Account CRUD (kas, bank, e-wallet)
- Default categories seeded

---

## Milestone 3 — Transactions ⚪
**Goal:** Core transaction recording

- Add income transaction
- Add expense transaction
- Add transfer between accounts
- Transaction list with filter (month, category)
- Transaction delete
- Balance auto-update on transaction

---

## Milestone 4 — Budgets & Saving Goals ⚪
**Goal:** Budget tracking and savings progress

- Monthly budget per category
- Budget progress indicator
- Over-budget warning (insight card)
- Saving goal CRUD
- Saving goal progress tracker
- Manual savings top-up

---

## Milestone 5 — Voice Input & Insights ⚪
**Goal:** Voice-first expense input + AI-style insights

- Browser SpeechRecognition integration
- Voice text → transaction parser
- Manual fallback always available
- Monthly spending insights
- Category breakdown chart (Chart.js)
- Monthly trend chart

---

## Milestone 6 — PWA & Deployment ⚪
**Goal:** Production-ready on Hostinger Premium

- PWA service worker (offline support)
- App install prompt
- Hostinger Premium deployment
- .htaccess tuning for Laravel
- Production config cache
- Error monitoring setup

---

## Post-launch ideas (backlog)
- Family sharing (multi-user per household)
- Recurring transactions
- CSV/PDF export
- Push notifications (budget warnings)
- Dark mode
