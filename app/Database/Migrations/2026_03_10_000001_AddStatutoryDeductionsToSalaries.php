<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatutoryDeductionsToSalaries extends Migration
{
    public function up()
    {
        // Add Philippine statutory deduction columns to salaries table
        $fields = [
            'sss_contribution' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
                'after'      => 'deductions',
            ],
            'philhealth_contribution' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
                'after'      => 'sss_contribution',
            ],
            'pagibig_contribution' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
                'after'      => 'philhealth_contribution',
            ],
            'withholding_tax' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
                'after'      => 'pagibig_contribution',
            ],
        ];

        $this->forge->addColumn('salaries', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('salaries', [
            'sss_contribution',
            'philhealth_contribution',
            'pagibig_contribution',
            'withholding_tax',
        ]);
    }
}
