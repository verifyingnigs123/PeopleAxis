<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddForeignKeyConstraints extends Migration
{
    /**
     * Helper method to safely add foreign key constraint
     */
    private function addForeignKeyIfNotExists($table, $constraint, $column, $refTable, $refColumn, $onDelete = 'SET NULL')
    {
        try {
            $this->db->query("ALTER TABLE `{$table}` ADD CONSTRAINT `{$constraint}` FOREIGN KEY (`{$column}`) REFERENCES `{$refTable}`(`{$refColumn}`) ON DELETE {$onDelete}");
        } catch (\Exception $e) {
            // Constraint already exists or other error - skip silently
            if (strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), 'already exists') !== false) {
                // Constraint already exists, skip
                return true;
            }
            // Re-throw if it's a different error
            throw $e;
        }
    }

    public function up()
    {
        // Disable FK checks to allow adding constraints despite existing invalid data
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        // Add FK to employees table
        $this->addForeignKeyIfNotExists('employees', 'fk_emp_dept', 'department_id', 'departments', 'id', 'SET NULL');
        $this->addForeignKeyIfNotExists('employees', 'fk_emp_pos', 'position_id', 'positions', 'id', 'SET NULL');
        $this->addForeignKeyIfNotExists('employees', 'fk_emp_role', 'role_id', 'roles', 'id', 'SET NULL');
        
        // Add FK to salaries table
        if ($this->db->tableExists('salaries')) {
            $this->addForeignKeyIfNotExists('salaries', 'fk_sal_emp', 'employee_id', 'employees', 'id', 'CASCADE');
        }
        
        // Add FK to attendance table
        if ($this->db->tableExists('attendance')) {
            $this->addForeignKeyIfNotExists('attendance', 'fk_att_emp', 'employee_id', 'employees', 'id', 'CASCADE');
        }
        
        // Add FK to attendance_logs table
        if ($this->db->tableExists('attendance_logs')) {
            $this->addForeignKeyIfNotExists('attendance_logs', 'fk_att_log_emp', 'employee_id', 'employees', 'id', 'CASCADE');
        }
        
        // Add FK to leaves table
        if ($this->db->tableExists('leaves')) {
            $this->addForeignKeyIfNotExists('leaves', 'fk_leave_emp', 'employee_id', 'employees', 'id', 'CASCADE');
        }
        
        // Add FK to leave_requests table
        if ($this->db->tableExists('leave_requests')) {
            $this->addForeignKeyIfNotExists('leave_requests', 'fk_leave_req_emp', 'employee_id', 'employees', 'id', 'CASCADE');
            $this->addForeignKeyIfNotExists('leave_requests', 'fk_leave_req_mgr', 'approved_by_manager', 'employees', 'id', 'SET NULL');
            $this->addForeignKeyIfNotExists('leave_requests', 'fk_leave_req_hr', 'approved_by_hr', 'employees', 'id', 'SET NULL');
        }
        
        // Add FK to users table
        $this->addForeignKeyIfNotExists('users', 'fk_user_role', 'role_id', 'roles', 'id', 'SET NULL');
        
        // Add FK to audit_logs table
        if ($this->db->tableExists('audit_logs')) {
            $this->addForeignKeyIfNotExists('audit_logs', 'fk_audit_user', 'user_id', 'users', 'id', 'SET NULL');
        }
        
        // Add FK to notifications table
        if ($this->db->tableExists('notifications')) {
            $this->addForeignKeyIfNotExists('notifications', 'fk_notify_user', 'user_id', 'users', 'id', 'CASCADE');
        }

        // Re-enable FK checks
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }


    public function down()
    {
        // Helper to safely drop FK
        $dropFk = function($table, $constraint) {
            try {
                $this->db->query("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
            } catch (\Exception $e) {
                // Constraint doesn't exist, skip silently
            }
        };

        // Drop all FK constraints
        $dropFk('employees', 'fk_emp_dept');
        $dropFk('employees', 'fk_emp_pos');
        $dropFk('employees', 'fk_emp_role');
        
        if ($this->db->tableExists('salaries')) {
            $dropFk('salaries', 'fk_sal_emp');
        }
        if ($this->db->tableExists('attendance')) {
            $dropFk('attendance', 'fk_att_emp');
        }
        if ($this->db->tableExists('attendance_logs')) {
            $dropFk('attendance_logs', 'fk_att_log_emp');
        }
        if ($this->db->tableExists('leaves')) {
            $dropFk('leaves', 'fk_leave_emp');
        }
        if ($this->db->tableExists('leave_requests')) {
            $dropFk('leave_requests', 'fk_leave_req_emp');
            $dropFk('leave_requests', 'fk_leave_req_mgr');
            $dropFk('leave_requests', 'fk_leave_req_hr');
        }
        
        $dropFk('users', 'fk_user_role');
        
        if ($this->db->tableExists('audit_logs')) {
            $dropFk('audit_logs', 'fk_audit_user');
        }
        if ($this->db->tableExists('notifications')) {
            $dropFk('notifications', 'fk_notify_user');
        }
    }
}
