<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Home routes
$routes->get('/', 'Home::index');
$routes->get('/home', 'Home::index');

// Auth routes
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::loginProcess');
$routes->get('/logout', 'Auth::logout');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::registerProcess');
$routes->get('/verify-email', 'Auth::verifyEmail');
$routes->post('/verify-email', 'Auth::verifyEmailProcess');
$routes->get('/dashboard', 'Dashboard::index');

// Users routes
$routes->get('/users', 'Users::index');
$routes->post('/users/store', 'Users::store');
$routes->get('/users/edit/(:num)', 'Users::edit/$1');
$routes->post('/users/update/(:num)', 'Users::update/$1');
$routes->delete('/users/delete/(:num)', 'Users::delete/$1');
// Allow GET for delete to avoid missing-route errors when link is accessed directly
$routes->get('/users/delete/(:num)', 'Users::delete/$1');

// Settings routes
$routes->get('/settings', 'Settings::index');
$routes->post('/settings/update', 'Settings::update');