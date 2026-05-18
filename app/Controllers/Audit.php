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

    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        $db = \Config\Database::connect();

        // Base: audit_logs joined with user name + role
        $base = fn() => $db->table('audit_logs al')
            ->select("al.*, u.name AS admin_name, r.name AS role_name")
            ->join('users u', 'u.id = al.user_id', 'left')
            ->join('roles r', 'r.id = u.role_id',  'left');

        // ── 1. All logs ─────────────────────────────────────────────
        $all = $base()->orderBy('al.timestamp','DESC')->limit(200)->get()->getResultObject();

        // ── 2. Authentication ────────────────────────────────────────
        $auth = $base()
            ->whereIn('al.action', ['Login','Logout','Failed Login','Account Locked'])
            ->orderBy('al.timestamp','DESC')->limit(200)->get()->getResultObject();

        // ── 3. User management ───────────────────────────────────────
        $users = $base()
            ->whereIn('al.action', ['CREATE','UPDATE','DELETE','RESTORE','Activate','Deactivate'])
            ->like('al.description', 'user', 'both')
            ->orderBy('al.timestamp','DESC')->limit(200)->get()->getResultObject();

        // ── 4. Employee management ───────────────────────────────────
        $employees = $base()
            ->like('al.description', 'employee', 'both')
            ->orderBy('al.timestamp','DESC')->limit(200)->get()->getResultObject();

        // ── 5. Leave management ──────────────────────────────────────
        $leaves = $base()
            ->groupStart()
                ->like('al.action',      'leave', 'both')
                ->orLike('al.description','leave', 'both')
            ->groupEnd()
            ->orderBy('al.timestamp','DESC')->limit(200)->get()->getResultObject();

        // ── 6. Salary / Payroll ──────────────────────────────────────
        $salary = $base()
            ->groupStart()
                ->like('al.action',      'salary', 'both')
                ->orLike('al.description','salary', 'both')
            ->groupEnd()
            ->orderBy('al.timestamp','DESC')->limit(200)->get()->getResultObject();

        // ── 7. Recent Sessions (one per user, latest login) ──────────
        $loginEvents = $db->table('audit_logs al')
            ->select("al.user_id, u.name AS user_name, r.name AS user_type, al.timestamp, al.description")
            ->join('users u', 'u.id = al.user_id', 'left')
            ->join('roles r', 'r.id = u.role_id',  'left')
            ->where('al.action', 'Login')
            ->orderBy('al.timestamp', 'DESC')
            ->limit(100)->get()->getResultObject();

        $logoutEvents = $db->table('audit_logs')
            ->select("user_id, timestamp")
            ->where('action','Logout')
            ->orderBy('timestamp','DESC')
            ->limit(100)->get()->getResultObject();

        $logoutMap = [];
        foreach ($logoutEvents as $lo) {
            $uid = (int)$lo->user_id;
            if (!isset($logoutMap[$uid])) $logoutMap[$uid] = $lo->timestamp;
        }

        $sessionsTable = [];
        $seenUsers = [];
        foreach ($loginEvents as $ev) {
            $uid = (int)$ev->user_id;
            if (isset($seenUsers[$uid])) continue;
            $seenUsers[$uid] = true;
            $lastLogin  = $ev->timestamp;
            $lastLogout = $logoutMap[$uid] ?? null;
            $status = ($lastLogout && $lastLogout > $lastLogin) ? 'expired' : 'active';
            preg_match('/IP:\s*([\d\.]+)/', (string)($ev->description ?? ''), $ipMatch);
            $sessionsTable[] = (object)[
                'user_id'    => $uid,
                'user_name'  => $ev->user_name ?? 'Unknown',
                'user_type'  => $ev->user_type ?? 'Unknown',
                'issued_at'  => $lastLogin,
                'last_seen'  => $lastLogin,
                'expires_at' => date('Y-m-d H:i:s', strtotime($lastLogin . ' +8 hours')),
                'ip_address' => $ipMatch[1] ?? '—',
                'status'     => $status,
            ];
        }

        // ── 8. Login Attempts (from login_attempts table) ────────────
        $loginAttempts = $db->table('login_attempts la')
            ->select("la.*, u.name AS user_name, r.name AS role_name")
            ->join('users u', 'u.id = la.user_id', 'left')
            ->join('roles r', 'r.id = u.role_id',  'left')
            ->orderBy('la.created_at','DESC')
            ->limit(200)->get()->getResultObject();

        // ── 9. Account Activity Events ───────────────────────────────
        $activityEvents = $base()
            ->whereIn('al.action', ['Login','Logout','Failed Login','Account Locked'])
            ->orderBy('al.timestamp','DESC')->limit(100)->get()->getResultObject();

        // ── 10. Intrusion Alerts ─────────────────────────────────────
        $intrusions = $db->table('login_attempts')
            ->select('email, ip_address, COUNT(*) as count, MAX(created_at) as triggered_at')
            ->where('result','failed')
            ->groupBy('email, ip_address')
            ->having('count >=', 2)
            ->orderBy('triggered_at','DESC')
            ->limit(50)->get()->getResultObject();

        // ── Stats ────────────────────────────────────────────────────
        $totalLogins  = $db->table('login_attempts')->where('result','success')->countAllResults();
        $totalFailed  = $db->table('login_attempts')->where('result','failed')->countAllResults();
        $todayLogins  = $db->table('login_attempts')->where('result','success')->where('DATE(created_at)', date('Y-m-d'))->countAllResults();
        $activeSessions = count(array_filter($sessionsTable, fn($s) => $s->status === 'active'));

        return view('audit/logs', [
            'all'            => $all,
            'auth'           => $auth,
            'users'          => $users,
            'employees'      => $employees,
            'leaves'         => $leaves,
            'salary'         => $salary,
            'sessionsTable'  => $sessionsTable,
            'loginAttempts'  => $loginAttempts,
            'activityEvents' => $activityEvents,
            'intrusions'     => $intrusions,
            'total'          => count($all),
            'stats' => [
                'total_logins'    => $totalLogins,
                'total_failed'    => $totalFailed,
                'today_logins'    => $todayLogins,
                'active_sessions' => $activeSessions,
            ],
        ]);
    }

    public static function log($userId, $action, $entityType, $entityId = null, $details = null)
    {
        $auditModel = new AuditModel();
        $auditModel->insert([
            'user_id'     => $userId,
            'action'      => $action,
            'description' => $details ?? $entityType,
            'timestamp'   => date('Y-m-d H:i:s'),
        ]);
    }
}
