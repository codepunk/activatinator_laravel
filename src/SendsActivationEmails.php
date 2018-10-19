<?php

namespace Codepunk\Activatinator;

use Codepunk\Activatinator\Support\Facades\Activatinator;
use Illuminate\Http\Request;

trait SendsActivationEmails
{
    /**
     * Get the post register redirect path.
     *
     * @return string
     */
    public function redirectTo() {
        return '/login';
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */ $user)
    {
        auth()->logout();
        return $this->sendActivationLinkEmail($request);
    }

    /**
     * Send an activation link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendActivationLinkEmail(Request $request) {
        $this->validateEmail($request);

        // We will send the user activation link to the user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendActivationLink(
            $request->only('email')
        );

        return $response == Activatinator::ACTIVATION_LINK_SENT
            ? $this->sendActivationLinkResponse($request, $response)
            : $this->sendActivationLinkFailedResponse($request, $response);
    }

    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->validate($request, ['email' => 'required|email']);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendActivationLinkResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            return response()->json(
                [ "message" => trans($response) ],
                200,
                [ 'Content-Type' => 'application/json' ]
            );
        } else {
            $request->flashOnly('email');
            return redirect($this->redirectPath())
                ->with('status', trans($response));
        }
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendActivationLinkFailedResponse(
        /** @noinspection PhpUnusedParameterInspection */ Request $request,
        $response)
    {
        if ($request->wantsJson()) {
            return response()->json(
                [ "message" => trans($response) ],
                400,
                [ 'Content-Type' => 'application/json' ]
            );
        } else {
            return redirect($this->redirectPath())
                ->withErrors(['email' => trans($response)]);
        }
    }

    /**
     * Get the broker to be used during activation.
     *
     * @return \Codepunk\Activatinator\Contracts\ActivatinatorBroker
     */
    public function broker()
    {
        return Activatinator::broker();
    }
}
