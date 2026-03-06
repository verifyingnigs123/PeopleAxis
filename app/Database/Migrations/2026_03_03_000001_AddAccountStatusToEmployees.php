<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAccountStatusToEmployees extends Migration
{
    public function up()
    {
        $this->forge->addColumn('employees', [
            'account_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
                'after' => 'status',
                'comment' => 'Status of system account: pending (waiting for approval), approved (account created), rejected'
            ],
            'approval_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'user_id',
                'comment' => 'Notes for approval or rejection'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('employees', ['account_status', 'approval_notes']);
    }
}
