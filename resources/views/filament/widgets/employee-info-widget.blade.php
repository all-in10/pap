<x-filament::widget>
    @if ($employee)
        <div class="space-y-4">
            <!-- Informações Pessoais do Funcionário -->
            <div class="bg-gradient-to-r from-amber-500/10 to-amber-600/10 rounded-lg p-4 border border-amber-500/20">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">
                    Informações Pessoais
                </h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <!-- Nome -->
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Nome</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        </p>
                    </div>

                    <!-- Cargo -->
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Cargo</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ $employee->designation?->name ?? 'N/A' }}
                        </p>
                    </div>

                    <!-- Departamento -->
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Departamento</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ $employee->department?->name ?? 'N/A' }}
                        </p>
                    </div>

                    <!-- Email -->
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ $employee->email ?? 'N/A' }}
                        </p>
                    </div>

                    <!-- Data de Admissão -->
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Admissão</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            @if($employee->date_hired)
                                {{ \Carbon\Carbon::parse($employee->date_hired)->format('d/m/Y') }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>

                    <!-- Telefone -->
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Telefone</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ $employee->phone_number ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Listagem de Pedidos de Férias/Licenças -->
            @if (count($timeoffs) > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-amber-500/5 to-amber-600/5 dark:from-gray-700/50 dark:to-gray-700">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">
                            Meus Pedidos de Licença/Férias
                        </h3>
                    </div>
                    
                    <div class="overflow-x-auto max-h-96">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase whitespace-nowrap">
                                        Início
                                    </th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase whitespace-nowrap">
                                        Fim
                                    </th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase whitespace-nowrap">
                                        Categoria
                                    </th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase whitespace-nowrap">
                                        Tipo
                                    </th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase whitespace-nowrap">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($timeoffs as $timeoff)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-3 py-2 text-xs text-gray-900 dark:text-white whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($timeoff->start_date)->format('d/m/y') }}
                                        </td>
                                        <td class="px-3 py-2 text-xs text-gray-900 dark:text-white whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($timeoff->end_date)->format('d/m/y') }}
                                        </td>
                                        <td class="px-3 py-2 text-xs text-gray-900 dark:text-white whitespace-nowrap">
                                            {{ $timeoff->category?->label ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 py-2 text-xs text-gray-900 dark:text-white whitespace-nowrap">
                                            <span class="text-xs">{{ \App\Models\Timeoff::TYPES[$timeoff->type]['label'] ?? $timeoff->type }}</span>
                                        </td>
                                        <td class="px-3 py-2">
                                            @switch($timeoff->status)
                                                @case('pending')
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                                                        Pendente
                                                    </span>
                                                    @break
                                                @case('approved')
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                                        Aprovado
                                                    </span>
                                                    @break
                                                @case('rejected')
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">
                                                        Rejeitado
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                        {{ ucfirst($timeoff->status) }}
                                                    </span>
                                            @endswitch
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="rounded-lg border border-gray-300 dark:border-gray-600 p-4 text-center bg-gray-50 dark:bg-gray-800">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Nenhum pedido de licença/férias registado.
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                        <a href="{{ route('filament.employee.resources.timeoffs.create') }}" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">
                            Criar um novo pedido
                        </a>
                    </p>
                </div>
            @endif

            <!-- Ações Rápidas -->
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('filament.employee.resources.timeoffs.create') }}" 
                   class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-lg font-semibold text-sm transition-colors duration-200 bg-blue-600 hover:bg-blue-700 text-white">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Solicitar
                </a>

                <a href="{{ route('filament.employee.resources.timeoffs.index') }}" 
                   class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-lg font-semibold text-sm transition-colors duration-200 bg-amber-600 hover:bg-amber-700 text-white">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Ver Tudo
                </a>
            </div>

            <!-- Observações (se existirem) -->
            @if ($employee->observations)
                <div class="bg-blue-500/10 rounded-lg p-3 border border-blue-500/20">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Observações</p>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $employee->observations }}</p>
                </div>
            @endif
        </div>
    @else

    @endif
</x-filament::widget>
