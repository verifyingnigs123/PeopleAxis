<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AssignManagerEmployeeToITSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $itDepartment = $db->table('departments')
            ->where('name', 'Information Technology')
            ->get()
            ->getRow();

        if (! $itDepartment) {
            $db->table('departments')->insert([
                'name'        => 'Information Technology',
                'description' => 'IT Department',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);

            $itDepartment = $db->table('departments')
                ->where('name', 'Information Technology')
                ->get()
                ->getRow();
        }

        if (! $itDepartment) {
            echo "Unable to find or create Information Technology department.\n";
            return;
        }

        $managerUser = $db->table('users')
            ->groupStart()
                ->where('email', 'manager@peopleaxis.com')
                ->orWhere('username', 'manager')
            ->groupEnd()
            ->orderBy('id', 'ASC')
            ->get()
            ->getRow();

        if (! $managerUser) {
            $managerUser = $db->table('users')
                ->where('role', 'manager')
                ->orderBy('id', 'ASC')
                ->get()
                ->getRow();
        }

        $employeeUser = $db->table('users')
            ->groupStart()
                ->where('email', 'employee@peopleaxis.com')
                ->orWhere('username', 'employee')
            ->groupEnd()
            ->orderBy('id', 'ASC')
            ->get()
            ->getRow();

        if (! $employeeUser) {
            $employeeUser = $db->table('users')
                ->where('role', 'employee')
                ->orderBy('id', 'ASC')
                ->get()
                ->getRow();
        }

        if (! $managerUser || ! $employeeUser) {
            echo "Manager or employee user account was not found.\n";
            return;
        }

        $db->table('departments')
            ->where('id', (int) $itDepartment->id)
            ->update([
                'manager_id' => (int) $managerUser->id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->assignEmployeeToDepartment($db, (int) $managerUser->id, (int) $itDepartment->id);
        $this->assignEmployeeToDepartment($db, (int) $employeeUser->id, (int) $itDepartment->id);

        echo "Assigned manager and employee to Information Technology department successfully.\n";
    }

    private function assignEmployeeToDepartment($db, int $userId, int $departmentId): void
    {
        $employee = $db->table('employees')
            ->where('user_id', $userId)
            ->orderBy('id', 'ASC')
            ->get()
            ->getRow();

        if (! $employee) {
            return;
        }

        $payload = [
            'department_id' => $departmentId,
            'status'        => 'active',
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        if ($db->fieldExists('account_status', 'employees')) {
            $payload['account_status'] = 'approved';
        }

        $db->table('employees')
            ->where('id', (int) $employee->id)
            ->update($payload);
    }
}
