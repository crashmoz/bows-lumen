<?php

use Illuminate\Support\Str;
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
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
// header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, X-Auth-Token, Origin, Authorization');

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/key', function() {
	return Str::random(32);
});

$router->group(['middleware' => 'auth','prefix' => 'api'], function ($router) 
{
    $router->get('me', 'AuthController@me');
});

// $router->group(['prefix' => 'api'], function () use ($router) 
// {
//    $router->post('register', 'AuthController@register');
   $router->post('login', 'AuthController@loginJwt');
// });

$router->get('foo', function () {
    return 'Hello World';
});

// Route::prefix('auth')->group(function() {
// 	Route::post('login', 'AuthController@login');
// 	Route::post('register', 'AuthController@register');
// 	Route::post('logout', 'AuthController@logout')->middleware('auth:api');
// });

$router->get('products', ['middleware' => 'auth', 'uses' => 'ProductController@index']);
// $router->get('products', 'ProductController@index');
$router->get('tables', 'TableController@index');
$router->get('order', 'OrderController@index');
$router->post('order', 'OrderController@store');