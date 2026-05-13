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
$routes->get('/leaves/team', 'Leaves::team');
$routes->get('/leaves/create', 'Leaves::create');
$routes->post('/leaves/store', 'Leaves::store');
$routes->post('/leaves/submit', 'Leaves::submit');
$routes->post('/leaves/approve-manager/(:num)', 'Leaves::approveByManager/$1');
$routes->post('/leaves/approve-hr/(:num)', 'Leaves::approveByHR/$1');
$routes->post('/leaves/approveByHR/(:num)', 'Leaves::approveByHR/$1');
$routes->post('/leaves/reject/(:num)', 'Leaves::reject/$1');
$routes->post('/leaves/emergency-back/(:num)', 'Leaves::emergencyBack/$1');
$routes->get('/leaves/hr-summary', 'Leaves::hrSummary');

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
$routes->get('/roles/getDetails/(:num)', 'Roles::getDetails/$1');

// Employees
$routes->get('/employees', 'Employees::index');
$routes->get('/employee', 'Employees::index');
$routes->get('/employee/pending-approvals', 'Employees::pendingApprovals');
$routes->get('/employee/create', 'Employees::create');
$routes->post('/employee/store', 'Employees::store');
$routes->get('/employee/show/(:num)', 'Employees::show/$1');
$routes->get('/employee/edit/(:num)', 'Employees::edit/$1');
$routes->get('/employee/get/(:num)', 'Employees::getEmployee/$1');
$routes->post('/employee/update/(:num)', 'Employees::update/$1');
$routes->post('/employee/re-apply/(:num)', 'Employees::reApply/$1');
$routes->post('/employee/delete/(:num)', 'Employees::delete/$1');
$routes->post('/employee/request-delete/(:num)', 'Employees::requestDelete/$1');
$routes->get('/employee/confirm-delete/(:num)', 'Employees::confirmDelete/$1');
$routes->post('/employee/confirm-delete/(:num)', 'Employees::delete/$1');
$routes->post('/employee/reject-delete/(:num)', 'Employees::rejectDelete/$1');
$routes->get('/employee/review/(:num)', 'Employees::review/$1');
$routes->post('/employee/approve-account/(:num)', 'Employees::approveAccount/$1');
$routes->post('/employee/reject-account/(:num)', 'Employees::rejectAccount/$1');
$routes->get('/employees/salary', 'Employees::salary');
$routes->get('/salary_rates', 'Employees::salary'); // Alias for the old link
$routes->post('/employees/salary/update', 'Employees::updateSalary');

// Settings routes
$routes->get('/settings', 'Settings::index');
$routes->post('/settings/update', 'Settings::update');

// Profile routes
$routes->get('/profile', 'Dashboard::profile');
$routes->post('/profile/update', 'Dashboard::updateProfile');
$routes->post('/profile/remove-photo', 'Dashboard::removeProfilePhoto');
$routes->get('/profile/check-status', 'Dashboard::checkProfilePhotoStatus');

// Attendance routes
$routes->get('/attendance', 'Attendance::index');
$routes->get('/attendance/logs', 'Attendance::logs');
$routes->get('/attendance/team', 'Attendance::team');
$routes->get('/attendance/now', 'Attendance::now');
$routes->get('/attendance/check-in', 'Attendance::checkIn');
$routes->get('/attendance/check-out', 'Attendance::checkOut');
$routes->get('/attendance/break-out', 'Attendance::breakOut');
$routes->get('/attendance/break-in', 'Attendance::breakIn');

// Activity Logs / Audit routes
$routes->get('/activity-logs', 'Audit::index');
$routes->get('/audit', 'Audit::index');

