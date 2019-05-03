<?php

namespace Codepunk\Activatinator;

use Closure;
use Codepunk\Activatinator\Contracts\TokenRepositoryInterface;
use Codepunk\Activatinator\Support\Facades\Activatinator;
use Illuminate\Support\Arr;
use UnexpectedValueException;
use Illuminate\Contracts\Auth\UserProvider;
use Codepunk\Activatinator\Contracts\ActivatinatorBroker as ActivatinatorBrokerContract;
use Codepunk\Activatinator\Contracts\Activable as ActivableContract;

class ActivatinatorBroker implements ActivatinatorBrokerContract
{
    /**
     * The activation token repository.
     *
     * @var \Codepunk\Activatinator\Contracts\TokenRepositoryInterface
     */
    protected $tokens;

    /**
     * The user provider implementation.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $users;

    /**
     * Create a new activation broker instance.
     *
     * @param  \Codepunk\Activatinator\Contracts\TokenRepositoryInterface  $tokens
     * @param  \Illuminate\Contracts\Auth\UserProvider  $users
     * @return void
     */
    public function __construct(TokenRepositoryInterface $tokens,
                                UserProvider $users)
    {
        $this->users = $users;
        $this->tokens = $tokens;
    }

    /**
     * Send an activation link to a user.
     *
     * @param  array  $credentials
     * @return string
     */
    public function sendActivationLink(array $credentials)
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return Activatinator::ACTIVATION_LINK_NOT_SENT;
        }

        // Once we have the reset token, we are ready to send the message out to this
        // user with a link to reset their password. We will then redirect back to
        // the current URI having nothing set in the session to indicate errors.
        $user->sendActivationNotification(
            $this->tokens->create($user)
        );

        return Activatinator::ACTIVATION_LINK_SENT;
    }

    /**
     * Activate the user for the given token.
     *
     * @param  array     $credentials
     * @param  string    $token
     * @param  \Closure  $callback
     * @return mixed
     */
    public function activate($credentials, $token, Closure $callback)
    {
        // If the responses from the validate method is not a user instance, we will
        // assume that it is a redirect and simply return it from this method and
        // the user is properly redirected having an error message on the post.
        $user = $this->validateActivation($credentials, $token);

        if (! $user instanceof ActivableContract) {
            return $user;
        }

        // Once the reset has been validated, we'll call the given callback with the
        // new password. This gives the user an opportunity to store the password
        // in their persistent storage. Then we'll delete the token and return.
        $callback($user);

        $this->tokens->delete($user);

        return Activatinator::ACTIVATED;
    }

    /**
     * Validate an activation for the given credentials.
     *
     * @param  array  $credentials
     * @param  string $token
     * @return \Codepunk\Activatinator\Contracts\Activable|string
     */
    protected function validateActivation($credentials, $token)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return Activatinator::INVALID_USER;
        }

        if (! $this->tokens->exists($user, $token)) {
            return Activatinator::INVALID_TOKEN;
        }

        return $user;
    }

    /**
     * Get the user for the given credentials.
     *
     * @param  array  $credentials
     * @return \Codepunk\Activatinator\Contracts\Activable|null
     *
     * @throws \UnexpectedValueException
     */
    public function getUser(array $credentials)
    {
        $credentials = Arr::except($credentials, ['token']);

        $user = $this->users->retrieveByCredentials($credentials);

        if ($user && ! $user instanceof ActivableContract) {
            throw new UnexpectedValueException('User must implement Activable interface.');
        }

        return $user;
    }

    /**
     * Create a new password reset token for the given user.
     *
     * @param  \Codepunk\Activatinator\Contracts\Activable $user
     * @return string
     */
    public function createToken(ActivableContract $user)
    {
        return $this->tokens->create($user);
    }

    /**
     * Delete password reset tokens of the given user.
     *
     * @param  \Codepunk\Activatinator\Contracts\Activable $user
     * @return void
     */
    public function deleteToken(ActivableContract $user)
    {
        $this->tokens->delete($user);
    }

    /**
     * Validate the given password reset token.
     *
     * @param  \Codepunk\Activatinator\Contracts\Activable $user
     * @param  string $token
     * @return bool
     */
    public function tokenExists(ActivableContract $user, $token)
    {
        return $this->tokens->exists($user, $token);
    }

    /**
     * Retrieve a user id by their unique activation token.
     *
     * @param  string  $token
     * @return string|null
     */
    public function retrieveUserIdByToken($token) {
        return $this->tokens->retrieveUserIdByToken($token);
    }

    /**
     * Get the password reset token repository implementation.
     *
     * @return \Codepunk\Activatinator\Contracts\TokenRepositoryInterface
     */
    public function getRepository()
    {
        return $this->tokens;
    }
}
