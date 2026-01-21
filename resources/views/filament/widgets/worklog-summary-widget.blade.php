<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $recentWorklogs = $this->getRecentWorklogs();
            $stats = $this->getCurrentMonthStats();
        @endphp

        <div class="space-y-6">
            <!-- Estatísticas do Mês -->
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $stats['total_days'] }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Dias Trabalhados</div>
                </div>
                <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ $stats['total_hours'] }}h
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Total de Horas</div>
                </div>
                <div class="text-center p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                    <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                        {{ $stats['total_extra'] }}h
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Horas Extras</div>
                </div>
            </div>

            <!-- Tabela de Registros Recentes -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Últimos Registros de Ponto
                </h3>
                @if (count($recentWorklogs) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left py-2 px-3 text-gray-600 dark:text-gray-400 font-medium">Data</th>
                                    <th class="text-left py-2 px-3 text-gray-600 dark:text-gray-400 font-medium">Entrada</th>
                                    <th class="text-left py-2 px-3 text-gray-600 dark:text-gray-400 font-medium">Saída</th>
                                    <th class="text-center py-2 px-3 text-gray-600 dark:text-gray-400 font-medium">Horas</th>
                                    <th class="text-center py-2 px-3 text-gray-600 dark:text-gray-400 font-medium">Extras</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentWorklogs as $log)
                                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="py-2 px-3 text-gray-900 dark:text-white">
                                            <div class="font-medium">{{ $log['work_date'] }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log['day_name'] }}</div>
                                        </td>
                                        <td class="py-2 px-3 text-gray-900 dark:text-white">
                                            {{ $log['start_time'] ?? '-' }}
                                        </td>
                                        <td class="py-2 px-3 text-gray-900 dark:text-white">
                                            {{ $log['end_time'] ?? '-' }}
                                        </td>
                                        <td class="py-2 px-3 text-center text-gray-900 dark:text-white">
                                            {{ $log['hours_worked'] }}h
                                        </td>
                                        <td class="py-2 px-3 text-center">
                                            @if ($log['extra_hours'] > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-300">
                                                    {{ $log['extra_hours'] }}h
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-600">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        Nenhum registro de ponto encontrado
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
