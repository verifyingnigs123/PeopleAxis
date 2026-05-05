<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfilePhotoModel extends Model
{
    protected $table = 'profile_photos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'user_id',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'uploaded_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';

    /**
     * Get the latest profile photo for a specific user
     */
    public function getLatestByUserId(int $userId)
    {
        return $this->where('user_id', $userId)
            ->where('deleted_at', null)
            ->orderBy('uploaded_at', 'DESC')
            ->first();
    }

    /**
     * Get all profile photos for a specific user (including soft deleted ones)
     */
    public function getAllByUserId(int $userId)
    {
        return $this->where('user_id', $userId)
            ->orderBy('uploaded_at', 'DESC')
            ->findAll();
    }

    /**
     * Save a new profile photo
     */
    public function saveProfilePhoto(int $userId, string $filePath, string $originalFilename = null, int $fileSize = null, string $mimeType = null)
    {
        return $this->insert([
            'user_id' => $userId,
            'file_path' => $filePath,
            'original_filename' => $originalFilename,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'uploaded_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Soft delete a profile photo
     */
    public function softDelete(int $photoId)
    {
        return $this->update($photoId, [
            'deleted_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Hard delete a profile photo and its file
     */
    public function hardDeletePhoto(int $photoId)
    {
        $photo = $this->find($photoId);
        if ($photo) {
            // Delete physical file
            if (!empty($photo->file_path)) {
                $filePath = FCPATH . $photo->file_path;
                if (is_file($filePath)) {
                    @unlink($filePath);
                }
            }
            // Delete database record
            return $this->delete($photoId, true);
        }
        return false;
    }

    /**
     * Get profile photos by user role
     */
    public function getPhotosByRole(string $role)
    {
        return $this->join('users', 'users.id = profile_photos.user_id')
            ->join('roles', 'roles.id = users.role_id')
            ->where('roles.name', $role)
            ->where('profile_photos.deleted_at', null)
            ->where('users.deleted_at', null)
            ->orderBy('profile_photos.uploaded_at', 'DESC')
            ->findAll();
    }

    /**
     * Get all active profile photos (excluding soft deleted)
     */
    public function getActivePhotos()
    {
        return $this->where('deleted_at', null)
            ->orderBy('uploaded_at', 'DESC')
            ->findAll();
    }

    /**
     * Get profile photos count by role
     */
    public function getCountByRole(string $role)
    {
        return $this->join('users', 'users.id = profile_photos.user_id')
            ->join('roles', 'roles.id = users.role_id')
            ->where('roles.name', $role)
            ->where('profile_photos.deleted_at', null)
            ->where('users.deleted_at', null)
            ->countAllResults();
    }
}
