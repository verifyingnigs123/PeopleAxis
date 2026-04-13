<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('UserSeeder');
        $this->call('DepartmentSeeder');
        $this->call('PositionSeeder');
        $this->call('EmployeeSeeder');
        $this->call('LinkUsersToEmployees');
        $this->call('AssignManagerEmployeeToITSeeder');
    }
}
