<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password - {{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased">
<div class="min-h-screen bg-gray-100 flex items-center justify-center px-4">
    <div class="bg-white rounded-lg shadow-md p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Change Your Password</h1>
        <p class="text-gray-600 mb-6 text-center">You must change your password before continuing.</p>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded">
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    New Password
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                    required
                />
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Confirm Password
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                />
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700 transition"
            >
                Change Password
            </button>
        </form>
    </div>
</div>
</body>
</html>
