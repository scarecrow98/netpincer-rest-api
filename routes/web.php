<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group([ 'prefix' => 'api/v1' ], function() use($router) {
    //partners
    $router->post('partners/list', 'PartnerController@list');
    $router->get('partners/partner-type-list', 'PartnerController@partnerTypeList');
    $router->post('partners/registrate', 'PartnerController@registrate');
    $router->get('partners/profile/{id}', 'PartnerController@profile');
    $router->get('partners/{id}/category-list', 'PartnerController@categoryList');
    $router->post('partners/geotest', 'PartnerController@geotest');

    //users
    $router->post('user/registrate', 'UserController@registrate');
    $router->post('user/login', 'UserController@login');

    //product allergens
    $router->get('general/allergens/list', 'GeneralController@allergenList');

    //auth
    $router->post('auth/login/user', 'AuthController@loginUser');
    $router->post('auth/login/partner', 'AuthController@loginPartner');

    $router->get('auth-test', function() {
        return auth()->guard('user')->user();
    });
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});
