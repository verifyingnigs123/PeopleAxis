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
$routes->get('/leaves', 'Leaves::index');
$routes->get('/leaves/create', 'Leaves::create');
$routes->post('/leaves/submit', 'Leaves::submit');
$routes->post('/leaves/approve-manager/(:num)', 'Leaves::approveByManager/$1');
$routes->post('/leaves/approve-hr/(:num)', 'Leaves::approveByHR/$1');
$routes->post('/leaves/reject/(:num)', 'Leaves::reject/$1');

// Biometric
$routes->get('/biometric/connect', 'Biometric::connect');
$routes->post('/biometric/manual-sync', 'Biometric::manualSync');

// Users routes - CRUD Operations
$routes->get('/users', 'Users::index');
$routes->get('/users/create', 'Users::create');
$routes->post('/users/store', 'Users::store');
$routes->get('/users/edit/(:num)', 'Users::edit/$1');
$routes->post('/users/update/(:num)', 'Users::update/$1');
$routes->post('/users/activate/(:num)', 'Users::activate/$1');
$routes->post('/users/deactivate/(:num)', 'Users::deactivate/$1');
$routes->post('/users/delete/(:num)', 'Users::delete/$1');
$routes->post('/users/restore/(:num)', 'Users::restore/$1');

// Roles routes
$routes->get('/roles', 'Roles::index');
$routes->get('/roles/create', 'Roles::create');
$routes->post('/roles/store', 'Roles::store');
$routes->get('/roles/edit/(:num)', 'Roles::edit/$1');
$routes->post('/roles/update/(:num)', 'Roles::update/$1');
$routes->post('/roles/delete/(:num)', 'Roles::delete/$1');
$routes->post('/roles/restore/(:num)', 'Roles::restore/$1');
$routes->get('/roles/getRole/(:num)', 'Roles::getRole/$1');

// Employees
$routes->get('/employees', 'Employees::index');
$routes->get('/employees/salary', 'Employees::salary');
$routes->post('/employees/salary/update', 'Employees::updateSalary');

// Settings routes
$routes->get('/settings', 'Settings::index');
$routes->post('/settings/update', 'Settings::update');

// Profile routes
$routes->get('/profile', 'Dashboard::profile');
$routes->post('/profile/update', 'Dashboard::updateProfile');

// Attendance routes
$routes->get('/attendance', 'Attendance::index');
$routes->get('/attendance/logs', 'Attendance::logs');
$routes->get('/attendance/team', 'Attendance::team');

// Activity Logs / Audit routes
$routes->get('/activity-logs', 'Audit::index');
$routes->get('/audit', 'Audit::index');

// Reports routes
$routes->get('/reports', 'Reports::index');
$routes->get('/reports/(:any)', 'Reports::view/$1');
$routes->post('/reports/generate', 'Reports::generate');

// Forgot Password routes
$routes->get('/forgot-password', 'Auth::forgotPassword');
$routes->post('/forgot-password', 'Auth::forgotPasswordProcess');
$routes->get('/verify-otp', 'Auth::verifyOtp');
$routes->post('/verify-otp', 'Auth::verifyOtpProcess');
$routes->get('/reset-password', 'Auth::resetPassword');
$routes->post('/reset-password', 'Auth::resetPasswordProcess');
