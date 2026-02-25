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
        'password' => 'permit_empty|min_length[6]',
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
        if (isset($data['data']['password']) && is_string($data['data']['password']) && strlen(trim($data['data']['password'])) > 0) {
            $data['data']['password'] = password_hash(trim($data['data']['password']), PASSWORD_BCRYPT);
        } else {
            // If password is present but empty, remove it so it won't overwrite existing value
            if (isset($data['data']['password'])) {
                unset($data['data']['password']);
            }
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

    /**
     * Soft delete user (deactivate)
     */
    public function deactivateUser($id)
    {
        return $this->update($id, ['is_active' => 0]);
    }

    /**
     * Activate user
     */
    public function activateUser($id)
    {
        return $this->update($id, ['is_active' => 1]);
    }

    /**
     * Get all users (including inactive) for admin
     */
    public function getAllUsers()
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Get user status text
     */
    public function getStatusText($isActive)
    {
        return $isActive ? 'ACTIVE' : 'INACTIVE';
    }

    /**
     * Get users by status
     */
    public function getUsersByStatus($status)
    {
        $isActive = ($status === 'ACTIVE') ? 1 : 0;
        return $this->where('is_active', $isActive)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Count users by status
     */
    public function countUsersByStatus($status)
    {
        $isActive = ($status === 'ACTIVE') ? 1 : 0;
        return $this->where('is_active', $isActive)->countAllResults();
    }
}
