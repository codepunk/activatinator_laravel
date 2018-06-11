<?php

namespace Codepunk\Activatinator\Controllers;

use App\Http\Controllers\Controller;
use Codepunk\Activatinator\SendsActivationEmails;
use Illuminate\Foundation\Auth\RedirectsUsers;

class SendActivationLinkController extends Controller
{
    use RedirectsUsers;
    use SendsActivationEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
}
