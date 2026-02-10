<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-gray-600 text-sm font-medium">Total de Colaboradores</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $this->getTotalEmployees() }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-gray-600 text-sm font-medium">Colaboradores Ativos</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $this->getActiveEmployees() }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <p class="text-gray-600 text-sm font-medium">Adicionados Recentemente</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $this->getRecentEmployees()->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Colaboradores Recentes</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($this->getRecentEmployees() as $employee)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $employee->first_name }} {{ $employee->last_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $employee->department?->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $employee->designation?->title ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $employee->email }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhum colaborador encontrado
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
