<?php

namespace App\Traits;

use Filament\Notifications\Notification;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Auth;

/**
 * Trait para notificar itens criados
 * Envia notificações separadas para cada item criado e persiste no histórico
 */
trait NotifiesCreatedItems
{
    /**
     * Envia uma notificação de sucesso separada para cada item criado e persiste no histórico
     * Fluxo: para cada item, cria registo no NotificationLog e envia notificação Filament
     * @param array $items Array de strings descrevendo itens criados
     * @param int|null $targetUserId ID opcional do usuário para relacionar a notificação
     */
    protected function notifyCreatedItems(array $items, ?int $targetUserId = null): void
    {
        foreach ($items as $item) {
            // Persiste no histórico de notificações
            try {
                NotificationLog::create([
                    'title' => 'Criado',
                    'body' => $item,
                    'data' => null,
                    'created_by' => Auth::id(),
                    'user_id' => $targetUserId,
                ]);
            } catch (\Throwable $e) {
                // Se falhar a persistência, não bloqueia notificações UI
            }

            // Envia a notificação Filament
            Notification::make()
                ->title('Criado')
                ->body($item)
                ->success()
                ->send();
        }
    }
}
