<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TodayTeamAttendanceSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

        $candidate = $db->table('employees')
            ->select('employees.id, employees.employee_id, employees.first_name, employees.last_name, employees.rfid_number, departments.manager_id')
            ->join('departments', 'departments.id = employees.department_id', 'inner')
            ->where('departments.manager_id IS NOT NULL', null, false)
            ->where('employees.user_id != departments.manager_id', null, false)
            ->groupStart()
                ->where('employees.status', 'active')
                ->orWhere('employees.status IS NULL', null, false)
                ->orWhere('employees.status', '')
            ->groupEnd()
            ->groupStart()
                ->where('employees.account_status', 'approved')
                ->orWhere('employees.account_status IS NULL', null, false)
            ->groupEnd()
            ->orderBy('employees.id', 'ASC')
            ->get()
            ->getRowArray();

        if (! $candidate) {
            echo "No eligible team employee found under a manager department.\n";
            return;
        }

        $employeeId = (int) ($candidate['id'] ?? 0);
        if ($employeeId <= 0) {
            echo "Unable to resolve employee id.\n";
            return;
        }

        $existing = $db->table('attendance_logs')
            ->where('employee_id', $employeeId)
            ->where('date', $today)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        $rfid = trim((string) ($candidate['rfid_number'] ?? ''));
        if ($rfid === '') {
            $rfid = 'RFID-' . str_pad((string) $employeeId, 6, '0', STR_PAD_LEFT);

            if ($db->fieldExists('rfid_number', 'employees')) {
                $db->table('employees')
                    ->where('id', $employeeId)
                    ->update([
                        'rfid_number' => $rfid,
                        'updated_at'  => $now,
                    ]);
            }
        }

        $payload = [
            'employee_id' => $employeeId,
            'date'        => $today,
            'time_in'     => '08:30:00',
            'time_out'    => '17:10:00',
            'status'      => 'Present',
            'updated_at'  => $now,
        ];

        if ($db->fieldExists('rfid_number', 'attendance_logs')) {
            $payload['rfid_number'] = $rfid;
        }

        if ($existing) {
            $db->table('attendance_logs')
                ->where('id', (int) $existing['id'])
                ->update($payload);
            $action = 'updated';
        } else {
            $payload['created_at'] = $now;
            $db->table('attendance_logs')->insert($payload);
            $action = 'inserted';
        }

        $name = trim(((string) ($candidate['first_name'] ?? '')) . ' ' . ((string) ($candidate['last_name'] ?? '')));
        $code = (string) ($candidate['employee_id'] ?? 'N/A');

        echo "Successfully {$action} today's attendance for {$name} ({$code}).\n";
        echo "Date: {$today}, In: 08:30:00, Out: 17:10:00, Status: Present\n";
    }
}
