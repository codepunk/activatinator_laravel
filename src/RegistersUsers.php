<?php

namespace Codepunk\Activatinator;

use Illuminate\Foundation\Auth\RegistersUsers as FrameworkRegistersUsers;

trait RegistersUsers
{
    use FrameworkRegistersUsers, SendsActivationEmails {
        SendsActivationEmails::registered insteadof FrameworkRegistersUsers;
    }
}
