<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['email', 'password', 'name', 'role', 'is_active'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'name'     => 'required|min_length[3]',
        'role'     => 'required|in_list[admin,user]',
    ];

    protected $validationMessages   = [
        'email' => [
            'required'    => 'Email is required.',
            'valid_email' => 'Please provide a valid email.',
            'is_unique'   => 'Email already exists.',
        ],
        'password' => [
            'required'    => 'Password is required.',
            'min_length'  => 'Password must be at least 6 characters.',
        ],
        'name' => [
            'required'   => 'Name is required.',
            'min_length' => 'Name must be at least 3 characters.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get user by email
     */
    public function getUserByEmail($email)
    {
        return $this->where('email', $email)
                    ->where('is_active', 1)
                    ->first();
    }

    /**
     * Get all active users
     */
    public function getActiveUsers()
    {
        return $this->where('is_active', 1)
                    ->findAll();
    }

    /**
     * Get all admins
     */
    public function getAdmins()
    {
        return $this->where('role', 'admin')
                    ->where('is_active', 1)
                    ->findAll();
    }

    /**
     * Get all regular users
     */
    public function getUsers()
    {
        return $this->where('role', 'user')
                    ->where('is_active', 1)
                    ->findAll();
    }

    /**
     * Hash password before saving
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }

        return $data;
    }

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Verify password
     */
    public function verifyPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }
}
