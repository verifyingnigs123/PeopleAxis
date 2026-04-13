<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNumberOfDaysToLeaveRequests extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('leave_requests')) {
            return;
        }

        if (! $this->db->fieldExists('number_of_days', 'leave_requests')) {
            $this->forge->addColumn('leave_requests', [
                'number_of_days' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'null'       => true,
                    'after'      => 'end_date',
                ],
            ]);
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('leave_requests')) {
            return;
        }

        if ($this->db->fieldExists('number_of_days', 'leave_requests')) {
            $this->forge->dropColumn('leave_requests', 'number_of_days');
        }
    }
}
