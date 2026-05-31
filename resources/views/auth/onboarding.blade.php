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
            --ks-dark: #010E26;
            --ks-white: #FDFBFD;
            --font-display: 'Nunito', ui-sans-serif, system-ui, sans-serif;
            --font-body: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        }

        * { box-sizing: border-box; }

        html,
        body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        body {
            background: var(--ks-white);
            color: var(--ks-dark);
            font-family: var(--font-body);
        }

        .auth-page {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            height: 100svh;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: #fff;
        }

        .auth-shell {
            display: flex;
            flex-direction: column;
            position: relative;
            width: 100vw;
            height: 100vh;
            height: 100svh;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: #fff;
        }

        .carousel-area {
            position: relative;
            flex: 0 0 72vh;
            flex-basis: 72svh;
            width: 100%;
            min-height: 0;
            margin: 0;
            padding: max(16px, env(safe-area-inset-top)) 18px 0;
            overflow: hidden;
            background: #fff;
        }

        .carousel-frame {
            position: relative;
            width: 100%;
            height: calc(100% - 42px);
            margin: 0;
            padding: 0;
            overflow: hidden;
            border-radius: 32px;
            background: #f4f0ff;
            box-shadow: 0 18px 48px rgba(1, 14, 38, .10);
        }

        .carousel-track {
            position: absolute;
            top: 0;
            left: 0;
            display: flex;
            width: 100%;
            height: 100%;
            transition: transform .55s cubic-bezier(.4, 0, .2, 1);
            will-change: transform;
        }

        .onboarding-slide {
            position: relative;
            flex: 0 0 100%;
            min-width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background: #000;
            overflow: hidden;
        }

        .carousel-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            object-position: center top;
        }

        .indicator-row {
            position: relative;
            z-index: 3;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
        }

        .indicator-dot {
            width: 10px;
            height: 9px;
            border: 0;
            border-radius: 999px;
            background: rgba(123, 63, 191, .22);
            cursor: pointer;
            transition: width .2s ease, background .2s ease, transform .2s ease;
        }

        .indicator-dot.is-active {
            width: 34px;
            background: #7B3FBF;
            transform: translateY(-1px);
        }

        .auth-actions {
            flex: 1 0 28vh;
            flex-basis: 28svh;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 14px;
            padding: 28px 24px max(24px, env(safe-area-inset-bottom));
            background: #fff;
            z-index: 4;
        }

        .create-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 420px;
            min-height: 56px;
            border: 0;
            border-radius: 20px;
            background: #7B3FBF;
            color: var(--ks-white);
            box-shadow: 0 16px 34px rgba(123, 63, 191, .28);
            font-family: var(--font-display);
            font-size: 17px;
            font-weight: 900;
            text-decoration: none;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .create-button:hover {
            background: #6A2FAE;
            transform: translateY(-2px);
            box-shadow: 0 20px 42px rgba(123, 63, 191, .34);
        }

        .create-button:active {
            transform: translateY(1px);
            box-shadow: 0 10px 24px rgba(123, 63, 191, .26);
        }

        .login-copy {
            margin: 0;
            text-align: center;
            color: rgba(1, 14, 38, .62);
            font-size: 14px;
            line-height: 1.5;
            font-weight: 700;
        }

        .login-copy a {
            color: #7B3FBF;
            font-weight: 900;
            text-decoration: none;
        }

        .login-copy a:hover { text-decoration: underline; }

    </style>
</head>
<body>
    <main class="auth-page" data-auth-onboarding>
        <section class="auth-shell" aria-label="Auth onboarding KeySaku">

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
                <a href="{{ route('register') }}" class="create-button">Buat Akun</a>
                <p class="login-copy">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
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
