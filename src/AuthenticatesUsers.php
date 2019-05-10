<?php

namespace Codepunk\Activatinator;

use Illuminate\Foundation\Auth\AuthenticatesUsers as FrameworkAuthenticatesUsers;

trait AuthenticatesUsers
{
    use FrameworkAuthenticatesUsers, ActivatesUsers {
        ActivatesUsers::showLoginForm insteadof FrameworkAuthenticatesUsers;
        ActivatesUsers::authenticated insteadof FrameworkAuthenticatesUsers;
    }
}
