<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alterar Palavra-passe - {{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#582f0e',
                        secondary: '#7f4f24',
                        info: '#936639',
                        danger: '#a68a64',
                        warning: '#b6ad90',
                        success: '#c2c5aa',
                        gray: '#acb79bff',
                        muted: '#656d4a',
                        accent: '#414833',
                        neutral: '#333d29',
                    }
                }
            }
        }
    </script>
</head>
<body class="antialiased">
<div class="min-h-screen bg-muted flex items-center justify-center px-4">
    <div class="bg-white rounded-lg shadow-md p-8 w-full max-w-md border-2 border-primary">
        <h1 class="text-2xl font-bold text-primary mb-6 text-center">Alterar Palavra-passe</h1>
        <p class="text-secondary mb-6 text-center">Tem de alterar a sua palavra-passe antes de continuar.</p>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-danger/20 border border-danger rounded">
                <ul class="list-disc list-inside text-sm text-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <p class="text-xs text-danger mt-2">Verifique os requisitos da palavra-passe e tente novamente.</p>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-secondary mb-2">
                    Nova Palavra-passe
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full px-4 py-2 border border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent @error('password') border-danger @enderror"
                    required
                />
                <p class="text-xs text-muted mt-1">A palavra-passe deve ter pelo menos 8 caracteres, incluir maiúsculas, minúsculas, números e símbolos, e não pode conter "password" nem ser igual à atual.</p>
                @error('password')
                    <p class="text-danger text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-secondary mb-2">
                    Confirmar Palavra-passe
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="w-full px-4 py-2 border border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"
                    required
                />
            </div>

            <button
                type="submit"
                class="w-full bg-primary text-white py-2 rounded-lg font-medium hover:bg-accent transition"
            >
                Alterar Palavra-passe
            </button>
        </form>
    </div>
</div>
</body>
</html>
