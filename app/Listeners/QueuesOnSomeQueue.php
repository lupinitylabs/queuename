<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class QueuesOnSomeQueue implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var string
     */
    public $queue = 'initial-queue';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->queue = 'destination-queue';
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        dd($this->job);
    }
}
