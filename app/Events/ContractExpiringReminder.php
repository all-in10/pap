<?php

namespace App\Events;

use App\Models\Contract;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;

class ContractExpiringReminder
{
    use Dispatchable, InteractsWithSockets;

    public Contract $contract;
    public int $daysUntilExpiry;

    /**
     * Create a new event instance.
     */
    public function __construct(Contract $contract, int $daysUntilExpiry)
    {
        $this->contract = $contract;
        $this->daysUntilExpiry = $daysUntilExpiry;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
