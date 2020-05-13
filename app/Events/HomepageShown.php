<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HomepageShown
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $timestamp;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('somechannel');
    }
}
