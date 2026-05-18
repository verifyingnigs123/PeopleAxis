<?php

if (!function_exists('logActivity')) {
    /**
     * Log user activity to the audit_logs table
     *
     * @param int|null $userId
     * @param string $action
     * @param string|null $description
     * @return bool
     */
    function logActivity($userId, $action, $description = null)
    {
        try {
            $auditModel = new \App\Models\AuditModel();
            $auditModel->log($userId, $action, $description);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to log activity: ' . $e->getMessage());
            return false;
        }
    }
}
