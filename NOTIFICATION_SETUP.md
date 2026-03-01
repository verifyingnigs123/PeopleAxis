# Real-Time Notification System - Setup & Usage Guide

## Overview
A complete real-time notification system has been implemented for PeopleAxis. The system features:
- **Notification Bell** in the navbar with unread count badge
- **Real-time fetching** - Notifications refresh every 30 seconds
- **Interactive dropdown** - View, mark as read, and delete notifications
- **Multiple notification types** - Info, Success, Warning, Danger
- **API endpoints** for managing notifications
- **Helper functions** for easy notification creation throughout the app

## Database Setup
The notification system uses a `notifications` table with the following structure:
- `id` - Primary key
- `user_id` - Foreign key to users table
- `title` - Notification title
- `message` - Notification message
- `type` - Type: info, success, warning, danger
- `link` - Optional link for the notification
- `icon` - Font Awesome icon class
- `is_read` - Boolean flag for read status
- `created_at`, `updated_at`, `deleted_at` - Timestamps

The migration has already been run automatically.

## API Endpoints

### Get Notifications
```
GET /api/notifications?limit=10
```
**Response:**
```json
{
  "success": true,
  "notifications": [
    {
      "id": 1,
      "user_id": 1,
      "title": "New Leave Request",
      "message": "John Doe has submitted a new leave request.",
      "type": "info",
      "link": "/leaves",
      "icon": "fas fa-calendar-check",
      "is_read": false,
      "created_at": "2024-01-10 10:30:00",
      "updated_at": "2024-01-10 10:30:00"
    }
  ],
  "count": 1
}
```

### Get Unread Count
```
GET /api/notifications/unread-count
```
**Response:**
```json
{
  "success": true,
  "unread_count": 3
}
```

### Mark Notification as Read
```
POST /api/notifications/{notificationId}/read
```

### Mark All as Read
```
POST /api/notifications/mark-all-read
```

### Delete Notification
```
DELETE /api/notifications/{notificationId}
```

## Helper Functions

### 1. `createNotification()`
Create a single notification for a user.

**Usage:**
```php
createNotification(
    $userId,                    // int - User ID
    'New Leave Request',        // string - Title
    'John Doe submitted a leave request.',  // string - Message
    'info',                     // string - Type: info, success, warning, danger
    '/leaves',                  // string|null - Optional link
    'fas fa-calendar-check'     // string - Font Awesome icon
);
```

**Example in Controller:**
```php
public function submitLeave() {
    // ... your code ...
    
    // Notify HR Admins
    $hrAdmins = $this->userModel->where('role_id', 2)->findAll();
    foreach ($hrAdmins as $admin) {
        createNotification(
            $admin->id,
            'New Leave Request',
            'Employee ' . $employee->name . ' has submitted a leave request',
            'info',
            base_url('/leaves'),
            'fas fa-calendar-check'
        );
    }
}
```

### 2. `createBulkNotifications()`
Create notifications for multiple users at once.

**Usage:**
```php
$userIds = [1, 2, 3, 4, 5];
$created = createBulkNotifications(
    $userIds,
    'System Maintenance',
    'The system will be under maintenance on Friday',
    'warning',
    null,
    'fas fa-wrench'
);
```

### 3. `notifyLeaveRequest()`
Pre-formatted notification for leave requests.

**Usage:**
```php
// Notify when leave is submitted
notifyLeaveRequest($hrAdminId, 'John Doe', 'submitted', $leaveId);

// Notify when leave is approved
notifyLeaveRequest($employeeId, 'John Doe', 'approved', $leaveId);

// Notify when leave is rejected
notifyLeaveRequest($employeeId, 'John Doe', 'rejected', $leaveId);
```

### 4. `notifyUserAction()`
Pre-formatted notification for user management actions.

**Usage:**
```php
// User created
notifyUserAction($adminId, 'created', 'Jane Smith');

// User activated
notifyUserAction($userId, 'activated');

// User deactivated
notifyUserAction($userId, 'deactivated');
```

### 5. `notifyRoleAssignment()`
Notify when a role is assigned to a user.

**Usage:**
```php
notifyRoleAssignment($userId, 'Manager');
```

### 6. `getUnreadNotificationCount()`
Get the number of unread notifications for a user.

**Usage:**
```php
$unreadCount = getUnreadNotificationCount(session()->get('user_id'));
```

### 7. `markAllNotificationsAsRead()`
Mark all notifications as read for a user.

**Usage:**
```php
markAllNotificationsAsRead(session()->get('user_id'));
```

### 8. `sendSystemNotification()`
Send a notification to all users in the system.

**Usage:**
```php
sendSystemNotification(
    'System Update',
    'A new version of PeopleAxis has been released',
    'info',
    '/dashboard',
    'fas fa-star'
);
```

## Integration Examples

### Example 1: Notify on Leave Approval
In your `Leaves.php` controller:
```php
public function approveByManager($leaveId) {
    // ... approval logic ...
    
    $leave = $this->leaveModel->find($leaveId);
    $employee = $this->employeeModel->find($leave->employee_id);
    
    // Create notification for employee
    notifyLeaveRequest(
        $employee->user_id,
        session()->get('name'),
        'approved',
        $leaveId
    );
    
    return redirect()->to('/leaves')->with('success', 'Leave approved');
}
```

