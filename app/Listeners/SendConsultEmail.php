<?php

namespace App\Listeners;

use App\Event\ConsultCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendConsultEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Event\ConsultCreated  $event
     * @return void
     */
    public function handle(ConsultCreated $event)
    {
        //
    }
}
