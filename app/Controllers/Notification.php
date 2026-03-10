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
            $unreadCount = $this->notificationModel->getUnreadCount($userId);
            
            return $this->response->setJSON([
                'success' => true,
                'notifications' => $notifications,
                'count' => count($notifications),
                'unread_count' => $unreadCount,
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
     * Server-Sent Events stream for near real-time notification updates.
     */
    public function stream()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setBody('Unauthorized');
        }

        $userId = (int) session()->get('user_id');

        // CRITICAL: release the PHP session lock immediately.
        // Without this, every other page request that needs the session
        // (i.e. every page load) is blocked until this SSE stream ends.
        session_write_close();

        @ini_set('output_buffering', 'off');
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', '1');
        }

        ignore_user_abort(true);
        set_time_limit(0);

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache, no-transform');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        $lastUnread = null;
        $lastLatestId = null;
        $startedAt = time();
        $maxRuntimeSeconds = 25;

        while (!connection_aborted() && (time() - $startedAt) < $maxRuntimeSeconds) {
            $unreadCount = (int) $this->notificationModel->getUnreadCount($userId);
            $latest = $this->notificationModel
                ->where('user_id', $userId)
                ->orderBy('id', 'DESC')
                ->first();
            $latestId = $latest ? (int) $latest->id : 0;

            if ($unreadCount !== $lastUnread || $latestId !== $lastLatestId) {
                $payload = json_encode([
                    'unread_count' => $unreadCount,
                    'latest_notification_id' => $latestId,
                    'ts' => time(),
                ]);

                echo "event: notification\n";
                echo "data: {$payload}\n\n";

                $lastUnread = $unreadCount;
                $lastLatestId = $latestId;
            } else {
                // Keep-alive comment so proxies do not close idle streams.
                echo ": ping\n\n";
            }

            if (ob_get_level() > 0) {
                @ob_flush();
            }
            @flush();
            sleep(2);
        }

        echo "event: close\n";
        echo "data: {}\n\n";
        if (ob_get_level() > 0) {
            @ob_flush();
        }
        @flush();
        return;
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
                'success'   => true,
                'message'   => 'Notification marked as read',
                'csrf_hash' => csrf_hash(),
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
                'success'   => true,
                'message'   => 'All notifications marked as read',
                'csrf_hash' => csrf_hash(),
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
                'success'   => true,
                'message'   => 'Notification deleted',
                'csrf_hash' => csrf_hash(),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to delete notification: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete notification'
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete all notifications for the logged-in user
     */
    public function deleteAll()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $userId = session()->get('user_id');

        try {
            $this->notificationModel->deleteAllNotifications($userId);

            return $this->response->setJSON([
                'success'   => true,
                'message'   => 'All notifications deleted',
                'csrf_hash' => csrf_hash(),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to delete all notifications: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete all notifications'
            ])->setStatusCode(500);
        }
    }
}
