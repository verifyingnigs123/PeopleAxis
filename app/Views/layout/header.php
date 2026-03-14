<?php $role = session()->get('role') ?? ''; $roleName = session()->get('role_name') ?? ''; ?>

<style>
    .layout-container {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 250px;
        background: var(--pa-surface);
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        overflow-y: auto;
        z-index: 100;
        border-right: 1px solid var(--pa-border-soft);
        padding: 18px 0;
    }

    .content-utility-bar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-bottom: 20px;
    }

    .utility-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-left: auto;
    }

    .mobile-sidebar-toggle,
    .notification-bell,
    .utility-menu-dropdown {
        border: 1px solid var(--pa-border-soft);
        background: var(--pa-surface);
        color: #355444;
        border-radius: 10px;
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0.55rem 0.8rem;
        box-shadow: 0 2px 8px rgba(35, 71, 52, 0.06);
        transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        text-decoration: none;
    }

    .mobile-sidebar-toggle {
        display: none;
    }

    .mobile-sidebar-toggle:hover,
    .mobile-sidebar-toggle:focus-visible,
    .notification-bell:hover,
    .notification-bell:focus-visible,
    .utility-menu-dropdown:hover,
    .utility-menu-dropdown:focus-visible {
        background: #f4faf6;
        color: #254132;
        border-color: #b6d3c1;
        outline: none;
    }

    .utility-menu-dropdown::after {
        margin-left: 0.35rem;
    }

    .utility-menu-label {
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sidebar::-webkit-scrollbar {
        width: 8px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: #f4faf6;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: #a7c5b2;
        border-radius: 6px;
    }

    .sidebar-section {
        margin-bottom: 18px;
    }

    .sidebar-section-title {
        padding: 0 16px;
        margin: 10px 0 8px;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--pa-muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        color: #355444;
        text-decoration: none;
        border-left: 3px solid transparent;
        transition: background-color 0.2s ease, color 0.2s ease;
        font-size: 0.93rem;
        cursor: pointer;
    }

    .sidebar-link i {
        width: 18px;
        text-align: center;
        color: #5f7b69;
    }

    .sidebar-link:hover {
        background: #eef7f1;
        color: #254132;
    }

    .sidebar-link:hover i {
        color: #5b9474;
    }

    .sidebar-link.active {
        background: #e1efe6;
        color: #254132;
        border-left-color: #6ea988;
        font-weight: 600;
    }

    .sidebar-link.active i {
        color: #6ea988;
    }

    .sidebar-link:focus {
        outline: 2px solid #9ac4ad;
        outline-offset: -2px;
    }

    .sidebar-link:focus:not(:focus-visible) {
        outline: none;
    }

    .sidebar-sync-form {
        padding: 0 16px;
        margin-bottom: 10px;
    }

    .sidebar-sync-form button {
        width: 100%;
        padding: 9px 12px;
        background: var(--pa-primary);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .sidebar-sync-form button:hover {
        background: var(--pa-primary-dark);
    }

    .sidebar-sync-form button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .content-area {
        margin-left: 250px;
        padding: 24px;
        flex: 1;
        min-width: 0;
        overflow-y: auto;
        background: var(--pa-bg);
    }

    .permission-note {
        margin: 12px 16px;
        padding: 10px 12px;
        background: #ecf7f0;
        border: 1px solid #c7ddd0;
        border-radius: 8px;
        font-size: 0.82rem;
        color: #4b6f5a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dropdown-menu {
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
        border: 1px solid #c7ddd0;
        border-radius: 8px;
    }

    .dropdown-item {
        padding: 0.55rem 0.85rem;
        color: #355444;
    }

    .dropdown-item:hover {
        background: #f4faf6;
        color: #254132;
    }

    .dropdown-item i {
        margin-right: 8px;
        width: 16px;
        text-align: center;
    }

    .btn-loading {
        pointer-events: none;
        opacity: 0.8;
    }

    .notification-dropdown {
        position: relative;
    }

    .notification-bell {
        position: relative;
        cursor: pointer;
        overflow: visible;
    }

    .notification-badge {
        position: absolute;
        top: -6px;
        right: -4px;
        background: #6ea988;
        color: #ffffff;
        border-radius: 999px;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
        animation: pulse 2s infinite;
        box-shadow: 0 2px 6px rgba(110, 169, 136, 0.35);
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

    .notification-dropdown-menu {
        position: absolute;
        top: calc(100% + 0.45rem);
        right: 0;
        background: #ffffff;
        border: 1px solid #c7ddd0;
        border-radius: 10px;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
        width: 360px;
        max-height: 480px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }

    .notification-dropdown-menu.show {
        display: block;
    }

    .notification-dropdown-header {
        padding: 12px 14px;
        border-bottom: 1px solid #dbe9e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f4faf6;
        border-radius: 10px 10px 0 0;
    }

    .notification-dropdown-header h5 {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 600;
        color: #254132;
    }

    .notification-dropdown-header .btn-clear {
        background: none;
        border: none;
        color: #5b9474;
        cursor: pointer;
        font-size: 0.8rem;
        padding: 0;
        text-decoration: none;
    }

    .notification-dropdown-header .btn-clear:hover {
        color: #254132;
        text-decoration: underline;
    }

    .notification-item {
        padding: 12px 14px;
        border-bottom: 1px solid #dbe9e0;
        transition: background 0.2s ease;
        cursor: pointer;
    }

    .notification-item:hover {
        background: #f4faf6;
    }

    .notification-item.unread {
        background: #ecf7f0;
    }

    .notification-item-content {
        display: flex;
        gap: 10px;
    }

    .notification-item-icon {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }

    .notification-item-icon.info {
        background: #ecf7f0;
        color: #5b9474;
    }

    .notification-item-icon.success {
        background: #e7f2ea;
        color: #355444;
    }

    .notification-item-icon.warning {
        background: #e1efe6;
        color: #5b9474;
    }

    .notification-item-icon.danger {
        background: #c7ddd0;
        color: #254132;
    }

    .notification-item-text {
        flex: 1;
        min-width: 0;
    }

    .notification-item-title {
        font-weight: 600;
        color: #254132;
        font-size: 0.9rem;
        margin-bottom: 4px;
    }

    .notification-item-message {
        color: #4b6f5a;
        font-size: 0.82rem;
        line-height: 1.4;
        margin-bottom: 4px;
    }

    .notification-item-time {
        font-size: 0.72rem;
        color: #95ad9f;
    }

    .notification-item-actions {
        display: flex;
        gap: 8px;
        margin-top: 8px;
    }

    .notification-item-actions a,
    .notification-item-actions button {
        padding: 0;
        font-size: 0.75rem;
        text-decoration: none;
        cursor: pointer;
        border: none;
        background: none;
        color: #5b9474;
        transition: color 0.2s ease;
    }

    .notification-item-actions a:hover,
    .notification-item-actions button:hover {
        color: #254132;
        text-decoration: underline;
    }

    .notification-empty {
        padding: 28px 14px;
        text-align: center;
        color: #5f7b69;
    }

    .notification-empty i {
        font-size: 1.8rem;
        margin-bottom: 10px;
        color: #c7ddd0;
    }

    .notification-empty p {
        margin: 0;
        font-size: 0.85rem;
    }

    .notification-loading {
        padding: 18px 14px;
        text-align: center;
        color: #5f7b69;
    }

    .notification-loading .spinner-border {
        width: 1.3rem;
        height: 1.3rem;
    }

    @media (max-width: 992px) {
        .utility-menu-label {
            max-width: 110px;
        }
    }

    @media (max-width: 768px) {
        .content-utility-bar {
            position: sticky;
            top: 0;
            z-index: 95;
            padding-bottom: 8px;
            background: linear-gradient(180deg, var(--pa-bg) 78%, rgba(240, 247, 242, 0));
            justify-content: space-between;
        }

        .mobile-sidebar-toggle {
            display: inline-flex;
        }

        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.25s ease;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.16);
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .content-area {
            margin-left: 0;
            padding: 16px;
        }

        .utility-actions {
            gap: 8px;
        }

        .permission-note {
            margin: 12px;
        }
    }

    @media (max-width: 576px) {
        .utility-menu-label {
            display: none;
        }

        .notification-dropdown-menu {
            width: min(92vw, 320px);
        }
    }
</style>

<?php if (session()->get('logged_in')): ?>

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

                <!-- Attendance Section - BIOMETRIC LOGS LINK -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Attendance</div>
                    <a class="sidebar-link" href="<?= base_url('biometric/logs') ?>">
                        <i class="fas fa-fingerprint"></i> View Biometric Logs
                    </a>
                </div>

                <!-- Leave Approvals Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Leave Requests</div>
                    <a class="sidebar-link" href="<?= base_url('leaves') ?>">
                        <i class="fas fa-calendar-check"></i> Approve & Manage Leaves
                    </a>
                </div>

                <!-- Reports Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Reports</div>
                    <a class="sidebar-link" href="<?= base_url('reports/attendance') ?>">
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
                    <a class="sidebar-link" href="<?= base_url('attendance/team') ?>">
                        <i class="fas fa-calendar"></i> View Team Attendance (Only)
                    </a>
                </div>

                <!-- Team Leave Approvals Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Leave Requests</div>
                    <a class="sidebar-link" href="<?= base_url('leaves/team') ?>">
                        <i class="fas fa-calendar-check"></i> Approve Team Leave Requests
                    </a>
                </div>

                <!-- Team Performance Section -->
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Performance</div>
                    <a class="sidebar-link" href="<?= base_url('reports/team') ?>">
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
        <div class="content-utility-bar">
            <button type="button" class="mobile-sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar navigation">
                <i class="fas fa-bars"></i>
            </button>

            <div class="utility-actions">
                <div class="notification-dropdown">
                    <button type="button" class="notification-bell" id="notificationBell" title="Notifications" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationBadge" style="display:none;">0</span>
                    </button>
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
                                <div class="spinner-border spinner-border-sm text-success" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="utility-menu-dropdown dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i>
                        <span class="utility-menu-label"><?= esc(session()->get('name') ?? 'User') ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="fas fa-user"></i> My Profile</a></li>
                        <?php if ($roleName === 'Super Admin'): ?>
                            <li><a class="dropdown-item" href="<?= base_url('settings') ?>"><i class="fas fa-cog"></i> Settings</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const role = '<?= $role ?>';
    const roleName = '<?= $roleName ?>';

    function normalizePath(path) {
        if (!path) {
            return '/';
        }

        // Keep root as-is, trim trailing slash for all other paths.
        return path.length > 1 && path.endsWith('/')
            ? path.slice(0, -1)
            : path;
    }

    // ===== Highlight Active Navigation Links =====
    function setActiveLink() {
        const sidebarLinks = Array.from(document.querySelectorAll('.sidebar-link'));
        const currentPath = normalizePath(window.location.pathname);

        sidebarLinks.forEach(link => link.classList.remove('active'));

        // Prefer exact path match so sibling links (e.g. /leaves and /leaves/create)
        // do not appear active at the same time.
        const exactMatch = sidebarLinks.find(link => {
            const href = link.getAttribute('href');
            const linkPath = href ? normalizePath(new URL(href, window.location.origin).pathname) : '';
            return linkPath === currentPath;
        });

        if (exactMatch) {
            exactMatch.classList.add('active');
            return;
        }

        // Fallback to the most specific parent route when exact match is not present.
        let bestMatch = null;
        let bestLength = -1;

        sidebarLinks.forEach(link => {
            const href = link.getAttribute('href');

            if (!href) {
                return;
            }

            const linkPath = normalizePath(new URL(href, window.location.origin).pathname);
            const isParentRoute = currentPath.startsWith(linkPath + '/');

            if (isParentRoute && linkPath.length > bestLength) {
                bestMatch = link;
                bestLength = linkPath.length;
            }
        });

        if (bestMatch) {
            bestMatch.classList.add('active');
        }
    }

    // ===== Dashboard Navigation =====
    function setupSidebarNavigation() {
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Allow form submissions
                if (this.tagName === 'FORM') return;
                
                // For regular links, add active class
                sidebarLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                if (window.innerWidth <= 768) {
                    const sidebar = document.querySelector('.sidebar');
                    if (sidebar) {
                        sidebar.classList.remove('open');
                        document.body.style.overflow = 'auto';
                    }
                }
            });
        });
    }

    // ===== Mobile Sidebar Toggle =====
    function setupMobileToggle() {
        const navToggler = document.getElementById('sidebarToggle');
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
    setActiveLink();
    setupSidebarNavigation();
    setupMobileToggle();
    setupFormHandlers();
    initializeTooltips();

    // ===== Log Initial Load =====
    logUserActivity('Dashboard loaded');

    // ===== Monitor URL Changes =====
    window.addEventListener('popstate', function() {
        setActiveLink();
    });
});

