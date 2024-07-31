<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index', ['as' => 'chat']);
$routes->get('/login', 'Login::index', ['as' => 'login']);
$routes->post('/login', 'Login::login');
$routes->get('/logout', 'Login::logout', ['as' => 'logout']);
$routes->post('/home/api', 'Home::fetch');
