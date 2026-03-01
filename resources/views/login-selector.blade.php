<!DOCTYPE html>
<html lang="pt-PT" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{ asset('images/Teamcorelogo.svg') }}" type="image/x-icon">
        <title>TeamCore - Login</title>
        <meta name="description" content="Aceda como Administrador, RH ou Colaborador">
        
        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#3b82f6">
        <meta name="application-name" content="TeamCore">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="TeamCore">
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <link rel="apple-touch-icon" href="{{ asset('pwa-icons/icon-192x192.png') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

        <style>
            :root,
            [data-theme="light"] {
                --primary:        #582f0e;
                --secondary:      #7f4f24;
                --info:           #936639;
                --danger:         #a68a64;
                --warning:        #b6ad90;
                --success:        #c2c5aa;
                --gray:           #acb79b;
                --muted:          #656d4a;
                --accent:         #414833;
                --neutral:        #333d29;
                --bg:             #f5f0e8;
                --bg-dark:        #1e1a14;
                --surface:        #ede5d8;
                --text:           #1e1a14;
                --text-soft:      #5a5040;
                --card-border:    rgba(88, 47, 14, 0.12);
                --card-hover-border: rgba(88, 47, 14, 0.28);
                --card-hover-shadow: rgba(88, 47, 14, 0.12);
                --icon-bg:        rgba(88, 47, 14, 0.10);
                --toggle-bg:      rgba(88, 47, 14, 0.08);
                --toggle-border:  rgba(88, 47, 14, 0.18);
                --toggle-icon:    #582f0e;
            }

            [data-theme="dark"] {
                --primary:        #c2a47a;
                --secondary:      #a68a64;
                --info:           #8a7355;
                --danger:         #6b5740;
                --warning:        #7a7660;
                --success:        #8a8d78;
                --gray:           #6b7560;
                --muted:          #9aa088;
                --accent:         #b0b898;
                --neutral:        #e8e2d8;
                --bg:             #141210;
                --bg-dark:        #0c0a08;
                --surface:        #1e1a14;
                --text:           #e8e2d8;
                --text-soft:      #a09880;
                --card-border:    rgba(194, 164, 122, 0.10);
                --card-hover-border: rgba(194, 164, 122, 0.30);
                --card-hover-shadow: rgba(0, 0, 0, 0.35);
                --icon-bg:        rgba(194, 164, 122, 0.10);
                --toggle-bg:      rgba(194, 164, 122, 0.08);
                --toggle-border:  rgba(194, 164, 122, 0.18);
                --toggle-icon:    #c2a47a;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                background-color: var(--bg);
                color: var(--text);
                font-family: 'DM Sans', sans-serif;
                font-weight: 400;
                line-height: 1.6;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                transition: background-color 0.4s ease, color 0.4s ease;
            }

            /* Navigation */
            nav {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 1.5rem 2rem;
                background: var(--surface);
                border-bottom: 1px solid var(--card-border);
                transition: background 0.4s ease;
            }

            .nav-logo {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                text-decoration: none;
                color: var(--text);
            }

            .nav-logo-mark {
                width: 36px;
                height: 36px;
                background: var(--primary);
                border-radius: 6px;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 3px;
                padding: 7px;
            }

            .nav-logo-mark span {
                border-radius: 2px;
                display: block;
            }

            .nav-logo-mark span:nth-child(1) { background: #c2c5aa; }
            .nav-logo-mark span:nth-child(2) { background: #a68a64; }
            .nav-logo-mark span:nth-child(3) { background: #b6ad90; }
            .nav-logo-mark span:nth-child(4) { background: #936639; }

            .nav-logo-name {
                display: flex;
                align-items: center;
                height: 36px;
            }

            .nav-logo-name img {
                height: 36px;
                width: auto;
                max-width: 140px;
                object-fit: contain;
            }

            .theme-toggle {
                width: 40px;
                height: 40px;
                border-radius: 8px;
                border: 1px solid var(--toggle-border);
                background: var(--toggle-bg);
                color: var(--toggle-icon);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background 0.2s, border-color 0.2s;
            }

            .theme-toggle:hover {
                background: rgba(88, 47, 14, 0.08);
            }

            /* Main Container */
            main {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 3rem 2rem;
            }

            .container {
                max-width: 900px;
                width: 100%;
            }

            .header {
                text-align: center;
                margin-bottom: 3.5rem;
            }

            .header h1 {
                font-family: 'Playfair Display', serif;
                font-size: clamp(2rem, 5vw, 3.5rem);
                font-weight: 900;
                color: var(--neutral);
                letter-spacing: -0.02em;
                margin-bottom: 0.75rem;
            }

            .header p {
                font-size: 1.05rem;
                color: var(--text-soft);
                max-width: 500px;
                margin: 0 auto;
            }

            /* Cards Grid */
            .cards-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 2rem;
                margin-bottom: 3rem;
            }

            .login-card {
                background: var(--surface);
                border: 1px solid rgba(88, 47, 14, 0.1);
                border-radius: 16px;
                padding: 2.5rem 2rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s;
                text-decoration: none;
                color: var(--text);
                cursor: pointer;
            }

            .login-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 20px 40px rgba(88, 47, 14, 0.12);
                border-color: rgba(88, 47, 14, 0.2);
            }

            .card-icon {
                width: 64px;
                height: 64px;
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 1.5rem;
                font-size: 2rem;
            }

            .card-icon.admin {
                background: rgba(88, 47, 14, 0.1);
                color: var(--primary);
            }

            .card-icon.hr {
                background: rgba(147, 102, 57, 0.1);
                color: var(--info);
            }

            .card-icon.employee {
                background: rgba(182, 173, 144, 0.1);
                color: var(--warning);
            }

            .card-title {
                font-family: 'Playfair Display', serif;
                font-size: 1.5rem;
                font-weight: 700;
                color: var(--neutral);
                margin-bottom: 0.5rem;
            }

            .card-desc {
                font-size: 0.9rem;
                color: var(--text-soft);
                line-height: 1.6;
                margin-bottom: 1.75rem;
            }

            .card-btn {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.75rem 1.75rem;
                border-radius: 8px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.2s;
                border: none;
                cursor: pointer;
                font-size: 0.95rem;
            }

            .card-btn.admin {
                background: var(--primary);
                color: #f5f0e8;
            }

            .card-btn.admin:hover {
                background: var(--secondary);
                transform: translateY(-2px);
            }

            .card-btn.hr {
                background: var(--info);
                color: #f5f0e8;
            }

            .card-btn.hr:hover {
                background: var(--secondary);
                transform: translateY(-2px);
            }

            .card-btn.employee {
                background: var(--warning);
                color: var(--neutral);
            }

            .card-btn.employee:hover {
                background: var(--info);
                transform: translateY(-2px);
            }

            /* Footer */
            footer {
                padding: 2rem;
                text-align: center;
                border-top: 1px solid var(--card-border);
                background: var(--surface);
            }

            .footer-copy {
                font-size: 0.8rem;
                color: var(--muted);
            }

            /* Responsive */
            @media (max-width: 768px) {
                nav {
                    padding: 1rem 1.25rem;
                }

                main {
                    padding: 2rem 1.25rem;
                }

                .header h1 {
                    font-size: clamp(1.5rem, 6vw, 2.5rem);
                }

                .header p {
                    font-size: 0.95rem;
                }

                .cards-grid {
                    gap: 1.5rem;
                    margin-bottom: 2rem;
                }

                .login-card {
                    padding: 2rem 1.5rem;
                }

                .card-icon {
                    width: 56px;
                    height: 56px;
                    font-size: 1.75rem;
                }
            }
        </style>
    </head>
    <body>
        <nav>
            <a href="/" class="nav-logo">
                <div class="nav-logo-mark">
                    <span></span><span></span><span></span><span></span>
                </div>
                <span class="nav-logo-name"><img src="{{ asset('images/Teamcorelogo.svg') }}" alt="TeamCore Logo"></span>
            </a>
            <button class="theme-toggle" id="themeToggle" aria-label="Alternar modo escuro">
                <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
            </button>
        </nav>

        <main>
            <div class="container">
                <div class="header">
                    <h1>Bem-vindo ao TeamCore</h1>
                    <p>Selecione o seu tipo de acesso para continuar</p>
                </div>

                <div class="cards-grid">
                    <!-- Admin Card -->
                    <a href="/admin/login" class="login-card">
                        <div class="card-icon admin">👨‍💼</div>
                        <h2 class="card-title">Administrador</h2>
                        <p class="card-desc">Acesso completo ao sistema e gestão geral</p>
                        <button type="button" class="card-btn admin">Entrar como Admin →</button>
                    </a>

                    <!-- HR Card -->
                    <a href="/hr/login" class="login-card">
                        <div class="card-icon hr">👥</div>
                        <h2 class="card-title">RH</h2>
                        <p class="card-desc">Gestão de colaboradores e aprovações</p>
                        <button type="button" class="card-btn hr">Entrar como RH →</button>
                    </a>

                    <!-- Employee Card -->
                    <a href="/employee/login" class="login-card">
                        <div class="card-icon employee">👤</div>
                        <h2 class="card-title">Colaborador</h2>
                        <p class="card-desc">Acesso aos seus dados e pedidos pessoais</p>
                        <button type="button" class="card-btn employee">Entrar como Colaborador →</button>
                    </a>
                </div>
            </div>
        </main>

        <footer>
            <p class="footer-copy">© {{ date('Y') }} TeamCore. Todos os direitos reservados.</p>
        </footer>

        <script>
            // Dark Mode Toggle
            const html = document.documentElement;
            const STORAGE_KEY = 'teamcore-theme';
            const themeToggle = document.getElementById('themeToggle');
            const iconMoon = themeToggle.querySelector('.icon-moon');
            const iconSun = themeToggle.querySelector('.icon-sun');

            (function () {
                const saved = localStorage.getItem(STORAGE_KEY);
                const prefer = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                const theme = saved || prefer;
                html.setAttribute('data-theme', theme);
                updateIcons(theme);
            })();

            function updateIcons(theme) {
                if (theme === 'dark') {
                    iconMoon.style.display = 'none';
                    iconSun.style.display = 'block';
                } else {
                    iconMoon.style.display = 'block';
                    iconSun.style.display = 'none';
                }
            }

            themeToggle.addEventListener('click', function () {
                const current = html.getAttribute('data-theme');
                const next = current === 'dark' ? 'light' : 'dark';
                html.setAttribute('data-theme', next);
                localStorage.setItem(STORAGE_KEY, next);
                updateIcons(next);
                themeToggle.style.transform = 'scale(0.85) rotate(20deg)';
                setTimeout(() => { themeToggle.style.transform = ''; }, 200);
            });
        </script>

        <!-- PWA Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/js/sw.js', { scope: '/' })
                    .then(function(registration) {
                        console.log('[PWA] Service Worker registered successfully', registration);
                        setInterval(function() {
                            registration.update().catch(function(error) {
                                console.warn('[PWA] Failed to update Service Worker:', error);
                            });
                        }, 60000);
                    })
                    .catch(function(error) {
                        console.error('[PWA] Service Worker registration failed:', error);
                        if (error.message) {
                            console.error('[PWA] Error details:', error.message);
                        }
                    });
            }
        </script>
    </body>
</html>
