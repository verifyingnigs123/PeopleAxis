<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $model = model('UserModel');
        $db = \Config\Database::connect();

        // Lookup role ids
        $roles = $db->table('roles')->get()->getResult();
        $roleMap = [];
        foreach ($roles as $r) {
            $roleMap[$r->name] = $r->id;
        }

        $seedUsers = [
            [
                'username' => 'superadmin',
                'email' => 'superadmin@peopleaxis.com',
                'password' => 'SuperAdmin123!',
                'name' => 'Super Admin',
                'role_id' => $roleMap['Super Admin'] ?? null,
            ],
            [
                'username' => 'hradmin',
                'email' => 'hradmin@peopleaxis.com',
                'password' => 'HrAdmin123!',
                'name' => 'HR Admin',
                'role_id' => $roleMap['HR Admin'] ?? null,
            ],
            [
                'username' => 'manager',
                'email' => 'manager@peopleaxis.com',
                'password' => 'Manager123!',
                'name' => 'Manager',
                'role_id' => $roleMap['Manager'] ?? null,
            ],
            [
                'username' => 'employee',
                'email' => 'employee@peopleaxis.com',
                'password' => 'Employee123!',
                'name' => 'Employee',
                'role_id' => $roleMap['Employee'] ?? null,
            ],
        ];

        $inserted = 0;
        foreach ($seedUsers as $u) {
            $exists = $model->where('email', $u['email'])->orWhere('username', $u['username'])->first();
            if (!$exists) {
                $u['is_active'] = 1;
                $u['created_at'] = date('Y-m-d H:i:s');
                $u['updated_at'] = date('Y-m-d H:i:s');
                $model->insert($u);
                $inserted++;
            }
        }

        if ($inserted > 0) {
            echo "Inserted {$inserted} users.\n";
        } else {
            echo "All role users already exist.\n";
        }
    }
}
