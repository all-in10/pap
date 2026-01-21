<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Botões de Ação para Criar Solicitações -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Solicitar Férias ou Justificativa de Ausência -->
            <div class="rounded-lg border border-green-200 bg-white p-6 shadow-sm dark:border-green-800 dark:bg-gray-800">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m7 8a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v10z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Férias / Justificativa</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Solicite férias ou justifique uma ausência do trabalho.
                </p>
                <a href="{{ route('filament.admin.resources.timeoffs.create') }}" class="inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-green-700 transition-colors">
                    Nova Solicitação
                </a>
            </div>

            <!-- Solicitar Licença -->
            <div class="rounded-lg border border-blue-200 bg-white p-6 shadow-sm dark:border-blue-800 dark:bg-gray-800">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Solicitação de Licença</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Solicite licença médica, parental ou especial.
                </p>
                <a href="{{ route('filament.admin.resources.timeoffs.create') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                    Nova Solicitação
                </a>
            </div>
        </div>

        <!-- Histórico de Solicitações -->
        <div class="space-y-6">
            <!-- Minhas Solicitações de Férias/Ausência -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m7 8a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v10z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Solicitações de Férias / Ausência</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">Tipo</th>
                                <th class="px-4 py-2 text-left">Data Inicial</th>
                                <th class="px-4 py-2 text-left">Data Final</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $timeoffs = $this->getTimeoffData();
                                $vacations = collect($timeoffs)->filter(fn($t) => $t['type'] !== 'license');
                            @endphp
                            @forelse ($vacations as $timeoff)
                                <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-2">
                                        @if ($timeoff['type'] === 'vacation')
                                            <span class="inline-block rounded bg-green-100 px-2 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/30 dark:text-green-300">Férias</span>
                                        @else
                                            <span class="inline-block rounded bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">Justificativa</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $timeoff['start_date'] }}</td>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $timeoff['end_date'] }}</td>
                                    <td class="px-4 py-2">
                                        @if ($timeoff['status'] === 'pending')
                                            <span class="inline-block rounded bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Pendente</span>
                                        @elseif ($timeoff['status'] === 'approved')
                                            <span class="inline-block rounded bg-green-100 px-2 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/30 dark:text-green-300">Aprovado</span>
                                        @else
                                            <span class="inline-block rounded bg-red-100 px-2 py-1 text-xs font-semibold text-red-800 dark:bg-red-900/30 dark:text-red-300">Recusado</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $timeoff['reason'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        Nenhuma solicitação de férias ou ausência encontrada
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Minhas Solicitações de Licença -->
            <div class="rounded-lg border border-blue-200 bg-white p-6 shadow-sm dark:border-blue-800 dark:bg-gray-800">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Solicitações de Licença</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-blue-50 dark:bg-blue-900/20">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300">Tipo de Licença</th>
                                <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300">Data Inicial</th>
                                <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300">Data Final</th>
                                <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300">Status</th>
                                <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300">Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $licenses = collect($timeoffs)->filter(fn($t) => $t['type'] === 'license');
                            @endphp
                            @forelse ($licenses as $license)
                                <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/10">
                                    <td class="px-4 py-2">
                                        <span class="inline-block rounded bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Licença</span>
                                    </td>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $license['start_date'] }}</td>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $license['end_date'] }}</td>
                                    <td class="px-4 py-2">
                                        @if ($license['status'] === 'pending')
                                            <span class="inline-block rounded bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">Pendente</span>
                                        @elseif ($license['status'] === 'approved')
                                            <span class="inline-block rounded bg-green-100 px-2 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/30 dark:text-green-300">Aprovada</span>
                                        @else
                                            <span class="inline-block rounded bg-red-100 px-2 py-1 text-xs font-semibold text-red-800 dark:bg-red-900/30 dark:text-red-300">Recusada</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">
                                        <span class="truncate" title="{{ $license['reason'] ?? '-' }}">{{ $license['reason'] ?? '-' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        <div class="space-y-2">
                                            <p>Nenhuma solicitação de licença encontrada</p>
                                            <p class="text-xs">Clique em "Solicitar Licença" acima para fazer uma nova solicitação</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
