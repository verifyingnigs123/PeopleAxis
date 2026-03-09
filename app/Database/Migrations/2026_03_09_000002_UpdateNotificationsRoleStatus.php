<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateNotificationsRoleStatus extends Migration
{
    public function up()
    {
        if (!$this->tableExists('notifications')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => false,
                ],
                'role' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => false,
                ],
                'message' => [
                    'type' => 'TEXT',
                    'null' => false,
                ],
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['read', 'unread'],
                    'default' => 'unread',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->addKey('role');
            $this->forge->addKey('status');
            $this->forge->addKey('created_at');
            $this->forge->createTable('notifications', true);
            return;
        }

        $fieldsToAdd = [];

        if (!$this->fieldExists('notifications', 'role')) {
            $fieldsToAdd['role'] = [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'user_id',
            ];
        }

        if (!$this->fieldExists('notifications', 'status')) {
            $fieldsToAdd['status'] = [
                'type' => 'ENUM',
                'constraint' => ['read', 'unread'],
                'default' => 'unread',
                'after' => 'message',
            ];
        }

        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('notifications', $fieldsToAdd);
        }

        // Backfill status from existing is_read values if present.
        if ($this->fieldExists('notifications', 'is_read')) {
            $this->db->query("UPDATE notifications SET status = CASE WHEN is_read = 1 THEN 'read' ELSE 'unread' END WHERE status IS NULL OR status = ''");
        } else {
            $this->db->query("UPDATE notifications SET status = 'unread' WHERE status IS NULL OR status = ''");
        }

        // Backfill role by joining users -> roles when those tables exist.
        if ($this->tableExists('users') && $this->tableExists('roles')) {
            $this->db->query("UPDATE notifications n JOIN users u ON u.id = n.user_id JOIN roles r ON r.id = u.role_id SET n.role = r.name WHERE n.role IS NULL OR n.role = ''");
        }

        // Ensure no null role remains.
        $this->db->query("UPDATE notifications SET role = 'Employee' WHERE role IS NULL OR role = ''");

        // Add indexes if missing.
        try {
            $this->db->query('CREATE INDEX idx_notifications_role ON notifications(role)');
        } catch (\Throwable $e) {
            // Ignore duplicate-index errors.
        }

        try {
            $this->db->query('CREATE INDEX idx_notifications_status ON notifications(status)');
        } catch (\Throwable $e) {
            // Ignore duplicate-index errors.
        }
    }

    public function down()
    {
        if ($this->tableExists('notifications')) {
            if ($this->fieldExists('notifications', 'role')) {
                $this->forge->dropColumn('notifications', 'role');
            }
            if ($this->fieldExists('notifications', 'status')) {
                $this->forge->dropColumn('notifications', 'status');
            }
        }
    }

    private function tableExists(string $table): bool
    {
        $safeTable = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $result = $this->db->query("SHOW TABLES LIKE '{$safeTable}'")->getRowArray();
        return !empty($result);
    }

    private function fieldExists(string $table, string $field): bool
    {
        $safeTable = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $safeField = preg_replace('/[^a-zA-Z0-9_]/', '', $field);
        $result = $this->db->query("SHOW COLUMNS FROM `{$safeTable}` LIKE '{$safeField}'")->getRowArray();
        return !empty($result);
    }
}
