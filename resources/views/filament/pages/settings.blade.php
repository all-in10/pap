<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-lg border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Configurações</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Gerencie suas configurações e histórico de notificações.</p>
        </div>

        <div class="rounded-lg border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-700 p-6">
            <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100">Histórico de Notificações</h3>
            @php
                $user = auth()->user();
                $query = \App\Models\NotificationLog::query();
                if (!$user->isAdmin() && !$user->isRoot()) {
                    $query->where(function ($q) use ($user) {
                        $q->where('user_id', $user->id)->orWhere('created_by', $user->id);
                    });
                }
                $notifications = $query->orderBy('created_at', 'desc')->limit(50)->get();
            @endphp

            <div class="mt-4">
                <table class="w-full text-sm table-auto">
                    <thead>
                        <tr class="text-left bg-gray-50 dark:bg-gray-700">
                            <th class="px-3 py-2 text-gray-900 dark:text-gray-100">Data</th>
                            <th class="px-3 py-2 text-gray-900 dark:text-gray-100">Título</th>
                            <th class="px-3 py-2 text-gray-900 dark:text-gray-100">Mensagem</th>
                            <th class="px-3 py-2 text-gray-900 dark:text-gray-100">Relacionado a</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $n)
                            <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $n->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $n->title }}</td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $n->body }}</td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $n->user?->email ?? $n->creator?->email ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-gray-500 dark:text-gray-400">Nenhuma notificação encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
