<?php

namespace Codepunk\Activatinator\Events;

use Illuminate\Queue\SerializesModels;

class UserActivated
{
    use SerializesModels;

    /**
     * The user.
     *
     * @var \Codepunk\Activatinator\Contracts\Activable
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param  \Codepunk\Activatinator\Contracts\Activable  $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}
