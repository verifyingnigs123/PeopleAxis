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

// Users routes - CRUD Operations
$routes->get('/users', 'Users::index');                  // READ - View all users
$routes->get('/users/create', 'Users::create');          // CREATE - Show create form
$routes->post('/users/store', 'Users::store');          // CREATE - Store new user
$routes->get('/users/edit/(:num)', 'Users::edit/$1');   // UPDATE - Show edit form
$routes->post('/users/update/(:num)', 'Users::update/$1'); // UPDATE - Update user
$routes->post('/users/activate/(:num)', 'Users::activate/$1');   // UPDATE - Activate user
$routes->post('/users/deactivate/(:num)', 'Users::deactivate/$1'); // DELETE - Deactivate user (soft delete)
$routes->delete('/users/delete/(:num)', 'Users::delete/$1'); // DELETE - Hard delete user

// Settings routes
$routes->get('/settings', 'Settings::index');
$routes->post('/settings/update', 'Settings::update');

// Forgot Password routes
$routes->get('/forgot-password', 'Auth::forgotPassword');
$routes->post('/forgot-password', 'Auth::forgotPasswordProcess');
$routes->get('/verify-otp', 'Auth::verifyOtp');
$routes->post('/verify-otp', 'Auth::verifyOtpProcess');
$routes->get('/reset-password', 'Auth::resetPassword');
$routes->post('/reset-password', 'Auth::resetPasswordProcess');
