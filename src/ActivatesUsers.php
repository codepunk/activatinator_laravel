<?php

namespace Codepunk\Activatinator;

use Codepunk\Activatinator\Events\UserActivated;
use Codepunk\Activatinator\Support\Facades\Activatinator;
use Illuminate\Http\Request;

trait ActivatesUsers
{
    /**
     * Display the login view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm(Request $request, $token = null)
    {
        if (isset($token)) {
            session()->flash('token', $token);
            session()->flash('status', trans('codepunk::activatinator.activate'));
        }

        return view('auth.login');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if (! $user->active) {
            session()->keep('token');
            $token = session()->get('token');
            if (isset($token)) {
                // Attempt to resolve token and activate user
                return $this->activate($request, $token);
            } else {
                $request->merge(['email' => $user->email]);
                return $this->sendActivateFailedResponse($request, Activatinator::INACTIVE_USER);
            }
        }

        return null;
    }

    /**
     * Activates the given user's account.
     *
     * @param  string  $token
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function activate(Request $request, $token)
    {
        // We're piggybacking off of AuthenticatesUsers, so ensure that login is valid.
        // We may not need this step, but keeping it for now.
        $this->validateLogin($request);

        // Here we will attempt to activate the user's account. If it is successful
        // we will update the active status on the user model and save it to the
        // database. Otherwise, we'll parse the error and return the response.
        $response = $this->broker()->activate(
            $this->credentials($request),
            $token,
            function ($user) {
                $this->activateUser($user);
            }
        );

        // If the user was successfully activated, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Activatinator::ACTIVATED
            ? $this->sendActivateResponse($response)
            : $this->sendActivateFailedResponse($request, $response);
    }

    /**
     * Activate the given user's account.
     *
     * @param  mixed  $user
     * @return void
     */
    protected function activateUser($user)
    {
        $user->setAttribute('active', true)
            ->save();

        event(new UserActivated($user));
    }

    /**
     * Get the response for a successful activation.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendActivateResponse($response)
    {
        return redirect($this->redirectPath());
    }

    /**
     * Get the response for a failed activation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendActivateFailedResponse(Request $request, $response)
    {
        auth()->logout();
        if ($request->wantsJson()) {
            return response()->json(
                [ "message" => trans($response) ],
                401,
                [ 'Content-Type' => 'application/json' ]
            );
        } else {
            return redirect()->route('login')
                ->withInput($request->only('email'))
                ->with('warning', trans($response))
                ->with('resend', true);
        }
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Codepunk\Activatinator\Contracts\ActivatinatorBroker
     */
    public function broker()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return Activatinator::broker();
    }
}
