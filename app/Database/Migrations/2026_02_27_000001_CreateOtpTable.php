<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOtpTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'otp' => [
                'type'       => 'VARCHAR',
                'constraint' => '6',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'is_used' => [
                'type'    => 'TINYINT',
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', false, true);
        $this->forge->addKey('email');
        $this->forge->createTable('otp');
    }

    public function down()
    {
        $this->forge->dropTable('otp');
    }
}
