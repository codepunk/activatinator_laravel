<?php

namespace Codepunk\Activatinator\Contracts;

use Codepunk\Activatinator\Contracts\Activable as ActivableContract;

interface TokenRepositoryInterface
{
    /**
     * Create a new token.
     *
     * @param  \Codepunk\Activatinator\Contracts\Activable  $user
     * @return string
     */
    public function create(ActivableContract $user);

    /**
     * Determine if a token record exists and is valid.
     *
     * @param  \Codepunk\Activatinator\Contracts\Activable  $user
     * @param  string  $token
     * @return bool
     */
    public function exists(ActivableContract $user, $token);

    /**
     * Delete a token record.
     *
     * @param  \Codepunk\Activatinator\Contracts\Activable  $user
     * @return void
     */
    public function delete(ActivableContract $user);

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired();

    /**
     * Retrieve a user id by their unique activation token.
     *
     * @param  string  $token
     * @return string|null
     */
    public function retrieveUserIdByToken($token);
}
