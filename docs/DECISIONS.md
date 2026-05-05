# DECISIONS — KeySaku

Architecture and technology decisions log.

---

## 001: Stack
Use Laravel 12, PHP 8.4, MySQL, Blade, Livewire, Tailwind CSS, and Alpine.js.

**Reason:**
Hostinger Premium supports PHP 8.4, and Laravel 12 officially supports PHP 8.4. This gives a modern Laravel foundation while staying compatible with shared hosting deployment.

**Local environment note:**
Development machine runs PHP 8.5.5 (superset of 8.4) — fully compatible. `composer.json` targets `^8.4` to match Hostinger's production PHP version.

---

## 002: Deployment target
Deploy first version to Hostinger Premium shared hosting with PHP 8.4 and MySQL.

**Reason:**
User already owns this hosting package, and the stack does not require a production Node.js server. Laravel + Blade + Livewire runs entirely on PHP. Vite is dev-only.

**Implication:**
- `public/` is the web root on Hostinger
- `.htaccess` must handle Laravel routing (already in `public/.htaccess`)
- Environment config via `.env` file uploaded to server root (not public/)

---

## 003: No auth in Milestone 1
Authentication (login/register) is explicitly excluded from the first milestone.

**Reason:**
Foundation-first approach. Get the UI shell, layout system, and design language right before adding user sessions and database dependencies.

---

## 004: Tailwind CSS 4 via Vite
Use Tailwind CSS v4 with the `@tailwindcss/vite` plugin (not PostCSS config file).

**Reason:**
Tailwind v4 uses `@import 'tailwindcss'` in CSS and integrates directly with Vite via the official plugin. No `tailwind.config.js` needed. Simpler setup.

**Constraint:**
Compiled CSS (`public/build/`) must be committed or built before deploying to Hostinger (no build step on shared hosting).

---

## 005: Livewire for interactive UI
Use Livewire v3 for stateful interactive components instead of a JavaScript SPA framework.

**Reason:**
- Keeps backend logic in PHP (Laravel strength)
- No API layer needed for CRUD operations
- Works perfectly on PHP shared hosting
- Alpine.js handles any lightweight client-side UI

---

## 006: Voice input deferred to Milestone 5
Browser SpeechRecognition API integration is planned but not in Milestone 1.

**Reason:**
Foundation first. Manual input fallback will always be available and built first (Milestone 3). Voice is an enhancement layer on top.

---

## 007: Indonesian UI copy
All user-facing strings, labels, and messages use Bahasa Indonesia.

**Reason:**
Target market is Indonesian families. Native language reduces cognitive friction.

**Implementation:**
- No Laravel i18n/localization setup needed in v1 (strings are inline in Blade)
- If multi-language is needed later, use Laravel `lang/` files


---

## 008: Authentication
Use Laravel Breeze Blade for MVP email/password authentication.

**Reason:**
Breeze is the standard Laravel starter for simple session-based auth, stays compatible with Hostinger PHP shared hosting, and avoids a production Node.js server requirement.

**Scope:**
- Register, login, and logout are enabled
- Dashboard and app pages require authentication
- Auth screens use Indonesian copy and KeySaku mobile-first visual style
- No social login, transaction CRUD, reports, or voice input in this milestone


---

## 009: Category management
Use a Livewire component for authenticated category management.

**Reason:**
Category CRUD is small, benefits from app-like interactions, and Livewire keeps the implementation on Laravel/PHP without requiring an API layer, axios, React, or Vue.

**Scope:**
- Categories belong to individual users
- Default income and expense categories are created for a user when the category page is first opened
- Category deletion is allowed while transactions are not implemented; future transaction usage checks must block unsafe deletion


---

## 010: Transaction data foundation
Use a dedicated `transactions` table with soft deletes and string-backed domain values for type and source.

**Reason:**
Soft deletes keep financial records recoverable, string values remain portable on MySQL shared hosting, and `source` prepares the model for future browser voice input without implementing voice features yet.

**Scope:**
- Supported types: `income`, `expense`
- Supported sources: `manual`, `voice`
- Category deletion is restricted by the database once transactions exist
- UI, reports, budgeting, and voice parsing are deferred
