<?php

namespace Codepunk\Activatinator\Controllers;

use App\Http\Controllers\Controller;
use Codepunk\Activatinator\ActivatesUsers;

class ActivateController extends Controller
{
    use ActivatesUsers;

    /**
     * Where to redirect users after activation.
     *
     * @var string
     */
    protected $redirectTo = '/home';

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
