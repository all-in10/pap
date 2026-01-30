@php
use Illuminate\Support\Facades\Blade;
@endphp

{{--
    Default Livewire layout. When a Filament panel is active, delegate to the panel layout
    so Livewire pages appear inside the Filament chrome. Otherwise render a minimal layout
    compatible with Livewire full-page components.
--}}

@if (class_exists(\Filament\Facades\Filament::class) && \Filament\Facades\Filament::getCurrentPanel())
    {{-- Use Filament panel base layout when inside a panel (keeps the panel chrome) --}}
    <x-filament-panels::components.layout.base :livewire="\$livewire ?? null">
        {{ $slot }}
    </x-filament-panels::components.layout.base>
@else
    <!doctype html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Teamcore') }}</title>

        @if (file_exists(public_path('css/app.css')))
            <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        @endif

        @stack('styles')

        @if (class_exists(\Livewire\Livewire::class))
            @livewireStyles
        @endif
    </head>
    <body class="antialiased">
        {{ $slot }}

        @stack('scripts')

        @if (class_exists(\Livewire\Livewire::class))
            @livewireScripts
        @endif

        @if (file_exists(public_path('js/app.js')))
            <script src="{{ asset('js/app.js') }}"></script>
        @endif
    </body>
    </html>
@endif