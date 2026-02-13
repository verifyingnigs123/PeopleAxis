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
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Senior Developer',
                'description' => 'Senior Software Developer',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Junior Developer',
                'description' => 'Junior Software Developer',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'HR Executive',
                'description' => 'Human Resources Executive',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Accountant',
                'description' => 'Finance Accountant',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Sales Executive',
                'description' => 'Sales Executive',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('positions')->insertBatch($data);

        echo "Positions seeded successfully!\n";
    }
}
