# Quick Reference - Notification System

## 🔔 Notification Bell Location
**Top Navigation Bar** - Left side, before User Menu dropdown. Shows red badge with unread count.

## 🚀 Quick Integration Examples

### Example 1: Notify on Leave Request Submission
```php
// In app/Controllers/Leaves.php
public function submit() {
    // ... validation and save code ...
    
    // Notify HR Admins
    $hrAdmins = $this->userModel->where('role_id', 2)->findAll();
    foreach ($hrAdmins as $admin) {
        createNotification(
            $admin->id,
            'New Leave Request',
            session()->get('name') . ' has submitted a leave request for ' . $days . ' days',
            'info',
            base_url('/leaves'),
            'fas fa-calendar-check'
        );
    }
    
    return redirect()->to('/leaves')->with('success', 'Leave request submitted');
}
```

### Example 2: Notify Employee When Leave is Approved
```php
// In app/Controllers/Leaves.php
public function approveByManager($leaveId) {
    // ... approval logic ...
    
    $leave = $this->leaveModel->find($leaveId);
    $employee = $this->employeeModel->find($leave->employee_id);
    
    // Notify employee
    notifyLeaveRequest(
        $employee->user_id,
        session()->get('name'),
        'approved',
        $leaveId
    );
    
    return redirect()->to('/leaves')->with('success', 'Leave approved');
}
```

### Example 3: Notify on User Creation
```php
// In app/Controllers/Users.php
public function store() {
    // ... user creation code ...
    
    $userId = $this->userModel->insert($userData);
    
    // Notify the new user
    notifyUserAction($userId, 'created');
    
    // Notify admins
    notifyBulkNotifications([1, 2, 3], 'New User Created', 'User ' . $userData['name'] . ' has been created', 'info');
}
```

### Example 4: Notify Multiple Users
```php
// Send to multiple users
$userIds = [1, 2, 3, 4, 5];
createBulkNotifications(
    $userIds,
    'System Update',
    'The system will be under maintenance tomorrow',
    'warning',
    null,
    'fas fa-exclamation-triangle'
);
```

### Example 5: System-wide Notification
```php
// Send to all users
sendSystemNotification(
    'Important Announcement',
    'New policy has been updated. Please review the changes.',
    'info',
    '/dashboard',
    'fas fa-bell'
);
```

## 📝 Notification Types and Colors

| Type | Color | Icon | Use Case |
|------|-------|------|----------|
| `info` | Blue | 🔵 | General information, reminders |
| `success` | Green | ✅ | Approved, completed, successful |
| `warning` | Yellow | ⚠️ | Pending, awaiting action, maintenance |
| `danger` | Red | ❌ | Rejected, errors, issues |

## 🎨 Custom Font Awesome Icons

Common icons to use:
- Leave: `fas fa-calendar-check`, `fas fa-calendar-times`
- User: `fas fa-user-plus`, `fas fa-user-minus`, `fas fa-user-circle`
- Approval: `fas fa-check-circle`, `fas fa-times-circle`, `fas fa-hourglass`
- Attendance: `fas fa-clock`, `fas fa-sign-in-alt`, `fas fa-sign-out-alt`
- Document: `fas fa-file`, `fas fa-document-check`
- Alert: `fas fa-bell`, `fas fa-exclamation-circle`, `fas fa-info-circle`

[Browse all icons at Font Awesome](https://fontawesome.com/icons)

## 🧪 Testing Notifications

1. Go to `/notification-demo` (Super Admin only)
2. Use the demo page to create test notifications
3. Check the notification bell in navbar to see them
4. Verify counts and auto-refresh

## 📊 API Endpoints

```
GET    /api/notifications                    - Get notifications
GET    /api/notifications/unread-count       - Get unread count
POST   /api/notifications/{id}/read          - Mark as read
POST   /api/notifications/mark-all-read      - Mark all as read
DELETE /api/notifications/{id}               - Delete notification
```

## 🔧 Helper Functions

```php
// Create single notification
createNotification($userId, $title, $message, $type, $link, $icon)

// Create for multiple users
createBulkNotifications($userIds, $title, $message, $type, $link, $icon)

// Get unread count
getUnreadNotificationCount($userId)

// Mark all as read
markAllNotificationsAsRead($userId)

// Pre-formatted notifications
notifyLeaveRequest($userId, $employeeName, 'submitted'|'approved'|'rejected', $leaveId)
notifyUserAction($userId, 'created'|'activated'|'deactivated', $userName)
notifyRoleAssignment($userId, $roleName)
sendSystemNotification($title, $message, $type, $link, $icon)
```

## 🔐 Security Notes

✓ Only logged-in users can access notifications
✓ Users only see their own notifications
✓ CSRF protection on all API endpoints
✓ All inputs are sanitized and escaped
✓ Super Admin required for demo page

## 📱 Mobile Support

- Responsive design works on all screen sizes
- Notification dropdown adjusts width for mobile
- Touch-friendly button sizes
- Accessible on iOS and Android

## 🐛 Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| Notifications not showing | Clear cache, check `/api/notifications` endpoint |
| Badge not updating | Refresh page, check database has new notifications |
| CSRF error | Ensure session is active, check X-CSRF-TOKEN header |
| Dropdown not opening | Check browser console for JavaScript errors |
| Notifications delay | Normal - refreshes every 30 seconds, click bell to manual refresh |

## 📚 Full Documentation

- **Setup Guide**: See `NOTIFICATION_SETUP.md`
- **Implementation Details**: See `NOTIFICATION_IMPLEMENTATION.md`
- **Model Code**: `app/Models/NotificationModel.php`
- **Helper Code**: `app/Helpers/NotificationHelper.php`
- **Controller Code**: `app/Controllers/Notification.php`

## ⚡ Performance Tips

1. Use `createBulkNotifications()` instead of loop for multiple users
2. Delete old notifications regularly (>30 days)
3. Consider batching notifications in off-peak hours
4. Monitor database table size

## 🎯 Next Steps

1. Review `NOTIFICATION_SETUP.md` for detailed documentation
2. Test with demo page: `/notification-demo`
3. Integrate notifications into existing workflows
4. Remove demo files before production deployment
5. Customize styling to match your theme

---

**Need Help?** Check the documentation files or review the code comments in:
- `app/Models/NotificationModel.php`
- `app/Controllers/Notification.php`
- `app/Helpers/NotificationHelper.php`
- `app/Views/layout/header.php` (JavaScript section)
