<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'        => 'Manager',
                'description' => 'Department Manager',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Senior Developer',
                'description' => 'Senior Software Developer',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Junior Developer',
                'description' => 'Junior Software Developer',
                'is_active'   => 1,
            ],
            [
                'name'        => 'HR Executive',
                'description' => 'Human Resources Executive',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Accountant',
                'description' => 'Finance Accountant',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Sales Executive',
                'description' => 'Sales Executive',
                'is_active'   => 1,
            ],
        ];

        $table = $this->db->table('positions');
        $now = date('Y-m-d H:i:s');
        $inserted = 0;
        $updated = 0;

        foreach ($data as $position) {
            $existing = $table->where('name', $position['name'])->get()->getRow();

            if (! $existing) {
                $payload = $position;
                $payload['created_at'] = $now;
                $payload['updated_at'] = $now;
                $table->insert($payload);
                $inserted++;
                continue;
            }

            $table->where('id', (int) $existing->id)->update([
                'description' => $position['description'],
                'is_active'   => 1,
                'updated_at'  => $now,
            ]);
            $updated++;
        }

        echo "Inserted {$inserted} positions. Updated {$updated} positions.\n";
    }
}
