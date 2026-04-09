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
                'role' => 'super_admin',
                'role_id' => $roleMap['Super Admin'] ?? null,
            ],
            [
                'username' => 'hradmin',
                'email' => 'hradmin@peopleaxis.com',
                'password' => 'HrAdmin123!',
                'name' => 'HR Admin',
                'role' => 'hr_admin',
                'role_id' => $roleMap['HR Admin'] ?? null,
            ],
            [
                'username' => 'manager',
                'email' => 'manager@peopleaxis.com',
                'password' => 'Manager123!',
                'name' => 'Manager',
                'role' => 'manager',
                'role_id' => $roleMap['Manager'] ?? null,
            ],
            [
                'username' => 'employee',
                'email' => 'employee@peopleaxis.com',
                'password' => 'Employee123!',
                'name' => 'Employee',
                'role' => 'employee',
                'role_id' => $roleMap['Employee'] ?? null,
            ],
        ];

        $inserted = 0;
        $updated = 0;
        foreach ($seedUsers as $u) {
            $exists = $model->where('email', $u['email'])->orWhere('username', $u['username'])->first();
            if (!$exists) {
                $u['is_active'] = 1;
                $u['created_at'] = date('Y-m-d H:i:s');
                $u['updated_at'] = date('Y-m-d H:i:s');
                $model->insert($u);
                $inserted++;
            } else {
                // Keep seed accounts aligned with expected role and active status.
                $needsUpdate = false;
                $patch = [
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                if (($exists->role_id ?? null) != ($u['role_id'] ?? null) && !empty($u['role_id'])) {
                    $patch['role_id'] = $u['role_id'];
                    $needsUpdate = true;
                }

                if (($exists->name ?? '') !== ($u['name'] ?? '')) {
                    $patch['name'] = $u['name'];
                    $needsUpdate = true;
                }

                if (($exists->role ?? '') !== ($u['role'] ?? '')) {
                    $patch['role'] = $u['role'];
                    $needsUpdate = true;
                }

                if (($exists->is_active ?? 1) != 1) {
                    $patch['is_active'] = 1;
                    $needsUpdate = true;
                }

                if ($needsUpdate) {
                    $model->update($exists->id, $patch);
                    $updated++;
                }
            }
        }

        if ($inserted > 0 || $updated > 0) {
            echo "Inserted {$inserted} users. Updated {$updated} users.\n";
        } else {
            echo "All role users already exist.\n";
        }
    }
}
