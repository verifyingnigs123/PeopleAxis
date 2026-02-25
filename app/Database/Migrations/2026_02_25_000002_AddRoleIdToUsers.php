<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleIdToUsers extends Migration
{
    public function up()
    {
        // Add username and role_id columns to users
        $fields = [
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ];

        $this->forge->addColumn('users', $fields);

        // Optional: set role_id based on existing role text if present
        $db = \Config\Database::connect();
        $users = $db->table('users')->get()->getResult();
        foreach ($users as $u) {
            if (!empty($u->role)) {
                $roleName = null;
                switch (strtolower($u->role)) {
                    case 'super':
                    case 'super admin':
                    case 'admin':
                        $roleName = 'Super Admin';
                        break;
                    case 'hr':
                    case 'hr admin':
                        $roleName = 'HR Admin';
                        break;
                    case 'manager':
                        $roleName = 'Manager';
                        break;
                    default:
                        $roleName = 'Employee';
                }

                $role = $db->table('roles')->where('name', $roleName)->get()->getRow();
                if ($role) {
                    $db->table('users')->where('id', $u->id)->update(['role_id' => $role->id]);
                }
            }
        }
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'username');
        $this->forge->dropColumn('users', 'role_id');
    }
}
