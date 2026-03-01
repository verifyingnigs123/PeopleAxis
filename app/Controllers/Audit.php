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

        // Get total count
        $total = $this->auditModel->countAll();

        // Get logs with pagination
        $logs = $this->auditModel
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage, 'default', $page);

        $data['logs'] = $logs;
        $data['pager'] = $this->auditModel->pager;
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
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details' => $details,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
