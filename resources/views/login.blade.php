<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - PAP</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full p-6 bg-white dark:bg-[#161615] rounded-lg shadow-lg">
        <div class="flex justify-center mb-6">
            @include('filament.brand')
        </div>
        <h1 class="text-2xl font-medium mb-6 text-center">Entrar no Sistema</h1>
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-6 text-center">Selecione seu papel para acessar o painel correspondente.</p>
        <div class="space-y-4">
            <a href="{{ route('filament.employee.auth.login') }}" class="block w-full px-5 py-3 bg-[#1b1b18] dark:bg-[#eeeeec] dark:text-[#1C1C1A] text-white rounded-sm text-center hover:bg-black dark:hover:bg-white transition">
                Entrar como Funcionário
            </a>
            <a href="{{ route('filament.admin.auth.login') }}" class="block w-full px-5 py-3 bg-[#1b1b18] dark:bg-[#eeeeec] dark:text-[#1C1C1A] text-white rounded-sm text-center hover:bg-black dark:hover:bg-white transition">
                Entrar como Administrador
            </a>
        </div>
    </div>
</body>
</html>