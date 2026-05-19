<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DeleteRequest;
use App\Models\NotificationModel;
use App\Models\UserModel;
use App\Models\ProfilePhotoModel;

class AdminController extends BaseController
{
    protected $deleteRequestModel;
    protected $notificationModel;
    protected $userModel;

    public function __construct()
    {
        $this->deleteRequestModel = new DeleteRequest();
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
    }

    /**
     * Check whether current session belongs to a Super Admin
     */
    private function isSuperAdmin(): bool
    {
        if (! session()->get('user_id')) {
            return false;
        }

        $role = session()->get('role') ?? '';
        $roleName = session()->get('role_name') ?? '';

        if (in_array(strtolower($role), ['super_admin', 'admin'], true)) {
            return true;
        }

        if (strtolower($roleName) === 'super admin') {
            return true;
        }

        return false;
    }

    /**
     * Display dashboard for Super Admin
     */
    public function index()
    {
        // Check Super Admin access
        if (! $this->isSuperAdmin()) {
            return redirect()->to('/login')->with('error', 'Access denied. Super Admin access required.');
        }

        $data = [
            'title' => 'Super Admin Dashboard',
            'user' => session()->get()
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Display all pending delete requests for Super Admin
     */
    public function deleteRequests()
    {
        // Check Super Admin access
        if (! $this->isSuperAdmin()) {
            return redirect()->to('/login')->with('error', 'Access denied. Super Admin access required.');
        }

        $requests = $this->deleteRequestModel->getPendingRequests()->paginate(10);
        
        $data = [
            'title' => 'Delete Requests - Pending Approval',
            'requests' => $requests,
            'pager' => $this->deleteRequestModel->pager,
            'user' => session()->get()
        ];

        return view('admin/delete_requests', $data);
    }

    /**
     * Show delete request details for review
     */
    public function reviewDeleteRequest($id)
    {
        // Check Super Admin access
        if (! $this->isSuperAdmin()) {
            return redirect()->to('/login')->with('error', 'Access denied. Super Admin access required.');
        }

        $request = $this->deleteRequestModel
            ->select('delete_requests.*, employees.first_name, employees.last_name, employees.email, employees.phone, 
                    employees.position, employees.department, employees.hire_date,
                    requester.username as requester_name, requester.name as requester_fullname')
            ->join('employees', 'employees.id = delete_requests.employee_id')
            ->join('users as requester', 'requester.id = delete_requests.requested_by')
            ->where('delete_requests.id', $id)
            ->first();

        if (!$request) {
            return redirect()->to('/admin/delete-requests')->with('error', 'Delete request not found.');
        }

        // Get employee's related data for comprehensive review
        $db = \Config\Database::connect();
        
        // Check for active records in related tables
        $relatedData = [
            'attendance_count' => $db->table('attendance_logs')->where('employee_id', $request['employee_id'])->countAllResults(),
            'leave_requests_count' => $db->table('leave_requests')->where('employee_id', $request['employee_id'])->countAllResults(),
            'salary_records_count' => $db->table('salaries')->where('employee_id', $request['employee_id'])->countAllResults(),
        ];

        $data = [
            'title' => 'Review Delete Request',
            'request' => $request,
            'related_data' => $relatedData,
            'user' => session()->get()
        ];

        return view('admin/review_delete_request', $data);
    }

    /**
     * Approve delete request
     */
    public function approveDeleteRequest($id)
    {
        // Check Super Admin access
        if (! $this->isSuperAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin access required.'
            ]);
        }

        $request = $this->deleteRequestModel->find($id);

        if (!$request || $request['status'] !== 'pending') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Delete request not found or already processed.'
            ]);
        }

        $reviewNotes = $this->request->getPost('review_notes');
        $reviewerId = session()->get('user_id');

        try {
            $success = $this->deleteRequestModel->approveRequest($id, $reviewerId, $reviewNotes);

            if ($success) {
                // Create real-time notification for HR Admin
                $this->notificationModel->createNotification(
                    $request['requested_by'],
                    'Delete Request Approved',
                    "Your delete request for employee has been approved by Super Admin.",
                    'success',
                    '/delete-requests'
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Delete request approved successfully. Employee has been soft deleted.',
                    'redirect' => '/admin/delete-requests'
                ]);
            } else {
                throw new \Exception('Failed to approve delete request');
            }

        } catch (\Exception $e) {
            log_message('error', 'Error approving delete request: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while approving the delete request. Please try again.'
            ]);
        }
    }

    /**
     * Reject delete request
     */
    public function rejectDeleteRequest($id)
    {
        // Check Super Admin access
        if (! $this->isSuperAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin access required.'
            ]);
        }

        $request = $this->deleteRequestModel->find($id);

        if (!$request || $request['status'] !== 'pending') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Delete request not found or already processed.'
            ]);
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'review_notes' => 'required|min_length[5]|max_length[1000]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $reviewNotes = $this->request->getPost('review_notes');
        $reviewerId = session()->get('user_id');

        try {
            $success = $this->deleteRequestModel->rejectRequest($id, $reviewerId, $reviewNotes);

            if ($success) {
                // Create real-time notification for HR Admin
                $this->notificationModel->createNotification(
                    $request['requested_by'],
                    'Delete Request Rejected',
                    "Your delete request has been rejected by Super Admin. Reason: {$reviewNotes}",
                    'warning',
                    '/delete-requests'
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Delete request rejected successfully.',
                    'redirect' => '/admin/delete-requests'
                ]);
            } else {
                throw new \Exception('Failed to reject delete request');
            }

        } catch (\Exception $e) {
            log_message('error', 'Error rejecting delete request: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while rejecting the delete request. Please try again.'
            ]);
        }
    }

    /**
     * Get all delete requests (including processed ones)
     */
    public function allDeleteRequests()
    {
        // Check if user is logged in and has Super Admin role
            // Check Super Admin access
            if (! $this->isSuperAdmin()) {
                return redirect()->to('/login')->with('error', 'Access denied. Super Admin access required.');
            }

        $status = $this->request->getGet('status', 'all');
        $requests = $this->deleteRequestModel
            ->select('delete_requests.*, employees.first_name, employees.last_name, employees.email, 
                    requester.username as requester_name, requester.name as requester_fullname,
                    reviewer.name as reviewer_name')
            ->join('employees', 'employees.id = delete_requests.employee_id')
            ->join('users as requester', 'requester.id = delete_requests.requested_by')
            ->join('users as reviewer', 'reviewer.id = delete_requests.reviewed_by', 'left')
            ->when($status !== 'all', function($query) use ($status) {
                return $query->where('delete_requests.status', $status);
            })
            ->orderBy('delete_requests.created_at', 'DESC')
            ->paginate(10);

        $data = [
            'title' => 'All Delete Requests',
            'requests' => $requests,
            'pager' => $this->deleteRequestModel->pager,
            'current_status' => $status,
            'user' => session()->get()
        ];

        return view('admin/all_delete_requests', $data);
    }

    /**
     * Get notifications for real-time updates
     */
    public function getNotifications()
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return $this->response->setJSON([]);
        }

        $userId = session()->get('user_id');
        $notifications = $this->notificationModel
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        return $this->response->setJSON([
            'notifications' => $notifications,
            'unread_count' => count($notifications)
        ]);
    }

    /**
     * View all profile photos organized by user role
     */
    public function profilePhotos()
    {
        // Check if user is logged in and has admin role
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $roleName = session()->get('role_name') ?? '';
        if (!in_array($roleName, ['Super Admin', 'HR Admin'])) {
            return redirect()->back()->with('error', 'Access denied. Admin access required.');
        }

        $profilePhotoModel = new ProfilePhotoModel();
        $db = \Config\Database::connect();

        // Get counts by role
        $roles = ['Super Admin', 'HR Admin', 'Manager', 'Employee'];
        $photosByRole = [];
        $statistics = [];

        foreach ($roles as $role) {
            $count = $profilePhotoModel->getCountByRole($role);
            $statistics[$role] = $count;

            // Get photos for this role (limit to 20 per role for display)
            $photos = $db->table('profile_photos')
                ->select('profile_photos.*, users.name, users.email, roles.name as role_name')
                ->join('users', 'users.id = profile_photos.user_id', 'left')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('profile_photos.deleted_at', null)
                ->where('users.deleted_at', null)
                ->where('roles.name', $role)
                ->orderBy('profile_photos.uploaded_at', 'DESC')
                ->limit(20)
                ->get()
                ->getResultArray();

            $photosByRole[$role] = $photos;
        }

        $data = [
            'title' => 'Profile Photos Gallery',
            'statistics' => $statistics,
            'photosByRole' => $photosByRole,
            'user' => session()->get(),
        ];

        return view('admin/profile_photos', $data);
    }

    /**
     * Get profile photos statistics API
     */
    public function profilePhotosStats()
    {
        // Check if user is logged in and has admin role
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'error' => 'Unauthorized'
            ]);
        }

        $roleName = session()->get('role_name') ?? '';
        if (!in_array($roleName, ['Super Admin', 'HR Admin'])) {
            return $this->response->setStatusCode(403)->setJSON([
                'error' => 'Access denied'
            ]);
        }

        $profilePhotoModel = new ProfilePhotoModel();
        $db = \Config\Database::connect();

        $roles = ['Super Admin', 'HR Admin', 'Manager', 'Employee'];
        $statistics = [
            'total' => 0,
            'by_role' => [],
            'recent_uploads' => [],
        ];

        foreach ($roles as $role) {
            $count = $profilePhotoModel->getCountByRole($role);
            $statistics['by_role'][$role] = $count;
            $statistics['total'] += $count;
        }

        // Get recent 10 uploads
        $recent = $db->table('profile_photos')
            ->select('profile_photos.*, users.name, roles.name as role_name')
            ->join('users', 'users.id = profile_photos.user_id', 'left')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('profile_photos.deleted_at', null)
            ->where('users.deleted_at', null)
            ->orderBy('profile_photos.uploaded_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $statistics['recent_uploads'] = $recent;

        return $this->response->setJSON($statistics);
    }

    /**
     * Render MFA management UI for Super Admin
     */
    public function mfaManage()
    {
        if (! $this->isSuperAdmin()) {
            return redirect()->to('/login')->with('error', 'Access denied. Super Admin access required.');
        }

        $users = $this->userModel->getMfaStatuses();

        $data = [
            'title' => 'MFA Management',
            'users' => $users,
            'user'  => session()->get()
        ];

        return view('admin/mfa_manage', $data);
    }

    /**
     * Delete a profile photo (for admins)
     */
    public function deleteProfilePhoto($photoId)
    {
        // Check if user is logged in and has admin role
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $roleName = session()->get('role_name') ?? '';
        if (!in_array($roleName, ['Super Admin'])) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Only Super Admins can delete profile photos'
            ]);
        }

        try {
            $profilePhotoModel = new ProfilePhotoModel();
            $profilePhoto = $profilePhotoModel->find($photoId);

            if (!$profilePhoto) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Profile photo not found'
                ]);
            }

            // Hard delete (file and record)
            $profilePhotoModel->hardDeletePhoto($photoId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Profile photo deleted successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Delete profile photo error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($notificationId)
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return $this->response->setJSON(['success' => false]);
        }

        try {
            $this->notificationModel->update($notificationId, ['is_read' => 1]);
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false]);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        // Check Super Admin access
        if (! $this->isSuperAdmin()) {
            return $this->response->setJSON([]);
        }

        $stats = [
            'pending_delete_requests' => $this->deleteRequestModel->where('status', 'pending')->countAllResults(),
            'approved_today' => $this->deleteRequestModel
                ->where('status', 'approved')
                ->where('DATE(reviewed_at)', date('Y-m-d'))
                ->countAllResults(),
            'rejected_today' => $this->deleteRequestModel
                ->where('status', 'rejected')
                ->where('DATE(reviewed_at)', date('Y-m-d'))
                ->countAllResults(),
            'total_requests' => $this->deleteRequestModel->countAllResults(),
        ];

        return $this->response->setJSON($stats);
    }

    /**
     * Return MFA status for all users (Super Admin only)
     */
    public function mfaStatus()
    {
        if (! $this->isSuperAdmin()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin access required.'
            ]);
        }

        $users = $this->userModel->getMfaStatuses();

        $payload = array_map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'mfa_enabled' => (int) $u->mfa_enabled,
                'mfa_status' => ((int) $u->mfa_enabled === 1) ? 'Enabled' : 'Disabled',
                'mfa_method' => $u->mfa_method,
                'is_active' => (int) $u->is_active,
            ];
        }, $users);

        return $this->response->setJSON([
            'success' => true,
            'users' => $payload,
        ]);
    }

    /**
     * Enable or disable MFA for a specific user (Super Admin only)
     */
    public function setMfaStatus($id)
    {
        if (! $this->isSuperAdmin()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin access required.'
            ]);
        }

        $action = $this->request->getPost('action'); // expected 'enable' or 'disable' or '1'/'0'

        if (empty($action)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Missing action parameter'
            ]);
        }

        $enable = false;
        if (in_array(strtolower($action), ['enable', '1', 'true'], true)) {
            $enable = true;
        }

        // Prevent modifying the Super Admin account
        try {
            $roleName = $this->userModel->getRoleName((int) $id);
            if ($roleName && strtolower(trim($roleName)) === 'super admin') {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Cannot modify MFA for the Super Admin account.'
                ]);
            }
        } catch (\Throwable $e) {
            // if role lookup fails, continue but be cautious
        }

        // Update user record
        try {
            $method = $this->request->getPost('method') ?? 'email';
            $updated = $this->userModel->setMfaEnabled((int) $id, $enable, $method);

            if ($updated === false) {
                throw new \Exception('Failed to update user record');
            }

            // If disabling, revoke trusted devices
            if (!$enable) {
                $deviceTokenModel = model('DeviceTokenModel');
                if ($deviceTokenModel) {
                    $deviceTokenModel->revokeAllDevices((int) $id);
                }
            }

            // Write audit log of admin action
            try {
                $auditModel = new \App\Models\AuditModel();
                $adminId = session()->get('user_id');
                $user = $this->userModel->find($id);
                $desc = ($enable ? 'Enabled' : 'Disabled') . ' MFA for user: ' . ($user->email ?? $id);
                $auditModel->log($adminId, 'Admin MFA Change', $desc);
            } catch (\Throwable $e) {
                log_message('error', 'Failed to write admin MFA audit log: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'MFA ' . ($enable ? 'enabled' : 'disabled') . ' for user.'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Admin setMfaStatus error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating MFA status.'
            ]);
        }
    }
}
