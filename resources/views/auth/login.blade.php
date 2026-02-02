<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login - PAP</title>
    <style>button{padding:10px 16px;margin:6px;}</style>
</head>
<body>
    <h1>Login</h1>

    <p>Escolha o painel:</p>

    @if(isset($panelLoginRoutes['admin']))
        <a href="{{ $panelLoginRoutes['admin'] }}"><button>Admin / Root</button></a>
    @endif

    @if(isset($panelLoginRoutes['hr']))
        <a href="{{ $panelLoginRoutes['hr'] }}"><button>HR</button></a>
    @endif

    @if(isset($panelLoginRoutes['employee']))
        <a href="{{ $panelLoginRoutes['employee'] }}"><button>Employee</button></a>
    @endif

    <p>Ou use seu e-mail / senha abaixo (sistema local):</p>
    <form method="POST" action="{{ route('auth.attempt') }}">
        @csrf
        <div>
            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" required>
        </div>
        <div>
            <label for="password">Senha</label>
            <input id="password" name="password" type="password" required>
        </div>
        <button type="submit">Entrar</button>
    </form>

</body>
</html>