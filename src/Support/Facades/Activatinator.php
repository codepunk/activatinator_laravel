<?php

namespace Codepunk\Activatinator\Support\Facades;

use Codepunk\Activatinator\Contracts\ActivatinatorBroker;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ActivatinatorBroker broker()
 */
class Activatinator extends Facade
{
    /**
     * Constant representing a successfully sent activation link.
     *
     * @var string
     */
    const ACTIVATION_LINK_SENT = 'codepunk::activatinator.sent';

    /**
     * Constant representing a successfully activated user.
     *
     * @var string
     */
    const ACTIVATED = 'codepunk::activatinator.activated';

    /**
     * Constant representing the user not found response.
     *
     * @var string
     */
    const INVALID_USER = 'codepunk::activatinator.user';

    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = 'codepunk::activatinator.token';

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'codepunk.activatinator';
    }
}
