<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDeleteRequestsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'employee_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'requested_by' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'reason' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
            ],
            'reviewed_by' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'null' => true,
            ],
            'reviewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'review_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('employee_id');
        $this->forge->addKey('requested_by');
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        
        $this->forge->addForeignKey('employee_id', 'employees', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('requested_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('reviewed_by', 'users', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('delete_requests');
    }

    public function down()
    {
        $this->forge->dropTable('delete_requests');
    }
}
