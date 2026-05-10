# KeySaku P0 Asset Checklist

Scope checklist ini hanya untuk asset landing page MVP KeySaku. Semua file berada di `public/assets/` agar aman dipakai langsung oleh browser.

## Status Umum

- [x] Struktur P0 dibuat di `public/assets/`.
- [x] Semua asset P0 memakai lowercase-kebab-case.
- [x] Semua placeholder visual P0 adalah SVG valid, bukan bitmap palsu.
- [x] Tidak ada external URL atau library gambar.
- [x] Manifest tersedia di `public/assets/asset-manifest.json`.
- [x] Landing MVP tersedia di `resources/views/pages/landing.blade.php`.

## Brand

- [x] `brand/logo-keysaku-horizontal.svg` — navbar dan hero brand.
- [x] `brand/logo-keysaku-mark.svg` — mark compact.
- [x] `brand/logo-keysaku-white.svg` — CTA/dark section.

## Hero

- [x] `landing/hero/hero-bg-blob.svg` — background blob.
- [x] `landing/hero/hero-floating-coins.svg` — dekorasi koin.
- [x] `landing/hero/hero-floating-badges.svg` — dekorasi badge/streak.

## Icons Finance

- [x] `icons/finance/icon-wallet.svg` — fitur catat uang.
- [x] `icons/finance/icon-coin.svg` — ringkasan uang.
- [x] `icons/finance/icon-receipt.svg` — riwayat transaksi.
- [x] `icons/finance/icon-piggy-bank.svg` — target tabungan.

## Icons Gamification

- [x] `icons/gamification/icon-target.svg` — challenge.
- [x] `icons/gamification/icon-trophy.svg` — achievement.
- [x] `icons/gamification/icon-badge.svg` — badge/reward.

## Icons Navigation

- [x] `icons/navigation/icon-home.svg` — beranda.
- [x] `icons/navigation/icon-transaction.svg` — transaksi.
- [x] `icons/navigation/icon-savings.svg` — tabungan.
- [x] `icons/navigation/icon-report.svg` — laporan.

## Empty State

- [x] `illustrations/empty-states/empty-transactions.svg` — transaksi kosong.

## Developer Notes

- Gunakan `asset('assets/...')` saat memanggil asset dari Blade.
- Jangan mengganti nama/path file yang sudah dipakai landing page; replace isi file saja.
- Saat asset final tersedia, optimasi SVG terlebih dahulu lalu replace file di path yang sama.
- Jika asset belum final, SVG placeholder ini tetap valid dan tidak menyebabkan broken image.
- Untuk production, review visual final, alt text, ukuran file, dan kontras warna.

## Asset Final/User Yang Sudah Dipindahkan

- [x] `brand/logo-keysaku-horizontal.svg`
- [x] `brand/logo-keysaku-mark.svg`
- [x] `brand/logo-keysaku-white.svg`
- [x] `brand/app-icon-512.png`
- [x] `brand/brand-pattern-coins.png`
- [x] `landing/hero/brand-blob-gradient.png`
- [x] `illustrations/characters/mascot-keysaku-happy.svg`
- [x] `illustrations/landing/illustration-expense-tracking.svg`
- [x] `illustrations/landing/illustration-phone-budgeting.svg`
- [x] `illustrations/landing/illustration-reward-badge.svg`
- [x] `illustrations/landing/illustration-savings-goal.svg`
- [x] `illustrations/landing/illustration-weekly-challenge.svg`
- [x] `illustrations/landing/illustration-youth-saving-money.svg`

Detail lengkap status asset ada di `public/assets/ASSET_INVENTORY.md`.
