<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6366f1 0%, #a5b4fc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .login-box {
            background: #fff;
            border-radius: 1.25rem;
            box-shadow: 0 8px 32px 0 rgba(31, 41, 55, 0.15);
            padding: 2.5rem 2rem 2rem 2rem;
            width: 100%;
            max-width: 370px;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .login-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #3730a3;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        label {
            font-size: 0.95rem;
            color: #4b5563;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 0.7rem 0.9rem;
            border: 1px solid #c7d2fe;
            border-radius: 0.5rem;
            font-size: 1rem;
            background: #f8fafc;
            margin-bottom: 0.5rem;
            transition: border 0.2s;
        }
        input[type="email"]:focus, input[type="password"]:focus {
            border: 1.5px solid #6366f1;
            outline: none;
        }
        .error {
            color: #dc2626;
            background: #fee2e2;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
            font-size: 0.97rem;
            text-align: center;
        }
        button[type="submit"] {
            width: 100%;
            background: linear-gradient(90deg, #6366f1 0%, #818cf8 100%);
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
            border: none;
            border-radius: 0.5rem;
            padding: 0.8rem 0;
            cursor: pointer;
            box-shadow: 0 2px 8px 0 rgba(99, 102, 241, 0.08);
            transition: background 0.2s;
        }
        button[type="submit"]:hover {
            background: linear-gradient(90deg, #4f46e5 0%, #6366f1 100%);
        }
    </style>
</head>
<body>
    <form class="login-box" method="POST" action="{{ route('login') }}">
        <div class="login-title">Login</div>
        @csrf
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required autofocus value="{{ old('email') }}">
        </div>
        <div>
            <label for="password">Senha</label>
            <input type="password" name="password" id="password" required>
        </div>
        @if($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
