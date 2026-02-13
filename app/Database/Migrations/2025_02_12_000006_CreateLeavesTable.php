<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeavesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'employee_id'    => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'leave_type'     => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'start_date'     => [
                'type' => 'DATE',
            ],
            'end_date'       => [
                'type' => 'DATE',
            ],
            'number_of_days' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
            ],
            'reason'         => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status'         => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'pending',
            ],
            'approved_by'    => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at'     => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'     => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', false, true);
        $this->forge->addKey('employee_id');
        $this->forge->createTable('leaves');
    }

    public function down()
    {
        $this->forge->dropTable('leaves');
    }
}
