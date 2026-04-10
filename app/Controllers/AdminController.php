<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DeleteRequest;
use App\Models\Notification;
use App\Models\User;

class AdminController extends BaseController
{
    protected $deleteRequestModel;
    protected $notificationModel;
    protected $userModel;

    public function __construct()
    {
        $this->deleteRequestModel = new DeleteRequest();
        $this->notificationModel = new Notification();
        $this->userModel = new User();
    }

    /**
     * Display dashboard for Super Admin
     */
    public function index()
    {
        // Check if user is logged in and has Super Admin role
        if (!session()->get('user_id') || session()->get('role') !== 'super_admin') {
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
        // Check if user is logged in and has Super Admin role
        if (!session()->get('user_id') || session()->get('role') !== 'super_admin') {
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
        // Check if user is logged in and has Super Admin role
        if (!session()->get('user_id') || session()->get('role') !== 'super_admin') {
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
        // Check if user is logged in and has Super Admin role
        if (!session()->get('user_id') || session()->get('role') !== 'super_admin') {
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
        // Check if user is logged in and has Super Admin role
        if (!session()->get('user_id') || session()->get('role') !== 'super_admin') {
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
        if (!session()->get('user_id') || session()->get('role') !== 'super_admin') {
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
        // Check if user is logged in and has Super Admin role
        if (!session()->get('user_id') || session()->get('role') !== 'super_admin') {
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
}
