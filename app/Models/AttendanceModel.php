<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table            = 'attendance';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['employee_id', 'attendance_date', 'check_in', 'check_out', 'status', 'remarks'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'employee_id'     => 'required|integer',
        'attendance_date' => 'required|valid_date',
        'status'          => 'required',
    ];

    public function getEmployeeAttendance($employeeId, $month = null)
    {
        $query = $this->where('employee_id', $employeeId);
        
        if ($month) {
            $query->like('attendance_date', $month, 'after');
        }
        
        return $query->orderBy('attendance_date', 'DESC')->findAll();
    }

    public function getDailyAttendance($date)
    {
        return $this->where('attendance_date', $date)->findAll();
    }
}
