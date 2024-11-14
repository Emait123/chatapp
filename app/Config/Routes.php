<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index', ['as' => 'chat']);
$routes->get('/login', 'Login::index', ['as' => 'login']);
$routes->post('/login', 'Login::login');
$routes->get('/logout', 'Login::logout', ['as' => 'logout']);
$routes->get('/timeoff', 'TimeoffList::index', ['as' => 'timeofflist']);
$routes->post('/timeoff', 'TimeoffList::fetch');
$routes->post('/home/api', 'Home::fetch');

$routes->get('employeelist', 'Employee::index', ['as' => 'employeelist']);
$routes->post('employeelist', 'Employee::process');
$routes->get('employee-timeoff', 'Employee::timeoffList');

$routes->get('/deletedata', 'DeleteData::index');
$routes->get('/tos', 'DeleteData::index');

$routes->get('/webhook', 'WebHook::index');
$routes->post('/webhook', 'WebHook::postRequest');
$routes->post('/telegram/webhook', 'WebHook::telegram_receiveMessage');

$routes->get('/tool', 'Tool::index');
$routes->post('/tool', 'Tool::processFile');