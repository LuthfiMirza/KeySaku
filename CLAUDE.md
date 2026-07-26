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

## Aturan Kerja

### Alur wajib tiap task
1. Baca docs/EXECUTION-PLAN.md, cari task yang diminta.
2. `git status` — kalau kotor, BERHENTI dan lapor.
3. `git checkout main && git pull`
4. `php artisan test` — catat angka awal.
5. Buat branch sesuai nama di plan. BRANCH DULU, BARU KODE.
6. Kerjakan HANYA yang tertulis di bagian "Ruang lingkup: DIKERJAKAN".
7. Test hijau → commit atomik → push → laporkan.

### Git
- Satu commit = satu perubahan utuh yang bisa dijelaskan satu kalimat.
- `git add <path>` eksplisit per file. DILARANG `git add .` atau `-A`.
- Sebelum tiap commit: `php artisan test` harus HIJAU. Dilarang commit
  dengan test merah.
- Sebelum tiap commit: `git diff --name-only tests/` — kalau ada file
  test LAMA yang termodifikasi, BERHENTI dan lapor.
- Conventional Commits, subject bahasa Inggris, maks 72 karakter,
  imperatif, tanpa titik. Body menjelaskan ALASAN, bukan mengulang diff.
- Type: feat, fix, refactor, test, docs, chore.
- Push tiap selesai satu task, lalu laporkan URL pull/new.
- Jangan menyebut Claude/AI/tooling di pesan commit.

### Dilarang keras
- `git push --force`, `git rebase`, `git reset --hard`, `git clean`
- `git commit --amend` pada commit yang sudah di-push
- Commit langsung ke main
- Membuat/merge/menutup Pull Request — user yang lakukan lewat web
- Install `gh` atau meminta token GitHub
- Install dependency baru tanpa izin
- Mengubah konfigurasi git atau .github/workflows
- Menghapus branch ui/bento-dark-dashboard (arsip, masih jadi sumber salvage)
- Commit: .env, storage/, vendor/, node_modules/, public/build/,
  *.sqlite, file log, kredensial apa pun

### Berhenti dan lapor kalau
- Working tree kotor di awal
- Test lama jadi merah
- Kontrak view putus (property/method yang dirujuk view hilang)
- Diff memuat perubahan di luar ruang lingkup task
- Ada anomali apa pun yang tidak tertulis di plan
Jangan improvisasi, jangan perbaiki sendiri, jangan lanjut.

### Format laporan wajib di akhir tiap task

    ## Laporan Task <ID> — <judul>
    Branch: <nama>
    Test: <awal> → <akhir> (<selisih> baru)
    File test lama disentuh: ya/tidak
    Commits:
      <hash> <subject>
    Diff stat vs main: <n> files, +<x> / -<y>
    Ruang lingkup: sesuai plan / ada penyimpangan (jelaskan)
    Definition of done: <centang per poin dari plan>
    Verifikasi manual untuk user: <langkah konkret di browser>
    URL PR: <pull/new dari output push>
    Anomali: <ada/tidak, jelaskan>

### Setelah task selesai
Perbarui kolom Status task itu di docs/EXECUTION-PLAN.md menjadi
SELESAI beserta nama branch-nya. Masukkan ke commit terakhir task.
