<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acesso Negado</title>
    <style>
        body { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f8fafc; color:#222; }
        .card { max-width:720px;margin:60px auto;padding:28px;border-radius:10px;background:#fff;border:1px solid #eee;text-align:center;box-shadow:0 6px 18px rgba(30,41,59,0.04); }
        .btn { display:inline-block;padding:10px 18px;border-radius:6px;text-decoration:none;color:#fff }
        .btn-primary { background:#2b7a78 }
        .btn-default { background:#374151 }
    </style>
</head>
<body>
<div class="card">
    <h1 style="margin-bottom:8px;color:#c0392b">Acesso Negado</h1>
    <p style="margin-bottom:16px">Não tem permissão para aceder ao painel <strong>{{ strtoupper($panel ?? request()->segment(1) ?? '') }}</strong>.</p>

    @if(empty($role))
        <p style="margin-bottom:16px">Por favor faça login na página correta para continuar.</p>
        <a href="/login" class="btn btn-default">Ir para Login</a>
    @else
        <p style="margin-bottom:16px">O seu perfil tem role <strong>{{ $role }}</strong>. Use o botão abaixo para aceder à página de login apropriada.</p>
        <a href="{{ $loginPath }}" class="btn btn-primary">Ir para {{ $loginPath }}</a>
    @endif

    <div style="margin-top:18px;color:#666;font-size:13px">Se acha que isto é um erro, contacte o administrador do sistema.</div>
</div>
</body>
</html>
