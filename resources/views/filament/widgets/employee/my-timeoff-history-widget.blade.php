<div class="space-y-6">
    @php
        $stats = $this->getTimeoffsStats();
    @endphp
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-gray-600 text-sm font-medium">Pendentes</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['pending'] ?? 0 }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-gray-600 text-sm font-medium">Aprovadas</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['approved'] ?? 0 }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <p class="text-gray-600 text-sm font-medium">Rejeitadas</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['rejected'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Histórico de Licenças/Férias</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Período</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Solicitação</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($this->getTimeoffsHistory() as $timeoff)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $timeoff->category?->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $timeoff->start_date?->format('d/m/Y') ?? 'N/A' }} - {{ $timeoff->end_date?->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $timeoff->created_at?->format('d/m/Y H:i') ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-medium 
                                    @if($timeoff->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($timeoff->status === 'approved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($timeoff->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                Você não possui solicitações de licença/férias
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
