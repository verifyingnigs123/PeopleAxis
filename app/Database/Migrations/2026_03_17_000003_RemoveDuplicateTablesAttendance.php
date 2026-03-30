<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDuplicateTablesAttendance extends Migration
{
    /**
     * This migration consolidates attendance and attendance_logs tables.
     * Keeps attendance_logs and drops attendance table.
     * Standardizes column names (check_in/check_out → time_in/time_out).
     */
    public function up()
    {
        // Check if attendance table exists
        if ($this->db->tableExists('attendance')) {
            // Migrate data from attendance to attendance_logs with column mapping
            $attendanceData = $this->db->table('attendance')->get()->getResultArray();
            
            if (!empty($attendanceData)) {
                foreach ($attendanceData as $record) {
                    // Check if this record already exists in attendance_logs
                    $exists = $this->db->table('attendance_logs')
                        ->where('employee_id', $record['employee_id'])
                        ->where('date', $record['attendance_date'])
                        ->countAllResults();
                    
                    if (!$exists) {
                        $this->db->table('attendance_logs')->insert([
                            'employee_id' => $record['employee_id'],
                            'date' => $record['attendance_date'],
                            'time_in' => $record['check_in'] ?? null,
                            'time_out' => $record['check_out'] ?? null,
                            'status' => $record['status'] ?? 'present',
                            'created_at' => $record['created_at'],
                            'updated_at' => $record['updated_at'],
                        ]);
                    }
                }
            }
            
            // Drop the old attendance table
            $this->forge->dropTable('attendance', true);
        }
    }

    public function down()
    {
        // Recreate attendance table if needed for rollback
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
            'attendance_date' => [
                'type' => 'DATE',
            ],
            'check_in' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'check_out' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'present',
            ],
            'remarks' => [
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

        $this->forge->addKey('id', false, true);
        $this->forge->addKey('employee_id');
        $this->forge->addKey('attendance_date');
        $this->forge->createTable('attendance');
    }
}
