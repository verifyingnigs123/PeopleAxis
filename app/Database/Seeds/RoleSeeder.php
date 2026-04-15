<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $table = $this->db->table('roles');
        $now = date('Y-m-d H:i:s');

        $roles = [
            [
                'name' => 'Super Admin',
                'description' => 'Full system access',
            ],
            [
                'name' => 'HR Admin',
                'description' => 'Manage HR functions',
            ],
            [
                'name' => 'Manager',
                'description' => 'Team manager',
            ],
            [
                'name' => 'Employee',
                'description' => 'Regular employee',
            ],
        ];

        $inserted = 0;
        $updated = 0;

        foreach ($roles as $role) {
            $existing = $table->where('name', $role['name'])->get()->getRow();

            if (! $existing) {
                $payload = $role;
                $payload['created_at'] = $now;
                $payload['updated_at'] = $now;
                $table->insert($payload);
                $inserted++;
                continue;
            }

            $table->where('id', (int) $existing->id)->update([
                'description' => $role['description'],
                'updated_at'  => $now,
            ]);
            $updated++;
        }

        echo "Inserted {$inserted} roles. Updated {$updated} roles.\n";
    }
}