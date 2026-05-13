<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPrivilegesToRolesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('roles', [
            'privileges' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'JSON array of role privileges/permissions',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('roles', 'privileges');
    }
}
