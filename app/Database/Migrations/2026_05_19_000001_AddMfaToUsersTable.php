<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMfaToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'mfa_enabled' => [
                'type'    => 'TINYINT',
                'default' => 0,
                'comment' => 'Whether MFA is enabled for this user',
            ],
            'mfa_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'email',
                'comment'    => 'MFA method: email',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['mfa_enabled', 'mfa_method']);
    }
}
