<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPositionIdToEmployees extends Migration
{
    /**
     * Add position_id column to employees table if it doesn't exist
     */
    public function up()
    {
        if ($this->db->tableExists('employees') && !$this->db->fieldExists('position_id', 'employees')) {
            $this->forge->addColumn('employees', [
                'position_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'department_id',
                    'comment' => 'Foreign key to positions table'
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('employees') && $this->db->fieldExists('position_id', 'employees')) {
            $this->forge->dropColumn('employees', 'position_id');
        }
    }
}
