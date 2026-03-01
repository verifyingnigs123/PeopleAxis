<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Notification extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Get all notifications for logged-in user
     */
    public function getNotifications()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $userId = session()->get('user_id');
        $limit = $this->request->getVar('limit') ?? 10;

        try {
            $notifications = $this->notificationModel->getUserNotifications($userId, (int)$limit);
            
            return $this->response->setJSON([
                'success' => true,
                'notifications' => $notifications,
                'count' => count($notifications)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch notifications: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to fetch notifications'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $userId = session()->get('user_id');

        try {
            $unreadCount = $this->notificationModel->getUnreadCount($userId);
            
            return $this->response->setJSON([
                'success' => true,
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch unread count: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to fetch unread count'
            ])->setStatusCode(500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $userId = session()->get('user_id');

        try {
            $this->notificationModel->markAsRead($notificationId, $userId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to mark notification as read: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ])->setStatusCode(500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $userId = session()->get('user_id');

        try {
            $this->notificationModel->markAllAsRead($userId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to mark all notifications as read: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark all notifications as read'
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete a notification
     */
    public function delete($notificationId)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $userId = session()->get('user_id');

        try {
            $this->notificationModel->deleteNotification($notificationId, $userId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification deleted'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to delete notification: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete notification'
            ])->setStatusCode(500);
        }
    }
}
