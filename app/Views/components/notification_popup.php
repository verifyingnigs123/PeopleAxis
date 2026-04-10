<!-- Real-time Notification Popup -->
<div id="notificationPopup" class="position-fixed" style="top: 80px; right: 20px; z-index: 1050; max-width: 350px;">
    <!-- Notifications will be dynamically added here -->
</div>

<style>
.notification-item {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    margin-bottom: 10px;
    border-left: 4px solid #007bff;
    animation: slideInRight 0.3s ease-out;
    transition: all 0.3s ease;
}

.notification-item.success {
    border-left-color: #198754;
}

.notification-item.warning {
    border-left-color: #ffc107;
}

.notification-item.danger {
    border-left-color: #dc3545;
}

.notification-item.info {
    border-left-color: #0dcaf0;
}

.notification-item:hover {
    transform: translateX(-5px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.notification-slide-out {
    animation: slideOutRight 0.3s ease-out forwards;
}

.notification-header {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 0.75rem 1rem 0.5rem;
    border-bottom: 1px solid #f0f0f0;
}

.notification-body {
    padding: 0.5rem 1rem 0.75rem;
}

.notification-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
}

.notification-message {
    font-size: 0.8rem;
    color: #666;
    line-height: 1.4;
}

.notification-time {
    font-size: 0.7rem;
    color: #999;
}

.notification-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: #999;
    cursor: pointer;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
}

.notification-close:hover {
    background: #f0f0f0;
    color: #666;
}

.notification-action {
    background: #007bff;
    color: white;
    border: none;
    padding: 0.25rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
    display: inline-block;
    margin-top: 0.5rem;
}

.notification-action:hover {
    background: #0056b3;
    color: white;
    text-decoration: none;
}

.notification-action.success:hover {
    background: #157347;
}

.notification-action.warning:hover {
    background: #d39e00;
}

.notification-action.danger:hover {
    background: #bb2d3b;
}

.notification-action.info:hover {
    background: #0b5ed7;
}
</style>

<script>
class NotificationPopup {
    constructor() {
        this.container = document.getElementById('notificationPopup');
        this.notifications = new Map();
        this.userId = null;
        this.pollInterval = null;
        this.lastCheck = new Date();
    }

    init(userId) {
        this.userId = userId;
        this.startPolling();
    }

    startPolling() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
        }

        // Check for new notifications every 15 seconds
        this.pollInterval = setInterval(() => {
            this.checkNotifications();
        }, 15000);

        // Initial check
        this.checkNotifications();
    }

    stopPolling() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }
    }

    async checkNotifications() {
        if (!this.userId) return;

        try {
            const response = await fetch('<?= site_url("/admin/notifications") ?>', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.processNotifications(data.notifications || []);
            }
        } catch (error) {
            console.error('Error checking notifications:', error);
        }
    }

    processNotifications(notifications) {
        notifications.forEach(notification => {
            if (!this.notifications.has(notification.id)) {
                this.showNotification(notification);
                this.notifications.set(notification.id, notification);
            }
        });
    }

    showNotification(notification) {
        const notificationEl = document.createElement('div');
        notificationEl.className = `notification-item ${notification.type}`;
        notificationEl.dataset.notificationId = notification.id;

        const timeAgo = this.getTimeAgo(new Date(notification.created_at));

        notificationEl.innerHTML = `
            <div class="notification-header">
                <div class="notification-title">${this.escapeHtml(notification.title)}</div>
                <button class="notification-close" onclick="notificationPopup.close(${notification.id})">&times;</button>
            </div>
            <div class="notification-body">
                <div class="notification-message">${this.escapeHtml(notification.message)}</div>
                <div class="notification-time">${timeAgo}</div>
                ${notification.link ? `<a href="${notification.link}" class="notification-action ${notification.type}">View Details</a>` : ''}
            </div>
        `;

        this.container.appendChild(notificationEl);

        // Auto-remove after 8 seconds
        setTimeout(() => {
            this.close(notification.id);
        }, 8000);

        // Play notification sound (optional)
        this.playNotificationSound(notification.type);
    }

    close(notificationId) {
        const notificationEl = document.querySelector(`[data-notification-id="${notificationId}"]`);
        if (notificationEl) {
            notificationEl.classList.add('notification-slide-out');
            setTimeout(() => {
                if (notificationEl.parentNode) {
                    notificationEl.parentNode.removeChild(notificationEl);
                }
            }, 300);
        }

        // Mark as read on server
        this.markAsRead(notificationId);
        
        // Remove from local map
        this.notifications.delete(notificationId);
    }

    async markAsRead(notificationId) {
        try {
            await fetch(`<?= site_url("/admin/mark-notification-read/") ?>${notificationId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                }
            });
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    getTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        
        if (seconds < 60) return 'Just now';
        if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
        if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
        return Math.floor(seconds / 86400) + ' days ago';
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    playNotificationSound(type) {
        // You can add audio feedback here if needed
        // For example: new Audio('/sounds/notification.mp3').play();
    }

    // Manual notification trigger for testing
    showManualNotification(title, message, type = 'info', link = null) {
        const notification = {
            id: Date.now(),
            title: title,
            message: message,
            type: type,
            link: link,
            created_at: new Date().toISOString()
        };
        
        this.showNotification(notification);
    }
}

// Initialize notification popup
const notificationPopup = new NotificationPopup();

// Auto-initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Get user ID from session or page data
    const userId = <?= session()->get('user_id') ?: 'null' ?>;
    if (userId) {
        notificationPopup.init(userId);
    }
});

// Clean up when page unloads
window.addEventListener('beforeunload', function() {
    notificationPopup.stopPolling();
});
</script>
