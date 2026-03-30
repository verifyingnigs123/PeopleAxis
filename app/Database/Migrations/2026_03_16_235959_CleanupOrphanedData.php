<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CleanupOrphanedData extends Migration
{
    /**
     * Remove orphaned records that violate FK constraints
     */
    public function up()
    {
        // Disable foreign key checks temporarily to clean up data
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        // Delete audit_logs records where user doesn't exist
        if ($this->db->tableExists('audit_logs') && $this->db->tableExists('users')) {
            $this->db->query('DELETE FROM audit_logs WHERE user_id NOT IN (SELECT id FROM users) AND user_id IS NOT NULL');
        }

        // Delete salary records where employee doesn't exist
        if ($this->db->tableExists('salaries') && $this->db->tableExists('employees')) {
            $this->db->query('DELETE FROM salaries WHERE employee_id NOT IN (SELECT id FROM employees)');
        }

        // Delete attendance records where employee doesn't exist
        if ($this->db->tableExists('attendance') && $this->db->tableExists('employees')) {
            $this->db->query('DELETE FROM attendance WHERE employee_id NOT IN (SELECT id FROM employees)');
        }

        // Delete attendance_logs records where employee doesn't exist
        if ($this->db->tableExists('attendance_logs') && $this->db->tableExists('employees')) {
            $this->db->query('DELETE FROM attendance_logs WHERE employee_id NOT IN (SELECT id FROM employees)');
        }

        // Delete leave records where employee doesn't exist
        if ($this->db->tableExists('leaves') && $this->db->tableExists('employees')) {
            $this->db->query('DELETE FROM leaves WHERE employee_id NOT IN (SELECT id FROM employees)');
        }

        // Delete leave_requests records where employee doesn't exist or managers don't exist
        if ($this->db->tableExists('leave_requests') && $this->db->tableExists('employees')) {
            $this->db->query('DELETE FROM leave_requests WHERE employee_id NOT IN (SELECT id FROM employees)');
            $this->db->query('DELETE FROM leave_requests WHERE approved_by_manager NOT IN (SELECT id FROM employees) AND approved_by_manager IS NOT NULL');
            $this->db->query('DELETE FROM leave_requests WHERE approved_by_hr NOT IN (SELECT id FROM employees) AND approved_by_hr IS NOT NULL');
        }

        // Delete notifications records where user doesn't exist
        if ($this->db->tableExists('notifications') && $this->db->tableExists('users')) {
            $this->db->query('DELETE FROM notifications WHERE user_id NOT IN (SELECT id FROM users)');
        }

        // Re-enable foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        // We can't restore deleted data, so down migration does nothing
        // Data cleanup is permanent
    }
}
