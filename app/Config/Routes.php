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
$routes->get('/dashboard', 'Dashboard::index');

// Leaves
$routes->post('/leaves/submit', 'Leaves::submit');
$routes->post('/leaves/approve-manager/(:num)', 'Leaves::approveByManager/$1');
$routes->post('/leaves/approve-hr/(:num)', 'Leaves::approveByHR/$1');
$routes->post('/leaves/reject/(:num)', 'Leaves::reject/$1');

// Biometric
$routes->post('/biometric/manual-sync', 'Biometric::manualSync');

// Users routes
$routes->get('/users', 'Users::index');
$routes->post('/users/store', 'Users::store');
$routes->get('/users/edit/(:num)', 'Users::edit/$1');
$routes->post('/users/update/(:num)', 'Users::update/$1');
$routes->post('/users/activate/(:num)', 'Users::activate/$1');
$routes->post('/users/deactivate/(:num)', 'Users::deactivate/$1');

// Settings routes
$routes->get('/settings', 'Settings::index');
$routes->post('/settings/update', 'Settings::update');