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
    protected $allowedFields    = ['employee_id', 'first_name', 'last_name', 'email', 'phone', 'department_id', 'position_id', 'date_of_birth', 'date_of_joining', 'status', 'account_status', 'user_id', 'approval_notes'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'employee_id'     => 'required|is_unique[employees.employee_id,id,{id}]',
        'first_name'      => 'required|min_length[2]',
        'last_name'       => 'required|min_length[2]',
        'email'           => 'required|valid_email|is_unique[employees.email,id,{id}]',
        'date_of_joining' => 'required|valid_date',
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