### Example 2: Notify on User Creation
In your `Users.php` controller:
```php
public function store() {
    // ... user creation logic ...
    
    $newUserId = $this->userModel->insert($userData);
    
    // Notify the new user
    notifyUserAction($newUserId, 'created', $userData['name']);
    
    // Notify Super Admins
    $admins = $this->userModel->where('role_id', 1)->findAll();
    foreach ($admins as $admin) {
        createNotification(
            $admin->id,
            'New User Created',
            'User ' . $userData['name'] . ' has been created',
            'success',
            base_url('/users'),
            'fas fa-user-plus'
        );
    }
}
```

### Example 3: Notify on Attendance Check-in
In your `Attendance.php` controller:
```php
public function checkin() {
    // ... check-in logic ...
    
    createNotification(
        session()->get('user_id'),
        'Check-in Successful',
        'You have checked in at ' . date('h:i A'),
        'success',
        '/attendance',
        'fas fa-check-circle'
    );
}
```

## Notification Types & Icons

### Default Types
- **info** - General information (fas fa-circle-info)
- **success** - Success messages (fas fa-check-circle)
- **warning** - Warning messages (fas fa-exclamation-triangle)
- **danger** - Error/danger messages (fas fa-times-circle)

### Recommended Icons
- Leave related: `fas fa-calendar-check`, `fas fa-calendar-times`
- User related: `fas fa-user-plus`, `fas fa-user-minus`, `fas fa-user-circle`
- Approval: `fas fa-thumbs-up`, `fas fa-thumbs-down`, `fas fa-hourglass`
- Attendance: `fas fa-clock-in`, `fas fa-sign-in-alt`, `fas fa-sign-out-alt`
- System: `fas fa-bell`, `fas fa-info-circle`, `fas fa-cog`

Check [Font Awesome Icons](https://fontawesome.com/icons) for more options.

## Frontend Features

### Notification Bell
Located in the top navigation bar, the notification bell displays:
- A bell icon with unread count badge (shown when there are unread notifications)
- Dropdown menu with all notifications
- Time ago display for each notification
- Direct action buttons (View, Delete)

### Auto-refresh
Notifications automatically refresh every 30 seconds. To manually refresh, click the bell icon.

### Marking as Read
- Click "Mark all read" button to mark all notifications as read
- Each notification shows as unread (highlighted in light blue) when not read

### Deleting Notifications
Click the "Delete" button on any notification to remove it.

## Database Cleanup

To delete old notifications (older than 30 days), use the model method:
```php
$notificationModel = new \App\Models\NotificationModel();
$deleted = $notificationModel->deleteOldNotifications(30); // 30 days
```

You can add this to a scheduled task or cron job to keep the database clean.

## Troubleshooting

### Notifications not showing
1. Verify the migration ran successfully: `php spark migrate:status`
2. Check that the notifications table exists in your database
3. Verify the user is logged in
4. Check browser console for JavaScript errors

### Badge not updating
1. Clear browser cache
2. Check that the API endpoint is accessible: `/api/notifications/unread-count`
3. Verify user session is active

### CSRF Token errors
The system uses CodeIgniter's CSRF protection. Ensure:
- `csrf_meta()` is included in your template
- The CSRF token is passed in request headers (automatically handled by the JavaScript)

## Best Practices

1. **Use specific titles** - Make titles descriptive so users know what's important
2. **Keep messages concise** - Users quickly glance at notifications
3. **Provide links** - Include links to relevant pages when possible
4. **Use appropriate types** - Use 'success' for positive actions, 'danger' for errors
5. **Don't overload** - Avoid sending too many notifications (limit to important events)
6. **Set correct icons** - Use consistent, recognizable icons for similar actions

## Advanced Customization

### Change refresh interval
Edit the JavaScript in `header.php` at the bottom:
```javascript
// Refresh notifications every 30 seconds
setInterval(fetchNotifications, 30000); // Change 30000 to desired milliseconds
```

### Customize styles
All notification styles are in the `<style>` section of `header.php`. Look for the `.notification-*` classes.

### Add sound notification
Add this to the notification rendering function to play a sound:
```javascript
const audio = new Audio('<?= base_url('/path/to/sound.mp3') ?>');
audio.play();
```

## Security Considerations

1. **User isolation** - Notifications are filtered by user_id, preventing cross-user access
2. **CSRF Protection** - All API requests are protected with CSRF tokens
3. **Input validation** - All user inputs are sanitized and escaped
4. **Authorization** - Only logged-in users can access notifications
5. **HTML escaping** - All notification content is HTML-escaped to prevent XSS

## Performance Optimization

- Notifications are indexed by `user_id`, `is_read`, and `created_at`
- Old notifications (>30 days) can be automatically deleted
- Pagination support available (use `limit` parameter)
- Foreign key constraints ensure data integrity

---

For support or issues, refer to the main README.md or check the code comments in:
- `app/Models/NotificationModel.php`
- `app/Controllers/Notification.php`
- `app/Helpers/NotificationHelper.php`
