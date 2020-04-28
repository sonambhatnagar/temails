<?php

/*
 * /**
 *
 * @author           sbhatnagar
 * @date             6/1/19
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


$api = app('Dingo\Api\Routing\Router');

$api->version(/**
 * @param $api
 */
    'v1', function ($api) {

    $api->group([
        'namespace'  => 'App\Http\Controllers'
    ], function () use ($api) {
        $api->get('/', 'EmailController@version');
    });


    $api->group([
        'middleware' => 'securityCheck',
        'namespace'  => 'App\Http\Controllers'
    ], function () use ($api) {
        $api->post('/send-email', 'EmailController@queueEmail');
        $api->get('/get-emails/{status?}', 'EmailController@getEmails');
    });

});

