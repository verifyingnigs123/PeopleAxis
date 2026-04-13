<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaveModel extends Model
{
    protected $table            = 'leave_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['employee_id', 'leave_type', 'start_date', 'end_date', 'number_of_days', 'reason', 'status', 'approved_by_manager', 'approved_by_hr', 'early_returned_at'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'employee_id'    => 'required|integer',
        'leave_type'     => 'required',
        'start_date'     => 'required|valid_date',
        'end_date'       => 'required|valid_date',
        'number_of_days' => 'required|numeric',
    ];

    public function getEmployeeLeaves($employeeId)
    {
        return $this->where('employee_id', $employeeId)->orderBy('start_date', 'DESC')->findAll();
    }

    public function getPendingLeaves()
    {
        return $this->where('status', 'pending')->findAll();
    }
}
