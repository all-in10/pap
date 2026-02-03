<x-filament::widget>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white/5 shadow-sm rounded p-4">
            <div class="text-sm text-gray-300">Utilizadores</div>
            <div class="text-2xl font-bold">{{ $usersCount }}</div>
        </div>

        <div class="bg-white/5 shadow-sm rounded p-4">
            <div class="text-sm text-gray-300">Colaboradores</div>
            <div class="text-2xl font-bold">{{ $employeesCount }}</div>
        </div>

        <div class="bg-white/5 shadow-sm rounded p-4">
            <div class="text-sm text-gray-300">Contratos ativos</div>
            <div class="text-2xl font-bold">{{ $activeContractsCount }}</div>
        </div>

        <div class="bg-white/5 shadow-sm rounded p-4">
            <div class="text-sm text-gray-300">Licenças pendentes</div>
            <div class="text-2xl font-bold">{{ $pendingTimeoffsCount }}</div>
        </div>
    </div>
</x-filament::widget>
