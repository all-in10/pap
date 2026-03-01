<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Você está offline. TeamCore será recarregado quando a conexão for restaurada.">
    <meta name="theme-color" content="#3b82f6">
    <title>Offline - TeamCore</title>
    <style>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .offline-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 500px;
            text-align: center;
            animation: slideUp 0.6s ease-out;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            color: #0f172a;
        }

        .status-message {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .connection-indicator {
            display: inline-block;
            margin-top: 16px;
            padding: 12px 20px;
            border-radius: 8px;
            background-color: #fee2e2;
            border-left: 4px solid #dc2626;
            color: #7f1d1d;
            font-size: 14px;
            font-weight: 500;
        }

        .connection-indicator.online {
            background-color: #dcfce7;
            border-left-color: #16a34a;
            color: #15803d;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-retry:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(102, 126, 234, 0.4);
        }

        .btn-retry:active {
            transform: translateY(0);
        }

        .btn-home {
            background: #e2e8f0;
            color: #0f172a;
        }

        .btn-home:hover {
            background: #d1d5db;
        }

        .cached-pages {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 2px solid #e2e8f0;
        }

        .cached-pages h3 {
            font-size: 14px;
            color: #64748b;
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
            background: #f1f5f9;
            border-radius: 6px;
            color: #3b82f6;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .cached-link:hover {
            background: #e2e8f0;
            transform: translateX(4px);
        }

        .network-status {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 8px 12px;
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            border-radius: 4px;
            font-size: 12px;
            color: #7f1d1d;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .network-status.online {
            background: #dcfce7;
            border-left-color: #16a34a;
            color: #15803d;
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
