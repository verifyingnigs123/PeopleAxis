<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginAttemptModel extends Model
{
    protected $table            = 'login_attempts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'user_type', 'user_id', 'email',
        'ip_address', 'user_agent', 'result', 'reason', 'created_at',
    ];
    protected $useTimestamps    = false; // we manage created_at manually

    /**
     * Record a login attempt.
     */
    public function record(
        string  $result,           // 'success' | 'failed'
        ?int    $userId    = null,
        ?string $email     = null,
        ?string $userType  = null,
        ?string $reason    = null,
        ?string $ip        = null,
        ?string $userAgent = null
    ): void {
        try {
            $this->insert([
                'result'     => $result,
                'user_id'    => $userId,
                'email'      => $email,
                'user_type'  => $userType,
                'reason'     => $reason,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[LoginAttemptModel] Failed to record attempt: ' . $e->getMessage());
        }
    }

    /**
     * Count failed attempts in last N minutes for a given email/IP.
     */
    public function countRecentFailed(string $email, int $minutes = 15): int
    {
        $since = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
        return (int) $this
            ->where('email', $email)
            ->where('result', 'failed')
            ->where('created_at >=', $since)
            ->countAllResults();
    }
}
