<?php

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/obtener_dependencias', 'DependenciaController@obtener_dependencias');

$router->post('/registrar', 'RegistroController@registrar');

$router->get('/obtener_solicitudes', 'SolicitudesController@obtener_solicitudes');

$router->post('/detalle_solicitud', 'SolicitudesController@detalle_solicitud');

$router->post('/actualizar_solicitud', 'SolicitudesController@actualizar_solicitud');

$router->post('/login', 'LoginController@login');

$router->post('/recover_password', 'LoginController@recover_password');