<div class="space-y-6">
    @php
        $data = $this->getHourBankData();
    @endphp
    
    @if ($data)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Saldo Atual de Horas</p>
                    <p class="text-4xl font-bold text-gray-900 mt-2">
                        {{ number_format($data['current_balance'], 2) }} h
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-gray-600 text-sm font-medium">Última Atualização</p>
                    <p class="text-lg font-semibold text-gray-900 mt-2">
                        {{ $data['last_accrual_date']?->format('d/m/Y') ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
                <p class="text-blue-900 text-sm font-medium">Total Acumulado</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">
                    {{ number_format($data['total_accumulated'], 2) }} h
                </p>
            </div>
            
            <div class="bg-purple-50 rounded-lg p-4 border-l-4 border-purple-500">
                <p class="text-purple-900 text-sm font-medium">Estado</p>
                <p class="text-lg font-semibold mt-1">
                    @if ($data['current_balance'] > 0)
                        <span class="text-green-600">✓ Positivo</span>
                    @elseif ($data['current_balance'] < 0)
                        <span class="text-red-600">✗ Negativo</span>
                    @else
                        <span class="text-gray-600">→ Zerado</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Histórico Recente</h3>
            </div>
            
            <div class="divide-y divide-gray-200">
                @forelse ($data['history'] as $record)
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $record->created_at?->format('d/m/Y H:i') ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Última Acumulação: {{ $record->last_accrual_date?->format('d/m/Y') ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold 
                                    @if($record->balance_hours > 0) text-green-600
                                    @elseif($record->balance_hours < 0) text-red-600
                                    @else text-gray-600
                                    @endif">
                                    {{ $record->balance_hours > 0 ? '+' : '' }}{{ number_format($record->balance_hours, 2) }} h
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-4 text-center text-sm text-gray-500">
                        Nenhum registro no histórico
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
            <p class="text-sm font-medium text-yellow-800">
                Não foi possível carregar os dados do banco de horas
            </p>
        </div>
    @endif
</div>
