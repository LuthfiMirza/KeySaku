@php
    $onboardingSlides = [
        [
            'key' => 'track',
            'title' => 'Track',
            'description' => 'Catat pemasukan dan pengeluaranmu dengan cara yang simpel, cepat, dan menyenangkan.',
            'image' => '/assets/auth/onboarding/illustration-track.png',
            'alt' => 'KeySaku onboarding track',
        ],
        [
            'key' => 'save',
            'title' => 'Save',
            'description' => 'Buat target tabungan, lihat progresmu naik, dan kumpulkan badge setiap kali kamu makin dekat.',
            'image' => '/assets/auth/onboarding/illustration-save.png',
            'alt' => 'KeySaku onboarding save',
        ],
        [
            'key' => 'trust',
            'title' => 'Trust',
            'description' => 'Kelola uangmu dengan aman, rapi, dan percaya diri setiap hari.',
            'image' => '/assets/auth/onboarding/illustration-trust.png',
            'alt' => 'KeySaku onboarding trust',
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KeySaku — Auth Onboarding</title>
    <meta name="description" content="Mulai petualangan finansial KeySaku: catat uang, nabung, naik level, dan kumpulkan badge.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --ks-purple: #7B3FBF;
            --ks-purple-dark: #6A2FAE;
            --ks-teal: #00B4A6;
            --ks-lime: #8DC63F;
            --ks-yellow: #FFDF49;
            --ks-pink: #FD64B6;
            --ks-bg: #FAF8FF;
            --ks-dark: #010E26;
            --ks-white: #FDFBFD;
            --font-display: 'Nunito', ui-sans-serif, system-ui, sans-serif;
            --font-body: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100%;
            background: var(--ks-bg);
            color: var(--ks-dark);
            font-family: var(--font-body);
            overflow-x: hidden;
        }

        .auth-page {
            position: relative;
            min-height: 100svh;
            display: flex;
            justify-content: center;
            background:
                radial-gradient(circle at 12% 12%, rgba(141, 198, 63, .18), transparent 26%),
                radial-gradient(circle at 88% 18%, rgba(253, 100, 182, .16), transparent 26%),
                radial-gradient(circle at 80% 90%, rgba(0, 180, 166, .16), transparent 28%),
                var(--ks-bg);
        }

        .auth-page::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image: radial-gradient(circle, rgba(1, 14, 38, .12) 1px, transparent 1px);
            background-size: 26px 26px;
            opacity: .35;
        }

        .auth-shell {
            position: relative;
            z-index: 1;
            width: min(100%, 430px);
            min-height: 100svh;
            display: flex;
            flex-direction: column;
            padding: max(10px, env(safe-area-inset-top)) 18px max(14px, env(safe-area-inset-bottom));
        }

        .auth-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 2px 8px;
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--ks-dark);
        }

        .brand-logo {
            display: block;
            width: min(230px, 64vw);
            height: auto;
        }

        .mini-sticker {
            border: 2px solid var(--ks-dark);
            border-radius: 12px;
            background: var(--ks-yellow);
            padding: 7px 10px;
            box-shadow: 3px 3px 0 var(--ks-dark);
            font-family: var(--font-display);
            font-size: 12px;
            font-weight: 900;
            transform: rotate(2deg);
        }

        .carousel-area {
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 8px 0 16px;
        }

        .carousel-frame {
            position: relative;
            min-height: min(64svh, 610px);
            border: 2px solid var(--ks-dark);
            border-radius: 32px;
            background: var(--ks-purple);
            box-shadow: 6px 6px 0 var(--ks-dark);
            overflow: hidden;
        }

        .carousel-track {
            display: flex;
            height: 100%;
            min-height: min(64svh, 610px);
            transition: transform .55s cubic-bezier(.4, 0, .2, 1);
            will-change: transform;
        }

        .onboarding-slide {
            position: relative;
            flex: 0 0 100%;
            min-width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 24px 22px 28px;
            isolation: isolate;
        }

        .onboarding-slide[data-tone="purple"] { background: var(--ks-purple); color: var(--ks-white); }
        .onboarding-slide[data-tone="teal"] { background: var(--ks-teal); color: var(--ks-dark); }
        .onboarding-slide[data-tone="lime"] { background: var(--ks-lime); color: var(--ks-dark); }

        .slide-pattern {
            position: absolute;
            inset: 0;
            z-index: -2;
            background-image: radial-gradient(circle, rgba(255, 255, 255, .26) 1.5px, transparent 1.5px);
            background-size: 28px 28px;
            opacity: .7;
        }

        .slide-shape {
            position: absolute;
            z-index: -1;
            border: 2px solid rgba(1, 14, 38, .35);
            transform: rotate(-8deg);
        }

        .shape-one {
            top: 18px;
            right: -34px;
            width: 130px;
            height: 130px;
            border-radius: 32px;
            background: rgba(255, 223, 73, .86);
        }

        .shape-two {
            bottom: 140px;
            left: -30px;
            width: 84px;
            height: 84px;
            border-radius: 999px;
            background: rgba(253, 100, 182, .82);
            transform: rotate(0deg);
        }

        .illustration-stage {
            position: relative;
            min-height: 300px;
            display: grid;
            place-items: center;
            margin-top: 8px;
        }

        .illustration-card {
            position: relative;
            width: min(260px, 72vw);
            height: min(260px, 72vw);
            border: 2px solid var(--ks-dark);
            border-radius: 32px;
            background: var(--ks-white);
            box-shadow: 6px 6px 0 var(--ks-dark);
            transform: rotate(-2deg);
        }

        .illustration-card::before {
            content: attr(data-asset);
            position: absolute;
            left: 16px;
            right: 16px;
            bottom: 14px;
            color: rgba(1, 14, 38, .42);
            font-size: 10px;
            font-weight: 800;
            line-height: 1.3;
            text-align: center;
        }

        .main-object {
            position: absolute;
            inset: 46px 44px auto;
            height: 112px;
            border: 2px solid var(--ks-dark);
            border-radius: 24px;
            background: var(--ks-purple);
            box-shadow: 4px 4px 0 var(--ks-dark);
            display: grid;
            place-items: center;
            font-size: 56px;
        }

        .onboarding-slide[data-tone="teal"] .main-object { background: var(--ks-yellow); }
        .onboarding-slide[data-tone="lime"] .main-object { background: var(--ks-teal); }

        .floating-icon {
            position: absolute;
            display: grid;
            place-items: center;
            width: 58px;
            height: 72px;
            border: 2px solid var(--ks-dark);
            border-radius: 18px;
            background: var(--ks-yellow);
            box-shadow: 3px 3px 0 var(--ks-dark);
            font-size: 26px;
            animation: float 3s ease-in-out infinite;
        }

        .floating-icon.one { top: 24px; left: 10px; transform: rotate(-8deg); }
        .floating-icon.two { right: 8px; top: 84px; background: var(--ks-pink); transform: rotate(7deg); animation-delay: .55s; }
        .floating-icon.three { left: 34px; bottom: 42px; background: var(--ks-lime); transform: rotate(4deg); animation-delay: 1.1s; }

        .progress-visual {
            position: absolute;
            right: 20px;
            bottom: 56px;
            width: 108px;
            border: 2px solid var(--ks-dark);
            border-radius: 14px;
            background: var(--ks-white);
            box-shadow: 3px 3px 0 var(--ks-dark);
            padding: 10px;
            transform: rotate(3deg);
        }

        .progress-visual span {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--ks-purple), var(--ks-teal), var(--ks-lime));
        }

        .slide-copy {
            position: relative;
            z-index: 2;
            padding-top: 18px;
        }

        .slide-copy h1 {
            margin: 0;
            font-family: var(--font-display);
            font-size: clamp(48px, 16vw, 76px);
            line-height: .92;
            font-weight: 900;
            letter-spacing: -.06em;
        }

        .slide-copy p {
            margin: 14px 0 0;
            max-width: 320px;
            font-size: 15.5px;
            line-height: 1.58;
            font-weight: 700;
        }

        .onboarding-slide[data-tone="purple"] .slide-copy p { color: rgba(253, 251, 253, .86); }
        .onboarding-slide[data-tone="teal"] .slide-copy p,
        .onboarding-slide[data-tone="lime"] .slide-copy p { color: rgba(1, 14, 38, .78); }

        .indicator-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 0 0;
        }

        .indicator-dot {
            width: 10px;
            height: 9px;
            border: 0;
            border-radius: 999px;
            background: #D8D0EA;
            cursor: pointer;
            transition: width .2s ease, background .2s ease, transform .2s ease;
        }

        .indicator-dot.is-active {
            width: 34px;
            background: var(--ks-purple);
            transform: translateY(-1px);
        }

        .auth-actions {
            flex: 0 0 auto;
            padding-top: 12px;
        }

        .create-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 56px;
            border: 2px solid var(--ks-dark);
            border-radius: 12px;
            background: var(--ks-purple);
            color: var(--ks-white);
            box-shadow: 4px 4px 0 var(--ks-dark);
            font-family: var(--font-display);
            font-size: 17px;
            font-weight: 900;
            text-decoration: none;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .create-button:hover {
            background: var(--ks-purple-dark);
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0 var(--ks-dark);
        }

        .create-button:active {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0 var(--ks-dark);
        }

        .login-copy {
            margin: 14px 0 0;
            text-align: center;
            color: rgba(1, 14, 38, .64);
            font-size: 14px;
            font-weight: 700;
        }

        .login-copy a {
            color: var(--ks-purple);
            font-weight: 900;
            text-decoration: none;
        }

        .login-copy a:hover { text-decoration: underline; }

        @keyframes float {
            0%, 100% { translate: 0 0; }
            50% { translate: 0 -10px; }
        }



        /* Poster-based carousel: final onboarding artwork fills the frame */
        .carousel-frame {
            position: relative;
            width: 100%;
            height: min(64vh, 590px);
            min-height: 500px;
            overflow: hidden;
            border-radius: 32px;
            border: 3px solid #010E26;
            background: #FAF8FF;
            box-shadow: 4px 4px 0 #010E26;
        }

        .carousel-track {
            display: flex;
            width: 100%;
            height: 100%;
            min-height: 0;
            transition: transform .55s cubic-bezier(.4, 0, .2, 1);
            will-change: transform;
        }

        .onboarding-slide {
            flex: 0 0 100%;
            min-width: 100%;
            height: 100%;
            padding: 0;
            display: block;
            background: #FAF8FF;
        }

        .carousel-image {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            object-position: center top;
        }

        @media (max-height: 760px) {
            .carousel-frame {
                height: 60vh;
                min-height: 440px;
            }
        }

        @media (max-width: 380px) {
            .carousel-frame {
                border-radius: 28px;
                min-height: 420px;
            }
        }

        @media (max-height: 720px) {
            .auth-shell { padding-top: 10px; padding-bottom: 12px; }
            .auth-brand { padding-bottom: 6px; }
            .brand-logo { width: min(200px, 60vw); height: auto; }
            .carousel-frame { min-height: 420px; }
            .carousel-track { min-height: 0; }
        }
    </style>
