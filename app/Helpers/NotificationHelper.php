<?php

/**
 * Notification Helper
 * This helper provides easy functions to create and manage notifications
 */

use App\Models\NotificationModel;

/**
 * Create a notification for a user
 * 
 * @param int $userId The ID of the user to notify
 * @param string $title The notification title
 * @param string $message The notification message
 * @param string $type The notification type (info, success, warning, danger)
 * @param string|null $link The optional link for the notification
 * @param string $icon The Font Awesome icon class
 * 
 * @return bool
 */
if (!function_exists('createNotification')) {
    function createNotification($userId, $title, $message, $type = 'info', $link = null, $icon = 'fas fa-bell')
    {
        try {
            $notificationModel = new NotificationModel();
            return $notificationModel->createNotification($userId, $title, $message, $type, $link, $icon);
        } catch (\Exception $e) {
            log_message('error', 'Failed to create notification: ' . $e->getMessage());
            return false;
        }
    }
}

/**
 * Create notifications for multiple users
 * 
 * @param array $userIds Array of user IDs
 * @param string $title The notification title
 * @param string $message The notification message
 * @param string $type The notification type (info, success, warning, danger)
 * @param string|null $link The optional link for the notification
 * @param string $icon The Font Awesome icon class
 * 
 * @return int Number of notifications created
 */
if (!function_exists('createBulkNotifications')) {
    function createBulkNotifications($userIds, $title, $message, $type = 'info', $link = null, $icon = 'fas fa-bell')
    {
        $created = 0;
        foreach ($userIds as $userId) {
            if (createNotification($userId, $title, $message, $type, $link, $icon)) {
                $created++;
            }
        }
        return $created;
    }
}

/**
 * Get unread notification count for a user
 * 
 * @param int $userId The user ID
 * 
 * @return int
 */
if (!function_exists('getUnreadNotificationCount')) {
    function getUnreadNotificationCount($userId)
    {
        try {
            $notificationModel = new NotificationModel();
            return $notificationModel->getUnreadCount($userId);
        } catch (\Exception $e) {
            log_message('error', 'Failed to get unread count: ' . $e->getMessage());
            return 0;
        }
    }
}

/**
 * Mark all notifications as read for a user
 * 
 * @param int $userId The user ID
 * 
 * @return bool
 */
if (!function_exists('markAllNotificationsAsRead')) {
    function markAllNotificationsAsRead($userId)
    {
        try {
            $notificationModel = new NotificationModel();
            return $notificationModel->markAllAsRead($userId);
        } catch (\Exception $e) {
            log_message('error', 'Failed to mark all as read: ' . $e->getMessage());
            return false;
        }
    }
}

/**
 * Send notification for leave request
 * 
 * @param int $userId User ID
 * @param string $employeeName Employee name
 * @param string $action Action (submitted, approved, rejected)
 * @param string|null $leaveId Leave ID for link
 * 
 * @return bool
 */
if (!function_exists('notifyLeaveRequest')) {
    function notifyLeaveRequest($userId, $employeeName, $action, $leaveId = null)
    {
        $titles = [
            'submitted' => 'New Leave Request',
            'approved' => 'Leave Request Approved',
            'rejected' => 'Leave Request Rejected',
        ];

        $messages = [
            'submitted' => "{$employeeName} has submitted a new leave request.",
            'approved' => "Your leave request has been approved.",
            'rejected' => "Your leave request has been rejected.",
        ];

        $types = [
            'submitted' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
        ];

        $icons = [
            'submitted' => 'fas fa-calendar-check',
            'approved' => 'fas fa-check-circle',
            'rejected' => 'fas fa-times-circle',
        ];

        $title = $titles[$action] ?? 'Leave Request';
        $message = $messages[$action] ?? '';
        $type = $types[$action] ?? 'info';
        $icon = $icons[$action] ?? 'fas fa-bell';
        $link = $leaveId ? base_url('/leaves') : null;

        return createNotification($userId, $title, $message, $type, $link, $icon);
    }
}

/**
 * Send notification for user management
 * 
 * @param int $userId User ID
 * @param string $action Action (created, activated, deactivated)
 * @param string|null $userName User name
 * 
 * @return bool
 */
if (!function_exists('notifyUserAction')) {
    function notifyUserAction($userId, $action, $userName = null)
    {
        $titles = [
            'created' => 'User Account Created',
            'activated' => 'Account Activated',
            'deactivated' => 'Account Deactivated',
        ];

        $messages = [
            'created' => "A new user account has been created for {$userName}.",
            'activated' => "Your account has been activated.",
            'deactivated' => "Your account has been deactivated.",
        ];

        $types = [
            'created' => 'info',
            'activated' => 'success',
            'deactivated' => 'warning',
        ];

        $icons = [
            'created' => 'fas fa-user-plus',
            'activated' => 'fas fa-check-circle',
            'deactivated' => 'fas fa-ban',
        ];

        $title = $titles[$action] ?? 'User Action';
        $message = $messages[$action] ?? '';
        $type = $types[$action] ?? 'info';
        $icon = $icons[$action] ?? 'fas fa-bell';

        return createNotification($userId, $title, $message, $type, null, $icon);
    }
}

/**
 * Send notification for role assignment
 * 
 * @param int $userId User ID
 * @param string $roleName Role name
 * 
 * @return bool
 */
if (!function_exists('notifyRoleAssignment')) {
    function notifyRoleAssignment($userId, $roleName)
    {
        return createNotification(
            $userId,
            'Role Assigned',
            "You have been assigned the {$roleName} role.",
            'success',
            base_url('/profile'),
            'fas fa-shield-alt'
        );
    }
}

/**
 * Send system notification to all users
 * 
 * @param string $title Notification title
 * @param string $message Notification message
 * @param string $type Notification type
 * @param string|null $link Optional link
 * @param string $icon Font Awesome icon
 * 
 * @return int Number of notifications sent
 */
if (!function_exists('sendSystemNotification')) {
    function sendSystemNotification($title, $message, $type = 'info', $link = null, $icon = 'fas fa-bullhorn')
    {
        try {
            $userModel = new \App\Models\UserModel();
            $users = $userModel->findAll();
            $userIds = array_map(fn($user) => $user->id, $users);
            return createBulkNotifications($userIds, $title, $message, $type, $link, $icon);
        } catch (\Exception $e) {
            log_message('error', 'Failed to send system notification: ' . $e->getMessage());
            return 0;
        }
    }
}
