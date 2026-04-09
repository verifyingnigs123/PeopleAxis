<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleSlugToUsers extends Migration
{
    private function usersTableExists(): bool
    {
        $res = $this->db->query("SHOW TABLES LIKE 'users'")->getResultArray();
        return ! empty($res);
    }

    private function usersRoleColumnExists(): bool
    {
        if (! $this->usersTableExists()) {
            return false;
        }

        $res = $this->db->query("SHOW COLUMNS FROM users LIKE 'role'")->getResultArray();
        return ! empty($res);
    }

    public function up()
    {
        if (! $this->usersTableExists()) {
            return;
        }

        if (! $this->usersRoleColumnExists()) {
            $this->forge->addColumn('users', [
                'role' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'employee',
                    'null'       => false,
                ],
            ]);
        }

        $db = \Config\Database::connect();

        $roleNameToSlug = [
            'super admin' => 'super_admin',
            'hr admin'    => 'hr_admin',
            'manager'     => 'manager',
            'employee'    => 'employee',
        ];

        $legacyRoleToSlug = [
            'super_admin' => 'super_admin',
            'super admin' => 'super_admin',
            'super'       => 'super_admin',
            'admin'       => 'super_admin',
            'hr_admin'    => 'hr_admin',
            'hr admin'    => 'hr_admin',
            'hr'          => 'hr_admin',
            'manager'     => 'manager',
            'employee'    => 'employee',
            'user'        => 'employee',
        ];

        $users = $db->table('users')
            ->select('users.id, users.role, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->get()
            ->getResult();

        foreach ($users as $user) {
            $slug = 'employee';

            $roleNameKey = strtolower((string) ($user->role_name ?? ''));
            $roleKey = strtolower((string) ($user->role ?? ''));

            if (isset($roleNameToSlug[$roleNameKey])) {
                $slug = $roleNameToSlug[$roleNameKey];
            } elseif (isset($legacyRoleToSlug[$roleKey])) {
                $slug = $legacyRoleToSlug[$roleKey];
            }

            $db->table('users')
                ->where('id', $user->id)
                ->update(['role' => $slug]);
        }

        // Keep role_id and role slug aligned.
        $roles = $db->table('roles')->select('id, name')->get()->getResult();
        $roleIdMap = [];
        foreach ($roles as $role) {
            $nameKey = strtolower((string) ($role->name ?? ''));
            if (isset($roleNameToSlug[$nameKey])) {
                $roleIdMap[$roleNameToSlug[$nameKey]] = (int) $role->id;
            }
        }

        $usersForRoleId = $db->table('users')->select('id, role')->get()->getResult();
        foreach ($usersForRoleId as $user) {
            $userRole = strtolower((string) ($user->role ?? 'employee'));
            if (isset($roleIdMap[$userRole])) {
                $db->table('users')
                    ->where('id', $user->id)
                    ->update(['role_id' => $roleIdMap[$userRole]]);
            }
        }

        // Explicitly enforce expected seed account role slugs.
        $db->table('users')->where('email', 'hradmin@peopleaxis.com')->update(['role' => 'hr_admin']);
        $db->table('users')->where('email', 'superadmin@peopleaxis.com')->update(['role' => 'super_admin']);

        if (isset($roleIdMap['hr_admin'])) {
            $db->table('users')->where('email', 'hradmin@peopleaxis.com')->update(['role_id' => $roleIdMap['hr_admin']]);
        }
        if (isset($roleIdMap['super_admin'])) {
            $db->table('users')->where('email', 'superadmin@peopleaxis.com')->update(['role_id' => $roleIdMap['super_admin']]);
        }
    }

    public function down()
    {
        if (! $this->usersTableExists()) {
            return;
        }

        if ($this->usersRoleColumnExists()) {
            $this->forge->dropColumn('users', 'role');
        }
    }
}
