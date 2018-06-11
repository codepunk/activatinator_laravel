<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// The explicit 'web' middleware is needed here since Laravel will automatically place routes into 'web'
// middlware if they are in the routes.php file but not elsewhere
Route::group(['middleware' => ['web']], function () {
    Route::post('/activate/send', [
        'uses' => '\Codepunk\Activatinator\Controllers\SendActivationLinkController@sendActivationLinkEmail',
        'as' => 'activation.activate.send',
    ]);

    Route::get('/activate/{token}', [
        'uses' => '\Codepunk\Activatinator\Controllers\ActivateController@showLoginForm',
        'as' => 'activation.activate',
    ]);
});
