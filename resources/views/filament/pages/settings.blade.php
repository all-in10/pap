<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-lg border border-gray-300 bg-white p-6">
            <h2 class="text-lg font-semibold text-gray-900">Configurações</h2>
            <p class="mt-2 text-sm text-gray-600">Gerencie suas configurações e histórico de notificações.</p>
        </div>

        <div class="rounded-lg border border-gray-300 bg-white p-6">
            <h3 class="text-md font-semibold text-gray-900">Histórico de Notificações</h3>
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
                        <tr class="text-left">
                            <th class="px-3 py-2">Data</th>
                            <th class="px-3 py-2">Título</th>
                            <th class="px-3 py-2">Mensagem</th>
                            <th class="px-3 py-2">Relacionado a</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $n)
                            <tr class="border-t">
                                <td class="px-3 py-2">{{ $n->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-3 py-2">{{ $n->title }}</td>
                                <td class="px-3 py-2">{{ $n->body }}</td>
                                <td class="px-3 py-2">{{ $n->user?->email ?? $n->creator?->email ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-gray-500">Nenhuma notificação encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
