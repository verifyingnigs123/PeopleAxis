<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAttemptsToOtpTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('otp', [
            'attempts' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'is_used',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('otp', 'attempts');
    }
}
