<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToEmployees extends Migration
{
    public function up()
    {
        $fields = [
            'biometric_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'rate' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'rate_type' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'employment_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'date_hired' => [
                'type' => 'DATE',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('employees', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('employees', 'biometric_id');
        $this->forge->dropColumn('employees', 'rate');
        $this->forge->dropColumn('employees', 'rate_type');
        $this->forge->dropColumn('employees', 'employment_type');
        $this->forge->dropColumn('employees', 'date_hired');
    }
}
