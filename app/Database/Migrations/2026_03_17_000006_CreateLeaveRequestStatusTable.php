<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeaveRequestStatusTable extends Migration
{
    /**
     * This migration creates a leave_request_status lookup table.
     * Normalizes leave request status values.
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
                'comment' => 'CSS color class for UI display',
            ],
            'is_approved' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'comment' => 'Whether this status represents an approved leave',
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
        $this->forge->createTable('leave_request_status', true);

        // Insert default leave request statuses
        $this->db->table('leave_request_status')->insertBatch([
            [
                'code' => 'PENDING',
                'name' => 'Pending',
                'description' => 'Awaiting approval from manager',
                'color' => 'warning',
                'is_approved' => false,
                'is_active' => 1,
            ],
            [
                'code' => 'MANAGER_APPROVED',
                'name' => 'Manager Approved',
                'description' => 'Approved by manager, awaiting HR approval',
                'color' => 'info',
                'is_approved' => false,
                'is_active' => 1,
            ],
            [
                'code' => 'APPROVED',
                'name' => 'Approved',
                'description' => 'Leave has been approved',
                'color' => 'success',
                'is_approved' => true,
                'is_active' => 1,
            ],
            [
                'code' => 'REJECTED',
                'name' => 'Rejected',
                'description' => 'Leave request has been rejected',
                'color' => 'danger',
                'is_approved' => false,
                'is_active' => 1,
            ],
            [
                'code' => 'CANCELLED',
                'name' => 'Cancelled',
                'description' => 'Leave request has been cancelled',
                'color' => 'secondary',
                'is_approved' => false,
                'is_active' => 1,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('leave_request_status', true);
    }
}