// ===== Global Navigation Function =====
function navigateTo(url) {
    window.location.href = url;
}

// ===== Logout Confirmation =====
function confirmLogout() {
    if (confirm('Are you sure you want to log out?')) {
        window.location.href = '<?= base_url('logout') ?>';
    }
}

// ===== Initialize Notification System =====
document.addEventListener('DOMContentLoaded', function() {
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
                    // Handle both full URLs (new) and bare paths (old notifications in DB)
                    if (link.startsWith('http://') || link.startsWith('https://')) {
                        window.location.href = link;
                    } else {
                        window.location.href = '<?= rtrim(base_url(), '/') ?>/' + link.replace(/^\//, '');
                    }
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
        const isOpen = notificationMenu.classList.toggle('show');
        notificationBell.setAttribute('aria-expanded', String(isOpen));
        if (isOpen) {
            fetchNotifications();
        }
    });

    // Close notification dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.notification-dropdown')) {
            if (notificationMenu) {
                notificationMenu.classList.remove('show');
            }
            notificationBell.setAttribute('aria-expanded', 'false');
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

// ===== Handle Sidebar Form Submissions =====
document.addEventListener('DOMContentLoaded', function() {
    const sidebarForms = document.querySelectorAll('.sidebar-sync-form');
    
    sidebarForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = this.querySelector('button[type="submit"]');
            const originalHtml = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';
            
            // Submit the form
            const formData = new FormData(this);
            
            fetch(this.getAttribute('action'), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
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
});
</script>

<script>
/* =====================================================================
   PJAX — Instant sidebar navigation
   Fetches only the #mainContent area and swaps it in-place so the
   navbar, sidebar, and notification stream are never torn down.
   ===================================================================== */
(function () {
    'use strict';
    var CONTAINER = '#mainContent';
    var navigating = false;

    /* ── thin progress bar at the very top of the screen ─────────────── */
    var bar = document.createElement('div');
    bar.id  = 'pjax-progress-bar';
    bar.style.cssText = 'position:fixed;top:0;left:0;z-index:99999;height:3px;width:0;'
        + 'background:linear-gradient(90deg,#6ea988 0%,#5b9474 100%);'
        + 'transition:width .3s ease,opacity .4s ease;pointer-events:none;opacity:0;'
        + 'border-radius:0 2px 2px 0;box-shadow:0 0 8px rgba(110,169,136,.45);';
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
        function normalizePath(path) {
            if (!path) return '/';
            return path.length > 1 && path.endsWith('/') ? path.slice(0, -1) : path;
        }

        var path = normalizePath(new URL(url, location.origin).pathname);
        var links = Array.from(document.querySelectorAll('.sidebar-link'));

        links.forEach(function(link) { link.classList.remove('active'); });

        var exact = links.find(function(link) {
            var href = link.getAttribute('href');
            if (!href) return false;

            try {
                var lp = normalizePath(new URL(href, location.origin).pathname);
                return lp === path;
            } catch (e) {
                return false;
            }
        });

        if (exact) {
            exact.classList.add('active');
            return;
        }

        var bestMatch = null;
        var bestLength = -1;

        links.forEach(function(link) {
            var href = link.getAttribute('href');
            if (!href) return;

            try {
                var lp = normalizePath(new URL(href, location.origin).pathname);
                var isParentRoute = path.startsWith(lp + '/');

                if (isParentRoute && lp.length > bestLength) {
                    bestMatch = link;
                    bestLength = lp.length;
                }
            } catch (e) {}
        });

        if (bestMatch) {
            bestMatch.classList.add('active');
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