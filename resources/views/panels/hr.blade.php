<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>HR Panel</title>
</head>
<body>
    <h1>HR Panel</h1>
    <p>Bem-vindo, {{ $user?->name ?? 'Usuário' }} ({{ $user?->role ?? '—' }})</p>

    <p><a href="{{ url('/') }}">Voltar</a></p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
