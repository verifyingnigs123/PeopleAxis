<?php

namespace App\Models;

use CodeIgniter\Model;

class SalaryModel extends Model
{
    protected $table            = 'salaries';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employee_id', 'base_salary', 'allowances', 'deductions',
        'sss_contribution', 'philhealth_contribution', 'pagibig_contribution', 'withholding_tax',
        'net_salary', 'effective_from',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'employee_id'   => 'required|integer',
        'base_salary'   => 'required|numeric',
        'net_salary'    => 'required|numeric',
        'effective_from' => 'required|valid_date',
    ];

    public function getEmployeeSalary($employeeId)
    {
        return $this->where('employee_id', $employeeId)
                    ->orderBy('effective_from', 'DESC')
                    ->first();
    }

    public function getEmployeeSalaryHistory($employeeId)
    {
        return $this->where('employee_id', $employeeId)
                    ->orderBy('effective_from', 'DESC')
                    ->findAll();
    }
}
