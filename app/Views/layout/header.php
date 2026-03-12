<?php $role = session()->get('role') ?? ''; $roleName = session()->get('role_name') ?? ''; ?>

<style>
    /* ===== Navigation Bar ===== */
    .navbar-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        padding: 0.75rem 0;
    }

    .navbar-custom .navbar-brand {
        color: white;
        font-weight: 700;
        font-size: 1.5rem;
        margin-right: 2rem;
    }

    .navbar-custom .nav-link {
        color: rgba(255, 255, 255, 0.9);
        margin: 0 0.5rem;
        transition: color 0.3s ease;
    }

    .navbar-custom .nav-link:hover {
        color: white;
    }

    .navbar-custom .user-menu-dropdown {
        color: rgba(255, 255, 255, 0.9);
    }

    /* ===== Layout Container ===== */
    .layout-container {
        display: flex;
        min-height: calc(100vh - 70px);
        background: #f8f9fa;
    }

    /* ===== Sidebar ===== */
    .sidebar {
        width: 260px;
        background: white;
        padding: 20px 0;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
        position: fixed;
        left: 0;
        top: 70px;
        height: calc(100vh - 70px);
        overflow-y: auto;
        z-index: 100;
        border-right: 1px solid #e9ecef;
    }

    .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: #f1f3f5;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 3px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: #adb5bd;
    }

    /* ===== Sidebar Section Title ===== */
    .sidebar-section {
        margin-bottom: 25px;
    }

    .sidebar-section-title {
        padding: 0 20px;
        margin: 15px 0 10px 0;
        font-size: 0.75rem;
        font-weight: 700;
        color: #667eea;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    /* ===== Sidebar Links ===== */
    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: #495057;
        text-decoration: none;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .sidebar-link:hover {
        background: rgba(102, 126, 234, 0.06);
        color: #667eea;
        border-left-color: rgba(102, 126, 234, 0.35);
    }

    .sidebar-link.active {
        background: #e8ecff;
        color: #4a5cc7;
        border-left-color: #4a5cc7;
        font-weight: 700;
    }

    .sidebar-link i {
        width: 20px;
        margin-right: 12px;
        text-align: center;
        font-size: 0.95rem;
    }

    /* ===== Sidebar Form Button (for sync) ===== */
    .sidebar-sync-form {
        padding: 0 20px;
        margin-bottom: 10px;
    }

    .sidebar-sync-form button {
        width: 100%;
        padding: 10px 15px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .sidebar-sync-form button:hover {
        background: #5568d3;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
    }

    /* ===== Content Area ===== */
    .content-area {
        margin-left: 260px;
        padding: 30px;
        flex: 1;
        overflow-y: auto;
        background: #f8f9fa;
    }

    /* ===== Permission Note (for restricted roles) ===== */
    .permission-note {
        padding: 12px 20px;
        margin: 15px 20px;
        background: #fff3cd;
        border-left: 3px solid #ffc107;
        border-radius: 4px;
        font-size: 0.85rem;
        color: #856404;
        display: flex;
        align-items: center;
    }

    .permission-note i {
        margin-right: 8px;
        font-size: 1rem;
    }

    /* ===== Responsive Design ===== */
    @media (max-width: 768px) {
        .sidebar {
            width: 0;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.open {
            width: 260px;
            transform: translateX(0);
        }

        .content-area {
            margin-left: 0;
        }
    }

    /* ===== Dropdown Menu Styles ===== */
    .dropdown-menu {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: none;
        border-radius: 6px;
    }

    .dropdown-item {
        padding: 10px 15px;
        color: #495057;
    }

    .dropdown-item:hover {
        background: #f1f3ff;
        color: #667eea;
    }

    .dropdown-item i {
        margin-right: 8px;
        width: 16px;
        text-align: center;
    }

    .sidebar-link.active {
        background: #e8ecff;
        color: #4a5cc7;
        border-left-color: #4a5cc7;
        font-weight: 700;
    }

    /* ===== Link Transition ===== */
    .sidebar-link {
        position: relative;
    }

    .sidebar-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(102, 126, 234, 0.05);
        border-radius: 0;
        z-index: -1;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .sidebar-link:hover::before {
        opacity: 1;
    }

    /* ===== Form Button States ===== */
    .sidebar-sync-form button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* ===== Sidebar Section Animation ===== */
    .sidebar-section {
        animation: slideInLeft 0.3s ease-out;
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* ===== Navbar Responsive ===== */
    @media (max-width: 992px) {
        .navbar-custom .nav-link {
            margin: 0.25rem 0;
        }
    }

    /* ===== Content Area Responsive ===== */
    @media (max-width: 768px) {
        .content-area {
            margin-left: 0;
            padding: 20px;
        }

        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .permission-note {
            margin: 20px 15px;
            padding: 10px 15px;
            font-size: 0.8rem;
        }
    }

    /* ===== Focus States for Accessibility ===== */
    .sidebar-link:focus {
        outline: 2px solid #667eea;
        outline-offset: -2px;
    }

    .sidebar-link:focus:not(:focus-visible) {
        outline: none;
    }

    /* ===== Loading States ===== */
    .btn-loading {
        pointer-events: none;
        opacity: 0.8;
    }

    /* ===== Notification Bell Styles ===== */
    .notification-bell {
        position: relative;
        color: rgba(255, 255, 255, 0.9);
        cursor: pointer;
        transition: color 0.3s ease;
        padding: 0.5rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        overflow: visible;
    }

    .notification-bell:hover {
        color: white;
    }

    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #e74c3c;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        animation: pulse 2s infinite;
        box-shadow: 0 2px 6px rgba(231, 76, 60, 0.4);
        z-index: 1001;
        line-height: 1;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }

    .notification-dropdown {
        position: relative;
    }

    .notification-dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        width: 380px;
        max-height: 500px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        margin-top: 0.5rem;
    }

    .notification-dropdown-menu.show {
        display: block;
    }

    .notification-dropdown-header {
        padding: 12px 16px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        border-radius: 6px 6px 0 0;
    }

    .notification-dropdown-header h5 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .notification-dropdown-header .btn-clear {
        background: none;
        border: none;
        color: #667eea;
        cursor: pointer;
        font-size: 0.85rem;
        padding: 0;
        text-decoration: none;
    }

    .notification-dropdown-header .btn-clear:hover {
        color: #5568d3;
        text-decoration: underline;
    }

    .notification-item {
        padding: 12px 16px;
        border-bottom: 1px solid #e9ecef;
        transition: background 0.2s ease;
        cursor: pointer;
    }

    .notification-item:hover {
        background: #f8f9fa;
    }

    .notification-item.unread {
        background: #f1f3ff;
    }

    .notification-item-content {
        display: flex;
        gap: 12px;
    }

    .notification-item-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .notification-item-icon.info {
        background: #e7f3ff;
        color: #1890ff;
    }

    .notification-item-icon.success {
        background: #f6ffed;
        color: #52c41a;
    }

    .notification-item-icon.warning {
        background: #fffbe6;
        color: #faad14;
    }

    .notification-item-icon.danger {
        background: #fff2f0;
        color: #ff4d4f;
    }

    .notification-item-text {
        flex: 1;
        min-width: 0;
    }

    .notification-item-title {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.95rem;
        margin-bottom: 4px;
    }

    .notification-item-message {
        color: #666;
        font-size: 0.85rem;
        line-height: 1.4;
        margin-bottom: 4px;
    }

    .notification-item-time {
        font-size: 0.75rem;
        color: #999;
    }

    .notification-item-actions {
        display: flex;
        gap: 8px;
        margin-top: 8px;
    }

    .notification-item-actions a,
    .notification-item-actions button {
        padding: 4px 8px;
        font-size: 0.75rem;
        text-decoration: none;
        cursor: pointer;
        border: none;
        background: none;
        color: #667eea;
        transition: color 0.2s ease;
    }

    .notification-item-actions a:hover,
    .notification-item-actions button:hover {
        color: #5568d3;
        text-decoration: underline;
    }

    .notification-empty {
        padding: 40px 16px;
        text-align: center;
        color: #999;
    }

    .notification-empty i {
        font-size: 2rem;
        margin-bottom: 12px;
        color: #ccc;
    }

    .notification-empty p {
        margin: 0;
        font-size: 0.9rem;
    }

    .notification-loading {
        padding: 20px 16px;
        text-align: center;
        color: #999;
    }

    .notification-loading .spinner-border {
        width: 1.5rem;
        height: 1.5rem;
    }

    @media (max-width: 576px) {
        .notification-dropdown-menu {
            width: 320px;
        }
    }
</style>

<?php if (session()->get('logged_in')): ?>

<!-- ===== TOP NAVIGATION BAR ===== -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid">
        <!-- Brand Logo -->
        <a class="navbar-brand" href="<?= base_url('dashboard') ?>">
            <i class="fas fa-users-cog"></i> PeopleAxis
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Dashboard Link -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('dashboard') ?>">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>

                <!-- Role-Based Quick Links -->
                <?php if ($roleName === 'Super Admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('users') ?>">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('activity-logs') ?>">
                            <i class="fas fa-history"></i> Audit Logs
                        </a>
                    </li>
                <?php elseif ($roleName === 'HR Admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('leaves') ?>">
                            <i class="fas fa-calendar-check"></i> Approvals
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('attendance/logs') ?>">
                            <i class="fas fa-clock"></i> Attendance
                        </a>
                    </li>
                <?php elseif ($roleName === 'Manager'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('attendance/team') ?>">
                            <i class="fas fa-users"></i> Team
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Notification Bell -->
                <li class="nav-item notification-dropdown">
                    <a class="nav-link notification-bell" id="notificationBell" href="#" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationBadge" style="display:none;">0</span>
                    </a>
                    <div class="notification-dropdown-menu" id="notificationMenu">
                        <div class="notification-dropdown-header">
                            <h5>Notifications</h5>
                            <div>
                                <button class="btn-clear" id="markAllRead" title="Mark all as read">Mark all read</button>
                                <button class="btn-clear" id="deleteAllNotifications" title="Delete all notifications">Delete all</button>
                            </div>
                        </div>
                        <div id="notificationList" style="max-height: 400px; overflow-y: auto;">
                            <div class="notification-loading">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle user-menu-dropdown" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> 
                        <?= esc(session()->get('name') ?? 'User') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="fas fa-user"></i> My Profile</a></li>
                        <?php if ($roleName === 'Super Admin'): ?>
                            <li><a class="dropdown-item" href="<?= base_url('settings') ?>"><i class="fas fa-cog"></i> Settings</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ===== MAIN CONTENT LAYOUT ===== -->
<div class="layout-container">

    <?php if ($role === 'user'): ?>
        <!-- ===== EMPLOYEE SIDEBAR ===== -->
        <aside class="sidebar">
            <nav>
                <!-- Dashboard -->
                <div class="sidebar-section">
                    <a href="<?= base_url('dashboard') ?>" class="sidebar-link">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </div>

                <!-- Profile Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Profile</div>
                    <a href="<?= base_url('profile') ?>" class="sidebar-link">
                        <i class="fas fa-user"></i> View My Profile
                    </a>
                </div>

                <!-- Attendance Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Attendance</div>
                    <a href="<?= base_url('attendance') ?>" class="sidebar-link">
                        <i class="fas fa-calendar"></i> View My Attendance
                    </a>
                </div>

                <!-- Leave Requests Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Leave Requests</div>
                    <a href="<?= base_url('leaves/create') ?>" class="sidebar-link">
                        <i class="fas fa-plus-circle"></i> Submit Leave Request
                    </a>
                    <a href="<?= base_url('leaves') ?>" class="sidebar-link">
                        <i class="fas fa-list"></i> View Leave Status
                    </a>
                </div>

                <!-- Restrictions Note -->
                <div class="permission-note">
                    <i class="fas fa-lock"></i>
                    <span>Cannot view other employees' data</span>
                </div>
            </nav>
        </aside>

    <?php elseif ($role === 'admin'): ?>
        <!-- ===== SUPER ADMIN SIDEBAR ===== -->
        <aside class="sidebar">
            <nav>
                <!-- Dashboard -->
                <div class="sidebar-section">
                    <a href="<?= base_url('dashboard') ?>" class="sidebar-link">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </div>

                <!-- User Management Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">User Management</div>
                    <a href="<?= base_url('users') ?>" class="sidebar-link">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                </div>

                <!-- System Configuration Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">System</div>
                    <a href="<?= base_url('settings') ?>" class="sidebar-link">
                        <i class="fas fa-cog"></i> Configure System Settings
                    </a>
                    <a href="<?= base_url('activity-logs') ?>" class="sidebar-link">
                        <i class="fas fa-history"></i> View Audit Logs
                    </a>
                </div>

                <!-- Biometric Management Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Biometric</div>
                    <a href="<?= base_url('biometric/connect') ?>" class="sidebar-link">
                        <i class="fas fa-link"></i> Connect & Sync Device
                    </a>
                    <form action="<?= base_url('biometric/manual-sync') ?>" method="post" class="sidebar-sync-form">
                        <?= csrf_field() ?>
                        <button type="submit" title="Sync biometric device logs">
                            <i class="fas fa-sync-alt"></i> Sync Device
                        </button>
                    </form>
                </div>

                <!-- Salary Management Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Salary</div>
                    <a href="<?= base_url('employees/salary') ?>" class="sidebar-link">
                        <i class="fas fa-dollar-sign"></i> Edit Employee Salary Rates
                    </a>
                </div>

                <!-- Employee Approvals Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Employee Approvals</div>
                    <a href="<?= base_url('employee/pending-approvals') ?>" class="sidebar-link">
                        <i class="fas fa-user-check"></i> Pending &amp; Rejected
                    </a>
                </div>

                <!-- Reports Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Reports</div>
                    <a href="<?= base_url('reports') ?>" class="sidebar-link">
                        <i class="fas fa-file-pdf"></i> View All Reports
                    </a>
                </div>
            </nav>
        </aside>

    <?php elseif ($role === 'hr'): ?>
        <!-- ===== HR ADMIN SIDEBAR ===== -->
        <aside class="sidebar">
            <nav>
                <!-- Dashboard -->
                <div class="sidebar-section">
                    <a href="<?= base_url('dashboard') ?>" class="sidebar-link">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </div>

                <!-- Employee Records Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Employees</div>
                    <a href="<?= base_url('employees') ?>" class="sidebar-link">
                        <i class="fas fa-users"></i> Manage Employee Records
                    </a>
                    <a href="<?= base_url('employees/salary') ?>" class="sidebar-link">
                        <i class="fas fa-money-bill"></i> View Employee Salary Rate
                    </a>
                </div>

                <!-- Attendance Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Attendance</div>
                    <a href="<?= base_url('attendance/logs') ?>" class="sidebar-link">
                        <i class="fas fa-clock"></i> View Biometric Attendance Logs
                    </a>
                </div>

                <!-- Leave Approvals Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Leave Requests</div>
                    <a href="<?= base_url('leaves') ?>" class="sidebar-link">
                        <i class="fas fa-calendar-check"></i> Approve Leave Requests
                    </a>
                </div>

                <!-- Reports & Sync Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Reports</div>
                    <a href="<?= base_url('reports/attendance') ?>" class="sidebar-link">
                        <i class="fas fa-file-alt"></i> Generate Attendance Reports
                    </a>
                </div>

                <!-- Biometric Sync Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Sync</div>
                    <form action="<?= base_url('biometric/manual-sync') ?>" method="post" class="sidebar-sync-form">
                        <?= csrf_field() ?>
                        <button type="submit" title="Sync biometric logs from device">
                            <i class="fas fa-sync-alt"></i> Sync Biometric Logs
                        </button>
                    </form>
                </div>

                <!-- Restrictions Note -->
                <div class="permission-note">
                    <i class="fas fa-ban"></i>
                    <span>Cannot change system configuration</span>
                </div>
            </nav>
        </aside>

    <?php elseif ($role === 'manager'): ?>
        <!-- ===== MANAGER SIDEBAR ===== -->
        <aside class="sidebar">
            <nav>
                <!-- Dashboard -->
                <div class="sidebar-section">
                    <a href="<?= base_url('dashboard') ?>" class="sidebar-link">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </div>

                <!-- Team Attendance Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Attendance</div>
                    <a href="<?= base_url('attendance/team') ?>" class="sidebar-link">
                        <i class="fas fa-calendar"></i> View Team Attendance (Only)
                    </a>
                </div>

                <!-- Team Leave Approvals Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Leave Requests</div>
                    <a href="<?= base_url('leaves/team') ?>" class="sidebar-link">
                        <i class="fas fa-calendar-check"></i> Approve Team Leave Requests
                    </a>
                </div>

                <!-- Team Performance Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Performance</div>
                    <a href="<?= base_url('reports/team') ?>" class="sidebar-link">
                        <i class="fas fa-chart-line"></i> View Team Performance
                    </a>
                </div>

                <!-- Restrictions Note -->
                <div class="permission-note">
                    <i class="fas fa-lock"></i>
                    <span>Cannot access salary rates or biometric sync</span>
                </div>
            </nav>
        </aside>

    <?php endif; ?>

    <!-- ===== CONTENT AREA ===== -->
    <div class="content-area" id="mainContent">

<script>
(function() {
    // Guard: only initialize once even if PJAX re-injects this script
    if (window.__headerInitialized) return;
    window.__headerInitialized = true;

document.addEventListener('DOMContentLoaded', function() {
    const currentUrl = window.location.pathname;
    const role = '<?= $role ?>';
    const roleName = '<?= $roleName ?>';

    // ===== Highlight Active Navigation Links =====
    function setActiveLink() {
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        const currentPath = window.location.pathname;
        
        if (sidebarLinks.length === 0) return;

        // Step 1: Clear ALL active classes from all sidebar links
        sidebarLinks.forEach(link => {
            link.classList.remove('active');
        });

        // Step 2: Parse all links and find candidates
        const linkCandidates = [];
        sidebarLinks.forEach((link, index) => {
            const href = link.getAttribute('href');
            if (!href) return;
            
            try {
                const linkPath = new URL(href, window.location.origin).pathname;
                linkCandidates.push({ index, link, path: linkPath });
            } catch (e) {
                // Invalid URL, skip
            }
        });

        if (linkCandidates.length === 0) return;

        // Step 3: Sort by path length (longest first) for specificity
        linkCandidates.sort((a, b) => b.path.length - a.path.length);

        // Step 4: Find THE single best match
        let bestMatchIndex = -1;

        // Try exact match first
        for (let i = 0; i < linkCandidates.length; i++) {
            if (linkCandidates[i].path === currentPath) {
                bestMatchIndex = linkCandidates[i].index;
                break;
            }
        }

        // If no exact match, try longest prefix match
        if (bestMatchIndex === -1) {
            for (let i = 0; i < linkCandidates.length; i++) {
                const candidate = linkCandidates[i];
                if (currentPath === candidate.path || 
                    (currentPath.startsWith(candidate.path) && 
                     (currentPath[candidate.path.length] === '/' || currentPath[candidate.path.length] === undefined))) {
                    bestMatchIndex = candidate.index;
                    break;
                }
            }
        }

        // Step 5: Apply active class ONLY to the best match
        if (bestMatchIndex !== -1) {
            sidebarLinks[bestMatchIndex].classList.add('active');
        }

        // Step 6: Verify - remove active from any other links (safety check)
        sidebarLinks.forEach((link, index) => {
            if (index !== bestMatchIndex) {
                link.classList.remove('active');
            }
        });
    }

    // ===== Mobile Sidebar Toggle =====
    function setupMobileToggle() {
        const navToggler = document.querySelector('.navbar-toggler');
        const sidebar = document.querySelector('.sidebar');
        
        if (navToggler && sidebar) {
            navToggler.addEventListener('click', function() {
                sidebar.classList.toggle('open');
                if (sidebar.classList.contains('open')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = 'auto';
                }
            });
            
            // Close sidebar when clicking outside
            document.addEventListener('click', function(e) {
                if (!sidebar.contains(e.target) && !navToggler.contains(e.target)) {
                    sidebar.classList.remove('open');
                    document.body.style.overflow = 'auto';
                }
            });
        }
    }

    // ===== Form Submission Handler =====
    function setupFormHandlers() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                // Show loading indicator if present
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.textContent;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                    
                    // Re-enable after 3 seconds (in case of error)
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }, 3000);
                }
            });
        });
    }

    // ===== Tooltip Initialization =====
    function initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // ===== Role-Based Logging =====
    function logUserActivity(action) {
        // Can be extended to log user activities
        console.log(`[${roleName}] Action: ${action}`);
    }

    // ===== Setup All Functionality =====
    // Call setActiveLink first before anything else
    setActiveLink();
    setupMobileToggle();
    setupFormHandlers();
    initializeTooltips();

    // ===== Log Initial Load =====
    logUserActivity('Dashboard loaded');

    // ===== Monitor URL Changes =====
    window.addEventListener('popstate', function() {
        setActiveLink();
    });

    // ===== Handle Sidebar Form Submissions =====
    const sidebarForms = document.querySelectorAll('.sidebar-sync-form');
    sidebarForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';
            
            const formData = new FormData(this);
            fetch(this.getAttribute('action'), {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                if (data.status === 'success') {
                    alert(data.message || 'Sync completed successfully!');
                } else {
                    alert(data.message || 'Sync failed. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                alert('An error occurred during sync. Please try again.');
            });
        });
    });

    // ===== Initialize Notification System =====
    const notificationBell = document.getElementById('notificationBell');
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationMenu = document.getElementById('notificationMenu');
    const notificationList = document.getElementById('notificationList');
    const markAllReadBtn = document.getElementById('markAllRead');
    const deleteAllBtn = document.getElementById('deleteAllNotifications');
    let notificationsPollHandle = null;
    let notificationEventSource = null;
    let streamReconnectHandle = null;
    
    if (!notificationBell || !notificationBadge) {
        console.warn('Notification elements not found');
        return;
    }

    // ── CSRF helpers (CI4 cookie-based CSRF) ──────────────────────────────────
    const CSRF_NAME = '<?= csrf_token() ?>';   // field name  (e.g. csrf_test_name)
    let   CSRF_HASH = '<?= csrf_hash() ?>';    // current hash – refreshed after each POST

    function csrfFormData() {
        const fd = new FormData();
        fd.append(CSRF_NAME, CSRF_HASH);
        return fd;
    }

    function refreshCsrf(responseData) {
        // CI4 can return a new hash in the response; update it so next call works
        if (responseData && responseData.csrf_hash) CSRF_HASH = responseData.csrf_hash;
    }
    // ─────────────────────────────────────────────────────────────────────────

    // Fetch and display notifications
    function fetchNotifications() {
        fetch('<?= base_url('/api/notifications') ?>', {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && Array.isArray(data.notifications)) {
                renderNotifications(data.notifications);
                updateBadge(data.unread_count ?? null, data.notifications);
            } else {
                showEmptyNotifications();
                updateBadge(0, []);
            }
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
            notificationList.innerHTML = '<div class="notification-empty"><i class="fas fa-exclamation-circle"></i><p>Error loading notifications</p></div>';
            updateBadge(0, []);
        });
    }

    function stopNotificationsPolling() {
        if (notificationsPollHandle) {
            clearInterval(notificationsPollHandle);
            notificationsPollHandle = null;
        }
    }

    function startNotificationsPolling(intervalMs) {
        stopNotificationsPolling();
        notificationsPollHandle = setInterval(fetchNotifications, intervalMs);
    }

    function connectNotificationStream() {
        if (typeof EventSource === 'undefined') {
            // Browser doesn't support SSE — fall back to 30s polling
            startNotificationsPolling(30000);
            return;
        }

        if (notificationEventSource) {
            notificationEventSource.close();
            notificationEventSource = null;
        }

        notificationEventSource = new EventSource('<?= base_url('/api/notifications/stream') ?>');

        notificationEventSource.onopen = function() {
            // SSE connected — stop any fallback polling
            stopNotificationsPolling();
        };

        notificationEventSource.addEventListener('notification', function() {
            fetchNotifications();
        });

        notificationEventSource.onerror = function() {
            if (notificationEventSource) {
                notificationEventSource.close();
                notificationEventSource = null;
            }

            // SSE dropped — fall back to 30s polling while reconnecting
            startNotificationsPolling(30000);

            if (streamReconnectHandle) {
                clearTimeout(streamReconnectHandle);
            }
            streamReconnectHandle = setTimeout(connectNotificationStream, 5000);
        };
    }

    // Render notifications in the dropdown
    function renderNotifications(notifications) {
        if (notifications.length === 0) {
            showEmptyNotifications();
            return;
        }

        let html = '';
        notifications.forEach(notif => {
            const timeAgo   = getTimeAgo(notif.created_at);
            const typeClass = notif.type || 'info';
            const statusVal = String(notif.status || '').toLowerCase();
            const isUnread  = statusVal ? statusVal === 'unread' : parseInt(notif.is_read) === 0;
            const unreadClass = isUnread ? 'unread' : '';
            const linkAttr  = notif.link ? `data-link="${notif.link}"` : '';
            const cursor    = notif.link ? 'style="cursor:pointer;"' : '';

            html += `
                <div class="notification-item ${unreadClass}" data-notification-id="${notif.id}" ${linkAttr} ${cursor}>
                    <div class="notification-item-content">
                        <div class="notification-item-icon ${typeClass}">
                            <i class="${notif.icon || 'fas fa-bell'}"></i>
                        </div>
                        <div class="notification-item-text">
                            <div class="notification-item-title">${escapeHtml(notif.title)}</div>
                            <div class="notification-item-message">${escapeHtml(notif.message)}</div>
                            <div class="notification-item-time">${timeAgo}</div>
                        </div>
                    </div>
                    <div class="notification-item-actions">
                        ${notif.link ? `<a href="${notif.link}" class="notification-link" onclick="event.stopPropagation();">View &rarr;</a>` : ''}
                        <button type="button" class="notification-delete" data-notification-id="${notif.id}" title="Delete" onclick="event.stopPropagation();">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </div>
                </div>
            `;
        });

        notificationList.innerHTML = html;

        // Click on the whole notification item → navigate + mark as read
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                const notifId = this.getAttribute('data-notification-id');
                const link    = this.getAttribute('data-link');

                // Mark as read, then navigate
                markNotificationRead(notifId, function() {
                    if (!link) return;

                    const rawLink = String(link).trim();
                    let targetUrl = '';

                    // Full absolute URL already present
                    if (/^https?:\/\//i.test(rawLink)) {
                        targetUrl = rawLink;
                    }
                    // Host-only or host+path without scheme (e.g. peopleaxis-production.up.railway.app/foo)
                    else if (/^[a-z0-9.-]+\.[a-z]{2,}(\/|$)/i.test(rawLink)) {
                        targetUrl = 'https://' + rawLink.replace(/^\/+/, '');
                    }
                    // Relative path from app root
                    else {
                        targetUrl = new URL(rawLink, '<?= rtrim(base_url(), '/') ?>/').href;
                    }

                    window.location.href = targetUrl;
                });
            });
        });

        // Standalone delete buttons
        document.querySelectorAll('.notification-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const notifId = this.getAttribute('data-notification-id');
                deleteNotification(notifId);
            });
        });
    }

    // Show empty state
    function showEmptyNotifications() {
        notificationList.innerHTML = `
            <div class="notification-empty">
                <i class="fas fa-inbox"></i>
                <p>No notifications</p>
            </div>
        `;
    }

    // Update badge with unread count
    function updateBadge(unreadCountFromApi, notifications) {
        if (!notificationBadge) return;

        let unreadCount = Number.isInteger(unreadCountFromApi)
            ? unreadCountFromApi
            : notifications.filter(n => {
                const statusVal = String(n.status || '').toLowerCase();
                return statusVal ? statusVal === 'unread' : parseInt(n.is_read) === 0;
            }).length;

        if (unreadCount > 0) {
            notificationBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            notificationBadge.style.display    = 'flex';
            notificationBadge.style.visibility = 'visible';
            notificationBadge.style.opacity    = '1';
        } else {
            notificationBadge.style.display    = 'none';
            notificationBadge.style.visibility = 'hidden';
        }
    }

    // Mark a single notification as read, then run callback
    function markNotificationRead(notifId, callback) {
        fetch(`<?= base_url('/api/notifications/') ?>${notifId}/read`, {
            method: 'POST',
            body: csrfFormData(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            refreshCsrf(data);
            if (typeof callback === 'function') callback();
        })
        .catch(() => {
            if (typeof callback === 'function') callback();
        });
    }

    // Mark all notifications as read
    function markAllNotificationsAsRead() {
        fetch('<?= base_url('/api/notifications/mark-all-read') ?>', {
            method: 'POST',
            body: csrfFormData(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            refreshCsrf(data);
            if (data.success) fetchNotifications();
        })
        .catch(error => console.error('Error:', error));
    }

    // Delete a single notification
    function deleteNotification(notificationId) {
        if (!confirm('Are you sure you want to delete this notification?')) return;

        const fd = csrfFormData();

        fetch(`<?= base_url('/api/notifications/') ?>${notificationId}/delete`, {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            refreshCsrf(data);
            if (data.success) fetchNotifications();
        })
        .catch(error => console.error('Error:', error));
    }

    function deleteAllNotifications() {
        if (!confirm('Delete all notifications? This cannot be undone.')) return;

        fetch('<?= base_url('/api/notifications/delete-all') ?>', {
            method: 'POST',
            body: csrfFormData(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            refreshCsrf(data);
            if (data.success) fetchNotifications();
        })
        .catch(error => console.error('Error:', error));
    }

    // Get time ago string
    function getTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        
        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + ' years ago';
        
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + ' months ago';
        
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + ' days ago';
        
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + ' hours ago';
        
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + ' minutes ago';
        
        return 'just now';
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Toggle notification dropdown
    notificationBell.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        notificationMenu.classList.toggle('show');
        if (notificationMenu.classList.contains('show')) {
            fetchNotifications();
        }
    });

    // Close notification dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.notification-dropdown')) {
            notificationMenu && notificationMenu.classList.remove('show');
        }
    });

    // Mark all as read
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            markAllNotificationsAsRead();
        });
    }

    if (deleteAllBtn) {
        deleteAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            deleteAllNotifications();
        });
    }

    // Initial load and real-time updates:
    // Fetch once immediately, then rely on SSE for real-time.
    // Polling (30s) only kicks in when SSE is unavailable or disconnected.
    fetchNotifications();
    connectNotificationStream();

    window.addEventListener('beforeunload', function() {
        if (notificationEventSource) {
            notificationEventSource.close();
        }
        stopNotificationsPolling();
        if (streamReconnectHandle) {
            clearTimeout(streamReconnectHandle);
        }
    });
});

})(); // end one-time guard
</script>

