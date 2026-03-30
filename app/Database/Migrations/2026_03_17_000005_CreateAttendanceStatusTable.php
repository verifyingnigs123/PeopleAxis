<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttendanceStatusTable extends Migration
{
    /**
     * This migration creates an attendance_status lookup table to normalize status values.
     * Replaces hardcoded string values for attendance status.
     */
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'comment' => 'CSS color class or hex code for UI display',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', false, true);
        $this->forge->createTable('attendance_status', true);

        // Insert default attendance statuses
        $this->db->table('attendance_status')->insertBatch([
            [
                'code' => 'PRESENT',
                'name' => 'Present',
                'description' => 'Employee was present',
                'color' => 'success',
                'is_active' => 1,
            ],
            [
                'code' => 'ABSENT',
                'name' => 'Absent',
                'description' => 'Employee was absent',
                'color' => 'danger',
                'is_active' => 1,
            ],
            [
                'code' => 'LATE',
                'name' => 'Late',
                'description' => 'Employee was late',
                'color' => 'warning',
                'is_active' => 1,
            ],
            [
                'code' => 'HALF_DAY',
                'name' => 'Half Day',
                'description' => 'Employee worked half day',
                'color' => 'info',
                'is_active' => 1,
            ],
            [
                'code' => 'LEAVE',
                'name' => 'On Leave',
                'description' => 'Employee is on approved leave',
                'color' => 'secondary',
                'is_active' => 1,
            ],
            [
                'code' => 'HOLIDAY',
                'name' => 'Holiday',
                'description' => 'Public holiday',
                'color' => 'primary',
                'is_active' => 1,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('attendance_status', true);
    }
}
