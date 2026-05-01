<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBreakTimesToAttendanceLogs extends Migration
{
    public function up()
    {
        // Add break_out and break_in columns to attendance_logs table
        $this->forge->addColumn('attendance_logs', [
            'break_out' => [
                'type' => 'TIME',
                'null' => true,
                'comment' => 'Time when employee goes on break',
            ],
            'break_in' => [
                'type' => 'TIME',
                'null' => true,
                'comment' => 'Time when employee returns from break',
            ],
        ]);
    }

    public function down()
    {
        // Drop the break_out and break_in columns
        $this->forge->dropColumn('attendance_logs', ['break_out', 'break_in']);
    }
}
