<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['namespace' => 'Api\Controllers','middleware' => '\Barryvdh\Cors\HandleCors::class'],
        function ($api) {
    
        $api->post('auth/{provider}', 'SocialController@getSocialAuth');

        // Login route
        $api->post('login', 'AuthController@authenticate');
        $api->post('register', 'AuthController@register');

        $api->post('reset', 'AuthController@reset');

        $api->post('users/{id}/password', 'AuthController@changePassword');

        $api->group(['middleware' => 'jwt.auth'], function ($api) {

            $api->put('users/{id}', 'UsersController@update');
            $api->get('users/me', 'AuthController@me');
            
            $api->get('propiedades/favoritos', 'FavoritosPropiedadesController@index');

            $api->post('propiedades/{id}/favoritos', 'FavoritosPropiedadesController@toggle');

            $api->get('propiedades/favoritos/count', 'FavoritosPropiedadesController@count');

            $api->get('validate_token', 'AuthController@validateToken');
        });
    });
});
