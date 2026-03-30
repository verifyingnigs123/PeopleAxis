<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDuplicateTablesLeaves extends Migration
{
    /**
     * This migration consolidates leaves and leave_requests tables.
     * Keeps leave_requests (better design) and drops leaves table (deprecated).
     */
    public function up()
    {
        // Check if leaves table exists before dropping
        if ($this->db->tableExists('leaves')) {
            // Migrate any remaining data from leaves to leave_requests (if not already done)
            $leavesData = $this->db->table('leaves')->get()->getResultArray();
            
            if (!empty($leavesData)) {
                foreach ($leavesData as $leave) {
                    // Check if this leave already exists in leave_requests
                    $exists = $this->db->table('leave_requests')
                        ->where('employee_id', $leave['employee_id'])
                        ->where('start_date', $leave['start_date'])
                        ->where('end_date', $leave['end_date'])
                        ->countAllResults();
                    
                    if (!$exists) {
                        $this->db->table('leave_requests')->insert([
                            'employee_id' => $leave['employee_id'],
                            'leave_type' => $leave['leave_type'],
                            'start_date' => $leave['start_date'],
                            'end_date' => $leave['end_date'],
                            'status' => $leave['status'] ?? 'pending',
                            'reason' => $leave['reason'] ?? null,
                            'approved_by_manager' => $leave['approved_by'] ?? null,
                            'created_at' => $leave['created_at'],
                            'updated_at' => $leave['updated_at'],
                        ]);
                    }
                }
            }
            
            // Drop the old leaves table
            $this->forge->dropTable('leaves', true);
        }
    }

    public function down()
    {
        // Recreate leaves table if needed for rollback
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
            'number_of_days' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'pending',
            ],
            'approved_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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

        $this->forge->addKey('id', false, true);
        $this->forge->addKey('employee_id');
        $this->forge->createTable('leaves');
    }
}
