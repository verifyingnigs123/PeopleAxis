<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\EmployeeModel;
use App\Models\AuditModel;

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

        // For now, record audit entry
        $this->auditModel->log($userId, 'Biometric Manual Sync', 'Manual sync triggered');

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

        return view('biometric/connect', $data);
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
