<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'        => 'Human Resources',
                'description' => 'HR Department',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Information Technology',
                'description' => 'IT Department',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Finance',
                'description' => 'Finance Department',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Operations',
                'description' => 'Operations Department',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Sales',
                'description' => 'Sales Department',
                'is_active'   => 1,
            ],
        ];

        $table = $this->db->table('departments');
        $now = date('Y-m-d H:i:s');
        $inserted = 0;
        $updated = 0;

        foreach ($data as $department) {
            $existing = $table->where('name', $department['name'])->get()->getRow();

            if (! $existing) {
                $payload = $department;
                $payload['created_at'] = $now;
                $payload['updated_at'] = $now;
                $table->insert($payload);
                $inserted++;
                continue;
            }

            $table->where('id', (int) $existing->id)->update([
                'description' => $department['description'],
                'is_active'   => 1,
                'updated_at'  => $now,
            ]);
            $updated++;
        }

        echo "Inserted {$inserted} departments. Updated {$updated} departments.\n";
    }
}
