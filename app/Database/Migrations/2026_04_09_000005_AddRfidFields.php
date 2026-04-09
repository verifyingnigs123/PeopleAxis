<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRfidFields extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('employees') && ! $db->fieldExists('rfid_number', 'employees')) {
            $this->forge->addColumn('employees', [
                'rfid_number' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                ],
            ]);
        }

        if ($db->tableExists('attendance_logs') && ! $db->fieldExists('rfid_number', 'attendance_logs')) {
            $this->forge->addColumn('attendance_logs', [
                'rfid_number' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'after' => 'employee_id',
                ],
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('attendance_logs') && $db->fieldExists('rfid_number', 'attendance_logs')) {
            $this->forge->dropColumn('attendance_logs', 'rfid_number');
        }

        if ($db->tableExists('employees') && $db->fieldExists('rfid_number', 'employees')) {
            $this->forge->dropColumn('employees', 'rfid_number');
        }
    }
}