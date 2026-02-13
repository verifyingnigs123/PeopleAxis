<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyRoleColumnToVarchar extends Migration
{
    public function up()
    {
        // Modify the role column from ENUM to VARCHAR
        $this->forge->modifyColumn('users', [
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'user',
            ],
        ]);
    }

    public function down()
    {
        // Revert back to ENUM if needed
        $this->forge->modifyColumn('users', [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'user'],
                'default'    => 'user',
            ],
        ]);
    }
}
