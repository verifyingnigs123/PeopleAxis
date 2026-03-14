<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleIdToEmployees extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('role_id', 'employees')) {
            $this->forge->addColumn('employees', [
                'role_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'department_id',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('role_id', 'employees')) {
            $this->forge->dropColumn('employees', 'role_id');
        }
    }
}
