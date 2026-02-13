<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttendanceTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'employee_id'   => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'attendance_date' => [
                'type' => 'DATE',
            ],
            'check_in'      => [
                'type' => 'TIME',
                'null' => true,
            ],
            'check_out'     => [
                'type' => 'TIME',
                'null' => true,
            ],
            'status'        => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'present',
            ],
            'remarks'       => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at'    => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'    => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', false, true);
        $this->forge->addKey('employee_id');
        $this->forge->addKey('attendance_date');
        $this->forge->createTable('attendance');
    }

    public function down()
    {
        $this->forge->dropTable('attendance');
    }
}
