<div class="space-y-6">
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm font-medium text-red-900">Contratos Vencendo</p>
                <p class="text-2xl font-bold text-red-900">{{ $this->getExpiringCount() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Próximos Vencimentos (30 dias)</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Colaborador</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Contrato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data de Início</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data de Término</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dias Restantes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($this->getExpiringContracts() as $contract)
                        @php
                            $daysRemaining = now()->diffInDays($contract->end_date);
                            $isUrgent = $daysRemaining <= 5;
                        @endphp
                        <tr class="hover:bg-gray-50 @if($isUrgent) bg-red-50 @endif">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $contract->employee?->first_name }} {{ $contract->employee?->last_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $contract->contract_type?->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $contract->start_date?->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $contract->end_date?->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-medium 
                                    @if($isUrgent) bg-red-100 text-red-800
                                    @elseif($daysRemaining <= 15) bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ $daysRemaining }} dias
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhum contrato vencendo nos próximos 30 dias
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
