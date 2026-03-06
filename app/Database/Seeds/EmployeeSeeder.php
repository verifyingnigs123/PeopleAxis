<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\EmployeeModel;
use App\Models\DepartmentModel;
use App\Models\PositionModel;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $employeeModel = new EmployeeModel();
        $departmentModel = new DepartmentModel();
        $positionModel = new PositionModel();
        $db = \Config\Database::connect();

        // Get default department
        $department = $departmentModel->first();
        if (!$department) {
            // Create default department if not exists
            $departmentId = $db->table('departments')->insert(['name' => 'General', 'description' => 'General Department']);
            $department = $departmentModel->first();
        }
        
        $departmentId = is_object($department) ? $department->id : $department['id'];

        // Get default position
        $position = $positionModel->first();
        if (!$position) {
            // Create default position if not exists
            $db->table('positions')->insert(['name' => 'Employee', 'description' => 'Regular Employee']);
            $position = $positionModel->first();
        }
        
        $positionId = is_object($position) ? $position->id : $position['id'];

        // Get all users
        $users = $db->table('users')->get()->getResult();

        $inserted = 0;
        foreach ($users as $user) {
            // Check if employee already exists for this user
            $exists = $employeeModel->where('user_id', $user->id)->first();
            
            if (!$exists) {
                // Generate employee ID
                $employeeId = 'EMP' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
                
                // Parse name
                $nameParts = explode(' ', $user->name, 2);
                $firstName = $nameParts[0] ?? 'User';
                $lastName = $nameParts[1] ?? 'Employee';

                $employeeData = [
                    'user_id' => $user->id,
                    'employee_id' => $employeeId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $user->email,
                    'phone' => '',
                    'department_id' => $departmentId,
                    'position_id' => $positionId,
                    'date_of_birth' => '1990-01-01',
                    'date_of_joining' => date('Y-m-d'),
                    'status' => 'active',
                ];

                $employeeModel->insert($employeeData);
                $inserted++;
            }
        }

        if ($inserted > 0) {
            echo "Inserted {$inserted} employee records.\n";
        } else {
            echo "All employee records already exist.\n";
        }
    }
}

