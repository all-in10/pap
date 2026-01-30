@extends('components.layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-4">Entrar no Teamcore</h1>

        @if($errors->any())
            <div class="mb-4 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('app.login.post') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">Email ou Username</label>
                <input type="text" name="identity" required value="{{ old('identity') }}" class="mt-1 block w-full rounded border-gray-300" placeholder="seu@email.com ou seu-username">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Senha</label>
                <input type="password" name="password" required class="mt-1 block w-full rounded border-gray-300">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center text-sm">
                    <input type="checkbox" name="remember" class="mr-2"> Lembrar-me
                </label>

                <a href="/password/change" class="text-sm text-amber-700">Alterar senha</a>
            </div>

            <div>
                <button type="submit" class="w-full py-2 px-4 bg-amber-800 hover:bg-amber-700 text-white rounded">Entrar</button>
            </div>
        </form>

        <hr class="my-6">

        <p class="mb-2 text-sm text-gray-600">Ou escolha o fluxo do painel:</p>
        <div class="space-y-3">
            <a href="/admin/login" class="w-full block text-center py-2 px-4 bg-amber-100 hover:bg-amber-200 text-amber-800 rounded">Entrar como Admin (via painel)</a>
            <a href="/hr/login" class="w-full block text-center py-2 px-4 bg-indigo-100 hover:bg-indigo-200 text-indigo-800 rounded">Entrar como RH (via painel)</a>
            <a href="/employee/login" class="w-full block text-center py-2 px-4 bg-green-100 hover:bg-green-200 text-green-800 rounded">Entrar como Employee (via painel)</a>
        </div>

        <p class="mt-6 text-xs text-gray-500">Se você já está autenticado, será redirecionado automaticamente para seu painel.</p>
    </div>
</div>
@endsection
