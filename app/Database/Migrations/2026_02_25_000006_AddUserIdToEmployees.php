<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserIdToEmployees extends Migration
{
    public function up()
    {
        $fields = [
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id',
                'comment' => 'Foreign key to users table',
            ],
        ];

        $this->forge->addColumn('employees', $fields);

        // Add foreign key constraint
        $this->db->query('ALTER TABLE employees ADD CONSTRAINT fk_employees_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL');
    }

    public function down()
    {
        // Drop foreign key constraint first
        $this->db->query('ALTER TABLE employees DROP FOREIGN KEY fk_employees_user_id');
        
        // Then drop the column
        $this->forge->dropColumn('employees', 'user_id');
    }
}
