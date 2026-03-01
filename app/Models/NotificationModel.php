<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $allowedFields = [
        'user_id',
        'title',
        'message',
        'type',
        'link',
        'is_read',
        'icon',
        'created_at',
        'updated_at'
    ];

    /**
     * Get unread notifications for a user
     */
    public function getUnreadNotifications($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', false)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get all notifications for a user with limit
     */
    public function getUserNotifications($userId, $limit = 10)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', false)
                    ->countAllResults();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId)
    {
        return $this->where('id', $notificationId)
                    ->where('user_id', $userId)
                    ->update(['is_read' => true]);
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId)
    {
        return $this->where('user_id', $userId)
                    ->update(['is_read' => true]);
    }

    /**
     * Create a new notification
     */
    public function createNotification($userId, $title, $message, $type = 'info', $link = null, $icon = 'fas fa-bell')
    {
        return $this->insert([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'is_read' => false,
            'icon' => $icon,
        ]);
    }

    /**
     * Delete old notifications (older than 30 days)
     */
    public function deleteOldNotifications($days = 30)
    {
        $dateLimit = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->where('created_at <', $dateLimit)->delete();
    }

    /**
     * Delete notification by ID and user ID
     */
    public function deleteNotification($notificationId, $userId)
    {
        return $this->where('id', $notificationId)
                    ->where('user_id', $userId)
                    ->delete();
    }
}
