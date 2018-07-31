<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class StatusChange
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $server;
    public $user;

    /**
     * Create a new event instance.
     *
     * @param int  $server
     * @param array $user
     * @return void
     */
    public function __construct(int $server, array $user)
    {
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('cad.' . $this->server);
    }

    public function broadcastWith()
    {
        return [
            'user_id' => $this->user['id'],
            'identifier' => $this->user['identiier_type'] . " " . $this->user['identifier'],
            'status' => $this->user['status'],
            'department' => $this->user['department'],
            'division' => $this->user['division']
        ];
    }
}
