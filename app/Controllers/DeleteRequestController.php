<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DeleteRequest;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\User;

class DeleteRequestController extends BaseController
{
    protected $deleteRequestModel;
    protected $employeeModel;
    protected $notificationModel;
    protected $userModel;

    public function __construct()
    {
        $this->deleteRequestModel = new DeleteRequest();
        $this->employeeModel = new Employee();
        $this->notificationModel = new Notification();
        $this->userModel = new User();
    }

    /**
     * Display list of delete requests for HR Admin
     */
    public function index()
    {
        // Check if user is logged in and has HR Admin role
        if (!session()->get('user_id') || session()->get('role') !== 'hr_admin') {
            return redirect()->to('/login')->with('error', 'Access denied. HR Admin access required.');
        }

        $userId = session()->get('user_id');
        $requests = $this->deleteRequestModel->getRequestsByUser($userId)->paginate(10);
        
        $data = [
            'title' => 'Delete Requests',
            'requests' => $requests,
            'pager' => $this->deleteRequestModel->pager,
            'user' => session()->get()
        ];

        return view('delete_requests/index', $data);
    }

    /**
     * Show form to create new delete request
     */
    public function create()
    {
        // Check if user is logged in and has HR Admin role
        if (!session()->get('user_id') || session()->get('role') !== 'hr_admin') {
            return redirect()->to('/login')->with('error', 'Access denied. HR Admin access required.');
        }

        // Get active employees for dropdown
        $employees = $this->employeeModel->where('account_status', 'active')->findAll();

        $data = [
            'title' => 'Create Delete Request',
            'employees' => $employees,
            'user' => session()->get()
        ];

        return view('delete_requests/create', $data);
    }

    /**
     * Store new delete request
     */
    public function store()
    {
        // Check if user is logged in and has HR Admin role
        if (!session()->get('user_id') || session()->get('role') !== 'hr_admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied. HR Admin access required.'
            ]);
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'employee_id' => 'required|integer|is_not_unique[employees.id]',
            'reason' => 'required|min_length[10]|max_length[1000]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $employeeId = $this->request->getPost('employee_id');
        $reason = $this->request->getPost('reason');
        $requestedBy = session()->get('user_id');

        // Check if there's already a pending request for this employee
        $existingRequest = $this->deleteRequestModel
            ->where('employee_id', $employeeId)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'A pending delete request already exists for this employee.'
            ]);
        }

        try {
            // Create delete request
            $requestId = $this->deleteRequestModel->insert([
                'employee_id' => $employeeId,
                'requested_by' => $requestedBy,
                'reason' => $reason,
                'status' => 'pending'
            ]);

            if (!$requestId) {
                throw new \Exception('Failed to create delete request');
            }

            // Get employee details for notification
            $employee = $this->employeeModel->find($employeeId);
            $requestingUser = $this->userModel->find($requestedBy);

            // Create notification for Super Admin
            $superAdmins = $this->userModel->where('role', 'super_admin')->findAll();
            
            foreach ($superAdmins as $admin) {
                $this->notificationModel->createNotification(
                    $admin['id'],
                    'New Delete Request',
                    "HR Admin {$requestingUser['name']} has requested to delete employee {$employee['first_name']} {$employee['last_name']}.",
                    'danger',
                    '/admin/delete-requests'
                );
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Delete request submitted successfully. Super Admin has been notified.',
                'redirect' => '/delete-requests'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error creating delete request: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while creating the delete request. Please try again.'
            ]);
        }
    }

    /**
     * Show delete request details
     */
    public function show($id)
    {
        // Check if user is logged in and has HR Admin role
        if (!session()->get('user_id') || session()->get('role') !== 'hr_admin') {
            return redirect()->to('/login')->with('error', 'Access denied. HR Admin access required.');
        }

        $request = $this->deleteRequestModel
            ->select('delete_requests.*, employees.first_name, employees.last_name, employees.email, employees.phone')
            ->join('employees', 'employees.id = delete_requests.employee_id')
            ->where('delete_requests.id', $id)
            ->where('delete_requests.requested_by', session()->get('user_id'))
            ->first();

        if (!$request) {
            return redirect()->to('/delete-requests')->with('error', 'Delete request not found.');
        }

        $data = [
            'title' => 'Delete Request Details',
            'request' => $request,
            'user' => session()->get()
        ];

        return view('delete_requests/show', $data);
    }

    /**
     * Get employee details for AJAX request
     */
    public function getEmployeeDetails($employeeId)
    {
        // Check if user is logged in and has HR Admin role
        if (!session()->get('user_id') || session()->get('role') !== 'hr_admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied.'
            ]);
        }

        $employee = $this->employeeModel->find($employeeId);

        if (!$employee) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Employee not found.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'employee' => [
                'id' => $employee['id'],
                'first_name' => $employee['first_name'],
                'last_name' => $employee['last_name'],
                'email' => $employee['email'],
                'phone' => $employee['phone'],
                'position' => $employee['position'],
                'department' => $employee['department'],
                'hire_date' => $employee['hire_date']
            ]
        ]);
    }

    /**
     * Cancel pending delete request
     */
    public function cancel($id)
    {
        // Check if user is logged in and has HR Admin role
        if (!session()->get('user_id') || session()->get('role') !== 'hr_admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied.'
            ]);
        }

        $request = $this->deleteRequestModel
            ->where('id', $id)
            ->where('requested_by', session()->get('user_id'))
            ->where('status', 'pending')
            ->first();

        if (!$request) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Delete request not found or cannot be cancelled.'
            ]);
        }

        try {
            $this->deleteRequestModel->delete($id);

            // Notify Super Admins about cancellation
            $superAdmins = $this->userModel->where('role', 'super_admin')->findAll();
            $requestingUser = $this->userModel->find(session()->get('user_id'));
            
            foreach ($superAdmins as $admin) {
                $this->notificationModel->createNotification(
                    $admin['id'],
                    'Delete Request Cancelled',
                    "HR Admin {$requestingUser['name']} has cancelled their delete request.",
                    'info'
                );
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Delete request cancelled successfully.'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error cancelling delete request: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while cancelling the delete request.'
            ]);
        }
    }

    /**
     * Get pending requests count for dashboard
     */
    public function getPendingCount()
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return $this->response->setJSON(['count' => 0]);
        }

        $count = 0;
        
        if (session()->get('role') === 'hr_admin') {
            // Count user's pending requests
            $count = $this->deleteRequestModel
                ->where('requested_by', session()->get('user_id'))
                ->where('status', 'pending')
                ->countAllResults();
        } elseif (session()->get('role') === 'super_admin') {
            // Count all pending requests for Super Admin
            $count = $this->deleteRequestModel
                ->where('status', 'pending')
                ->countAllResults();
        }

        return $this->response->setJSON(['count' => $count]);
    }
}
