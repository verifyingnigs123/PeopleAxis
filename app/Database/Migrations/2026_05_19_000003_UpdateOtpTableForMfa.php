<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateOtpTableForMfa extends Migration
{
    public function up()
    {
        // Check if columns don't exist before adding
        $fields = $this->db->getFieldData('otp');
        $fieldNames = array_column($fields, 'name');

        if (!in_array('mfa_context', $fieldNames)) {
            $this->forge->addColumn('otp', [
                'mfa_context' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'password_reset',
                    'comment'    => 'OTP context: password_reset or login',
                ],
            ]);
        }

        if (!in_array('user_id', $fieldNames)) {
            $this->forge->addColumn('otp', [
                'user_id' => [
                    'type'    => 'INT',
                    'null'    => true,
                    'comment' => 'User ID for login MFA',
                ],
            ]);
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldData('otp');
        $fieldNames = array_column($fields, 'name');

        if (in_array('mfa_context', $fieldNames)) {
            $this->forge->dropColumn('otp', 'mfa_context');
        }

        if (in_array('user_id', $fieldNames)) {
            $this->forge->dropColumn('otp', 'user_id');
        }
    }
}
