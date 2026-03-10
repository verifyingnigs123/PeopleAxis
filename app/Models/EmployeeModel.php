<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table            = 'employees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['employee_id', 'first_name', 'last_name', 'email', 'phone', 'department_id', 'role_id', 'date_of_birth', 'date_of_joining', 'date_hired', 'status', 'account_status', 'user_id', 'approval_notes', 'biometric_id', 'rate', 'rate_type', 'employment_type'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationMessages = [
        'employee_id' => [
            'required' => 'Employee ID is required.',
            'is_unique' => 'Employee ID already exists.',
        ],
        'first_name' => [
            'required' => 'First name is required.',
            'min_length' => 'First name must be at least 2 characters.',
        ],
        'last_name' => [
            'required' => 'Last name is required.',
            'min_length' => 'Last name must be at least 2 characters.',
        ],
        'email' => [
            'required' => 'Email is required.',
            'valid_email' => 'Please provide a valid email address.',
            'is_unique' => 'Email address already exists.',
        ],
        'date_of_birth' => [
            'valid_date' => 'Please enter a valid date of birth.',
            'check_age' => 'Employee must be at least 18 years old.',
        ],
        'date_of_joining' => [
            'required' => 'Date of joining is required.',
            'valid_date' => 'Please enter a valid date of joining.',
        ],
    ];

    public function getActiveEmployees()
    {
        return $this->where('status', 'active')->findAll();
    }

    public function getEmployeesByDepartment($departmentId)
    {
        return $this->where('department_id', $departmentId)->findAll();
    }

    /**
     * Get pending employee accounts waiting for Super Admin approval
     */
    public function getPendingEmployees()
    {
        return $this->where('account_status', 'pending')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get approved employees with user accounts
     */
    public function getApprovedEmployees()
    {
        return $this->where('account_status', 'approved')
                    ->findAll();
    }

    /**
     * Get rejected employee accounts
     */
    public function getRejectedEmployees()
    {
        return $this->where('account_status', 'rejected')
                    ->findAll();
    }
}

