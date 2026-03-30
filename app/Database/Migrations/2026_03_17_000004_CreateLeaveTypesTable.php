<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeaveTypesTable extends Migration
{
    /**
     * This migration creates a leave_types lookup table to normalize leave data.
     * Replaces hardcoded string values for leave types.
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->createTable('leave_types', true);

        // Insert default leave types
        $this->db->table('leave_types')->insertBatch([
            [
                'name' => 'Casual Leave',
                'description' => 'Casual or personal leave',
                'is_active' => 1,
            ],
            [
                'name' => 'Sick Leave',
                'description' => 'Leave due to illness or medical appointments',
                'is_active' => 1,
            ],
            [
                'name' => 'Earned Leave',
                'description' => 'Annual paid vacation leave',
                'is_active' => 1,
            ],
            [
                'name' => 'Unpaid Leave',
                'description' => 'Leave without pay',
                'is_active' => 1,
            ],
            [
                'name' => 'Maternity Leave',
                'description' => 'Leave for pregnant employees',
                'is_active' => 1,
            ],
            [
                'name' => 'Paternity Leave',
                'description' => 'Leave for new fathers',
                'is_active' => 1,
            ],
            [
                'name' => 'Bereavement Leave',
                'description' => 'Leave due to death of family member',
                'is_active' => 1,
            ],
            [
                'name' => 'Study Leave',
                'description' => 'Leave for educational purposes',
                'is_active' => 1,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('leave_types', true);
    }
}
