<?php

namespace Codepunk\Activatinator\Contracts;

interface Activable
{
    /**
     * Get the e-mail address where activation links are sent.
     *
     * @return int
     */
    public function getIdForActivation();

    /**
     * Send the activation notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendActivationNotification($token);
}
