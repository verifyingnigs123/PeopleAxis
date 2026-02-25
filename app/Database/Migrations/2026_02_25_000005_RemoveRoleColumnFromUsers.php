<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveRoleColumnFromUsers extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('users')) {
            // drop the old role text column
            $this->forge->dropColumn('users', 'role');
        }
    }

    public function down()
    {
        // Recreate the role column if rolling back
        if ($this->db->tableExists('users')) {
            $fields = [
                'role' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'Employee',
                ],
            ];
            $this->forge->addColumn('users', $fields);
        }
    }
}
