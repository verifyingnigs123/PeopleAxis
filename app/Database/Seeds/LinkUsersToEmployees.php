<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LinkUsersToEmployees extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get all users
        $users = $db->table('users')->get()->getResult();
        
        foreach ($users as $user) {
            // Check if this user already has an employee record
            $employeeExists = $db->table('employees')
                ->where('user_id', $user->id)
                ->countAllResults();
            
            if ($employeeExists == 0) {
                // Try to find matching employee by email
                $employee = $db->table('employees')
                    ->where('email', $user->email)
                    ->first();
                
                if ($employee) {
                    // Link the employee to the user
                    $db->table('employees')
                        ->where('id', $employee->id)
                        ->update(['user_id' => $user->id]);
                    echo "Linked user '{$user->email}' to employee record.\n";
                } else {
                    echo "No employee record found for user '{$user->email}'. Please create employee record manually.\n";
                }
            }
        }
        
        echo "Employee linking completed.\n";
    }
}
