<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $pending = $this->getPendingLicenses();
            $approved = $this->getApprovedLicenses();
            $total = $this->getTotalLicenses();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Total de Licenças -->
            <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/10 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total de Solicitações</div>
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $total }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-500 mt-2">Todas as licenças solicitadas</div>
            </div>

            <!-- Licenças Pendentes -->
            <div class="p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-900/10 rounded-lg border border-yellow-200 dark:border-yellow-800">
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Aguardando Análise</div>
                <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pending }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-500 mt-2">Solicitações pendentes</div>
            </div>

            <!-- Licenças Aprovadas -->
            <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-900/10 rounded-lg border border-green-200 dark:border-green-800">
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Aprovadas</div>
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $approved }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-500 mt-2">Licenças ativas</div>
            </div>
        </div>

        <!-- Informações Adicionais -->
        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 7a1 1 0 000 2h6a1 1 0 100-2H8zm0 3a1 1 0 000 2h4a1 1 0 100-2h-4z" clip-rule="evenodd"></path>
                </svg>
                Sobre Licenças
            </h4>
            <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
                <p>
                    As solicitações de licença devem ser encaminhadas através deste sistema. Cada solicitação será analisada pelo departamento de RH conforme a legislação vigente.
                </p>
                <p>
                    Tipos de licença disponíveis:
                </p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li><strong>Médica/Recuperação:</strong> Licença por motivo de saúde</li>
                    <li><strong>Parental/Maternidade:</strong> Licença parental ou maternidade</li>
                    <li><strong>Sem Vencimento:</strong> Período sem remuneração autorizado</li>
                    <li><strong>Especial:</strong> Outras licenças conforme a legislação</li>
                </ul>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
