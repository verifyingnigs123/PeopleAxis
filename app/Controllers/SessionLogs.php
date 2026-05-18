<?php

namespace App\Controllers;

use App\Models\LoginAttemptModel;
use App\Models\AuditModel;

class SessionLogs extends BaseController
{
    protected $loginAttemptModel;
    protected $auditModel;

    public function __construct()
    {
        $this->loginAttemptModel = new LoginAttemptModel();
        $this->auditModel        = new AuditModel();
    }

    /**
     * Dedicated Session / Login Activity page — Super Admin only.
     */
    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        $db = \Config\Database::connect();

        // ── 1. Recent Sessions: last login per user (from audit_logs) ──────────────────
        $recentSessions = $db->table('audit_logs al')
            ->select("
                al.id,
                al.user_id,
                u.name  AS user_name,
                r.name  AS user_type,
                al.description,
                al.timestamp AS issued_at
            ")
            ->join('users u',  'u.id = al.user_id', 'left')
            ->join('roles r',  'r.id = u.role_id',  'left')
            ->whereIn('al.action', ['Login', 'Logout'])
            ->orderBy('al.timestamp', 'DESC')
            ->limit(50)
            ->get()
            ->getResultObject();

        // For each user, find their very last login and last logout to derive session status
        $sessionMap = [];
        foreach ($recentSessions as $row) {
            $uid = (int) $row->user_id;
            if (!isset($sessionMap[$uid])) {
                $sessionMap[$uid] = [
                    'user_id'   => $uid,
                    'user_name' => $row->user_name ?? 'Unknown',
                    'user_type' => $row->user_type ?? 'Unknown',
                    'last_login'  => null,
                    'last_logout' => null,
                    'description' => $row->description,
                ];
            }

            // Parse action from description (Login / Logout stored as action column)
            $isLogin  = str_contains(strtolower((string)($row->description ?? '')), 'logged in');
            $isLogout = str_contains(strtolower((string)($row->description ?? '')), 'logged out');

            if ($isLogin  && $sessionMap[$uid]['last_login']  === null) {
                $sessionMap[$uid]['last_login'] = $row->issued_at;
            }
            if ($isLogout && $sessionMap[$uid]['last_logout'] === null) {
                $sessionMap[$uid]['last_logout'] = $row->issued_at;
            }
        }

        // Re-fetch in a cleaner way: separate login / logout lists
        $loginEvents = $db->table('audit_logs al')
            ->select("al.user_id, u.name AS user_name, r.name AS user_type, al.timestamp, al.description")
            ->join('users u', 'u.id = al.user_id', 'left')
            ->join('roles r', 'r.id = u.role_id',  'left')
            ->where('al.action', 'Login')
            ->orderBy('al.timestamp', 'DESC')
            ->limit(100)
            ->get()->getResultObject();

        $logoutEvents = $db->table('audit_logs al')
            ->select("al.user_id, al.timestamp")
            ->where('al.action', 'Logout')
            ->orderBy('al.timestamp', 'DESC')
            ->limit(100)
            ->get()->getResultObject();

        // Build logout lookup: userId -> latest logout timestamp
        $logoutMap = [];
        foreach ($logoutEvents as $lo) {
            $uid = (int)$lo->user_id;
            if (!isset($logoutMap[$uid])) {
                $logoutMap[$uid] = $lo->timestamp;
            }
        }

        // Build the sessions table: one row per unique user (their last login)
        $sessionsTable = [];
        $seenUsers = [];
        foreach ($loginEvents as $ev) {
            $uid = (int)$ev->user_id;
            if (isset($seenUsers[$uid])) continue;
            $seenUsers[$uid] = true;

            $lastLogin  = $ev->timestamp;
            $lastLogout = $logoutMap[$uid] ?? null;

            // Status: if last logout is after last login → Expired, else → Active
            $status = 'active';
            if ($lastLogout && $lastLogout > $lastLogin) {
                $status = 'expired';
            }

            // Extract IP from description
            preg_match('/IP:\s*([\d\.]+)/', (string)($ev->description ?? ''), $ipMatch);
            $ip = $ipMatch[1] ?? '—';

            $sessionsTable[] = (object)[
                'user_id'     => $uid,
                'user_name'   => $ev->user_name ?? 'Unknown',
                'user_type'   => $ev->user_type ?? 'Unknown',
                'issued_at'   => $lastLogin,
                'last_seen'   => $lastLogin,
                'expires_at'  => date('Y-m-d H:i:s', strtotime($lastLogin . ' +8 hours')),
                'ip_address'  => $ip,
                'status'      => $status,
            ];
        }

        // ── 2. Recent Login Attempts ──────────────────────────────────────────────────
        $loginAttempts = $db->table('login_attempts la')
            ->select("la.*, u.name AS user_name, r.name AS role_name")
            ->join('users u', 'u.id = la.user_id', 'left')
            ->join('roles r', 'r.id = u.role_id',  'left')
            ->orderBy('la.created_at', 'DESC')
            ->limit(100)
            ->get()->getResultObject();

        // ── 3. Account Activity Events (login/logout from audit_logs) ─────────────────
        $activityEvents = $db->table('audit_logs al')
            ->select("al.id, al.user_id, u.name AS user_name, r.name AS user_type, al.action, al.description, al.timestamp")
            ->join('users u', 'u.id = al.user_id', 'left')
            ->join('roles r', 'r.id = u.role_id',  'left')
            ->whereIn('al.action', ['Login', 'Logout', 'Failed Login', 'Account Locked'])
            ->orderBy('al.timestamp', 'DESC')
            ->limit(60)
            ->get()->getResultObject();

        // ── 4. Stats ──────────────────────────────────────────────────────────────────
        $totalLogins  = $db->table('login_attempts')->where('result', 'success')->countAllResults();
        $totalFailed  = $db->table('login_attempts')->where('result', 'failed')->countAllResults();
        $todayLogins  = $db->table('login_attempts')
            ->where('result', 'success')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->countAllResults();
        $activeSessions = count(array_filter($sessionsTable, fn($s) => $s->status === 'active'));

        return view('auth/session_logs', [
            'sessionsTable'  => $sessionsTable,
            'loginAttempts'  => $loginAttempts,
            'activityEvents' => $activityEvents,
            'stats' => [
                'total_logins'    => $totalLogins,
                'total_failed'    => $totalFailed,
                'today_logins'    => $todayLogins,
                'active_sessions' => $activeSessions,
            ],
        ]);
    }
}
