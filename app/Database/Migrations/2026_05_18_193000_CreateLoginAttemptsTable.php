<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoginAttemptsTable extends Migration
{
    public function up()
    {
        $tableExists = static function ($db, string $table): bool {
            $row = $db->query(
                'SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?',
                [$table]
            )->getRowArray();
            return ((int) ($row['cnt'] ?? 0)) > 0;
        };

        if ($tableExists($this->db, 'login_attempts')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'result' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'reason' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('email');
        $this->forge->addKey('result');
        $this->forge->addKey('created_at');
        $this->forge->createTable('login_attempts', true);

        // Backfill historical attempts from audit logs for immediate reporting.
        if ($tableExists($this->db, 'audit_logs')) {
            $auditRows = $this->db->table('audit_logs')
                ->select('user_id, action, timestamp')
                ->whereIn('action', ['Login', 'Failed Login', 'Account Locked'])
                ->orderBy('timestamp', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($auditRows as $row) {
                $action = (string) ($row['action'] ?? '');
                $result = $action === 'Login' ? 'success' : 'failed';
                $reason = null;
                if ($action === 'Failed Login') {
                    $reason = 'invalid_credentials';
                } elseif ($action === 'Account Locked') {
                    $reason = 'account_locked';
                }

                $this->db->table('login_attempts')->insert([
                    'user_id' => $row['user_id'] ?? null,
                    'result' => $result,
                    'reason' => $reason,
                    'created_at' => $row['timestamp'] ?? date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('login_attempts', true);
    }
}
