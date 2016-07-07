<?php

$api = app('Dingo\Api\Routing\Router');


$api->version('v1', function ($api) {
    // Set our namespace for the underlying routes
    $api->group(['namespace' => 'Api\Controllers', 'middleware' => '\Barryvdh\Cors\HandleCors::class'], function ($api) {
        // Login route
        $api->post('login', 'AuthController@authenticate');
        $api->post('register', 'AuthController@register');


        $api->group( [ 'middleware' => 'jwt.auth' ], function ($api) {

            $api->get('users/me', 'AuthController@me');

            $api->get('validate_token', 'AuthController@validateToken');
        });
    });
});
