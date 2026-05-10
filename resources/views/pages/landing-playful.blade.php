@php
    $assetUrl = function (string $path): ?string {
        return file_exists(public_path($path)) ? asset($path) : null;
    };

    $brand = [
        'logo' => $assetUrl('assets/brand/logo-keysaku-horizontal.svg'),
        'mark' => $assetUrl('assets/brand/logo-keysaku-mark.svg'),
        'white' => $assetUrl('assets/brand/logo-keysaku-white.svg'),
    ];

    $hero = [
        'coins' => $assetUrl('assets/landing/hero/hero-floating-coins.svg'),
        'badges' => $assetUrl('assets/landing/hero/hero-floating-badges.svg'),
    ];

    $icons = [
        'wallet' => $assetUrl('assets/icons/finance/icon-wallet.svg'),
        'coin' => $assetUrl('assets/icons/finance/icon-coin.svg'),
        'receipt' => $assetUrl('assets/icons/finance/icon-receipt.svg'),
        'piggy' => $assetUrl('assets/icons/finance/icon-piggy-bank.svg'),
        'target' => $assetUrl('assets/icons/gamification/icon-target.svg'),
        'trophy' => $assetUrl('assets/icons/gamification/icon-trophy.svg'),
        'badge' => $assetUrl('assets/icons/gamification/icon-badge.svg'),
    ];

    $illustrations = [
        'mascot' => $assetUrl('assets/illustrations/characters/mascot-keysaku-happy.svg') ?? $assetUrl('assets/illustrations/finance/illustration-piggy-bank.svg'),
        'saving' => $assetUrl('assets/illustrations/landing/illustration-savings-goal.svg') ?? $assetUrl('assets/illustrations/finance/illustration-piggy-bank.svg'),
    ];

    $featureCards = [
        ['icon' => '💸', 'title' => 'Catat Transaksi', 'copy' => 'Input pengeluaran & pemasukan dalam 2 detik.'],
        ['icon' => '🎯', 'title' => 'Budget Pintar', 'copy' => 'Atur limit tiap kategori biar nggak kebablasan.'],
        ['icon' => '🐷', 'title' => 'Target Nabung', 'copy' => 'Set goal tabungan dengan deadline dan pantau progresnya.'],
        ['icon' => '📊', 'title' => 'Ringkasan Visual', 'copy' => 'Lihat pola keuanganmu dalam grafik yang gampang dibaca.'],
    ];

    $stats = [
        ['target' => 10000, 'suffix' => '+', 'label' => 'Pengguna Aktif'],
        ['target' => 24, 'prefix' => 'Rp ', 'suffix' => 'M', 'label' => 'Total Tabungan Terkumpul', 'decimal' => true],
        ['target' => 87, 'suffix' => '%', 'label' => 'User Capai Target Bulan Ini'],
        ['target' => 49, 'suffix' => ' ⭐', 'label' => 'Rating di App Store', 'decimal' => true],
    ];

    $steps = [
        ['number' => '01', 'title' => 'Catat uang masuk & keluar', 'copy' => 'Mulai dari transaksi kecil: jajan, transport, transfer, sampai pemasukan bulanan.'],
        ['number' => '02', 'title' => 'Atur budget dan target', 'copy' => 'Buat batas kategori dan goal nabung supaya arah uangmu lebih jelas.'],
        ['number' => '03', 'title' => 'Kumpulkan XP dari kebiasaan baik', 'copy' => 'Streak, milestone, dan challenge bikin proses nabung terasa seperti adventure.'],
    ];

    $testimonials = [
        ['name' => 'Naya', 'role' => 'Mahasiswa', 'quote' => 'Aku jadi tahu uang jajanku habis di mana. Target nabungnya bikin lebih semangat.'],
        ['name' => 'Raka', 'role' => 'Fresh Graduate', 'quote' => 'Biasanya budgeting bikin males, tapi KeySaku terasa kayak game kecil yang kepakai tiap hari.'],
        ['name' => 'Dina', 'role' => 'Content Creator', 'quote' => 'Progress dan badge-nya bikin aku konsisten catat transaksi tanpa terasa dipaksa.'],
    ];

    $faqs = [
        ['question' => 'KeySaku cocok untuk siapa?', 'answer' => 'KeySaku cocok untuk pelajar, mahasiswa, first jobber, dan siapa pun yang ingin mulai mengatur uang dengan cara ringan dan menyenangkan.'],
        ['question' => 'Apakah KeySaku gratis?', 'answer' => 'Fitur dasar dirancang gratis untuk mencatat transaksi, melihat ringkasan, dan membuat target tabungan sederhana.'],
        ['question' => 'Apa bedanya dengan aplikasi keuangan biasa?', 'answer' => 'KeySaku memakai pengalaman gamified: level, streak, badge, challenge, dan milestone agar kebiasaan finansial terasa lebih hidup.'],
        ['question' => 'Apakah data transaksi saya aman?', 'answer' => 'Landing ini masih tahap MVP visual. Pada implementasi produksi, keamanan data dan privasi akan jadi prioritas utama sebelum rilis publik.'],
        ['question' => 'Apakah bisa bikin banyak target tabungan?', 'answer' => 'Ya, konsep produknya mendukung banyak target seperti dana darurat, gadget, liburan, kendaraan, atau wishlist personal.'],
    ];
