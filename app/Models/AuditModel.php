<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'action', 'description', 'timestamp'];

    public function log($userId, $action, $description = null)
    {
        $this->insert([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}
