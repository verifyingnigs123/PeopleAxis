<?php

namespace App\Controllers;

use App\Models\AuditModel;

class Audit extends BaseController
{
    protected $auditModel;

    public function __construct()
    {
        $this->auditModel = new AuditModel();
    }

    /**
     * Display audit logs / activity logs
     */
    public function index()
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        // Get pagination settings
        $perPage = 20;
        $page = $this->request->getVar('page') ?? 1;

        // Get logs with admin name joined from users table using model's paginate
        $logs = $this->auditModel
            ->select('audit_logs.*, users.name as admin_name')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->orderBy('audit_logs.timestamp', 'DESC')
            ->paginate($perPage, 'default', $page);

        $pager = $this->auditModel->pager;
        $total = $this->auditModel->countAll();

        $data['logs'] = $logs;
        $data['pager'] = $pager;
        $data['total'] = $total;

        return view('audit/logs', $data);
    }

    /**
     * Log an action (called internally)
     */
    public static function log($userId, $action, $entityType, $entityId, $details = null)
    {
        $auditModel = new AuditModel();
        $auditModel->insert([
            'user_id' => $userId,
            'action' => $action,
            'description' => $details ?? $entityType,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}