<script>
/* =====================================================================
   PJAX — Instant sidebar navigation
   Fetches only the #mainContent area and swaps it in-place so the
   navbar, sidebar, and notification stream are never torn down.
   ===================================================================== */
(function () {
    'use strict';
    // Guard: only initialize PJAX once even if PJAX re-injects this script block
    if (window.__pjaxInitialized) return;
    window.__pjaxInitialized = true;

    var CONTAINER = '#mainContent';
    var navigating = false;

    /* ── thin progress bar at the very top of the screen ─────────────── */
    var bar = document.createElement('div');
    bar.id  = 'pjax-progress-bar';
    bar.style.cssText = 'position:fixed;top:0;left:0;z-index:99999;height:3px;width:0;'
        + 'background:linear-gradient(90deg,#667eea 0%,#764ba2 100%);'
        + 'transition:width .3s ease,opacity .4s ease;pointer-events:none;opacity:0;'
        + 'border-radius:0 2px 2px 0;box-shadow:0 0 8px rgba(102,126,234,.5);';
    document.body.appendChild(bar);

    function showBar()   { bar.style.opacity = '1'; bar.style.width = '65%'; }
    function finishBar() { bar.style.width = '100%'; setTimeout(function(){ bar.style.opacity='0'; bar.style.width='0'; }, 380); }
    function resetBar()  { bar.style.transition='none'; bar.style.width='0'; bar.style.opacity='0'; setTimeout(function(){ bar.style.transition='width .3s ease,opacity .4s ease'; }, 50); }

    /* ── re-execute <script> tags (innerHTML skips them by design) ─────── */
    function runScripts(container) {
        container.querySelectorAll('script').forEach(function(old) {
            var s = document.createElement('script');
            Array.prototype.forEach.call(old.attributes, function(a){ s.setAttribute(a.name, a.value); });
            s.textContent = old.textContent;
            old.parentNode.replaceChild(s, old);
        });
    }

    /* ── re-init Bootstrap tooltips on newly loaded content ────────────── */
    function initTooltips() {
        try {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el){
                bootstrap.Tooltip.getOrCreateInstance(el);
            });
        } catch(e){}
    }

    /* ── highlight the active sidebar link ─────────────────────────────── */
    function updateActiveLink(url) {
        var path = new URL(url, location.origin).pathname;
        var sidebarLinks = document.querySelectorAll('.sidebar-link');
        
        // Step 1: Clear all active
        sidebarLinks.forEach(function(link) {
            link.classList.remove('active');
        });

        // Step 2: Parse all links
        var candidates = [];
        sidebarLinks.forEach(function(link, index) {
            var href = link.getAttribute('href');
            if (!href) return;
            try {
                var linkPath = new URL(href, location.origin).pathname;
                candidates.push({ index: index, link: link, path: linkPath });
            } catch(e) {}
        });

        if (candidates.length === 0) return;

        // Step 3: Sort by length descending
        candidates.sort(function(a, b) { return b.path.length - a.path.length; });

        // Step 4: Find best match
        var bestIndex = -1;
        
        // Try exact match
        for (var i = 0; i < candidates.length; i++) {
            if (candidates[i].path === path) {
                bestIndex = candidates[i].index;
                break;
            }
        }
        
        // Try prefix match if no exact
        if (bestIndex === -1) {
            for (var i = 0; i < candidates.length; i++) {
                var cand = candidates[i];
                if (path === cand.path || 
                    (path.startsWith(cand.path) && 
                     (path[cand.path.length] === '/' || path[cand.path.length] === undefined))) {
                    bestIndex = cand.index;
                    break;
                }
            }
        }

        // Step 5: Apply active to only best match
        if (bestIndex !== -1) {
            sidebarLinks[bestIndex].classList.add('active');
        }
    }

    /* ── core: fetch a page and swap only #mainContent ─────────────────── */
    function pjaxLoad(url, push) {
        if (navigating) return;
        navigating = true;
        showBar();

        fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: { 'X-PJAX': '1', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(res) {
            /* server redirected (e.g. session expired → /login) */
            if (res.redirected) { location.href = res.url; return null; }
            if (!res.ok)        { location.href = url;     return null; }
            return res.text();
        })
        .then(function(html) {
            if (!html) return;

            var doc     = new DOMParser().parseFromString(html, 'text/html');
            var newMain = doc.querySelector(CONTAINER);

            /* if the response has no #mainContent it's a special page — hard-navigate */
            if (!newMain) { location.href = url; return; }

            /* swap content */
            var main = document.querySelector(CONTAINER);
            main.innerHTML = newMain.innerHTML;

            /* update page <title> */
            document.title = doc.title;

            /* refresh CSRF meta so forms / AJAX inside new page work */
            var newMeta = doc.querySelector('meta[name="X-CSRF-TOKEN"],meta[name="csrf-token"],meta[name="csrf_token"]');
            var curMeta = document.querySelector('meta[name="X-CSRF-TOKEN"],meta[name="csrf-token"],meta[name="csrf_token"]');
            if (newMeta && curMeta) curMeta.setAttribute('content', newMeta.getAttribute('content'));

            /* push browser history */
            if (push) history.pushState({ pjax: true, url: url }, '', url);

            /* boot the newly inserted page scripts */
            runScripts(main);
            initTooltips();
            updateActiveLink(url);
            window.scrollTo({ top: 0, behavior: 'instant' });

            finishBar();
            navigating = false;
        })
        .catch(function() {
            resetBar();
            navigating = false;
            location.href = url;   /* network error — fall back to normal load */
        });
    }

    /* ── intercept sidebar link clicks ─────────────────────────────────── */
    document.addEventListener('click', function(e) {
        var link = e.target.closest('.sidebar-link');
        if (!link) return;

        var href = link.getAttribute('href');
        if (!href || href === '#' || href.indexOf('javascript:') === 0) return;
        if (link.target === '_blank' || link.hasAttribute('download')) return;
        /* skip external URLs */
        if (/^https?:\/\//.test(href) && href.indexOf(location.origin) !== 0) return;

        e.preventDefault();
        var url = new URL(href, location.origin).href;

        /* clicking the already-active link just re-highlights and scrolls up */
        if (url === location.href) { updateActiveLink(url); window.scrollTo({top:0,behavior:'smooth'}); return; }

        pjaxLoad(url, true);
    });

    /* ── browser back / forward buttons ────────────────────────────────── */
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.pjax) {
            pjaxLoad(e.state.url || location.href, false);
        } else {
            location.reload();
        }
    });

    /* seed the initial page into history so the first back-click has state */
    history.replaceState({ pjax: true, url: location.href }, '', location.href);
}());
</script>

<?php endif; ?>