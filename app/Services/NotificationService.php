<?php

namespace App\Services;

use App\Models\Timeoff;
use App\Models\Contract;
use App\Models\NotificationLog;
use App\Events\TimeoffApproved;
use App\Events\TimeoffRejected;
use App\Events\ContractExpiringReminder;
use Illuminate\Support\Facades\Event;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Envia notificação de timeoff aprovado
     */
    public static function sendTimeoffApprovedNotification(Timeoff $timeoff): void
    {
        Event::dispatch(new TimeoffApproved($timeoff));
    }

    /**
     * Envia notificação de timeoff rejeitado
     */
    public static function sendTimeoffRejectedNotification(Timeoff $timeoff): void
    {
        Event::dispatch(new TimeoffRejected($timeoff));
    }

    /**
     * Envia reminder de contrato expirando
     */
    public static function sendContractExpiringReminder(Contract $contract): void
    {
        $daysUntilExpiry = now()->diffInDays($contract->end_date);

        if ($daysUntilExpiry > 0 && $daysUntilExpiry <= 30) {
            Event::dispatch(new ContractExpiringReminder($contract, $daysUntilExpiry));
        }
    }

    /**
     * Marca notificação como lida
     */
    public static function markAsRead(NotificationLog $notification): void
    {
        $notification->update(['is_read' => true]);
    }

    /**
     * Marca todas as notificações de um usuário como lidas
     */
    public static function markAllAsRead(?int $userId): void
    {
        if ($userId) {
            NotificationLog::where('user_id', $userId)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }
    }

    /**
     * Obtém notificações não lidas de um usuário
     */
    public static function getUnreadNotifications(?int $userId): int
    {
        if (!$userId) {
            return 0;
        }

        return NotificationLog::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Obtém últimas notificações de um usuário
     */
    public static function getLatestNotifications(?int $userId, int $limit = 10)
    {
        if (!$userId) {
            return collect();
        }

        return NotificationLog::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Verifica contratos expirando nos próximos 30 dias
     * Deve ser chamado por CRON job
     */
    public static function checkExpiringContracts(): int
    {
        $expiringContracts = Contract::where('status', 'active')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays(30)])
            ->get();

        $count = 0;
        foreach ($expiringContracts as $contract) {
            // Verificar se já não foi enviada notificação hoje
            $existingNotification = NotificationLog::where('type', 'contract_expiring')
                ->whereJsonContains('data->contract_id', $contract->id)
                ->whereDate('created_at', now())
                ->exists();

            if (!$existingNotification) {
                self::sendContractExpiringReminder($contract);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Limpa notificações antigas (mais de 90 dias)
     */
    public static function deleteOldNotifications(int $daysOld = 90): int
    {
        return NotificationLog::where('created_at', '<', now()->subDays($daysOld))
            ->delete();
    }
}
