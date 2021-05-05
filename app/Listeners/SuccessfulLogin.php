<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use DateTime;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SuccessfulLogin
{
    private $request;
    /**
     * @var Carbon
     */
    private $carbon;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $event->user->lastvisitdate = new DateTime;
        $event->user->save();
    }
}
