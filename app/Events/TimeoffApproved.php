<?php

namespace App\Events;

use App\Models\Timeoff;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;

class TimeoffApproved
{
    use Dispatchable, InteractsWithSockets;

    public Timeoff $timeoff;

    /**
     * Create a new event instance.
     */
    public function __construct(Timeoff $timeoff)
    {
        $this->timeoff = $timeoff;
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
