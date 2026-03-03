<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acesso Negado</title>
    <style>
        :root,
        [data-theme="light"] {
            --primary:        #582f0e;
            --secondary:      #7f4f24;
            --text:           #1e1a14;
            --text-soft:      #5a5040;
            --bg:             #f5f0e8;
            --surface:        #ede5d8;
            --card-border:    rgba(88, 47, 14, 0.12);
        }

        [data-theme="dark"] {
            --primary:        #c2a47a;
            --secondary:      #a68a64;
            --text:           #e8e2d8;
            --text-soft:      #a09880;
            --bg:             #141210;
            --surface:        #1e1a14;
            --card-border:    rgba(194, 164, 122, 0.10);
        }

        body { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:var(--bg); color:var(--text); transition: background-color 0.4s ease, color 0.4s ease; }
        .card { max-width:720px;margin:60px auto;padding:28px;border-radius:10px;background:var(--surface);border:1px solid var(--card-border);text-align:center;box-shadow:0 6px 18px rgba(0,0,0,0.1); }
        .btn { display:inline-block;padding:10px 18px;border-radius:6px;text-decoration:none;color:var(--surface) }
        .btn-primary { background:var(--primary) }
        .btn-default { background:var(--secondary) }
    </style>
</head>
<body>
<div class="card">
    <h1 style="margin-bottom:8px;color:#c0392b">Caminho Errado</h1>
    <p style="margin-bottom:16px">O seu painel não é <strong>{{ strtoupper($panel ?? request()->segment(1) ?? '') }}</strong>.</p>

    @if(empty($role))
        <p style="margin-bottom:16px">Por favor faça login na página correta para continuar.</p>
        <a href="/login" class="btn btn-default">Ir para Login</a>
    @else
        <p style="margin-bottom:16px">O seu painel é o <strong>{{ $role }}</strong>. Use o botão abaixo para aceder à página de login apropriada.</p>
        <a href="{{ $loginPath }}" class="btn btn-primary">Ir para o painel  correto</a>
    @endif

    <div style="margin-top:18px;color:#666;font-size:13px">Se acha que isto é um erro, contacte o administrador do sistema.</div>
</div>
</body>
</html>
