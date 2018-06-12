<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Activatinator Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default user activation options for your
    | application. You may change these defaults as required, but as
    | is, they are a perfect start for just about any application.
    |
    */

    'defaults' => [
        'activations' => 'users',
    ],

    /*
	|--------------------------------------------------------------------------
	| Activating Accounts
	|--------------------------------------------------------------------------
	|
	| You may specify multiple activation configurations if you have more
	| than one user table/model in the application, and you want to have
	| separate activation settings based on the specific user types.
	|
	| The expire time is the number of minutes that the reset token should be
	| considered valid. This security feature keeps tokens short-lived so
	| they have less time to be guessed. You may change this as needed.
	|
	*/

    'activations' => [
        'users' => [
            'provider' => 'users',
            'table' => 'activation_requests',
            'expire' => 60,
        ],
    ],
];
