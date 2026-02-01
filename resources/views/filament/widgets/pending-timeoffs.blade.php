@php
/** @var \Illuminate\Pagination\LengthAwarePaginator $pending */
$pending = $this->getPending();
@endphp

<x-filament-widgets::widget class="fi-wi-table">
    <div class="fi-wi-header">
        <h3 class="text-lg font-semibold">Solicitações de Licença Pendentes</h3>
    </div>

    @if($pending->count() === 0)
        <div class="fi-wi-body">
            <p class="mt-3 text-sm text-gray-500">Nenhuma solicitação pendente.</p>
        </div>
    @else
        <div class="fi-wi-body">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="text-left text-gray-600 bg-gray-50">
                        <tr>
                            <th class="px-3 py-2">Funcionário</th>
                            <th class="px-3 py-2">Período</th>
                            <th class="px-3 py-2">Tipo</th>
                            <th class="px-3 py-2 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($pending as $timeoff)
                            <tr class="border-t">
                                <td class="px-3 py-2">{{ $timeoff->employee?->user?->name ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $timeoff->start_date->format('d/m/Y') }} — {{ $timeoff->end_date->format('d/m/Y') }}</td>
                                <td class="px-3 py-2">{{ ucfirst($timeoff->type) }}</td>
                                <td class="px-3 py-2 text-right">
                                    @can('update', $timeoff)
                                        <div class="inline-flex items-center gap-2">
                                            <form method="POST" action="{{ route('filament.hr.timeoffs.approve', $timeoff) }}" style="display:inline">
                                                @csrf
                                                <button class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-500" type="submit">Aprovar</button>
                                            </form>
                                            <form method="POST" action="{{ route('filament.hr.timeoffs.reject', $timeoff) }}" style="display:inline">
                                                @csrf
                                                <button class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-500" type="submit">Rejeitar</button>
                                            </form>
                                            <a href="{{ \App\Filament\Resources\TimeoffResource::getUrl('edit', ['record' => $timeoff]) }}" class="inline-flex items-center px-3 py-1 text-xs text-gray-700 hover:underline">Ver</a>
                                        </div>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $pending->links() }}
            </div>
        </div>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Widgets\View\WidgetsRenderHook::TABLE_WIDGET_END, scopes: static::class) }}
</x-filament-widgets::widget>
