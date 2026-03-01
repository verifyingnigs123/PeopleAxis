<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToRoles extends Migration
{
    public function up()
    {
        $this->forge->addColumn('roles', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
                'after' => 'updated_at'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('roles', 'deleted_at');
    }
}
