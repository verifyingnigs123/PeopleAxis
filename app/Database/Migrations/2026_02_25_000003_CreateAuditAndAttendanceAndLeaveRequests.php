<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuditAndAttendanceAndLeaveRequests extends Migration
{
    public function up()
    {
        // audit_logs
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'timestamp' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('audit_logs', true);

        // attendance_logs
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'employee_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'time_in' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'time_out' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'present',
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
        $this->forge->addKey('id', true);
        $this->forge->createTable('attendance_logs', true);

        // leave_requests
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'employee_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'leave_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'pending',
            ],
            'approved_by_manager' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'approved_by_hr' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey('id', true);
        $this->forge->createTable('leave_requests', true);

        // Migrate existing leaves data into leave_requests if leaves table exists
        $db = \Config\Database::connect();
        if ($db->tableExists('leaves')) {
            $leaves = $db->table('leaves')->get()->getResultArray();
            if (!empty($leaves)) {
                foreach ($leaves as $l) {
                    $db->table('leave_requests')->insert([
                        'employee_id' => $l['employee_id'] ?? null,
                        'leave_type' => $l['leave_type'] ?? null,
                        'start_date' => $l['start_date'] ?? null,
                        'end_date' => $l['end_date'] ?? null,
                        'status' => $l['status'] ?? 'pending',
                        'reason' => $l['reason'] ?? null,
                        'created_at' => $l['created_at'] ?? date('Y-m-d H:i:s'),
                        'updated_at' => $l['updated_at'] ?? null,
                    ]);
                }
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('audit_logs', true);
        $this->forge->dropTable('attendance_logs', true);
        $this->forge->dropTable('leave_requests', true);
    }
}
