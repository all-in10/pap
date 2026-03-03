<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alterar Senha</title>
    <style>
        :root,
        [data-theme="light"] {
            --primary:        #582f0e;
            --text:           #1e1a14;
            --text-soft:      #5a5040;
            --bg:             #f5f0e8;
            --surface:        #ede5d8;
            --card-border:    rgba(88, 47, 14, 0.12);
        }

        [data-theme="dark"] {
            --primary:        #c2a47a;
            --text:           #e8e2d8;
            --text-soft:      #a09880;
            --bg:             #141210;
            --surface:        #1e1a14;
            --card-border:    rgba(194, 164, 122, 0.10);
        }

        body { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, Arial; background:var(--bg); color:var(--text); transition: background-color 0.4s ease, color 0.4s ease; }
        .card { max-width:520px;margin:60px auto;padding:28px;border-radius:8px;background:var(--surface);border:1px solid var(--card-border) }
        label{display:block;margin-bottom:6px;font-weight:600;color:var(--text)}
        input{width:100%;padding:10px;border:1px solid var(--card-border);border-radius:6px;margin-bottom:12px;background:var(--bg);color:var(--text)}
        .btn{background:var(--primary);color:var(--surface);padding:10px 14px;border-radius:6px;text-decoration:none;border:none}
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