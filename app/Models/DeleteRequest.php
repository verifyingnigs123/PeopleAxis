<?php

namespace App\Models;

use CodeIgniter\Model;

class DeleteRequest extends Model
{
    protected $table            = 'delete_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employee_id',
        'requested_by', 
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id' => 'integer',
        'employee_id' => 'integer',
        'requested_by' => 'integer',
        'reviewed_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'employee_id' => 'required|integer|is_not_unique[employees.id]',
        'requested_by' => 'required|integer|is_not_unique[users.id]',
        'reason' => 'required|min_length[10]|max_length[1000]',
        'status' => 'required|in_list[pending,approved,rejected]',
        'reviewed_by' => 'permit_empty|integer|is_not_unique[users.id]',
        'review_notes' => 'permit_empty|max_length[1000]',
    ];
    protected $validationMessages   = [
        'employee_id' => [
            'required' => 'Employee ID is required.',
            'integer' => 'Employee ID must be a valid integer.',
            'is_not_unique' => 'Employee must exist in the system.',
        ],
        'requested_by' => [
            'required' => 'Requested by user is required.',
            'integer' => 'Requested by must be a valid integer.',
            'is_not_unique' => 'Requesting user must exist in the system.',
        ],
        'reason' => [
            'required' => 'Reason for deletion is required.',
            'min_length' => 'Reason must be at least 10 characters long.',
            'max_length' => 'Reason cannot exceed 1000 characters.',
        ],
        'status' => [
            'required' => 'Status is required.',
            'in_list' => 'Status must be one of: pending, approved, rejected.',
        ],
        'reviewed_by' => [
            'integer' => 'Reviewed by must be a valid integer.',
            'is_not_unique' => 'Reviewing user must exist in the system.',
        ],
        'review_notes' => [
            'max_length' => 'Review notes cannot exceed 1000 characters.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setStatusTimestamp'];
    protected $afterInsert    = ['logAudit'];
    protected $beforeUpdate   = ['setStatusTimestamp'];
    protected $afterUpdate    = ['logAudit'];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Set reviewed_at timestamp when status changes to approved or rejected
     */
    protected function setStatusTimestamp(array $data): array
    {
        if (isset($data['data']['status']) && 
            in_array($data['data']['status'], ['approved', 'rejected'])) {
            $data['data']['reviewed_at'] = date('Y-m-d H:i:s');
        }
        
        return $data;
    }

    /**
     * Log audit trail for delete request actions
     */
    protected function logAudit(array $data): array
    {
        $auditModel = new \App\Models\AuditLog();
        
        $action = isset($data['id']) ? 'updated' : 'created';
        $details = json_encode([
            'delete_request_id' => $data['id'] ?? null,
            'employee_id' => $data['data']['employee_id'] ?? null,
            'status' => $data['data']['status'] ?? 'pending',
            'action' => $action
        ]);
        
        $auditModel->insert([
            'user_id' => session()->get('user_id'),
            'action' => "delete_request_{$action}",
            'table_name' => 'delete_requests',
            'record_id' => $data['id'] ?? null,
            'details' => $details,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'CLI',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        return $data;
    }

    /**
     * Get employee relationship
     */
    public function getEmployee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id');
    }

    /**
     * Get requesting user relationship
     */
    public function getRequestingUser()
    {
        return $this->belongsTo('App\Models\User', 'requested_by');
    }

    /**
     * Get reviewing user relationship
     */
    public function getReviewingUser()
    {
        return $this->belongsTo('App\Models\User', 'reviewed_by');
    }

    /**
     * Get pending requests for Super Admin
     */
    public function getPendingRequests()
    {
        return $this->select('delete_requests.*, employees.first_name, employees.last_name, employees.email, 
                            requester.username as requester_name, requester.name as requester_fullname')
                    ->join('employees', 'employees.id = delete_requests.employee_id')
                    ->join('users as requester', 'requester.id = delete_requests.requested_by')
                    ->where('delete_requests.status', 'pending')
                    ->orderBy('delete_requests.created_at', 'DESC');
    }

    /**
     * Get requests by HR Admin
     */
    public function getRequestsByUser($userId)
    {
        return $this->select('delete_requests.*, employees.first_name, employees.last_name, employees.email')
                    ->join('employees', 'employees.id = delete_requests.employee_id')
                    ->where('delete_requests.requested_by', $userId)
                    ->orderBy('delete_requests.created_at', 'DESC');
    }

    /**
     * Approve delete request and soft delete employee
     */
    public function approveRequest($requestId, $reviewerId, $notes = null)
    {
        $db = \Config\Database::connect();
        
        try {
            $db->transStart();
            
            // Update delete request
            $this->update($requestId, [
                'status' => 'approved',
                'reviewed_by' => $reviewerId,
                'review_notes' => $notes,
                'reviewed_at' => date('Y-m-d H:i:s')
            ]);
            
            // Get employee ID
            $request = $this->find($requestId);
            $employeeId = $request['employee_id'];
            
            // Soft delete employee
            $employeeModel = new \App\Models\Employee();
            $employeeModel->delete($employeeId);
            
            // Create notification
            $notificationModel = new \App\Models\Notification();
            $notificationModel->createNotification(
                $request['requested_by'],
                'Delete Request Approved',
                "Your request to delete employee (ID: {$employeeId}) has been approved.",
                'success',
                '/admin/delete-requests'
            );
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return true;
            
        } catch (\Exception $e) {
            log_message('error', 'Error approving delete request: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject delete request
     */
    public function rejectRequest($requestId, $reviewerId, $notes = null)
    {
        try {
            $this->update($requestId, [
                'status' => 'rejected',
                'reviewed_by' => $reviewerId,
                'review_notes' => $notes,
                'reviewed_at' => date('Y-m-d H:i:s')
            ]);
            
            // Get request details
            $request = $this->find($requestId);
            
            // Create notification
            $notificationModel = new \App\Models\Notification();
            $notificationModel->createNotification(
                $request['requested_by'],
                'Delete Request Rejected',
                "Your request to delete employee (ID: {$request['employee_id']}) has been rejected.",
                'warning'
            );
            
            return true;
            
        } catch (\Exception $e) {
            log_message('error', 'Error rejecting delete request: ' . $e->getMessage());
            return false;
        }
    }
}
