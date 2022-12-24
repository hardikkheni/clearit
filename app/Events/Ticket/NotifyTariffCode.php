<?php

namespace App\Events\Ticket;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotifyTariffCode
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $user;
    public $agent;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        Ticket $ticket,
        User $user,
        User $agent
    ) {
        $this->ticket = $ticket;
        $this->user = $user;
        $this->agent = $agent;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('channel-name');
        return [];
    }
}
