<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>TeamCore - Uma nova gestão de Recursos Humanos</title>
        <meta name="description" content="TeamCore: solução intuitiva de RH para gerenciar funcionários, contratos, time tracking e férias">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

        <style>
            :root {
                --primary:   #582f0e;
                --secondary: #7f4f24;
                --info:      #936639;
                --danger:    #a68a64;
                --warning:   #b6ad90;
                --success:   #c2c5aa;
                --gray:      #acb79b;
                --muted:     #656d4a;
                --accent:    #414833;
                --neutral:   #333d29;
                --bg:        #f5f0e8;
                --bg-dark:   #1e1a14;
                --surface:   #ede5d8;
                --text:      #1e1a14;
                --text-soft: #5a5040;
            }

            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

            html { scroll-behavior: smooth; }

            body {
                background-color: var(--bg);
                color: var(--text);
                font-family: 'DM Sans', sans-serif;
                font-weight: 400;
                line-height: 1.6;
                overflow-x: hidden;
            }

            /* ── NOISE TEXTURE OVERLAY ── */
            body::before {
                content: '';
                position: fixed;
                inset: 0;
                background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
                background-repeat: repeat;
                background-size: 200px;
                pointer-events: none;
                z-index: 1000;
                opacity: 0.4;
            }

            /* ── NAV ── */
            nav {
                position: fixed;
                top: 0; left: 0; right: 0;
                z-index: 100;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 1.25rem 4rem;
                background: rgba(245, 240, 232, 0.88);
                backdrop-filter: blur(14px);
                border-bottom: 1px solid rgba(88, 47, 14, 0.12);
            }

            .nav-logo {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                text-decoration: none;
                color: var(--text);
            }

            .logo-mark {
                width: 36px;
                height: 36px;
                background: var(--primary);
                border-radius: 6px;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 3px;
                padding: 7px;
            }

            .logo-mark span {
                border-radius: 2px;
                display: block;
            }

            .logo-mark span:nth-child(1) { background: #c2c5aa; }
            .logo-mark span:nth-child(2) { background: #a68a64; }
            .logo-mark span:nth-child(3) { background: #b6ad90; }
            .logo-mark span:nth-child(4) { background: #936639; }

            .logo-name {
                font-family: 'Playfair Display', serif;
                font-weight: 700;
                font-size: 1.25rem;
                letter-spacing: -0.02em;
                color: var(--primary);
                display: flex;
                align-items: center;
                height: 36px;
            }

            .logo-name img {
                height: 36px;
                width: auto;
                max-width: 140px;
                object-fit: contain;
                display: block;
            }

            .nav-links {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .nav-link {
                text-decoration: none;
                color: var(--text-soft);
                font-size: 0.875rem;
                font-weight: 500;
                padding: 0.5rem 1rem;
                border-radius: 6px;
                transition: color 0.2s, background 0.2s;
                border: 1px solid transparent;
            }

            .nav-link:hover {
                color: var(--primary);
                background: rgba(88, 47, 14, 0.06);
            }

            .nav-cta {
                background: var(--primary);
                color: #f5f0e8 !important;
                border-color: var(--primary) !important;
                font-weight: 600;
                padding: 0.55rem 1.4rem;
                border-radius: 6px;
                text-decoration: none;
                font-size: 0.875rem;
                transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
                box-shadow: 0 2px 8px rgba(88, 47, 14, 0.25);
            }

            .nav-cta:hover {
                background: var(--secondary) !important;
                transform: translateY(-1px);
                box-shadow: 0 4px 16px rgba(88, 47, 14, 0.35) !important;
            }

            /* ── HERO ── */
            .hero {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 8rem 4rem 4rem;
                position: relative;
                overflow: hidden;
            }

            /* decorative circles */
            .hero::after {
                content: '';
                position: absolute;
                width: 700px;
                height: 700px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(88,47,14,0.08) 0%, transparent 70%);
                top: -150px;
                right: -150px;
                pointer-events: none;
                animation: breathe 6s ease-in-out infinite;
            }

            @keyframes breathe {
                0%, 100% { transform: scale(1); opacity: 0.8; }
                50% { transform: scale(1.08); opacity: 1; }
            }

            .hero-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.45rem 1.1rem;
                background: rgba(88, 47, 14, 0.08);
                border: 1px solid rgba(88, 47, 14, 0.2);
                border-radius: 999px;
                font-size: 0.8rem;
                font-weight: 600;
                color: var(--secondary);
                letter-spacing: 0.05em;
                text-transform: uppercase;
                margin-bottom: 2rem;
                animation: fadeUp 0.8s ease forwards;
            }

            .badge-dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: var(--info);
                animation: pulse-dot 2s ease-in-out infinite;
            }

            @keyframes pulse-dot {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.5; transform: scale(1.4); }
            }

            .hero-headline {
                font-family: 'Playfair Display', serif;
                font-size: clamp(3rem, 7vw, 6rem);
                font-weight: 900;
                line-height: 1.05;
                letter-spacing: -0.03em;
                text-align: center;
                color: var(--neutral);
                max-width: 900px;
                margin-bottom: 1.75rem;
                animation: fadeUp 0.8s 0.15s ease both;
            }

            .hero-headline em {
                font-style: normal;
                color: var(--primary);
                position: relative;
            }

            .hero-headline em::after {
                content: '';
                position: absolute;
                bottom: 4px;
                left: 0;
                right: 0;
                height: 3px;
                background: var(--info);
                border-radius: 2px;
                transform: scaleX(0);
                transform-origin: left;
                animation: underlineIn 0.6s 0.9s ease forwards;
            }

            @keyframes underlineIn {
                to { transform: scaleX(1); }
            }

            .hero-sub {
                font-size: 1.15rem;
                color: var(--text-soft);
                max-width: 560px;
                text-align: center;
                line-height: 1.75;
                margin-bottom: 2.75rem;
                font-weight: 400;
                animation: fadeUp 0.8s 0.3s ease both;
            }

            .hero-actions {
                display: flex;
                gap: 1rem;
                flex-wrap: wrap;
                justify-content: center;
                animation: fadeUp 0.8s 0.45s ease both;
            }

            @keyframes fadeUp {
                from { opacity: 0; transform: translateY(24px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .btn-primary {
                display: inline-flex;
                align-items: center;
                gap: 0.6rem;
                padding: 1rem 2.2rem;
                background: var(--primary);
                color: #f5f0e8;
                border-radius: 8px;
                font-weight: 600;
                font-size: 1rem;
                text-decoration: none;
                transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
                box-shadow: 0 4px 16px rgba(88, 47, 14, 0.3), inset 0 1px 0 rgba(255,255,255,0.15);
            }

            .btn-primary:hover {
                background: var(--secondary);
                transform: translateY(-2px);
                box-shadow: 0 8px 24px rgba(88, 47, 14, 0.35);
            }

            .btn-outline {
                display: inline-flex;
                align-items: center;
                gap: 0.6rem;
                padding: 1rem 2.2rem;
                background: transparent;
                color: var(--secondary);
                border: 2px solid var(--info);
                border-radius: 8px;
                font-weight: 600;
                font-size: 1rem;
                text-decoration: none;
                transition: background 0.2s, transform 0.15s, color 0.2s;
            }

            .btn-outline:hover {
                background: rgba(88, 47, 14, 0.07);
                transform: translateY(-2px);
                color: var(--primary);
            }

            /* ── STATS STRIP ── */
            .stats-strip {
                width: 100%;
                padding: 2.5rem 4rem;
                background: var(--neutral);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 4rem;
                flex-wrap: wrap;
            }

            .stat-item {
                text-align: center;
            }

            .stat-num {
                font-family: 'Playfair Display', serif;
                font-size: 2.25rem;
                font-weight: 700;
                color: var(--success);
                line-height: 1;
                margin-bottom: 0.25rem;
            }

            .stat-label {
                font-size: 0.8rem;
                color: var(--warning);
                letter-spacing: 0.08em;
                text-transform: uppercase;
                font-weight: 500;
            }

            .stat-sep {
                width: 1px;
                height: 40px;
                background: rgba(198, 197, 170, 0.2);
            }

            /* ── FEATURES ── */
            .features {
                padding: 8rem 4rem;
                max-width: 1200px;
                margin: 0 auto;
            }

            .section-label {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.75rem;
                font-weight: 700;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                color: var(--info);
                margin-bottom: 1rem;
            }

            .section-label::before {
                content: '';
                display: block;
                width: 24px;
                height: 2px;
                background: var(--info);
                border-radius: 2px;
            }

            .section-title {
                font-family: 'Playfair Display', serif;
                font-size: clamp(2rem, 4vw, 3rem);
                font-weight: 700;
                color: var(--neutral);
                margin-bottom: 1rem;
                letter-spacing: -0.02em;
                line-height: 1.15;
            }

            .section-sub {
                color: var(--text-soft);
                font-size: 1.05rem;
                max-width: 520px;
                line-height: 1.7;
                margin-bottom: 4rem;
            }

            .features-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
            }

            .feature-card {
                background: var(--surface);
                border: 1px solid rgba(88, 47, 14, 0.12);
                border-radius: 16px;
                padding: 2.25rem;
                transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s;
                position: relative;
                overflow: hidden;
                cursor: pointer;
            }

            .feature-card::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, rgba(88,47,14,0.04) 0%, transparent 60%);
                opacity: 0;
                transition: opacity 0.3s;
                pointer-events: none;
            }

            .feature-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 40px rgba(88, 47, 14, 0.12);
                border-color: rgba(88, 47, 14, 0.28);
            }

            .feature-card:hover::before { opacity: 1; }

            /* ── LARGE CARD ── */
            .feature-card.large {
                grid-row: span 2;
                background: var(--primary);
                color: #f5f0e8;
                border-color: transparent;
                display: flex;
                flex-direction: column;
            }

            .feature-card.large .feature-title  { color: #f5f0e8; }
            .feature-card.large .feature-desc   { color: rgba(245,240,232,0.72); }
            .feature-card.large .feature-number { color: rgba(245,240,232,0.38); }

            .feature-card.large .feature-icon-wrap {
                background: rgba(245, 240, 232, 0.15);
                color: var(--success);
            }

            .feature-card.large:hover {
                background: var(--secondary);
                box-shadow: 0 24px 48px rgba(88, 47, 14, 0.3);
            }

            /* ── ICON ── */
            .feature-icon-wrap {
                width: 52px;
                height: 52px;
                border-radius: 12px;
                background: rgba(88, 47, 14, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--primary);
                margin-bottom: 1.5rem;
                flex-shrink: 0;
                transition: background 0.2s;
            }

            .feature-card:hover .feature-icon-wrap { background: rgba(88,47,14,0.16); }

            .feature-number {
                font-size: 0.7rem;
                font-weight: 700;
                letter-spacing: 0.1em;
                color: var(--muted);
                margin-bottom: 0.5rem;
                display: block;
            }

            .feature-title {
                font-family: 'Playfair Display', serif;
                font-size: 1.3rem;
                font-weight: 700;
                color: var(--neutral);
                margin-bottom: 0.75rem;
                line-height: 1.25;
            }

            .feature-desc {
                font-size: 0.9rem;
                color: var(--text-soft);
                line-height: 1.65;
            }

            /* ── SAIBA MAIS BUTTON ── */
            .feature-toggle {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                margin-top: 1.5rem;
                font-size: 0.85rem;
                font-weight: 600;
                color: var(--info);
                background: none;
                border: none;
                padding: 0;
                cursor: pointer;
                transition: gap 0.2s, color 0.2s;
                font-family: 'DM Sans', sans-serif;
            }

            .feature-card.large .feature-toggle { color: var(--success); }
            .feature-toggle:hover { gap: 0.7rem; }

            .feature-toggle .toggle-icon {
                transition: transform 0.35s ease;
                flex-shrink: 0;
            }

            .feature-toggle.open .toggle-icon { transform: rotate(90deg); }

            /* ── DETAIL DRAWER ── */
            .feature-detail {
                overflow: hidden;
                max-height: 0;
                transition: max-height 0.45s cubic-bezier(0.4,0,0.2,1), opacity 0.35s ease, margin-top 0.35s ease;
                opacity: 0;
                margin-top: 0;
            }

            .feature-detail.open {
                max-height: 600px;
                opacity: 1;
                margin-top: 1.5rem;
            }

            .feature-detail-inner {
                border-top: 1px solid rgba(88,47,14,0.15);
                padding-top: 1.4rem;
            }

            .feature-card.large .feature-detail-inner {
                border-top-color: rgba(245,240,232,0.2);
            }

            /* pills */
            .detail-pills {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                margin-bottom: 1.1rem;
            }

            .detail-pill {
                font-size: 0.72rem;
                font-weight: 600;
                letter-spacing: 0.05em;
                padding: 0.3rem 0.75rem;
                border-radius: 999px;
                background: rgba(88,47,14,0.1);
                color: var(--secondary);
                border: 1px solid rgba(88,47,14,0.15);
            }

            .feature-card.large .detail-pill {
                background: rgba(245,240,232,0.12);
                color: var(--success);
                border-color: rgba(245,240,232,0.2);
            }

            /* bullet items */
            .detail-items {
                list-style: none;
                display: flex;
                flex-direction: column;
                gap: 0.65rem;
            }

            .detail-items li {
                display: flex;
                align-items: flex-start;
                gap: 0.6rem;
                font-size: 0.88rem;
                color: var(--text-soft);
                line-height: 1.55;
            }

            .feature-card.large .detail-items li { color: rgba(245,240,232,0.75); }

            .detail-items li::before {
                content: '';
                display: block;
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: var(--info);
                margin-top: 0.45rem;
                flex-shrink: 0;
            }

            .feature-card.large .detail-items li::before { background: var(--success); }

            /* tech stack strip inside detail */
            .detail-stack {
                display: flex;
                flex-wrap: wrap;
                gap: 0.45rem;
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(88,47,14,0.1);
            }

            .feature-card.large .detail-stack { border-top-color: rgba(245,240,232,0.12); }

            .stack-tag {
                font-size: 0.7rem;
                font-weight: 700;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                padding: 0.25rem 0.6rem;
                border-radius: 4px;
                background: rgba(88,47,14,0.07);
                color: var(--muted);
            }

            .feature-card.large .stack-tag {
                background: rgba(245,240,232,0.08);
                color: rgba(245,240,232,0.55);
            }

            /* ── HOW IT WORKS ── */
            .how {
                background: var(--neutral);
                padding: 8rem 4rem;
                position: relative;
                overflow: hidden;
            }

            .how::before {
                content: '';
                position: absolute;
                width: 500px;
                height: 500px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(198,197,170,0.06) 0%, transparent 70%);
                bottom: -100px;
                left: -100px;
                pointer-events: none;
            }

            .how-inner {
                max-width: 1100px;
                margin: 0 auto;
            }

            .how .section-label { color: var(--success); }
            .how .section-label::before { background: var(--success); }
            .how .section-title { color: var(--success); }
            .how .section-sub { color: var(--warning); }

            .steps {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 2rem;
                margin-top: 1rem;
            }

            .step {
                position: relative;
                padding: 2rem;
                border: 1px solid rgba(198, 197, 170, 0.15);
                border-radius: 14px;
                background: rgba(245, 240, 232, 0.04);
                transition: background 0.25s, border-color 0.25s, transform 0.25s;
            }

            .step:hover {
                background: rgba(245, 240, 232, 0.07);
                border-color: rgba(198, 197, 170, 0.3);
                transform: translateY(-3px);
            }

            .step-num {
                font-family: 'Playfair Display', serif;
                font-size: 3.5rem;
                font-weight: 900;
                color: rgba(198, 197, 170, 0.15);
                line-height: 1;
                margin-bottom: 1rem;
                display: block;
            }

            .step-title {
                font-family: 'Playfair Display', serif;
                font-size: 1.2rem;
                font-weight: 700;
                color: var(--success);
                margin-bottom: 0.75rem;
            }

            .step-desc {
                font-size: 0.9rem;
                color: var(--warning);
                line-height: 1.65;
            }

            /* ── CTA ── */
            .cta-section {
                padding: 8rem 4rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                position: relative;
                overflow: hidden;
            }

            .cta-section::before {
                content: '';
                position: absolute;
                width: 800px;
                height: 800px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(88,47,14,0.09) 0%, transparent 65%);
                top: 50%; left: 50%;
                transform: translate(-50%, -50%);
                pointer-events: none;
            }

            .cta-section .section-label { align-self: center; }

            .cta-title {
                font-family: 'Playfair Display', serif;
                font-size: clamp(2.25rem, 5vw, 4rem);
                font-weight: 900;
                color: var(--neutral);
                letter-spacing: -0.03em;
                line-height: 1.1;
                max-width: 700px;
                margin-bottom: 1.25rem;
            }

            .cta-sub {
                font-size: 1.05rem;
                color: var(--text-soft);
                max-width: 480px;
                margin-bottom: 2.75rem;
                line-height: 1.7;
            }

            .cta-actions {
                display: flex;
                gap: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }

            /* ── FOOTER ── */
            footer {
                background: var(--neutral);
                padding: 2.5rem 4rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 1.5rem;
            }

            .footer-brand {
                display: flex;
                align-items: center;
                gap: 0.6rem;
                text-decoration: none;
            }

            .footer-logo-mark {
                width: 28px;
                height: 28px;
                background: var(--secondary);
                border-radius: 5px;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 2px;
                padding: 5px;
            }

            .footer-logo-mark span {
                border-radius: 2px;
                display: block;
            }

            .footer-logo-mark span:nth-child(1) { background: #c2c5aa; }
            .footer-logo-mark span:nth-child(2) { background: #a68a64; }
            .footer-logo-mark span:nth-child(3) { background: #b6ad90; }
            .footer-logo-mark span:nth-child(4) { background: #936639; }

            .footer-name {
                font-family: 'Playfair Display', serif;
                font-weight: 700;
                color: var(--success);
                font-size: 1.05rem;
            }

            .footer-copy {
                font-size: 0.8rem;
                color: var(--muted);
            }

            /* ── SCROLL ANIMATIONS ── */
            .reveal {
                opacity: 0;
                transform: translateY(32px);
                transition: opacity 0.7s ease, transform 0.7s ease;
            }

            .reveal.visible {
                opacity: 1;
                transform: translateY(0);
            }

            .reveal-delay-1 { transition-delay: 0.1s; }
            .reveal-delay-2 { transition-delay: 0.2s; }
            .reveal-delay-3 { transition-delay: 0.3s; }

            /* ── RESPONSIVE ── */
            @media (max-width: 900px) {
                nav { padding: 1rem 1.5rem; }
                .hero { padding: 7rem 1.5rem 4rem; }
                .features { padding: 5rem 1.5rem; }
                .features-grid { grid-template-columns: 1fr; }
                .feature-card.large { grid-row: span 1; }
                .how { padding: 5rem 1.5rem; }
                .steps { grid-template-columns: 1fr; }
                .cta-section { padding: 5rem 1.5rem; }
                .stats-strip { gap: 2rem; padding: 2rem 1.5rem; }
                .stat-sep { display: none; }
                footer { padding: 2rem 1.5rem; flex-direction: column; align-items: flex-start; }
            }
        </style>
    </head>
    <body>

        <!-- ── NAV ── -->
        <nav>
            <a href="/" class="nav-logo">
                <div class="logo-mark">
                    <span></span><span></span><span></span><span></span>
                </div>
                <span class="logo-name"><img src="{{ asset('images/Teamcorelogo.svg') }}" alt="TeamCore Logo"></span>
            </a>
            <div class="nav-links">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="nav-link">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link">Entrar</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-cta">Começar Agora</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>

        <!-- ── HERO ── -->
        <section class="hero">
            <div class="hero-badge">
                <span class="badge-dot"></span>
                Nova Geração de RH
            </div>
            <h1 class="hero-headline">
                Gestão de pessoas<br>com <em>clareza</em> e propósito
            </h1>
            <p class="hero-sub">
                Centralize, simplifique e automatize os processos críticos de gestão de pessoal. TeamCore é a plataforma moderna para PMEs que buscam eficiência real.
            </p>
            <div class="hero-actions">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary">
                        Comece Gratuitamente
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                @endif
                <a href="#features" class="btn-outline">
                    Ver Funcionalidades
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
            </div>
        </section>

        <!-- ── STATS ── -->
        <div class="stats-strip">
            <div class="stat-item">
                <div class="stat-num">100%</div>
                <div class="stat-label">Intuitivo</div>
            </div>
            <div class="stat-sep"></div>
            <div class="stat-item">
                <div class="stat-num">4</div>
                <div class="stat-label">Módulos Core</div>
            </div>
            <div class="stat-sep"></div>
            <div class="stat-item">
                <div class="stat-num">∞</div>
                <div class="stat-label">Colaboradores</div>
            </div>
            <div class="stat-sep"></div>
            <div class="stat-item">
                <div class="stat-num">24/7</div>
                <div class="stat-label">Disponível</div>
            </div>
        </div>

        <!-- ── FEATURES ── -->
        <section class="features" id="features">
            <span class="section-label reveal">Funcionalidades</span>
            <h2 class="section-title reveal reveal-delay-1">Tudo que a sua equipe<br>precisa em um só lugar</h2>
            <p class="section-sub reveal reveal-delay-2">Módulos integrados projetados para eliminar fricção e aumentar a clareza na gestão de pessoas.</p>

            <div class="features-grid">

                <!-- ── CARD 01: Gestão de Funcionários (LARGE) ── -->
                <div class="feature-card large reveal">
                    <div class="feature-icon-wrap">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <span class="feature-number">01</span>
                    <h3 class="feature-title">Gestão de Funcionários</h3>
                    <p class="feature-desc">Centralize dados pessoais, contactos, cargos e departamentos de todos os colaboradores em um único sistema integrado. Histórico completo, sempre acessível.</p>

                    <button class="feature-toggle" onclick="toggleDetail(this)">
                        Saiba mais
                        <svg class="toggle-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>

                    <div class="feature-detail">
                        <div class="feature-detail-inner">
                            <div class="detail-pills">
                                <span class="detail-pill">CRUD Completo</span>
                                <span class="detail-pill">RBAC</span>
                                <span class="detail-pill">PT-PT</span>
                                <span class="detail-pill">Filament Resource</span>
                            </div>
                            <ul class="detail-items">
                                <li>Perfis detalhados com dados pessoais, foto, contactos e documentação associada</li>
                                <li>Estrutura organizacional com departamentos, cargos (Designations) e níveis hierárquicos</li>
                                <li>Criação automática de utilizador, contrato e banco de horas ao registar um novo funcionário (Observer Pattern)</li>
                                <li>Controlo de acesso por função: Administrador, RH e Funcionário isolam os seus próprios dados</li>
                                <li>Validação rigorosa de e-mail com Custom Rule (rejeita domínios inválidos como <code style="font-size:0.8em;opacity:0.75">teste@teste</code>)</li>
                                <li>Logs de auditoria via Spatie Activity Log para rastreabilidade completa</li>
                            </ul>
                            <div class="detail-stack">
                                <span class="stack-tag">Laravel 12</span>
                                <span class="stack-tag">Filament 3.3</span>
                                <span class="stack-tag">Eloquent ORM</span>
                                <span class="stack-tag">Spatie Roles</span>
                                <span class="stack-tag">MySQL 8</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── CARD 02: Contratos & Remuneração ── -->
                <div class="feature-card reveal reveal-delay-1">
                    <div class="feature-icon-wrap">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <span class="feature-number">02</span>
                    <h3 class="feature-title">Contratos & Remuneração</h3>
                    <p class="feature-desc">Gerencie tipos de contrato, salários por designação e acompanhe o status de todos os acordos laborais em tempo real.</p>

                    <button class="feature-toggle" onclick="toggleDetail(this)">
                        Saiba mais
                        <svg class="toggle-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>

                    <div class="feature-detail">
                        <div class="feature-detail-inner">
                            <div class="detail-pills">
                                <span class="detail-pill">Contrato automático</span>
                                <span class="detail-pill">Exportação PDF</span>
                                <span class="detail-pill">Status em tempo real</span>
                            </div>
                            <ul class="detail-items">
                                <li>Suporte a múltiplos tipos de contrato configuráveis (ContractType)</li>
                                <li>Salário base associado ao cargo, com benefícios e subsídios individuais</li>
                                <li>Estados visuais: Ativo, Suspenso e Encerrado com badges de cor</li>
                                <li>Exportação de contratos em PDF com localização PT-PT via DomPDF</li>
                                <li>Contrato criado automaticamente ao registar funcionário, com tipo e salário da designação</li>
                            </ul>
                            <div class="detail-stack">
                                <span class="stack-tag">DomPDF 3.1</span>
                                <span class="stack-tag">Filament</span>
                                <span class="stack-tag">Eloquent</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── CARD 03: Time Tracking & Banco de Horas ── -->
                <div class="feature-card reveal reveal-delay-2">
                    <div class="feature-icon-wrap">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <span class="feature-number">03</span>
                    <h3 class="feature-title">Time Tracking & Banco de Horas</h3>
                    <p class="feature-desc">Registre horas trabalhadas, calcule automaticamente extras e mantenha o banco de horas atualizado para cada colaborador.</p>

                    <button class="feature-toggle" onclick="toggleDetail(this)">
                        Saiba mais
                        <svg class="toggle-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>

                    <div class="feature-detail">
                        <div class="feature-detail-inner">
                            <div class="detail-pills">
                                <span class="detail-pill">Cálculo automático</span>
                                <span class="detail-pill">Horas extras</span>
                                <span class="detail-pill">Pausas</span>
                            </div>
                            <ul class="detail-items">
                                <li>Registos de presença diários com entrada, saída e intervalos (break_start / break_end)</li>
                                <li>Cálculo automático de horas líquidas com arredondamento para horas e meia-horas</li>
                                <li>Horas extras com multiplicadores diferenciados conforme política da empresa</li>
                                <li>Banco de Horas (Hourbank) por funcionário com saldo acumulado e deficitário</li>
                                <li>Banco de horas criado automaticamente ao registar funcionário, com data de accrual</li>
                                <li>Worklogs de atividades detalhados para rastreabilidade diária</li>
                            </ul>
                            <div class="detail-stack">
                                <span class="stack-tag">Attendance Model</span>
                                <span class="stack-tag">Hourbank Model</span>
                                <span class="stack-tag">Pest Tests</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── CARD 04: Timeoff & Férias ── -->
                <div class="feature-card reveal reveal-delay-1">
                    <div class="feature-icon-wrap">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <span class="feature-number">04</span>
                    <h3 class="feature-title">Timeoff & Férias</h3>
                    <p class="feature-desc">Solicitação e aprovação de licenças, férias e ausências com categorias customizáveis e visualização integrada no painel.</p>

                    <button class="feature-toggle" onclick="toggleDetail(this)">
                        Saiba mais
                        <svg class="toggle-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>

                    <div class="feature-detail">
                        <div class="feature-detail-inner">
                            <div class="detail-pills">
                                <span class="detail-pill">Aprovação por RH</span>
                                <span class="detail-pill">Categorias custom</span>
                                <span class="detail-pill">Painel integrado</span>
                            </div>
                            <ul class="detail-items">
                                <li>Pedidos de férias, licenças e ausências pelos próprios colaboradores</li>
                                <li>Fluxo de aprovação pelo departamento de RH com notificação contextual</li>
                                <li>Categorias de ausência totalmente customizáveis (TimeoffCategory)</li>
                                <li>Justificações de faltas com documentos de suporte</li>
                                <li>Visualização integrada no dashboard com estado de cada pedido</li>
                            </ul>
                            <div class="detail-stack">
                                <span class="stack-tag">Timeoff Model</span>
                                <span class="stack-tag">Filament Actions</span>
                                <span class="stack-tag">RBAC</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ── HOW IT WORKS ── -->
        <section class="how">
            <div class="how-inner">
                <span class="section-label reveal">Como funciona</span>
                <h2 class="section-title reveal reveal-delay-1">Simples de adoptar,<br>poderoso na prática</h2>
                <p class="section-sub reveal reveal-delay-2">Comece a usar em minutos. Sem curva de aprendizagem longa, sem configurações complexas.</p>

                <div class="steps">
                    <div class="step reveal">
                        <span class="step-num">01</span>
                        <h3 class="step-title">Contacte-nos</h3>
                        <p class="step-desc">Entre em contacto connosco para uma implementação personalizada e descubra como o TeamCore pode transformar a sua gestão de RH.</p>
                    </div>
                    <div class="step reveal reveal-delay-1">
                        <span class="step-num">02</span>
                        <h3 class="step-title">Configure os módulos</h3>
                        <p class="step-desc">Adapte os módulos do TeamCore às necessidades específicas da sua empresa.</p>
                    </div>
                    <div class="step reveal reveal-delay-2">
                        <span class="step-num">03</span>
                        <h3 class="step-title">Gestão simples e eficiente</h3>
                        <p class="step-desc">Dashboard unificado com visão completa da equipe. Decisões baseadas em dados, não em suposições.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── CTA ── -->
        <section class="cta-section">
            <span class="section-label reveal">Comece hoje</span>
            <h2 class="cta-title reveal reveal-delay-1">Pronto para transformar a sua gestão de RH?</h2>
            <p class="cta-sub reveal reveal-delay-2">Junte-se a organizações que estão a modernizar os seus processos com o TeamCore.</p>
            <div class="cta-actions reveal reveal-delay-3">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary">
                        Comece Gratuitamente
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                @endif
                <a href="#" class="btn-outline">Documentação</a>
            </div>
        </section>

        <!-- ── FOOTER ── -->
        <footer>
            <a href="/" class="footer-brand">
            </a>
            <p class="footer-copy">© {{ date('Y') }} TeamCore. Todos os direitos reservados.</p>
        </footer>

        <script>
            // Scroll reveal
            const reveals = document.querySelectorAll('.reveal');
            const obs = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('visible');
                        obs.unobserve(e.target);
                    }
                });
            }, { threshold: 0.12 });
            reveals.forEach(el => obs.observe(el));

            // Feature detail toggle
            function toggleDetail(btn) {
                const card   = btn.closest('.feature-card');
                const detail = btn.nextElementSibling;
                const isOpen = detail.classList.contains('open');

                // Close all other open drawers
                document.querySelectorAll('.feature-detail.open').forEach(d => {
                    d.classList.remove('open');
                    d.previousElementSibling.classList.remove('open');
                    d.previousElementSibling.textContent = '';
                    d.previousElementSibling.innerHTML = 'Saiba mais <svg class="toggle-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>';
                });

                if (!isOpen) {
                    detail.classList.add('open');
                    btn.classList.add('open');
                    btn.innerHTML = 'Fechar <svg class="toggle-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>';
                } else {
                    detail.classList.remove('open');
                    btn.classList.remove('open');
                    btn.innerHTML = 'Saiba mais <svg class="toggle-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>';
                }
            }
        </script>
    </body>
</html>