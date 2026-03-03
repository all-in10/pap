<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Você está offline. TeamCore será recarregado quando a conexão for restaurada.">
    <meta name="theme-color" content="#582f0e">
    <title>Offline - TeamCore</title>
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

        html,
        body {
            width: 100%;
            height: 100%;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text);
            transition: background-color 0.4s ease, color 0.4s ease;
        }

        .offline-container {
            background: var(--surface);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 500px;
            text-align: center;
            animation: slideUp 0.6s ease-out;
            border: 1px solid var(--card-border);
            transition: background 0.4s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .offline-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }

        h1 {
            font-size: 28px;
            margin-bottom: 16px;
            color: var(--neutral);
        }

        .status-message {
            font-size: 16px;
            color: var(--text-soft);
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .connection-indicator {
            display: inline-block;
            margin-top: 16px;
            padding: 12px 20px;
            border-radius: 8px;
            background-color: rgba(88, 47, 14, 0.08);
            border-left: 4px solid var(--danger);
            color: var(--text);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .connection-indicator.online {
            background-color: rgba(88, 47, 14, 0.12);
            border-left-color: var(--success);
            color: var(--neutral);
        }

        .connection-indicator::before {
            content: '●';
            margin-right: 8px;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .actions {
            margin-top: 32px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        button {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-retry {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--surface);
        }

        .btn-retry:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(88, 47, 14, 0.3);
        }

        .btn-retry:active {
            transform: translateY(0);
        }

        .btn-home {
            background: var(--bg);
            color: var(--text);
            border: 1px solid var(--card-border);
        }

        .btn-home:hover {
            background: var(--card-border);
            border-color: var(--card-hover-border);
        }

        .cached-pages {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 2px solid var(--card-border);
        }

        .cached-pages h3 {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .cached-pages-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .cached-link {
            display: block;
            padding: 8px 12px;
            background: var(--bg);
            border-radius: 6px;
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
            border: 1px solid var(--card-border);
        }

        .cached-link:hover {
            background: var(--card-border);
            border-color: var(--card-hover-border);
            transform: translateX(4px);
        }

        .network-status {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 8px 12px;
            background: rgba(88, 47, 14, 0.08);
            border-left: 4px solid var(--danger);
            border-radius: 4px;
            font-size: 12px;
            color: var(--text);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .network-status.online {
            background: rgba(88, 47, 14, 0.12);
            border-left-color: var(--success);
            color: var(--neutral);
        }

        @media (max-width: 600px) {
            .offline-container {
                margin: 20px;
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
            }

            .offline-icon {
                width: 100px;
                height: 100px;
                font-size: 50px;
                margin-bottom: 20px;
            }

            .network-status {
                top: 10px;
                right: 10px;
                font-size: 11px;
            }
        }
    </style>
</head>

<body>
    <div class="network-status" id="networkStatus">
        ● Offline
    </div>

    <div class="offline-container">
        <div class="offline-icon">📡</div>
        <h1>Você está offline</h1>

        <p class="status-message">
            Parece que você perdeu a conexão com a internet. Não se preocupe, o TeamCore continuará funcionando com
            os dados que foram sincronizados anteriormente.
        </p>

        <div class="connection-indicator" id="connectionIndicator">
            Aguardando conexão...
        </div>

        <div class="actions">
            <button class="btn-retry" onclick="location.reload()">
                🔄 Tentar novamente
            </button>
            <button class="btn-home" onclick="goHome()">
                🏠 Ir para home
            </button>
        </div>

        <div class="cached-pages">
            <h3>Páginas disponíveis offline:</h3>
            <div class="cached-pages-list">
                <a href="/" class="cached-link">📍 Dashboard</a>
                <a href="/profile" class="cached-link">👤 Meu Perfil</a>
                <a href="/offline" class="cached-link">📡 Status Offline</a>
            </div>
        </div>
    </div>

    <script>
        const networkStatus = document.getElementById('networkStatus');
        const connectionIndicator = document.getElementById('connectionIndicator');

        function updateConnectionStatus() {
            const isOnline = navigator.onLine;

            if (isOnline) {
                networkStatus.textContent = '● Online';
                networkStatus.classList.add('online');
                connectionIndicator.textContent = '● Conectado! Página será recarregada em breve...';
                connectionIndicator.classList.add('online');

                // Auto-reload when back online
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                networkStatus.textContent = '● Offline';
                networkStatus.classList.remove('online');
                connectionIndicator.textContent = '● Fazendo check de conexão...';
                connectionIndicator.classList.remove('online');
            }
        }

        // Check initial status
        updateConnectionStatus();

        // Listen for connection changes
        window.addEventListener('online', updateConnectionStatus);
        window.addEventListener('offline', updateConnectionStatus);

        // Periodic checks
        setInterval(() => {
            // Tentar fazer fetch leve para verificar conexão real
            fetch('/manifest.json', { method: 'HEAD', cache: 'no-store' })
                .then(() => {
                    if (!navigator.onLine) {
                        // Browser thinks offline mas conseguiu requisição
                        updateConnectionStatus();
                    }
                })
                .catch(() => {
                    // Sem conexão real
                    if (navigator.onLine) {
                        navigator.onLine = false; // Force offline state
                    }
                });
        }, 3000);

        function goHome() {
            window.location.href = '/';
        }

        // Suporte a volta via back button
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                updateConnectionStatus();
            }
        });
    </script>
</body>

</html>