// Reports routes
$routes->get('/reports', 'Reports::index');
$routes->get('/reports/attendance', 'Reports::attendance');
$routes->get('/reports/team', 'Reports::team');
$routes->get('/reports/generate/employee', 'Reports::generateEmployee');
$routes->get('/reports/generate', 'Reports::generate');
$routes->get('/reports/generate/attendance', 'Reports::generateAttendance');
$routes->get('/reports/export/attendance-excel', 'Reports::exportAttendanceExcel');
$routes->get('/reports/generate/leave', 'Reports::generateLeave');
$routes->get('/reports/generate/salary', 'Reports::generateSalary');
$routes->get('/reports/export/salary-excel', 'Reports::exportSalaryExcel');
$routes->get('/reports/generate/department', 'Reports::generateDepartment');
$routes->get('/reports/export/department-excel', 'Reports::exportDepartmentExcel');
$routes->get('/reports/generate/(:any)', 'Reports::generate/$1');
$routes->get('/reports/(:any)', 'Reports::view/$1');
$routes->post('/reports/generate', 'Reports::generate');
$routes->post('/reports/generate/attendance', 'Reports::attendance');
$routes->post('/reports/generate/(:any)', 'Reports::generate/$1');

// Notification routes (API)
$routes->get('/api/notifications', 'Notification::getNotifications');
$routes->get('/api/notifications/stream', 'Notification::stream');
$routes->get('/api/notifications/unread-count', 'Notification::getUnreadCount');
$routes->post('/api/notifications/(:num)/read', 'Notification::markAsRead/$1');
$routes->post('/api/notifications/mark-all-read', 'Notification::markAllAsRead');
$routes->post('/api/notifications/(:num)/delete', 'Notification::delete/$1');
$routes->post('/api/notifications/delete-all', 'Notification::deleteAll');
$routes->delete('/api/notifications/(:num)', 'Notification::delete/$1');

// Delete Request routes for HR Admin
$routes->get('/delete-requests', 'DeleteRequestController::index');
$routes->get('/delete-requests/create', 'DeleteRequestController::create');
$routes->post('/delete-requests/store', 'DeleteRequestController::store');
$routes->get('/delete-requests/(:num)', 'DeleteRequestController::show/$1');
$routes->get('/delete-requests/get-employee/(:num)', 'DeleteRequestController::getEmployeeDetails/$1');
$routes->post('/delete-requests/cancel/(:num)', 'DeleteRequestController::cancel/$1');
$routes->get('/delete-requests/get-pending-count', 'DeleteRequestController::getPendingCount');

// Admin routes for Super Admin
$routes->get('/admin', 'AdminController::index');
$routes->get('/admin/delete-requests', 'AdminController::deleteRequests');
$routes->get('/admin/review-delete-request/(:num)', 'AdminController::reviewDeleteRequest/$1');
$routes->post('/admin/approve-delete-request/(:num)', 'AdminController::approveDeleteRequest/$1');
$routes->post('/admin/reject-delete-request/(:num)', 'AdminController::rejectDeleteRequest/$1');
$routes->get('/admin/all-delete-requests', 'AdminController::allDeleteRequests');
$routes->get('/admin/notifications', 'AdminController::getNotifications');
$routes->post('/admin/mark-notification-read/(:num)', 'AdminController::markNotificationRead/$1');
$routes->get('/admin/dashboard-stats', 'AdminController::getDashboardStats');

// Admin Profile Photos routes
$routes->get('/admin/profile-photos', 'AdminController::profilePhotos');
$routes->get('/admin/profile-photos/stats', 'AdminController::profilePhotosStats');
$routes->post('/admin/profile-photos/delete/(:num)', 'AdminController::deleteProfilePhoto/$1');

// Forgot Password routes
$routes->get('/forgot-password', 'Auth::forgotPassword');
$routes->post('/forgot-password', 'Auth::forgotPasswordProcess');
$routes->get('/verify-otp', 'Auth::verifyOtp');
$routes->post('/verify-otp', 'Auth::verifyOtpProcess');
$routes->get('/reset-password', 'Auth::resetPassword');
$routes->post('/reset-password', 'Auth::resetPasswordProcess');
