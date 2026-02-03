<x-filament::page>
    <div class="filament-page">
        <h1 class="text-2xl font-bold">Admin Dashboard</h1>
        <p>Welcome to the admin panel.</p>

        <div class="mt-6">
            @php
                $widget = app(\App\Filament\Widgets\GeneralStats::class);
            @endphp
            {!! $widget->render() !!}
        </div>
        </div>
    </div>
</x-filament::page>
