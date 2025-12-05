<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RemoteActionTriggered implements ShouldBroadcastNow
{
    use SerializesModels;

    public $payload;
    public $initiator_id;

    /**
     * Create a new event instance.
     */
    public function __construct(array $payload, $initiator_id)
    {
        $this->payload = $payload;
        $this->initiator_id = $initiator_id;
        \Log::info('RemoteActionTriggered event created', [
            'payload' => $payload,
            'initiator_id' => $initiator_id
        ]);
    }

   
    public function broadcastOn()
    {
        return new Channel('units'); 
    }

    public function broadcastAs()
    {
        return 'RemoteActionTriggered';
    }

        public function broadcastWith()
    {
        return [
            'payload' => $this->payload,
            'initiator_id' => $this->initiator_id,
        ];
    }
}
