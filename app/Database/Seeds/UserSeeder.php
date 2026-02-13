<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $model = model('UserModel');

        // Check if admin already exists
        $adminExists = $model->where('email', 'admin@peopleaxis.com')->first();

        if (!$adminExists) {
            $data = [
                [
                    'email'      => 'admin@gmail.com',
                    'password'   => password_hash('admin123', PASSWORD_BCRYPT),
                    'name'       => 'Admin',
                    'role'       => 'admin',
                    'is_active'  => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'email'      => 'user@gmail.com',
                    'password'   => password_hash('user123', PASSWORD_BCRYPT),
                    'name'       => 'Test User',
                    'role'       => 'user',
                    'is_active'  => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
            ];

            $model->insertBatch($data);

            echo "Users seeded successfully!\n";
            echo "Admin: admin@gmail.com / admin123\n";
            echo "User: user@gmail.com / user123\n";
        } else {
            echo "Users already exist. Skipping seed.\n";
        }
    }
}
