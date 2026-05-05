<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProfilePhotosTable extends Migration
{
    public function up()
    {
        // Create profile_photos table to store all profile pictures for all user roles
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'original_filename' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'file_size' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'mime_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'uploaded_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('deleted_at');
        $this->forge->addKey('created_at');
        
        // Create the table
        $this->forge->createTable('profile_photos', true);
        
        // Add foreign key constraint to users table
        try {
            $this->db->query("ALTER TABLE `profile_photos` ADD CONSTRAINT `fk_profile_photos_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE");
        } catch (\Exception $e) {
            // Constraint might already exist
        }
    }

    public function down()
    {
        // Drop foreign key if exists
        try {
            $this->db->query("ALTER TABLE `profile_photos` DROP FOREIGN KEY `fk_profile_photos_user`");
        } catch (\Exception $e) {
            // Constraint might not exist
        }
        
        // Drop table
        $this->forge->dropTable('profile_photos', true);
    }
}