@endphp

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KeySaku — Atur Duit, Naik Level</title>
    <meta name="description" content="KeySaku adalah aplikasi keuangan gamified untuk anak muda: catat transaksi, kejar target nabung, naik level, dan kumpulkan reward.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --color-brand: #7B3FBF;
            --color-brand-dark: #6A2FAE;
            --color-brand-teal: #00B4A6;
            --color-brand-lime: #8DC63F;
            --color-accent-yellow: #FFDF49;
            --color-accent-pink: #FD64B6;
            --color-accent-lavender: #EDE8F5;
            --color-bg-primary: #FAF8FF;
            --color-dark: #010E26;
            --color-dark-soft: #2D2D44;
            --color-text-main: #010E26;
            --color-text-muted: #6B7080;
            --color-text-white: #FFFFFF;
            --cream: var(--color-bg-primary);
            --coral: var(--color-brand);
            --orange: var(--color-brand-dark);
            --yellow: var(--color-accent-yellow);
            --mint: var(--color-brand-teal);
            --lime: var(--color-brand-lime);
            --lavender: var(--color-accent-lavender);
            --dark: var(--color-dark);
            --soft-blue: var(--color-accent-lavender);
            --font-display: 'Nunito', ui-sans-serif, system-ui, sans-serif;
            --font-body: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        }

        * { box-sizing: border-box; }
        body { margin: 0; background: var(--color-bg-primary); color: var(--color-text-main); font-family: var(--font-body); }
        .font-display, h1, h2, h3, .badge, .stat-number, .level-label, .section-label { font-family: var(--font-display); }
        .page-shell { overflow: hidden; background: var(--color-bg-primary); }
        .section-pad { padding: 72px 20px; }
        .container { width: min(1120px, 100%); margin: 0 auto; }
        .pill { border-radius: 999px; }
        .soft-shadow { box-shadow: 0 24px 60px rgba(26, 26, 46, .14); }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 48px; padding: 0 22px; border-radius: 999px; border: 2px solid var(--dark); font-weight: 900; text-decoration: none; transition: transform .15s ease, box-shadow .15s ease, background .15s ease; }
        .btn-primary { background: var(--coral); color: #fff; box-shadow: 4px 4px 0 var(--dark); }
        .btn-primary:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0 var(--dark); }
        .btn-primary:active { transform: translate(2px, 2px); box-shadow: 2px 2px 0 var(--dark); }
        .btn-secondary { color: var(--dark); background: transparent; box-shadow: 4px 4px 0 var(--dark); }
        .btn-secondary:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0 var(--dark); }
        .btn-secondary:active { transform: translate(2px, 2px); box-shadow: 2px 2px 0 var(--dark); }
        .btn-light { background: var(--color-text-white); color: var(--color-dark); box-shadow: 4px 4px 0 var(--color-dark); }
        .section-label { display: inline-flex; align-items: center; gap: 8px; padding: 9px 15px; border-radius: 999px; background: #fff; color: var(--coral); font-weight: 900; font-size: 13px; letter-spacing: .06em; text-transform: uppercase; box-shadow: 0 10px 24px rgba(26,26,46,.08); }

        .announcement { position: sticky; top: 0; z-index: 80; overflow: hidden; background: var(--color-brand); color: var(--color-text-white); font-size: 13px; letter-spacing: .05em; text-transform: uppercase; }
        .marquee-track { display: flex; width: max-content; animation: marquee 20s linear infinite; }
        .marquee-item { display: flex; align-items: center; min-width: max-content; gap: 14px; padding: 9px 12px; }
        .marquee-divider, .mq-divider { display: inline-grid; place-items: center; width: 14px; height: 14px; color: rgba(255,255,255,.5); margin: 0 16px; font-size: 10px; line-height: 1; transform: rotate(-12deg); }
        .marquee-light { font-weight: 400; }
        .marquee-bold { font-weight: 800; }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

        .navbar { position: sticky; top: 35px; z-index: 70; border-bottom: 2px solid var(--dark); background: #fff; }
        .nav-inner { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 12px 20px; }
        .nav-logo img { height: 76px; width: auto; display: block; }
        .nav-logo { display: inline-flex; align-items: center; min-width: 220px; flex-shrink: 0; }
        .nav-links { display: none; align-items: center; gap: 24px; font-weight: 900; }
        .nav-links a { color: var(--dark); text-decoration: none; border-bottom: 2px solid transparent; padding-bottom: 3px; transition: border-color .15s ease, transform .15s ease; }
        .nav-links a:hover { border-bottom-color: var(--coral); transform: rotate(-1deg); }
        .hamburger { display: inline-flex; flex-direction: column; gap: 5px; width: 42px; height: 42px; align-items: center; justify-content: center; border: 0; border-radius: 16px; background: #fff; box-shadow: 0 10px 24px rgba(26,26,46,.08); }
        .hamburger span { width: 18px; height: 3px; border-radius: 999px; background: var(--dark); }
        .mobile-menu { display: none; padding: 0 20px 16px; }
        .mobile-menu.is-open { display: block; }
        .mobile-menu a { display: block; padding: 13px 16px; border-radius: 18px; color: var(--dark); font-weight: 900; text-decoration: none; background: rgba(255,255,255,.72); margin-top: 8px; }

        .hero { position: relative; padding: 76px 20px 82px; background: var(--color-bg-primary); }
        .hero-bg-pattern { position: absolute; inset: 0; z-index: 0; background-image: radial-gradient(circle, #d4c5b0 1px, transparent 1px); background-size: 28px 28px; opacity: .4; }
        .hero-grid { position: relative; z-index: 2; display: grid; gap: 42px; align-items: center; }
        .hero h1 { margin: 18px 0 0; max-width: 620px; font-size: clamp(40px, 13vw, 56px); line-height: .92; letter-spacing: -.055em; }
        .hero p { max-width: 610px; margin: 20px 0 0; font-size: 18px; line-height: 1.7; color: var(--color-text-muted); }
        .hero-actions { display: flex; flex-direction: column; gap: 12px; margin-top: 28px; }
        .hero-visual { position: relative; min-height: 520px; display: grid; place-items: center; }
        .hero-badge { font-family: var(--font-display); font-weight: 800; font-size: 11px; letter-spacing: .08em; text-transform: uppercase; background: var(--color-accent-yellow); color: var(--color-dark); border: 2px solid var(--color-dark); border-radius: 999px; padding: 6px 16px; display: inline-flex; align-items: center; gap: 6px; transform: rotate(-2deg); box-shadow: 3px 3px 0 var(--color-dark); }
        .headline-wrap { position: relative; display: inline-block; }
        .headline-squiggle { position: absolute; left: 4px; right: 0; bottom: -10px; width: min(280px, 72vw); height: 12px; }
        .asset-placeholder { display: block; background: rgba(255,107,53,.1); border: 2px dashed var(--coral); border-radius: 8px; }
        .mascot-hero { position: absolute; right: 8px; bottom: 4px; width: 200px; height: 200px; z-index: 1; opacity: .9; }
        .deco-coin { position: absolute; right: 20px; top: 26px; width: 40px; height: 40px; z-index: 3; }
        .deco-star-1 { position: absolute; left: 14px; top: 68px; width: 24px; height: 24px; z-index: 3; }
        .deco-star-2 { display: inline-block; width: 18px; height: 18px; margin-left: 10px; vertical-align: middle; }
        .deco-shape { position: absolute; pointer-events: none; z-index: 1; }
        .phone-wrap { position: relative; z-index: 2; width: min(292px, 78vw); transform: rotate(-5deg); animation: phone-float 3s ease-in-out infinite; }
        @keyframes phone-float { 0%,100% { transform: rotate(-5deg) translateY(0); } 50% { transform: rotate(-5deg) translateY(-10px); } }
        .phone-frame-brand { overflow: hidden; border: 10px solid var(--coral); border-radius: 42px; background: var(--coral); box-shadow: 7px 7px 0 var(--dark); outline: 2px solid var(--dark); }
        .phone { overflow: hidden; border: 10px solid var(--coral); border-radius: 42px; background: var(--coral); box-shadow: 7px 7px 0 var(--dark); outline: 2px solid var(--dark); }
        .phone-screen { min-height: 560px; border-radius: 30px; background: #FAF8FF; overflow: hidden; }
        .phone-top { padding: 22px 20px; background: #fff; }
        .phone-card { margin: 16px; border-radius: 26px; padding: 18px; background: var(--coral); color: #fff; }
        .phone-progress { height: 12px; border-radius: 999px; background: rgba(255,255,255,.32); overflow: hidden; margin-top: 15px; }
        .phone-progress span { display: block; width: 72%; height: 100%; border-radius: 999px; background: var(--yellow); }
        .phone-mini-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding: 0 16px 16px; }
        .phone-mini { border-radius: 22px; background: #fff; padding: 14px; box-shadow: 0 10px 24px rgba(26,26,46,.08); }
        .phone-nav { display: flex; justify-content: space-around; margin: 0 16px 18px; padding: 13px; border-radius: 26px; background: var(--dark); font-size: 18px; }
        .float-card { position: absolute; z-index: 4; padding: 12px 15px; border: 1.5px solid var(--dark); font-weight: 1000; animation: float 3s ease-in-out infinite; }
        .badge-streak { left: -8px; top: 58px; transform: rotate(-3deg); border-radius: 12px; background: var(--yellow); box-shadow: 4px 4px 0 var(--dark); }
        .badge-saving { left: -14px; bottom: 184px; transform: rotate(2deg); border-radius: 8px; background: #8DC63F; color: var(--dark); box-shadow: 3px 3px 0 var(--dark); animation-delay: .5s; }
        .badge-trophy { right: -10px; bottom: 76px; transform: rotate(-1.5deg); border-radius: 16px; background: #fff; border-color: #7B3FBF; box-shadow: 5px 5px 0 var(--dark); animation-delay: 1s; }
        .float-asset { position: absolute; z-index: 3; animation: float 3.4s ease-in-out infinite; pointer-events: none; }
        .float-asset.coin { right: 2px; top: 16px; width: 112px; }
        .float-asset.piggy { left: 4px; bottom: 18px; width: 92px; animation-delay: .7s; }
        .sparkle-mark { position: absolute; width: 18px; height: 18px; border-radius: 4px; background: var(--yellow); box-shadow: 34px -24px 0 var(--mint), 68px 12px 0 var(--coral); animation: twinkle 2s ease-in-out infinite; transform: rotate(18deg); }
        .sparkle-mark.hero-left { left: 18px; top: 70px; }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-12px); } }
        @keyframes twinkle { 0%,100% { opacity: 1; transform: scale(1); } 50% { opacity: .3; transform: scale(.6); } }

        .trust { border-block: 1px solid rgba(26,26,46,.08); background: #fff; padding: 18px 0; overflow: hidden; }
        .trust-label { text-align: center; margin-bottom: 12px; color: #727288; font-size: 13px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em; }
        .logo-box { display: grid; place-items: center; min-width: 160px; height: 54px; border-radius: 18px; background: #f7f7fb; color: #9a9aaa; font-weight: 900; border: 1px solid #ededf4; }

        .problem { background: var(--color-accent-lavender); }
        .center { text-align: center; }
        .section-title { margin: 14px auto 0; max-width: 780px; font-size: clamp(34px, 8vw, 54px); line-height: 1; letter-spacing: -.045em; }
        .section-copy { max-width: 680px; margin: 16px auto 0; color: #5e5e72; font-size: 18px; line-height: 1.7; }
        .pain-grid, .feature-grid, .game-grid, .stats-grid { display: grid; gap: 18px; margin-top: 34px; }
        .pain-card, .feature-card, .stat-card { border-radius: 28px; background: #fff; padding: 24px; box-shadow: 0 18px 42px rgba(26,26,46,.08); transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; }
        .pain-card:hover, .feature-card:hover { transform: translateY(-6px); box-shadow: 0 20px 46px rgba(255,107,53,.15); }
        .pain-card .emoji, .feature-card .emoji { font-size: 48px; line-height: 1; }
        .pain-card h3, .feature-card h3 { margin: 16px 0 0; font-size: 22px; font-weight: 1000; }
        .pain-card p, .feature-card p { color: #626278; line-height: 1.65; }
        .answer-text { margin-top: 30px; font-size: 24px; font-weight: 1000; }
        .feature-card { border: 2px solid transparent; }
        .feature-card:hover { border-color: var(--coral); }

        .game { position: relative; overflow: hidden; background: var(--color-dark); color: var(--color-text-white); }
        .game::before { content: ''; position: absolute; inset: 0; opacity: .22; background-image: radial-gradient(#fff 1.4px, transparent 1.4px); background-size: 28px 28px; }
        .game .container { position: relative; z-index: 1; }
        .game-card { border: 1px solid rgba(255,255,255,.14); border-radius: 32px; background: rgba(255,255,255,.08); padding: 24px; backdrop-filter: blur(14px); }
        .level-bar { height: 16px; border-radius: 999px; background: rgba(255,255,255,.14); overflow: hidden; }
        .level-fill { width: 0; height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--mint), var(--yellow), var(--coral)); transition: width 1.2s cubic-bezier(.4,0,.2,1); }
        .game.revealed-section .level-fill { width: 74%; }
        .level-badges, .reward-row { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px; }
        .level-badges span, .reward-row span { display: grid; place-items: center; min-width: 54px; height: 54px; border-radius: 18px; background: linear-gradient(135deg, var(--yellow), var(--coral)); color: var(--dark); font-weight: 1000; }
        .streak-number { font-size: 56px; line-height: 1; font-weight: 1000; color: var(--yellow); }
        .challenge { margin-top: 18px; border-radius: 22px; padding: 16px; background: #fff; color: var(--dark); font-weight: 900; }
        .game-sparkle { position: absolute; width: 12px; height: 12px; border-radius: 50%; background: var(--yellow); animation: twinkle 2s ease-in-out infinite; }
        .game-sparkle.s1 { top: 18%; left: 12%; } .game-sparkle.s2 { top: 22%; right: 16%; animation-delay: .5s; } .game-sparkle.s3 { bottom: 12%; left: 22%; animation-delay: 1s; }

        .savings { background: #e8fdf5; }
        .savings-grid { display: grid; gap: 30px; align-items: center; }
        .goal-card { border-radius: 36px; background: #fff; padding: 24px; box-shadow: 0 24px 60px rgba(6,214,160,.14); }
        .progress-shell { height: 16px; border-radius: 999px; background: #eef1f4; overflow: hidden; margin: 20px 0 12px; }
        .progress-fill { width: 0; height: 16px; background: linear-gradient(90deg, var(--coral), var(--yellow)); border-radius: 999px; transition: width 1.2s cubic-bezier(.4,0,.2,1); }
        .milestones { display: grid; grid-template-columns: repeat(4,1fr); gap: 8px; margin-top: 18px; }
        .milestones span { border-radius: 16px; padding: 10px 6px; background: #f5f7fb; text-align: center; font-weight: 900; font-size: 12px; }
        .milestones span.unlocked { background: var(--yellow); color: var(--dark); }
        .check-list { display: grid; gap: 12px; margin-top: 22px; padding: 0; list-style: none; font-weight: 900; color: #3d3d52; }

        .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .stat-card { text-align: center; }
        .stat-number { font-family: var(--font-display); font-size: 38px; line-height: 1; color: var(--coral); font-weight: 700; }
        .stat-card p { color: #626278; font-weight: 900; }

        .how-it-works { background: #fff; position: relative; }
        .steps-grid, .testimonial-grid { display: grid; gap: 18px; margin-top: 34px; }
        .step-card, .testimonial-card, .faq-card { border: 2px solid var(--color-dark); border-radius: 26px; background: var(--color-text-white); box-shadow: 5px 5px 0 var(--color-dark); padding: 24px; }
        .step-number { display: inline-grid; place-items: center; width: 54px; height: 54px; border-radius: 18px; background: var(--color-accent-yellow); border: 2px solid var(--color-dark); font-family: var(--font-display); font-weight: 900; }
        .step-card:nth-child(2) { transform: rotate(1deg); background: #f2fff8; }
        .step-card:nth-child(3) { transform: rotate(-1deg); background: #fff3fa; }
        .testimonial-section { background: var(--color-accent-lavender); }
        .testimonial-card { background: #fff; }
        .testimonial-card blockquote { margin: 0; color: #414155; line-height: 1.7; }
        .testimonial-card strong { display: block; margin-top: 18px; font-family: var(--font-display); font-size: 18px; }
        .testimonial-card span { color: var(--color-text-muted); font-weight: 800; }
        .faq-section { background: var(--color-bg-primary); }
        .faq-list { display: grid; gap: 14px; margin: 34px auto 0; max-width: 860px; }
        .faq-card { padding: 0; overflow: hidden; }
        .faq-question { display: flex; align-items: center; justify-content: space-between; gap: 18px; width: 100%; padding: 20px 22px; border: 0; background: transparent; color: var(--color-dark); font-family: var(--font-display); font-size: 18px; font-weight: 900; text-align: left; cursor: pointer; }
        .faq-icon { flex: 0 0 auto; display: grid; place-items: center; width: 30px; height: 30px; border-radius: 999px; background: var(--color-accent-yellow); border: 2px solid var(--color-dark); box-shadow: 2px 2px 0 var(--color-dark); }
        .faq-answer { display: none; padding: 0 22px 20px; color: var(--color-text-muted); line-height: 1.75; }
        .faq-card.is-open .faq-answer { display: block; }
        .faq-card.is-open .faq-icon { background: var(--color-accent-green); }

        .final-cta { position: relative; overflow: hidden; background: var(--color-brand); color: var(--color-text-white); }
        .cta-grid { position: relative; z-index: 1; display: grid; gap: 28px; align-items: center; }
        .mascot { max-width: 280px; width: 80%; margin: 0 auto; filter: drop-shadow(0 26px 36px rgba(26,26,46,.2)); animation: float 3.2s ease-in-out infinite; }
        .cta-coin { position: absolute; width: 18px; height: 18px; border-radius: 50%; background: var(--yellow); animation: float 3s ease-in-out infinite; }
        .cta-coin.c1 { left: 12%; top: 18%; } .cta-coin.c2 { right: 13%; top: 28%; animation-delay: .7s; } .cta-coin.c3 { right: 24%; bottom: 16%; animation-delay: 1.2s; }

        .footer { background: var(--color-dark); color: #b9bac8; padding: 38px 20px; }
        .footer-grid { display: grid; gap: 24px; }
        .footer img { height: 42px; width: auto; }
        .footer a { color: #d8d9e5; text-decoration: none; font-weight: 800; }
        .footer-links, .socials { display: flex; flex-wrap: wrap; gap: 16px; }
        .socials span { display: grid; place-items: center; width: 40px; height: 40px; border-radius: 14px; background: rgba(255,255,255,.08); }

        .reveal { opacity: 0; transform: translateY(30px); transition: opacity .6s ease, transform .6s ease; }
        .reveal.revealed { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: .1s; } .reveal-delay-2 { transition-delay: .2s; } .reveal-delay-3 { transition-delay: .3s; }



        /* Major section overhaul: LESA-like bold world rhythm */
        section { margin-top: 0; border-top: none; }
        .section-pad { padding: 80px 20px; }
        .soft-shadow, .pain-card, .feature-card, .goal-card, .stat-card, .step-card, .testimonial-card, .faq-card { box-shadow: 5px 5px 0 var(--color-dark); }
        .reveal { opacity: 0; transform: translateY(50px); transition: opacity .6s ease, transform .6s ease; }

        .hero { min-height: 92vh; display: flex; align-items: center; padding: 94px 20px 0; overflow: hidden; }
        .hero h1 { font-size: clamp(52px, 6vw, 80px); line-height: 1.05; }
        .hero-splash { position: absolute; right: 0; top: 0; width: 55%; height: 100%; background: #7B3FBF; clip-path: ellipse(85% 100% at 100% 50%); z-index: 0; opacity: .08; }
        .hero-ground { position: absolute; bottom: -2px; left: 0; right: 0; height: 120px; background: #7B3FBF; border-radius: 60% 60% 0 0 / 40px 40px 0 0; z-index: 1; }
        .hero-grid { align-items: center; padding-bottom: 42px; }
        .hero-visual { min-height: 640px; z-index: 2; }
        .phone-wrap { width: min(420px, 88vw); margin-bottom: -80px; position: relative; z-index: 2; }
        @media (min-width: 1024px) { .phone-wrap { width: min(420px, 48vw); } }
        .phone-screen { min-height: 650px; }
        .deco-star-burst { width: 36px !important; }
        .deco-diamond { width: 24px !important; }
        .deco-swoosh { width: 44px !important; }
        .deco-coin { width: 48px !important; }
        .deco-star-1 { width: 32px !important; }

        .trust { min-height: 72px; display: flex; flex-direction: column; justify-content: center; background: #7B3FBF; border-block: 0; padding: 10px 0; }
        .trust-label { margin-bottom: 8px; color: rgba(255,255,255,.7); }
        .logo-box { min-width: auto; height: auto; padding: 8px 20px; border-radius: 999px; background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.3); font-weight: 600; backdrop-filter: blur(4px); }

        .problem { background: #FFDF49; color: var(--color-dark); }
        .problem .section-title, .problem .answer-text { color: var(--color-dark); }
        .problem .pain-card { border: 2px solid var(--color-dark); border-radius: 20px; background: #fff; box-shadow: 5px 5px 0 var(--color-dark); }
        .problem .pain-card .emoji { font-size: 56px; }
        .problem .pain-card h3 { font-family: var(--font-display); font-weight: 700; color: var(--color-dark); }
        .answer-text { font-weight: 800; font-size: 20px; color: var(--color-dark); }

        #fitur { background: var(--color-bg-primary); }
        #fitur .section-label { background: #00B4A6; color: var(--color-dark); border: 2px solid var(--color-dark); box-shadow: 3px 3px 0 var(--color-dark); border-radius: 999px; font-weight: 800; text-transform: none; }
        .feature-card { border: 2px solid var(--color-dark); border-radius: 20px; background: #fff; box-shadow: 4px 4px 0 var(--color-dark); }
        .feature-card:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0 var(--color-dark); }
        .feature-card .emoji { display: inline-flex; margin-bottom: 12px; padding: 12px; border-radius: 12px; background: #FFDF49; font-size: 48px; }

        .game { background: #010E26; }
        .game .section-label { background: #FD64B6; color: #fff; border: 2px solid #fff; box-shadow: 3px 3px 0 rgba(255,255,255,.3); }
        .game .section-title { color: #fff; font-size: clamp(36px, 4vw, 56px); }
        .game-card { background: rgba(255,255,255,.06); border: 1.5px solid rgba(255,255,255,.15); border-radius: 24px; box-shadow: none; }
        .level-badges span { background: #8DC63F; color: #010E26; border: 2px solid #7B3FBF; border-radius: 999px; font-family: var(--font-display); font-weight: 800; }
        .challenge { background: #7B3FBF; color: #fff; border-radius: 16px; font-weight: 700; }
        .reward-row span { background: #FFDF49; color: var(--color-dark); border-radius: 999px; font-weight: 800; }
        .game .btn-primary { background: #00B4A6; color: #fff; border: 2px solid #fff; box-shadow: 4px 4px 0 rgba(255,255,255,.2); font-size: 18px; padding: 16px 40px; }

        .savings { background: #00B4A6; color: var(--color-dark); }
        .savings .section-label { background: #fff; color: var(--color-dark); border: 2px solid var(--color-dark); box-shadow: 3px 3px 0 var(--color-dark); }
        .savings .section-title, .savings .section-copy, .savings .check-list { color: var(--color-dark) !important; }
        .savings .check-list { font-weight: 600; }
        .goal-card { border: 2px solid var(--color-dark); border-radius: 20px; background: #fff; box-shadow: 6px 6px 0 var(--color-dark); }
        .progress-fill { background: #8DC63F; }
        .milestones span { background: #fff; color: var(--color-dark); border: 2px solid var(--color-dark); border-radius: 999px; }
        .milestones span.unlocked { background: #7B3FBF; color: #fff; border: 2px solid var(--color-dark); }

        .stats-section { background: #7B3FBF; color: #fff; }
        .stats-section .section-title { color: #fff; font-weight: 900; }
        .stat-card { background: rgba(255,255,255,.12); border: 1.5px solid rgba(255,255,255,.2); border-radius: 20px; box-shadow: none; backdrop-filter: blur(8px); }
        .stat-number { color: #8DC63F; font-family: var(--font-display); font-size: 40px; font-weight: 900; }
        .stat-card p { color: rgba(255,255,255,.8); }

        .how-it-works { background: var(--color-bg-primary); }
        .how-it-works .section-label { background: #FD64B6; color: #fff; border: 2px solid var(--color-dark); box-shadow: 3px 3px 0 var(--color-dark); }
        .step-number { width: 44px; height: 44px; border-radius: 50%; background: #7B3FBF; color: #fff; border: 3px solid var(--color-dark); box-shadow: 3px 3px 0 var(--color-dark); font-family: var(--font-display); font-weight: 900; font-size: 18px; }
        .step-card { background: #fff; border: 2px solid var(--color-dark); border-radius: 20px; box-shadow: 5px 5px 0 var(--color-dark); transition: transform .15s ease, box-shadow .15s ease; }
        .step-card:hover { transform: translate(-2px, -2px); box-shadow: 7px 7px 0 var(--color-dark); }

        .testimonial-section { background: #EDE8F5; }
        .testimonial-card { background: #fff; border: 2px solid var(--color-dark); border-radius: 20px; box-shadow: 4px 4px 0 var(--color-dark); }
        .testimonial-card:nth-child(1) { transform: rotate(-1deg); }
        .testimonial-card:nth-child(2) { transform: scale(1.02); }
        .testimonial-card:nth-child(3) { transform: rotate(1deg); }
        .testimonial-card blockquote::before { content: '“'; display: block; color: #7B3FBF; font-size: 48px; font-family: Georgia, serif; line-height: .8; opacity: .3; }
        .testimonial-card strong { font-weight: 800; }
        .testimonial-card span { color: #7B3FBF; font-weight: 600; }

        .faq-section { background: #010E26; color: #fff; }
        .faq-section .section-label { background: #FFDF49; color: var(--color-dark); border: 2px solid rgba(255,255,255,.3); box-shadow: 3px 3px 0 rgba(255,255,255,.15); }
        .faq-section .section-title { color: #fff; font-weight: 900; }
        .faq-section .section-copy { color: rgba(255,255,255,.6); }
        .faq-card { background: rgba(255,255,255,.06); border: 1.5px solid rgba(255,255,255,.12); border-radius: 16px; box-shadow: none; margin-bottom: 12px; }
        .faq-question { color: #fff; font-weight: 700; }
        .faq-answer { color: rgba(255,255,255,.75); }
        .faq-icon { background: #7B3FBF; color: #fff; border: 0; width: 32px; height: 32px; box-shadow: none; }
        .faq-card.is-open .faq-icon { background: #7B3FBF; }

        .final-cta { background: #7B3FBF; color: #fff; }
        .final-cta .section-title { color: #fff; font-size: clamp(36px, 4vw, 52px); font-weight: 900; }
        .final-cta .section-copy { color: rgba(255,255,255,.85) !important; }
        .final-cta .btn-light { background: #00B4A6; color: #fff; border: 2px solid #fff; box-shadow: 4px 4px 0 rgba(255,255,255,.3); font-weight: 800; font-size: 18px; border-radius: 999px; padding: 16px 40px; }
        .mascot-placeholder { width: 180px; height: 180px; margin: 0 auto; background: rgba(255,255,255,.2); border-radius: 24px; display: flex; align-items: center; justify-content: center; font-size: 64px; animation: float 3.2s ease-in-out infinite; }

        .footer { background: #0F0F1E; color: rgba(255,255,255,.5); }
        .footer a { color: rgba(255,255,255,.7); }
        .footer a:hover { color: #fff; }
        .footer .socials span { background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2); transition: background .15s ease; }
        .footer .socials span:hover { background: rgba(255,255,255,.2); }
        .footer p { color: rgba(255,255,255,.4); }
        .footer p:first-of-type { color: rgba(255,255,255,.5); }

        @media (min-width: 640px) { .hero-actions { flex-direction: row; } }
        @media (min-width: 768px) { .pain-grid, .game-grid, .steps-grid, .testimonial-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } .feature-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .savings-grid, .cta-grid, .footer-grid { grid-template-columns: 1fr 1fr; } .stats-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
        @media (min-width: 1024px) { .section-pad { padding: 96px 24px; } .hero { padding: 86px 24px 110px; } .hero-grid { grid-template-columns: .95fr 1.05fr; } .nav-links { display: flex; } .hamburger { display: none; } .nav-cta { display: inline-flex !important; } .nav-logo img { height: 88px; } .nav-logo { min-width: 260px; } }


        /* Final visual fixes */
        .hero-section { min-height: 100vh; padding-bottom: 0 !important; overflow: hidden; position: relative; }
        .hero-content-wrap { display: grid; align-items: center; min-height: calc(100vh - 80px); gap: 0; }
        .phone-mockup-wrap { position: relative; z-index: 2; margin-bottom: -120px !important; padding-bottom: 0 !important; }
        .trust-section { margin-top: 0 !important; padding-top: 24px !important; }
        .hero-headline, .hero h1, section:first-of-type h1 { font-size: clamp(56px, 7vw, 88px) !important; line-height: 1 !important; letter-spacing: -.02em !important; }
        .underline-squiggle, [class*="squiggle"] { display: none !important; }
        .highlight-text { position: relative; display: inline-block; }
        .highlight-text::after { content: ''; position: absolute; bottom: -8px; left: 0; width: 100%; height: 6px; background: #7B3FBF; border-radius: 3px; transform: rotate(-1deg); }

        .partner-pill, .trust-item, .partner-name, .logo-box { background: rgba(255,255,255,.2) !important; color: #fff !important; border: 1.5px solid rgba(255,255,255,.4) !important; border-radius: 999px !important; padding: 8px 20px !important; font-weight: 700 !important; font-family: var(--font-display) !important; white-space: nowrap !important; }
        .trust-label, [class*="trust"] p, [class*="partner"] > p { color: rgba(255,255,255,.6) !important; font-size: 12px !important; letter-spacing: .1em !important; text-transform: uppercase !important; }

        .features-section { background-image: radial-gradient(circle, rgba(80,90,205,.06) 1px, transparent 1px) !important; background-size: 24px 24px !important; }
        .feature-card:nth-child(1) .feature-icon-wrap, .feature-card:nth-child(1) .icon-bg { background: #7B3FBF !important; color: #fff; }
        .feature-card:nth-child(2) .feature-icon-wrap, .feature-card:nth-child(2) .icon-bg { background: #00B4A6 !important; color: #fff; }
        .feature-card:nth-child(3) .feature-icon-wrap, .feature-card:nth-child(3) .icon-bg { background: #8DC63F !important; color: #010E26; }
        .feature-card:nth-child(4) .feature-icon-wrap, .feature-card:nth-child(4) .icon-bg { background: #FD64B6 !important; color: #fff; }

        .step-card, [class*="step-card"], .cara-kerja-card { background: #fff !important; border: 2px solid #010E26 !important; box-shadow: 5px 5px 0 #010E26 !important; border-radius: 20px !important; }
        .step-card:nth-child(1) .step-number, .step-card:nth-child(1) .step-num { background: #7B3FBF !important; color: #fff !important; border: 2px solid #010E26 !important; }
        .step-card:nth-child(2) .step-number, .step-card:nth-child(2) .step-num { background: #00B4A6 !important; color: #fff !important; border: 2px solid #010E26 !important; }
        .step-card:nth-child(3) .step-number, .step-card:nth-child(3) .step-num { background: #8DC63F !important; color: #010E26 !important; border: 2px solid #010E26 !important; }

        .testimonial-card:nth-child(1), .testi-card:first-child { transform: rotate(-2deg) !important; }
        .testimonial-card:nth-child(2), .testi-card:nth-child(2) { transform: scale(1.04) !important; position: relative; z-index: 2; box-shadow: 6px 6px 0 #010E26 !important; }
        .testimonial-card:nth-child(3), .testi-card:last-child { transform: rotate(1.5deg) !important; }
        .testimonial-card, .testi-card { transition: transform .2s ease !important; }
        .testimonial-card:hover, .testi-card:hover { transform: rotate(0deg) translateY(-4px) !important; }

        @media (min-width: 1024px) { .hero-content-wrap { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 767px) { .hero-content-wrap { min-height: auto; gap: 24px; } .phone-mockup-wrap { margin-bottom: -96px !important; } .nav-logo img { height: 64px; } .nav-logo { min-width: 184px; } .nav-inner { padding: 8px 16px; } }



        /* LESA-style button and typography polish */
        h1, h2, h3 { font-weight: 900; }
        .btn, button, a.btn { font-family: var(--font-display); font-weight: 800; }
        .badge, .pill, .label, .section-label { font-weight: 800; }
        nav a { font-weight: 700; }
        .btn-primary, .btn-coral, [class*="btn-download"], .cta-btn, .hero-cta-primary {
            background: #FDFBFD !important;
            color: #010E26 !important;
            border-radius: 12px !important;
            border: 2px solid #010E26 !important;
            box-shadow: 4px 4px 0 0 #010E26 !important;
            font-family: var(--font-display) !important;
            font-weight: 800 !important;
            font-size: 16px !important;
            padding: 14px 28px !important;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: transform .15s ease, box-shadow .15s ease;
            text-decoration: none;
        }
        .btn-primary:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0 0 #010E26 !important; }
        .btn-primary:active { transform: translate(2px, 2px); box-shadow: 2px 2px 0 0 #010E26 !important; }
        .btn-accent, .game .btn-primary {
            background: #7B3FBF !important;
            color: #fff !important;
            border-radius: 12px !important;
            border: 2px solid #fff !important;
            box-shadow: 4px 4px 0 0 rgba(255,255,255,.3) !important;
            font-weight: 800 !important;
            padding: 14px 28px !important;
        }
        .btn-accent:hover, .game .btn-primary:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0 0 rgba(255,255,255,.3) !important; }
        .btn-navbar, .nav-cta {
            background: #FDFBFD !important;
            color: #010E26 !important;
            border-radius: 12px !important;
            border: 2px solid #010E26 !important;
            box-shadow: 3px 3px 0 0 #010E26 !important;
            font-weight: 800 !important;
            font-size: 14px !important;
            padding: 10px 20px !important;
        }
        .final-cta .btn-light {
            background: #00B4A6 !important;
            color: #fff !important;
            border-radius: 12px !important;
            border: 2px solid #fff !important;
            box-shadow: 4px 4px 0 0 rgba(255,255,255,.3) !important;
        }



        .hero-scene { position: relative; width: 100%; height: 500px; overflow: hidden; }
        .scene-bg-card { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 320px; height: 220px; background: rgba(255,255,255,.15); border-radius: 24px; border: 2px solid rgba(255,255,255,.3); }
        .hero-scene .phone-mockup-wrap { position: absolute; top: 50%; left: 50%; transform: translate(-40%, -50%) rotate(-5deg); z-index: 2; width: min(330px, 72vw); margin-bottom: 0 !important; animation: none; }
        .hero-scene .phone-frame-brand { transform: none; }
        .hero-scene .badge-streak { position: absolute; top: 15%; left: 5%; background: #FFDF49; border: 2px solid #010E26; box-shadow: 3px 3px 0 #010E26; border-radius: 12px; padding: 8px 14px; font-weight: 800; font-size: 13px; color: #010E26; transform: rotate(-3deg); z-index: 3; }
        .hero-scene .badge-saving { position: absolute; bottom: 15%; left: 8%; background: #3EDB7C; border: 2px solid #010E26; box-shadow: 3px 3px 0 #010E26; border-radius: 12px; padding: 8px 14px; font-weight: 800; font-size: 13px; color: #010E26; transform: rotate(-1.5deg); z-index: 3; }
        .hero-scene .badge-trophy { position: absolute; bottom: 25%; right: 5%; background: #FDFBFD; border: 2px solid #010E26; box-shadow: 4px 4px 0 #010E26; border-radius: 12px; padding: 8px 14px; font-weight: 800; font-size: 13px; color: #010E26; transform: rotate(2deg); z-index: 3; }
        .scene-progress { position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 4; }
        .scene-progress span { width: 10px; height: 6px; background: rgba(255,255,255,.4); border-radius: 3px; }
        .scene-progress span:first-child { width: 32px; background: #fff; }
        @media (min-width: 1024px) { .hero-scene .phone-mockup-wrap { width: min(360px, 34vw); } }


        .game .btn-primary { background: #00B4A6 !important; color: #fff !important; border: 2px solid #fff !important; box-shadow: 4px 4px 0 0 rgba(255,255,255,.3) !important; }

        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; scroll-behavior: auto !important; transition-duration: .01ms !important; } }
    </style>
</head>
<body>
    <div class="page-shell">
        <div class="announcement" aria-label="Info fitur KeySaku">
            <div class="marquee-track" aria-hidden="true">
                @for($loopIndex = 0; $loopIndex < 2; $loopIndex++)
                    <div class="marquee-item">
                        <span class="mq-item marquee-light">Catat Transaksi</span><span class="mq-divider">✦</span>
                        <span class="mq-item marquee-bold">Kejar Target</span><span class="mq-divider">✦</span>
                        <span class="mq-item marquee-light">Hemat Mingguan</span><span class="mq-divider">✦</span>
                        <span class="mq-item marquee-bold">Naik Level</span><span class="mq-divider">✦</span>
                        <span class="mq-item marquee-light">Kumpulin Badge</span><span class="mq-divider">✦</span>
                        <span class="mq-item marquee-bold">Reward Nunggu</span><span class="mq-divider">✦</span>
                    </div>
                @endfor
            </div>
        </div>

        <header class="navbar">
            <nav class="container nav-inner" aria-label="Navigasi utama">
                <a href="#hero" class="nav-logo" aria-label="KeySaku landing page">
                    @if($brand['logo'])
                        <img src="{{ $brand['logo'] }}" alt="Logo KeySaku">
                    @else
                        <strong class="font-display">KeySaku</strong>
                    @endif
                </a>
                <div class="nav-links">
                    <a href="#fitur">Fitur</a>
                    <a href="#gamifikasi">Gamifikasi</a>
                    <a href="#target">Target</a>
                    <a href="#cara-kerja">Cara Kerja</a>
                    <a href="#faq">FAQ</a>
                    <a href="#download">Download</a>
                </div>
                <a href="{{ route('register') }}" class="btn btn-primary nav-cta" style="display:none">Mulai Gratis</a>
                <button class="hamburger" type="button" data-menu-toggle aria-label="Buka menu" aria-expanded="false"><span></span><span></span><span></span></button>
            </nav>
            <div class="mobile-menu" data-mobile-menu>
                <a href="#fitur">Fitur</a><a href="#gamifikasi">Gamifikasi</a><a href="#target">Target</a><a href="#cara-kerja">Cara Kerja</a><a href="#faq">FAQ</a><a href="#download">Download</a><a href="{{ route('register') }}">Mulai Gratis</a>
            </div>
        </header>

        <main>
            <section id="hero" class="hero hero-section">
                <div class="hero-bg-pattern" aria-hidden="true"></div>
                <div class="hero-splash" aria-hidden="true"></div>
                <div class="hero-ground" aria-hidden="true"></div>
                <svg class="deco-shape deco-star-burst float" style="top:8%; right:6%; width:22px;" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M10 0 L11.8 7.2 L18.5 3.8 L14.8 10 L18.5 16.2 L11.8 12.8 L10 20 L8.2 12.8 L1.5 16.2 L5.2 10 L1.5 3.8 L8.2 7.2 Z" fill="#8DC63F"/>
                </svg>
                <svg class="deco-shape deco-diamond float float-delay-1" style="top:15%; left:4%; width:14px;" viewBox="0 0 14 14" aria-hidden="true">
                    <rect x="2" y="2" width="10" height="10" rx="1" fill="#FFDF49" transform="rotate(45 7 7)"/>
                </svg>
                <svg class="deco-shape deco-swoosh float float-delay-2" style="top:55%; right:3%; width:28px;" viewBox="0 0 30 20" aria-hidden="true">
                    <path d="M28 2 C20 2 4 8 2 18" stroke="#FD64B6" stroke-width="4" stroke-linecap="round" fill="none"/>
                </svg>
                <svg class="deco-shape float float-delay-1" style="bottom:20%; left:2%; width:18px;" viewBox="0 0 18 10" aria-hidden="true">
                    <path d="M4 0 L18 0 L14 10 L0 10 Z" fill="#FD64B6"/>
                </svg>
                <svg class="deco-shape float float-delay-2" style="top:42%; left:38%; width:10px;" viewBox="0 0 10 10" aria-hidden="true">
                    <circle cx="5" cy="5" r="5" fill="#EA6DA4" opacity="0.7"/>
                </svg>
                <svg class="deco-shape" style="bottom:15%; left:32%; width:32px; transform:rotate(15deg);" viewBox="0 0 35 20" aria-hidden="true">
                    <path d="M2 18 C10 18 28 5 33 2" stroke="#00B4A6" stroke-width="3.5" stroke-linecap="round" fill="none"/>
                    <path d="M28 0 L33 2 L30 7" stroke="#00B4A6" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
                <div class="asset-placeholder mascot-hero" data-asset="assets/mascot/keysaku-mascot.svg" aria-hidden="true"></div>
                <div class="container hero-grid hero-content-wrap">
                    <div class="reveal">
                        <span class="section-label hero-badge">SAVING ADVENTURE</span>
                        <h1 class="font-display">Atur Duit,<br><span class="headline-wrap highlight-text">Naik Level 🚀<span class="asset-placeholder headline-squiggle" data-asset="assets/decorations/underline-squiggle.svg" aria-hidden="true"></span></span><svg class="deco-star-2" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 0 L11.8 7.2 L18.5 3.8 L14.8 10 L18.5 16.2 L11.8 12.8 L10 20 L8.2 12.8 L1.5 16.2 L5.2 10 L1.5 3.8 L8.2 7.2 Z" fill="#00B4A6"/></svg></h1>
                        <p>Catat pengeluaran, kejar target nabung, dan kumpulin reward—semua dalam satu app yang bikin keuanganmu makin seru.</p>
                        <div class="hero-actions"><a href="{{ route('register') }}" class="btn btn-primary">Download Sekarang</a><a href="#demo" class="btn btn-secondary">Lihat Demo</a></div>
                    </div>
                    <div class="hero-visual reveal reveal-delay-1" id="demo">
                        <div class="hero-scene">
                            <div class="scene-bg-card" aria-hidden="true"></div>
                            <svg class="deco-shape deco-coin float" style="right:20px; top:26px; width:40px;" viewBox="0 0 40 40" aria-hidden="true">
                                <circle cx="20" cy="20" r="16" fill="#FFDF49" stroke="#010E26" stroke-width="2"/>
                                <path d="M20 10v20M12 20h16" stroke="#010E26" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                            <svg class="deco-shape deco-star-1 float" style="left:14px; top:68px; width:24px;" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M10 0 L11.8 7.2 L18.5 3.8 L14.8 10 L18.5 16.2 L11.8 12.8 L10 20 L8.2 12.8 L1.5 16.2 L5.2 10 L1.5 3.8 L8.2 7.2 Z" fill="#FD64B6"/>
                            </svg>
                            <div class="badge-streak float float-delay-1">🔥 Streak 7 hari!</div>
                            <div class="badge-trophy float float-delay-2">🏆 Badge Hemat Pro</div>
                            <div class="badge-saving float">+Rp 50.000 ditabung</div>
                            <div class="phone-wrap phone-mockup-wrap"><div class="phone-frame-brand"><div class="phone-screen"><div class="phone-top"><strong>Halo, Saku Hero!</strong><div style="font-size:28px;font-weight:1000">Rp 2.450.000</div></div><div class="phone-card"><small>Challenge minggu ini</small><h3 style="margin:6px 0 0;font-size:24px">Nabung 5 hari beruntun</h3><div class="phone-progress"><span></span></div></div><div class="phone-mini-grid"><div class="phone-mini"><small>XP</small><strong>+240</strong></div><div class="phone-mini"><small>Level</small><strong>12</strong></div><div class="phone-mini"><small>Budget</small><strong>68%</strong></div><div class="phone-mini"><small>Reward</small><strong>3</strong></div></div><div class="phone-nav"><span>🏠</span><span>💳</span><span>💰</span><span>📊</span></div></div></div></div>
                            <div class="scene-progress" aria-hidden="true"><span></span><span></span><span></span></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="trust trust-section" aria-label="Partner dan trust strip">
                <p class="trust-label">Dipercaya Oleh</p>
                <div class="marquee-track" style="animation-duration:34s"><div class="marquee-item"><span class="logo-box">Partner A</span><span class="logo-box">Campus Hub</span><span class="logo-box">FinClub</span><span class="logo-box">YouthPay</span><span class="logo-box">SakuLab</span><span class="logo-box">MoneyUp</span></div><div class="marquee-item"><span class="logo-box">Partner A</span><span class="logo-box">Campus Hub</span><span class="logo-box">FinClub</span><span class="logo-box">YouthPay</span><span class="logo-box">SakuLab</span><span class="logo-box">MoneyUp</span></div></div>
            </section>

            <section class="problem section-pad">
                <div class="container center"><h2 class="section-title font-display reveal">Pernah Ngerasa Ini?</h2><div class="pain-grid"><article class="pain-card reveal"><div class="emoji">😱</div><h3>Gaji habis tapi nggak tahu ke mana</h3></article><article class="pain-card reveal reveal-delay-1"><div class="emoji">😓</div><h3>Niat nabung tapi selalu gagal di tengah jalan</h3></article><article class="pain-card reveal reveal-delay-2"><div class="emoji">🤯</div><h3>Budget berantakan setiap akhir bulan</h3></article></div><p class="answer-text reveal reveal-delay-3">KeySaku punya jawabannya 👇</p></div>
            </section>

            <section id="fitur" class="section-pad features-section">
                <div class="container center"><span class="section-label reveal">Fitur Utama</span><h2 class="section-title font-display reveal">Semua yang Kamu Butuhkan</h2><div class="feature-grid">@foreach($featureCards as $index => $feature)<article class="feature-card reveal reveal-delay-{{ min($index, 3) }}"><div class="emoji feature-icon-wrap">{{ $feature['icon'] }}</div><h3>{{ $feature['title'] }}</h3><p>{{ $feature['copy'] }}</p></article>@endforeach</div></div>
            </section>

            <section id="gamifikasi" class="game section-pad" data-section-reveal>
                <span class="game-sparkle s1"></span><span class="game-sparkle s2"></span><span class="game-sparkle s3"></span>
                <div class="container center"><span class="section-label reveal">🎮 Game Mode Aktif</span><h2 class="section-title font-display reveal">Keuanganmu = Adventure Barumu</h2><p class="section-copy reveal reveal-delay-1" style="color:rgba(255,255,255,.72)">Setiap kebiasaan finansial yang baik kasih kamu XP, naik level, dan unlock reward nyata.</p><div class="game-grid"><article class="game-card reveal"><h3>LEVEL SISTEM</h3><div class="level-bar"><div class="level-fill"></div></div><p>Level 1 → Level 12: dari Pemula ke Financial Master.</p><div class="level-badges"><span>Lv1</span><span>Lv6</span><span>Lv12</span></div></article><article class="game-card reveal reveal-delay-1"><h3>STREAK & CHALLENGE</h3><div class="streak-number">🔥 7</div><p>Catat 7 hari berturut-turut = bonus XP + badge eksklusif.</p><div class="challenge">Challenge: Nggak jajan impulsif 5 hari</div></article><article class="game-card reveal reveal-delay-2"><h3>REWARD NYATA</h3><div style="font-size:56px">🎁</div><p>Tukar XP dengan voucher, cashback, dan hadiah partner.</p><div class="reward-row"><span>10K</span><span>25K</span><span>50K</span></div></article></div><div style="margin-top:36px"><a href="{{ route('register') }}" class="btn btn-primary">Mulai Petualanganmu →</a></div></div>
            </section>

            <section id="target" class="savings section-pad savings-section">
                <div class="container savings-grid"><div class="goal-card reveal"><h3 style="font-size:28px;margin:0" class="font-display">🏍️ DP Motor Baru</h3><div class="progress-shell"><div class="progress-fill"></div></div><strong>Rp 3.400.000 / Rp 5.000.000</strong><p>47 hari lagi</p><div class="milestones"><span class="unlocked">25%</span><span class="unlocked">50%</span><span>75%</span><span>100%</span></div></div><div class="reveal reveal-delay-1"><span class="section-label">Target Nabung</span><h2 class="section-title font-display" style="margin-left:0">Set Target, Kejar Bareng</h2><p class="section-copy" style="margin-left:0">Buat target, pantau progres, dan rayakan tiap milestone.</p><ul class="check-list"><li>✅ Bisa bikin multiple target sekaligus</li><li>✅ Reminder otomatis tiap minggu</li><li>✅ Unlock badge di setiap milestone</li></ul></div></div>
            </section>

            <section class="section-pad stats-section">
                <div class="container center"><h2 class="section-title font-display reveal">Ribuan Anak Muda Sudah Naik Level</h2><div class="stats-grid">@foreach($stats as $stat)<article class="stat-card reveal"><div class="stat-number"><span class="counter" data-target="{{ $stat['target'] }}" data-prefix="{{ $stat['prefix'] ?? '' }}" data-suffix="{{ $stat['suffix'] ?? '' }}" data-decimal="{{ ! empty($stat['decimal']) ? 'true' : 'false' }}">0</span></div><p>{{ $stat['label'] }}</p></article>@endforeach</div></div>
            </section>

            <section id="cara-kerja" class="how-it-works section-pad">
                <div class="container center">
                    <span class="section-label reveal">Cara Kerja</span>
                    <h2 class="section-title font-display reveal">Mulai dari Catatan Kecil, Jadi Kebiasaan Besar</h2>
                    <p class="section-copy reveal reveal-delay-1">KeySaku dibuat seperti perjalanan finansial: kamu mulai dari langkah sederhana, lalu naik level lewat kebiasaan yang konsisten.</p>
                    <div class="steps-grid">
                        @foreach($steps as $index => $step)
                            <article class="step-card reveal reveal-delay-{{ min($index, 3) }}">
                                <span class="step-number step-num">{{ $step['number'] }}</span>
                                <h3>{{ $step['title'] }}</h3>
                                <p>{{ $step['copy'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="testimonial-section section-pad">
                <div class="container center">
                    <span class="section-label reveal">Cerita Pengguna</span>
                    <h2 class="section-title font-display reveal">Nabung Jadi Lebih Terasa Progress-nya</h2>
                    <div class="testimonial-grid">
                        @foreach($testimonials as $index => $testimonial)
                            <article class="testimonial-card reveal reveal-delay-{{ min($index, 2) }}">
                                <blockquote>“{{ $testimonial['quote'] }}”</blockquote>
                                <strong>{{ $testimonial['name'] }}</strong>
                                <span>{{ $testimonial['role'] }}</span>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="faq" class="faq-section section-pad">
                <div class="container center">
                    <span class="section-label reveal">Q&A</span>
                    <h2 class="section-title font-display reveal">Yang Sering Ditanyain</h2>
                    <p class="section-copy reveal reveal-delay-1">Jawaban singkat sebelum kamu mulai petualangan finansial bareng KeySaku.</p>
                    <div class="faq-list">
                        @foreach($faqs as $index => $faq)
                            <article class="faq-card reveal reveal-delay-{{ min($index % 4, 3) }} {{ $index === 0 ? 'is-open' : '' }}">
                                <button class="faq-question" type="button" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                                    <span>{{ $faq['question'] }}</span>
                                    <span class="faq-icon" aria-hidden="true">{{ $index === 0 ? '−' : '+' }}</span>
                                </button>
                                <div class="faq-answer">{{ $faq['answer'] }}</div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="download" class="final-cta section-pad">
                <span class="cta-coin c1"></span><span class="cta-coin c2"></span><span class="cta-coin c3"></span>
                <div class="container cta-grid"><div class="reveal"><div class="mascot-placeholder" aria-label="Placeholder mascot KeySaku" role="img">🐷</div></div><div class="reveal reveal-delay-1"><h2 class="section-title font-display" style="margin-left:0;color:#fff">Siap Naik Level Finansialmu?</h2><p class="section-copy" style="margin-left:0;color:rgba(255,255,255,.86)">Gratis selamanya untuk fitur dasar. Download sekarang dan mulai petualangan keuanganmu.</p><div style="margin-top:28px"><a href="{{ route('register') }}" class="btn btn-light">Download Gratis →</a></div></div></div>
            </section>
        </main>

        <footer class="footer">
            <div class="container footer-grid"><div>@if($brand['white'])<img src="{{ $brand['white'] }}" alt="Logo KeySaku putih">@else<strong class="font-display">KeySaku</strong>@endif<p style="font-weight:900;margin-top:14px">Atur Duit, Naik Level.</p></div><div><div class="footer-links"><a href="#">Tentang</a><a href="#fitur">Fitur</a><a href="#">Blog</a><a href="#">Kontak</a><a href="#">Kebijakan Privasi</a></div><div class="socials" style="margin-top:18px"><span>IG</span><span>TT</span><span>X</span></div><p style="margin-top:18px">© 2025 KeySaku. All rights reserved.</p></div></div>
        </footer>
    </div>

    <script>
        (() => {
            const menuToggle = document.querySelector('[data-menu-toggle]');
            const mobileMenu = document.querySelector('[data-mobile-menu]');
            if (menuToggle && mobileMenu) {
                menuToggle.addEventListener('click', () => {
                    const isOpen = mobileMenu.classList.toggle('is-open');
                    menuToggle.setAttribute('aria-expanded', String(isOpen));
                });
                mobileMenu.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
                    mobileMenu.classList.remove('is-open');
                    menuToggle.setAttribute('aria-expanded', 'false');
                }));
            }

            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });
            document.querySelectorAll('.reveal').forEach((el) => revealObserver.observe(el));

            const gameSection = document.querySelector('[data-section-reveal]');
            if (gameSection) {
                new IntersectionObserver((entries, observer) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('revealed-section');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.35 }).observe(gameSection);
            }

            const savingsSection = document.querySelector('.savings-section');
            const progressFill = document.querySelector('.progress-fill');
            if (savingsSection && progressFill) {
                new IntersectionObserver((entries, observer) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            progressFill.style.width = '68%';
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.5 }).observe(savingsSection);
            }

            const formatCounter = (value, decimal, prefix, suffix) => {
                const shown = decimal ? (value / 10).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) : Math.floor(value).toLocaleString('id-ID');
                return `${prefix}${shown}${suffix}`;
            };
            const animateCounter = (el) => {
                const target = Number(el.dataset.target || 0);
                const prefix = el.dataset.prefix || '';
                const suffix = el.dataset.suffix || '';
                const decimal = el.dataset.decimal === 'true';
                const duration = 1500;
                const start = performance.now();
                const step = (now) => {
                    const progress = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    el.textContent = formatCounter(target * eased, decimal, prefix, suffix);
                    if (progress < 1) requestAnimationFrame(step);
                };
                requestAnimationFrame(step);
            };
            const statsSection = document.querySelector('.stats-section');
            if (statsSection) {
                new IntersectionObserver((entries, observer) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.querySelectorAll('.counter').forEach(animateCounter);
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.35 }).observe(statsSection);
            }


            document.querySelectorAll('.faq-question').forEach((button) => {
                button.addEventListener('click', () => {
                    const card = button.closest('.faq-card');
                    const isOpen = card.classList.toggle('is-open');
                    button.setAttribute('aria-expanded', String(isOpen));
                    const icon = button.querySelector('.faq-icon');
                    if (icon) icon.textContent = isOpen ? '−' : '+';
                });
            });
        })();
    </script>
</body>
</html>
