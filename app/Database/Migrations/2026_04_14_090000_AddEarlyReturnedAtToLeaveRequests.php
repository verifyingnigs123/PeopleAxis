<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEarlyReturnedAtToLeaveRequests extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('leave_requests')) {
            return;
        }

        if (! $this->db->fieldExists('early_returned_at', 'leave_requests')) {
            $this->forge->addColumn('leave_requests', [
                'early_returned_at' => [
                    'type'  => 'DATETIME',
                    'null'  => true,
                    'after' => 'approved_by_hr',
                ],
            ]);
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('leave_requests')) {
            return;
        }

        if ($this->db->fieldExists('early_returned_at', 'leave_requests')) {
            $this->forge->dropColumn('leave_requests', 'early_returned_at');
        }
    }
}
