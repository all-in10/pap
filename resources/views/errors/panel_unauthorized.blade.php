<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>erro 403 - Acesso não autorizado</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="bg-white shadow-md rounded-lg p-8 max-w-lg text-center">
        <h1 class="text-3xl font-bold mb-4">erro 403</h1>
        <p class="text-lg text-gray-700 mb-6">Acesso não autorizado</p>

        <p class="text-sm text-gray-500 mb-6">Você não tem permissão para acessar este painel.</p>

        <a href="{{ $redirectTo }}" class="inline-block px-5 py-3 bg-blue-600 text-white rounded hover:bg-blue-700">Voltar ao meu painel</a>
    </div>
</body>
</html>