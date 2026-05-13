<?php

namespace App\Helpers;

use Config\Database;

class PrivilegeHelper
{
    /**
     * Check if user has a specific privilege
     */
    public static function hasPrivilege(string $privilegeName): bool
    {
        $userId = session()->get('user_id');
        $roleId = session()->get('role_id');

        if (!$userId || !$roleId) {
            return false;
        }

        $db = Database::connect();
        
        // Get role with privileges
        $role = $db->table('roles')
            ->select('privileges')
            ->where('id', $roleId)
            ->get()
            ->getRow();

        if (!$role || empty($role->privileges)) {
            return false;
        }

        // Decode JSON privileges
        try {
            $privileges = json_decode($role->privileges, true) ?? [];
            return in_array($privilegeName, $privileges, true);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if user has all specified privileges
     */
    public static function hasAllPrivileges(array $privilegeNames): bool
    {
        foreach ($privilegeNames as $privilege) {
            if (!self::hasPrivilege($privilege)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if user has any of the specified privileges
     */
    public static function hasAnyPrivilege(array $privilegeNames): bool
    {
        foreach ($privilegeNames as $privilege) {
            if (self::hasPrivilege($privilege)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all privileges for current user
     */
    public static function getAllPrivileges(): array
    {
        $roleId = session()->get('role_id');

        if (!$roleId) {
            return [];
        }

        $db = Database::connect();
        
        $role = $db->table('roles')
            ->select('privileges')
            ->where('id', $roleId)
            ->get()
            ->getRow();

        if (!$role || empty($role->privileges)) {
            return [];
        }

        try {
            return json_decode($role->privileges, true) ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Check if user can manage users (privilege-based or default role-based)
     */
    public static function canManageUsers(): bool
    {
        // First check privilege-based access
        if (self::hasPrivilege('users_create') || self::hasPrivilege('users_edit') || self::hasPrivilege('users_delete') || self::hasPrivilege('users_view')) {
            return true;
        }

        // Fallback to role-based (for default roles without privileges)
        $role = strtolower((string) session()->get('role'));
        $roleName = strtolower((string) session()->get('role_name'));

        return in_array($role, ['admin', 'super_admin'], true)
            || $roleName === 'super admin';
    }

    /**
     * Check if user can manage employees
     */
    public static function canManageEmployees(): bool
    {
        if (self::hasPrivilege('employees_create') || self::hasPrivilege('employees_edit') || self::hasPrivilege('employees_delete') || self::hasPrivilege('employees_view')) {
            return true;
        }

        $role = strtolower((string) session()->get('role'));
        $roleName = strtolower((string) session()->get('role_name'));

        return in_array($role, ['admin', 'super_admin', 'hr'], true)
            || in_array($roleName, ['super admin', 'hr admin'], true);
    }

    /**
     * Check if user can manage leaves
     */
    public static function canManageLeaves(): bool
    {
        if (self::hasPrivilege('leaves_view') || self::hasPrivilege('leaves_approve') || self::hasPrivilege('leaves_reject')) {
            return true;
        }

        $role = strtolower((string) session()->get('role'));
        $roleName = strtolower((string) session()->get('role_name'));

        return in_array($role, ['admin', 'super_admin', 'manager'], true)
            || in_array($roleName, ['super admin', 'manager'], true);
    }

    /**
     * Check if user can view attendance
     */
    public static function canViewAttendance(): bool
    {
        if (self::hasPrivilege('attendance_view') || self::hasPrivilege('attendance_manage')) {
            return true;
        }

        $role = strtolower((string) session()->get('role'));
        $roleName = strtolower((string) session()->get('role_name'));

        return in_array($role, ['admin', 'super_admin', 'manager'], true)
            || in_array($roleName, ['super admin', 'manager'], true);
    }

    /**
     * Check if user can manage attendance
     */
    public static function canManageAttendance(): bool
    {
        return self::hasPrivilege('attendance_manage');
    }

    /**
     * Check if user can view reports
     */
    public static function canViewReports(): bool
    {
        return self::hasPrivilege('attendance_reports');
    }

    // ===== Specific privilege wrapper methods =====

    /**
     * User Management Privileges
     */
    public static function canViewUsers(): bool
    {
        return self::hasPrivilege('users_view');
    }

    public static function canCreateUsers(): bool
    {
        return self::hasPrivilege('users_create');
    }

    public static function canEditUsers(): bool
    {
        return self::hasPrivilege('users_edit');
    }

    public static function canDeleteUsers(): bool
    {
        return self::hasPrivilege('users_delete');
    }

    /**
     * Employee Management Privileges
     */
    public static function canViewEmployees(): bool
    {
        return self::hasPrivilege('employees_view');
    }

    public static function canCreateEmployees(): bool
    {
        return self::hasPrivilege('employees_create');
    }

    public static function canEditEmployees(): bool
    {
        return self::hasPrivilege('employees_edit');
    }

    public static function canDeleteEmployees(): bool
    {
        return self::hasPrivilege('employees_delete');
    }

    /**
     * Leave Management Privileges
     */
    public static function canViewLeaves(): bool
    {
        return self::hasPrivilege('leaves_view');
    }

    public static function canApproveLeaves(): bool
    {
        return self::hasPrivilege('leaves_approve');
    }

    public static function canRejectLeaves(): bool
    {
        return self::hasPrivilege('leaves_reject');
    }

    /**
     * Attendance Privileges
     */
    public static function canViewAttendanceReports(): bool
    {
        return self::hasPrivilege('attendance_reports');
    }
}
