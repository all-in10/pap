<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alterar Senha</title>
    <style>
        body { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, Arial; background:#f8fafc; }
        .card { max-width:520px;margin:60px auto;padding:28px;border-radius:8px;background:#fff;border:1px solid #eee }
        label{display:block;margin-bottom:6px;font-weight:600}
        input{width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;margin-bottom:12px}
        .btn{background:#2b7a78;color:#fff;padding:10px 14px;border-radius:6px;text-decoration:none;border:none}
    </style>
</head>
<body>
<div class="card">
    <h1>Alterar Senha</h1>
    <p>É necessário alterar a senha para continuar.</p>

    @if($errors->any())
        <div style="color:#c0392b;margin-bottom:12px">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <label for="password">Nova Senha</label>
        <input id="password" name="password" type="password" required minlength="8">

        <label for="password_confirmation">Confirmar Senha</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8">

        <button class="btn" type="submit">Atualizar Senha</button>
    </form>
</div>
</body>
</html>