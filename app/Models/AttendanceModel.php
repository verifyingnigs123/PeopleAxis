<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table            = 'attendance_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['employee_id', 'rfid_number', 'date', 'time_in', 'time_out', 'status'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'employee_id' => 'required|integer',
        'date'        => 'required|valid_date',
        'status'      => 'required',
    ];

    public function getEmployeeAttendance($employeeId, $month = null)
    {
        $query = $this->where('employee_id', $employeeId);

        if ($month) {
            $query->like('date', $month, 'after');
        }

        return $query->orderBy('date', 'DESC')->findAll();
    }

    public function getDailyAttendance($date)
    {
        return $this->where('date', $date)->findAll();
    }

    public function getSummary()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('attendance_logs');
        $totalPresent = $builder->where('status', 'Present')->countAllResults(false);
        $totalLate = $builder->where('status', 'Late')->countAllResults(false);
        $totalAbsent = $builder->where('status', 'Absent')->countAllResults(false);

        return [
            'present' => $totalPresent,
            'late' => $totalLate,
            'absent' => $totalAbsent,
        ];
    }

    public function getTeamAttendance($teamEmployees)
    {
        $ids = array_column($teamEmployees, 'id');
        if (empty($ids)) return [];
        return $this->whereIn('employee_id', $ids)->findAll();
    }
}
