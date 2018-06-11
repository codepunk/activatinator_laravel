<?php

namespace Codepunk\Activatinator\Contracts;

use Closure;

interface ActivatinatorBroker
{
    /**
     * Send an activation link to a user.
     *
     * @param  array  $credentials
     * @return string
     */
    public function sendActivationLink(array $credentials);

    /**
     * Activate the user for the given token.
     *
     * @param  array     $credentials
     * @param  string    $token
     * @param  \Closure  $callback
     * @return mixed
     */
    public function activate($credentials, $token, Closure $callback);
}