</head>
<body>
    <main class="auth-page" data-auth-onboarding>
        <section class="auth-shell" aria-label="Auth onboarding KeySaku">
            <header class="auth-brand">
                <a href="{{ route('landing.playful') }}" class="brand-mark" aria-label="KeySaku landing page">
                    <img src="{{ asset('assets/brand/logo-keysaku-horizontal.svg') }}" alt="Logo KeySaku" class="brand-logo">
                </a>
            </header>

            <div class="carousel-area">
                <div class="carousel-frame" aria-roledescription="carousel" aria-label="Pengenalan fitur KeySaku">
                    <div class="carousel-track" data-carousel-track>
                        @foreach($onboardingSlides as $index => $slide)
                            <article
                                class="onboarding-slide"
                                aria-label="Slide {{ $index + 1 }}: {{ $slide['title'] }}"
                                aria-hidden="{{ $index === 0 ? 'false' : 'true' }}"
                            >
                                <img
                                    src="{{ $slide['image'] }}"
                                    alt="{{ $slide['alt'] }}"
                                    class="carousel-image"
                                    loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                >
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="indicator-row" role="tablist" aria-label="Pilih slide onboarding">
                    @foreach($onboardingSlides as $index => $slide)
                        <button
                            type="button"
                            class="indicator-dot {{ $index === 0 ? 'is-active' : '' }}"
                            data-slide-index="{{ $index }}"
                            aria-label="Tampilkan slide {{ $slide['title'] }}"
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                        ></button>
                    @endforeach
                </div>
            </div>

            <footer class="auth-actions">
                <a href="{{ route('register') }}" class="create-button">Create Account</a>
                <p class="login-copy">Already have an account? <a href="{{ route('login') }}">Log In</a></p>
            </footer>
        </section>
    </main>

    <script>
        (() => {
            const root = document.querySelector('[data-auth-onboarding]');
            if (!root) return;

            const track = root.querySelector('[data-carousel-track]');
            const slides = Array.from(root.querySelectorAll('.onboarding-slide'));
            const dots = Array.from(root.querySelectorAll('[data-slide-index]'));
            const slideCount = slides.length;
            let activeSlide = 0;
            let timer = null;

            const render = () => {
                track.style.transform = `translateX(-${activeSlide * 100}%)`;
                slides.forEach((slide, index) => {
                    slide.setAttribute('aria-hidden', index === activeSlide ? 'false' : 'true');
                });
                dots.forEach((dot, index) => {
                    const isActive = index === activeSlide;
                    dot.classList.toggle('is-active', isActive);
                    dot.setAttribute('aria-selected', String(isActive));
                });
            };

            const start = () => {
                stop();
                timer = window.setInterval(() => {
                    activeSlide = (activeSlide + 1) % slideCount;
                    render();
                }, 5000);
            };

            const stop = () => {
                if (timer) {
                    window.clearInterval(timer);
                    timer = null;
                }
            };

            dots.forEach((dot) => {
                dot.addEventListener('click', () => {
                    activeSlide = Number(dot.dataset.slideIndex || 0);
                    render();
                    start();
                });
            });

            root.addEventListener('mouseenter', stop);
            root.addEventListener('mouseleave', start);
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) stop();
                else start();
            });
            window.addEventListener('beforeunload', stop);

            render();
            start();
        })();
    </script>
</body>
</html>
