<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Department Section -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Departamento</h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ $this->getDepartmentData() ?? 'Não atribuído' }}
            </p>
        </div>

        <!-- Hoursbank Section -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Banco de Horas</h3>
            <p class="mt-2 text-2xl font-bold text-blue-600 dark:text-blue-400">
                {{ $this->getHoursbankData() }} horas
            </p>
        </div>

        <!-- Worklog Section -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Últimos Registos de Trabalho</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left">Data</th>
                            <th class="px-4 py-2 text-left">Hora Inicial</th>
                            <th class="px-4 py-2 text-left">Hora Final</th>
                            <th class="px-4 py-2 text-left">Horas Trabalhadas</th>
                            <th class="px-4 py-2 text-left">Horas Extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->getWorklogData() as $log)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-4 py-2">{{ $log['work_date'] }}</td>
                                <td class="px-4 py-2">{{ $log['start_time'] ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log['end_time'] ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $log['hours_worked'] ?? 0 }}</td>
                                <td class="px-4 py-2">{{ $log['extra_hours'] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Timeoff Request Form -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Solicitar Férias ou Justificativa de Ausência</h3>
            <form wire:submit="submit" class="mt-4 space-y-4">
                {{ $this->form }}

                <div class="flex gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Submeter Solicitação
                    </button>
                </div>
            </form>
        </div>

        <!-- My Timeoff Requests -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Minhas Solicitações</h3>
            <div class="mt-4 overflow-x-auto">
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
                        @foreach ($this->getTimeoffData() as $timeoff)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-4 py-2">
                                    @if ($timeoff['type'] === 'vacation')
                                        <span class="inline-block rounded bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">Férias</span>
                                    @else
                                        <span class="inline-block rounded bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800">Justificativa</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $timeoff['start_date'] }}</td>
                                <td class="px-4 py-2">{{ $timeoff['end_date'] }}</td>
                                <td class="px-4 py-2">
                                    @if ($timeoff['status'] === 'pending')
                                        <span class="inline-block rounded bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">Pendente</span>
                                    @elseif ($timeoff['status'] === 'approved')
                                        <span class="inline-block rounded bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">Aprovado</span>
                                    @else
                                        <span class="inline-block rounded bg-red-100 px-2 py-1 text-xs font-semibold text-red-800">Recusado</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $timeoff['reason'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
