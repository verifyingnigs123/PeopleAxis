<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\EmployeeModel;
use App\Models\AuditModel;
use App\Controllers\Audit;

class Biometric extends BaseController
{
    protected $attendanceModel;
    protected $employeeModel;
    protected $auditModel;

    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->employeeModel = new EmployeeModel();
        $this->auditModel = new AuditModel();
    }

    public function manualSync()
    {
        // This is a stub method. In production, integrate with device SDK/API.
        $session = session();
        $userId = $session->get('user_id');

        // Example: fetch logs from external API (not implemented)
        // $logs = $this->fetchFromDevice();

        // Log audit entry
        Audit::log($userId, 'SYNC', 'Biometric', 'Manual biometric sync triggered');

        return redirect()->back()->with('success', 'Biometric sync started (stub)');
    }

    /**
     * Show biometric device connection page
     */
    public function connect()
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        // Get device status (placeholder)
        $data['deviceStatus'] = [
            'connected' => false,
            'lastSync' => null,
            'records' => 0
        ];
        
        // Pass lastSync separately for view compatibility
        $data['lastSync'] = $data['deviceStatus']['lastSync'];

        return view('biometric/connect', $data);
    }

    /**
     * Display biometric attendance logs
     */
    public function logs()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        try {
            // Get biometric attendance logs with employee details
            $logs = $this->attendanceModel
                ->select("attendance_logs.*, CONCAT(employees.first_name, ' ', employees.last_name) as name, employees.employee_id")
                ->join('employees', 'employees.id = attendance_logs.employee_id', 'left')
                ->orderBy('attendance_logs.date', 'DESC')
                ->orderBy('attendance_logs.time_in', 'DESC')
                ->paginate(50);

            $data['logs'] = $logs;
            $data['pager'] = $this->attendanceModel->pager;

            return view('biometric/logs', $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to load biometric logs: ' . $e->getMessage());
        }
    }

    // Helper to map biometric logs into attendance records
    protected function mapLogToAttendance($biometricLog)
    {
        // $biometricLog expected: ['biometric_id'=>..., 'timestamp'=>...]
        $employee = $this->employeeModel->where('biometric_id', $biometricLog['biometric_id'])->first();
        if (!$employee) return;

        $date = date('Y-m-d', strtotime($biometricLog['timestamp']));
        $time = date('H:i:s', strtotime($biometricLog['timestamp']));

        // Try find existing record for that day
        $existing = $this->attendanceModel->where('employee_id', $employee->id)->where('date', $date)->first();
        if (!$existing) {
            $this->attendanceModel->insert([
                'employee_id' => $employee->id,
                'date' => $date,
                'time_in' => $time,
                'status' => (strtotime($time) > strtotime('08:00:00')) ? 'Late' : 'Present',
            ]);
        } else {
            // update time_out if empty
            if (empty($existing->time_out)) {
                $this->attendanceModel->update($existing->id, ['time_out' => $time]);
            }
        }
    }
}
