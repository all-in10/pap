<x-filament::page>
    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="rounded-lg bg-gradient-to-r from-primary-500 to-primary-600 p-6 text-white shadow-lg">
            <h1 class="text-3xl font-bold">Bem-vindo ao Dashboard</h1>
            <p class="mt-2 opacity-90">Acompanhe suas férias, licenças, banco de horas e presença.</p>
        </div>

        <!-- Main Content -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <!-- Vacation Balance Card -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Saldo de Férias</p>
                        <p class="mt-2 text-3xl font-bold">{{ $employee?->vacation_balance ?? 0 }} dias</p>
                    </div>
                    <x-heroicon-o-calendar class="h-12 w-12 text-primary-500" />
                </div>
            </div>

            <!-- Timeoffs Card -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Licenças Pendentes</p>
                        <p class="mt-2 text-3xl font-bold">{{ $employee?->getPendingTimeoffsCount() ?? 0 }}</p>
                    </div>
                    <x-heroicon-o-clock class="h-12 w-12 text-warning-500" />
                </div>
            </div>

            <!-- Hour Bank Card -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Banco de Horas</p>
                        <p class="mt-2 text-3xl font-bold">{{ number_format($employee?->getLatestHourBankBalance() ?? 0, 1) }}h</p>
                    </div>
                    <x-heroicon-o-arrow-trending-up class="h-12 w-12 text-success-500" />
                </div>
            </div>
        </div>
    </div>
</x-filament::page>
