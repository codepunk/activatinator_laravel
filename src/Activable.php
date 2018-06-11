<?php

namespace Codepunk\Activatinator;

use Codepunk\Activatinator\Notifications\ActivateUser as ActivateUserNotification;

trait Activable
{
    /**
     * Get the user ID to look up the e-mail address where activation links are sent.
     *
     * @return int
     */
    public function getIdForActivation() {
        /** @noinspection PhpUndefinedFieldInspection */
        return $this->id;
    }

    /**
     * Send the activation notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendActivationNotification($token) {
        $this->notify(new ActivateUserNotification($token));
    }
}
