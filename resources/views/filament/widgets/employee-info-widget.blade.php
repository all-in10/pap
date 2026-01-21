<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $employee = $this->getEmployee();
            $totalHours = $this->getTotalHoursWorked();
            $totalExtra = $this->getTotalExtraHours();
            $balance = $this->getHoursbankBalance();
        @endphp

        @if ($employee)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informações Pessoais -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ $employee->first_name }} {{ $employee->last_name }}
                    </h3>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Departamento:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $employee->department?->name ?? 'N/A' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Cargo:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $employee->designation?->name ?? 'N/A' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Email:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $employee->email }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Estatísticas de Horas -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Estatísticas de Horas
                    </h3>
                    <dl class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Total de Horas:</dt>
                            <dd class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $totalHours }}h
                            </dd>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Horas Extras:</dt>
                            <dd class="text-xl font-bold text-green-600 dark:text-green-400">
                                {{ $totalExtra }}h
                            </dd>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Saldo Banco de Horas:</dt>
                            <dd class="text-xl font-bold text-purple-600 dark:text-purple-400">
                                {{ $balance }}h
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        @else
            <div class="text-center text-gray-500">
                Informações do funcionário não disponíveis
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
