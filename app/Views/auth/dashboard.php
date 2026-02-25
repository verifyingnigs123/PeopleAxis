<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    /* ===== Page Header ===== */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .page-header h1 {
        color: #2c3e50;
        font-weight: 700;
        margin: 0;
    }

    .page-header p {
        color: #95a5a6;
        margin: 5px 0 0 0;
    }

    /* ===== Statistics Cards ===== */
    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .stat-card i {
        font-size: 2.5rem;
        color: #667eea;
        margin-bottom: 15px;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 10px 0;
    }

    .stat-label {
        color: #95a5a6;
        font-weight: 500;
    }

    /* ===== Card Styles ===== */
    .card {
        background: white;
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        font-weight: 600;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
    }

    .card-header i {
        margin-right: 8px;
    }

    .card-body {
        padding: 20px;
    }

    /* ===== Quick Actions ===== */
    .btn-outline-primary {
        border: 2px solid #667eea;
        color: #667eea;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background: #667eea;
        border-color: #667eea;
        color: white;
    }

    .btn-outline-primary i {
        margin-right: 5px;
    }

    /* ===== Activity Items ===== */
    .activity-item {
        padding: 12px 0;
    }

    .activity-item p {
        color: #2c3e50;
        margin: 0;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
    }

    /* ===== Alert Messages ===== */
    .alert {
        border-radius: 8px;
        border: none;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .alert-warning {
        background: #fff3cd;
        color: #856404;
    }

    .alert i {
        margin-right: 8px;
    }

    /* ===== Table Styles ===== */
    .table {
        color: #495057;
    }

    .table thead th {
        background: #f8f9fa;
        color: #2c3e50;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    /* ===== Footer ===== */
    .footer {
        background: white;
        padding: 20px;
        text-align: center;
        color: #95a5a6;
        border-top: 1px solid #dee2e6;
        margin-top: 40px;
    }

    .footer a {
        color: #667eea;
        text-decoration: none;
        margin: 0 5px;
    }

    .footer a:hover {
        text-decoration: underline;
    }

    /* ===== Loading Spinner ===== */
    .loading-spinner {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .loading-spinner.active {
        display: flex;
    }

    /* ===== Responsive Design ===== */
    @media (max-width: 768px) {
        .sidebar {
            width: 0;
            position: fixed;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .content-area {
            margin-left: 0;
            padding: 15px;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .stat-card {
            padding: 15px;
        }

        .stat-value {
            font-size: 2rem;
        }

        .stat-card i {
            font-size: 2rem;
        }

        .row.mb-4 {
            margin-bottom: 1rem !important;
        }

        .col-md-3,
        .col-lg-6,
        .col-lg-12 {
            flex-basis: 100%;
        }
    }

    @media (max-width: 576px) {
        .navbar-brand {
            font-size: 1.2rem;
        }

        .page-header h1 {
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 1.5rem;
        }
    }

    /* ===== Utility Classes ===== */
    .text-muted {
        color: #95a5a6 !important;
    }

    .text-center {
        text-align: center;
    }

    .mb-4 {
        margin-bottom: 1.5rem;
    }

    .mb-3 {
        margin-bottom: 1rem;
    }

    .row.two-col {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    @media (max-width: 992px) {
        .row.two-col {
            grid-template-columns: 1fr;
        }
    }

    /* ===== ADMIN DASHBOARD STYLES ===== */
    .admin-navbar {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;
    }

    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .admin-header h1 {
        color: #1e3c72;
        font-weight: 700;
        margin: 0;
    }

    .admin-header p {
        color: #95a5a6;
        margin: 5px 0 0 0;
    }

    /* ===== Admin Stats ===== */
    .admin-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-box {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
    }

    .stat-box:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .stat-box i {
        font-size: 2rem;
        color: #2a5298;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f4ff;
        border-radius: 8px;
    }

    .stat-info h5 {
        margin: 0;
        color: #95a5a6;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .stat-info h3 {
        margin: 8px 0 0 0;
        color: #1e3c72;
        font-weight: 700;
        font-size: 1.8rem;
    }

    /* ===== Admin Panel ===== */
    .admin-panel {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
        overflow: hidden;
    }

    .panel-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .panel-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.3rem;
    }

    .panel-header i {
        margin-right: 8px;
    }

    .search-box {
        flex: 1;
        min-width: 200px;
    }

    .search-box input {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 0.9rem;
    }

    .panel-body {
        padding: 20px;
    }

    /* ===== Admin Table ===== */
    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table thead th {
        background: #f8f9fa;
        color: #1e3c72;
        font-weight: 600;
        padding: 12px 15px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.9rem;
    }

    .admin-table tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
        font-size: 0.95rem;
    }

    .admin-table tbody tr:hover {
        background: #f8f9fa;
    }

    .admin-table .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        border-radius: 4px;
    }

    .admin-table .action-buttons {
        display: flex;
        gap: 8px;
    }

    .admin-table .btn-sm {
        padding: 0.35rem 0.65rem;
        font-size: 0.75rem;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-edit {
        background: #3498db;
        color: white;
    }

    .btn-edit:hover {
        background: #2980b9;
    }

    .btn-delete {
        background: #e74c3c;
        color: white;
    }

    .btn-delete:hover {
        background: #c0392b;
    }

    /* ===== Quick Actions ===== */
    .admin-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .action-card {
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        text-align: center;
        transition: all 0.3s ease;
    }

    .action-card:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        transform: translateY(-5px);
    }

    .action-card i {
        font-size: 2.5rem;
        color: #2a5298;
        margin-bottom: 15px;
    }

    .action-card h3 {
        color: #1e3c72;
        font-weight: 700;
        margin: 10px 0;
    }

    .action-card p {
        color: #95a5a6;
        font-size: 0.9rem;
        margin: 10px 0;
    }

    .action-card .btn-outline-primary {
        border-color: #2a5298;
        color: #2a5298;
        margin-top: 10px;
    }

    .action-card .btn-outline-primary:hover {
        background: #2a5298;
        border-color: #2a5298;
        color: white;
    }

    /* ===== Admin Container with Sidebar ===== */
    .admin-container {
        display: flex;
        min-height: calc(100vh - 70px);
        background: #f5f6fa;
    }

    .admin-sidebar {
        width: 260px;
        background: white;
        padding: 20px 0;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
        position: fixed;
        height: calc(100vh - 70px);
        overflow-y: auto;
        z-index: 100;
    }

    .admin-sidebar nav {
        padding: 0;
    }

    .admin-sidebar .sidebar-title {
        padding: 15px 20px 10px;
        font-weight: 700;
        color: #2a5298;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .admin-sidebar .nav-link {
        display: block;
        padding: 12px 20px;
        color: #495057;
        text-decoration: none;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .admin-sidebar .nav-link:hover {
        background: #f1f4ff;
        border-left-color: #2a5298;
        color: #2a5298;
    }

    .admin-sidebar .nav-link.active {
        background: #f1f4ff;
        border-left-color: #2a5298;
        color: #2a5298;
        font-weight: 600;
    }

    .admin-sidebar .nav-link i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    .admin-content {
        margin-left: 260px;
        padding: 30px;
        background: #f5f6fa;
        min-height: calc(100vh - 70px);
        flex: 1;
        overflow-y: auto;
    }

    /* ===== Responsive Admin ===== */
    @media (max-width: 768px) {
        .admin-sidebar {
            width: 0;
            position: fixed;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .admin-sidebar.active {
            transform: translateX(0);
        }

        .admin-content {
            margin-left: 0;
            padding: 15px;
        }

        .admin-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .admin-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .panel-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .search-box {
            width: 100%;
        }

        .admin-table thead {
            display: none;
        }

        .admin-table tbody tr {
            display: block;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .admin-table tbody td {
            display: block;
            text-align: right;
            padding-left: 50%;
            position: relative;
            border: none;
        }

        .admin-table tbody td:before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            font-weight: 600;
            text-align: left;
        }
    }
</style>

<?php $roleName = session()->get('role_name') ?? session()->get('role'); ?>

<!-- Super Admin Dashboard -->
<?php if ($roleName === 'Super Admin'): ?>
        <div class="admin-header">
            <div>
                <h1><i class="fas fa-shield-alt"></i> Administration Panel</h1>
                <p>System Management & User Control</p>
            </div>
        </div>

        <!-- Admin Stats -->
        <div class="admin-stats">
            <div class="stat-box">
                <i class="fas fa-users"></i>
                <div class="stat-info">
                    <h5>Total Users</h5>
                    <h3><?= $totalUsers ?? 0 ?></h3>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-user-shield"></i>
                <div class="stat-info">
                    <h5>Admins</h5>
                    <h3><?= $adminCount ?? 0 ?></h3>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-user-check"></i>
                <div class="stat-info">
                    <h5>Active Users</h5>
                    <h3><?= $activeUsers ?? 0 ?></h3>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-user-slash"></i>
                <div class="stat-info">
                    <h5>Inactive Users</h5>
                    <h3><?= $inactiveUsers ?? 0 ?></h3>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="admin-actions">
            <div class="action-card">
                <i class="fas fa-lock"></i>
                <h3>Roles & Permissions</h3>
                <p>Manage user roles and access control</p>
                <a href="<?= base_url('roles') ?>" class="btn btn-outline-primary">Manage</a>
            </div>
            <div class="action-card">
                <i class="fas fa-history"></i>
                <h3>Activity Logs</h3>
                <p>View system and user activity</p>
                <a href="<?= base_url('activity-logs') ?>" class="btn btn-outline-primary">View Logs</a>
            </div>
            <div class="action-card">
                <i class="fas fa-sliders-h"></i>
                <h3>System Settings</h3>
                <p>Configure system settings</p>
                <a href="<?= base_url('settings') ?>" class="btn btn-outline-primary">Settings</a>
            </div>
            <div class="action-card">
                <i class="fas fa-database"></i>
                <h3>Backup & Restore</h3>
                <p>Manage database backups</p>
                <a href="<?= base_url('backups') ?>" class="btn btn-outline-primary">Backup</a>
            </div>
        </div>

<?php elseif ($roleName === 'HR Admin'): ?>
        <!-- HR Admin Dashboard -->
        <div class="admin-header">
            <div>
                <h1><i class="fas fa-user-md"></i> HR Dashboard</h1>
                <p>Manage employees, attendance and leaves</p>
            </div>
        </div>

        <div class="admin-stats">
            <div class="stat-box">
                <i class="fas fa-users"></i>
                <div class="stat-info">
                    <h5>Total Employees</h5>
                    <h3><?= $totalEmployees ?? 0 ?></h3>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-calendar-check"></i>
                <div class="stat-info">
                    <h5>Pending Leaves</h5>
                    <h3><?= $pendingLeaves ?? 0 ?></h3>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-file-alt"></i>
                <div class="stat-info">
                    <h5>Attendance Reports</h5>
                    <h3>Generate</h3>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-download"></i>
                <div class="stat-info">
                    <h5>Biometric Sync</h5>
                    <h3>Available</h3>
                </div>
            </div>
        </div>

        <div class="admin-actions">
            <div class="action-card">
                <i class="fas fa-user-cog"></i>
                <h3>Manage Employees</h3>
                <p>Create, update, and manage employee records</p>
                <a href="<?= base_url('employees') ?>" class="btn btn-outline-primary">Employees</a>
            </div>
            <div class="action-card">
                <i class="fas fa-clock"></i>
                <h3>Attendance Logs</h3>
                <p>View biometric attendance logs</p>
                <a href="<?= base_url('attendance/logs') ?>" class="btn btn-outline-primary">View Logs</a>
            </div>
            <div class="action-card">
                <i class="fas fa-check-circle"></i>
                <h3>Approve Leaves</h3>
                <p>Approve or reject leave requests</p>
                <a href="<?= base_url('leaves') ?>" class="btn btn-outline-primary">Leaves</a>
            </div>
            <div class="action-card">
                <i class="fas fa-sync"></i>
                <h3>Sync Biometric</h3>
                <p>Manually sync biometric device logs</p>
                <form action="<?= base_url('biometric/manual-sync') ?>" method="post" style="display:inline-block">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-primary">Sync</button>
                </form>
            </div>
        </div>

<?php elseif ($roleName === 'Manager'): ?>
        <!-- Manager Dashboard -->
        <div class="admin-header">
            <div>
                <h1><i class="fas fa-users-cog"></i> Manager Dashboard</h1>
                <p>Team attendance and approvals</p>
            </div>
        </div>

        <div class="admin-stats">
            <div class="stat-box">
                <i class="fas fa-user-friends"></i>
                <div class="stat-info">
                    <h5>Team Members</h5>
                    <h3><?= $teamCount ?? 0 ?></h3>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-clock"></i>
                <div class="stat-info">
                    <h5>Team Attendance</h5>
                    <h3><?= is_array($teamAttendance) ? count($teamAttendance) : 0 ?></h3>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-chart-line"></i>
                <div class="stat-info">
                    <h5>Performance</h5>
                    <h3>View</h3>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-user-secret"></i>
                <div class="stat-info">
                    <h5>Salary Rate</h5>
                    <h3>Hidden</h3>
                </div>
            </div>
        </div>

        <div class="admin-actions">
            <div class="action-card">
                <i class="fas fa-eye"></i>
                <h3>View Team Attendance</h3>
                <p>Only team-level attendance data</p>
                <a href="<?= base_url('attendance/team') ?>" class="btn btn-outline-primary">Team Attendance</a>
            </div>
            <div class="action-card">
                <i class="fas fa-check"></i>
                <h3>Approve Team Leaves</h3>
                <p>Approve team leave requests</p>
                <a href="<?= base_url('leaves/team') ?>" class="btn btn-outline-primary">Approve</a>
            </div>
            <div class="action-card">
                <i class="fas fa-chart-pie"></i>
                <h3>Team Performance</h3>
                <p>View performance metrics</p>
                <a href="<?= base_url('reports/team') ?>" class="btn btn-outline-primary">Performance</a>
            </div>
        </div>

<?php elseif ($roleName === 'Employee'): ?>
        <!-- Employee Dashboard -->
        <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= session()->get('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (isset($warning)): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?= $warning ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-times-circle"></i> <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <main>

        <div class="page-header">
            <div>
                <h1><i class="fas fa-tachometer-alt"></i> My Dashboard</h1>
                <p>Welcome, <?= session()->get('name') ?></p>
            </div>
            <div>
                <a href="<?= base_url('profile') ?>" class="btn btn-primary">
                    <i class="fas fa-user"></i> My Profile
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <div class="stat-value"><?= isset($attendance) ? count($attendance) : 0 ?></div>
                    <div class="stat-label">My Attendance Records</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="fas fa-calendar-alt"></i>
                    <div class="stat-value"><?= isset($leaves) ? count($leaves) : 0 ?></div>
                    <div class="stat-label">My Leave Requests</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="fas fa-id-card"></i>
                    <div class="stat-value">Profile</div>
                    <div class="stat-label">View & Edit</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-clock"></i> Attendance
                    </div>
                    <div class="card-body">
                        <a href="<?= base_url('attendance') ?>" class="btn btn-outline-primary">View My Attendance</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-calendar"></i> Leave Requests
                    </div>
                    <div class="card-body">
                        <a href="<?= base_url('leaves/create') ?>" class="btn btn-outline-primary">Submit Leave</a>
                        <a href="<?= base_url('leaves') ?>" class="btn btn-outline-primary">My Leaves</a>
                    </div>
                </div>
            </div>
        </div>

        </main>

<?php else: ?>

<!-- Default/Fallback Dashboard for Other Roles -->
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle"></i> <strong>Access Limited</strong> - Your role does not have a defined dashboard. Please contact your administrator.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
    // Initialize dashboard statistics
    function initializeDashboard() {
        // You can fetch data from your API/controllers here
        // For now, using sample data
        updateStatistics();
        initializeCharts();
    }

    function updateStatistics() {
        // Update statistics cards with data from your database
        document.getElementById('totalEmployees').textContent = '125';
        document.getElementById('totalDepartments').textContent = '8';
        document.getElementById('activeRecruitment').textContent = '5';
        document.getElementById('pendingLeaves').textContent = '3';
    }

    function initializeCharts() {
        // Employee Status Chart
        const employeeStatusCtx = document.getElementById('employeeStatusChart').getContext('2d');
        new Chart(employeeStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'On Leave', 'Inactive'],
                datasets: [{
                    data: [100, 15, 10],
                    backgroundColor: [
                        '#27ae60',
                        '#f39c12',
                        '#95a5a6'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Department Distribution Chart
        const departmentCtx = document.getElementById('departmentChart').getContext('2d');
        new Chart(departmentCtx, {
            type: 'bar',
            data: {
                labels: ['Sales', 'IT', 'HR', 'Finance', 'Marketing', 'Operations', 'Admin', 'Support'],
                datasets: [{
                    label: 'Number of Employees',
                    data: [18, 25, 8, 12, 15, 20, 10, 17],
                    backgroundColor: '#667eea',
                    borderColor: '#5a67d8',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                indexAxis: undefined,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeDashboard();
    });

    // Set active nav link based on current URL
    const currentUrl = window.location.pathname;
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        if (link.href.includes(currentUrl.split('/').pop())) {
            link.classList.add('active');
        }
    });

    // Show loading spinner on form submission
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const spinner = document.getElementById('loadingSpinner');
            if (spinner) {
                spinner.classList.add('active');
            }
        });
    });

    // Hide loading spinner when page loads
    window.addEventListener('load', function() {
        const spinner = document.getElementById('loadingSpinner');
        if (spinner) {
            spinner.classList.remove('active');
        }
    });

    // Admin Dashboard - Load Users
    <?php if (session()->get('role_name') === 'Super Admin'): ?>
    function loadAdminDashboard() {
        // Sample user data - In production, fetch from your API
        const users = [
            {
                id: 1,
                name: 'Admin User',
                email: 'admin@gmail.com',
                role: 'admin',
                status: 'active',
                created_at: '2026-02-12'
            },
            {
                id: 2,
                name: 'Test User',
                email: 'user@gmail.com',
                role: 'user',
                status: 'active',
                created_at: '2026-02-12'
            }
        ];

        // Update statistics
        document.getElementById('totalUsers').textContent = users.length;
        document.getElementById('adminCount').textContent = users.filter(u => u.role === 'admin').length;
        document.getElementById('activeUsers').textContent = users.filter(u => u.status === 'active').length;
        document.getElementById('inactiveUsers').textContent = users.filter(u => u.status === 'inactive').length;

        // Populate table
        const tableBody = document.getElementById('usersTableBody');
        if (tableBody) {
            tableBody.innerHTML = '';
            users.forEach(user => {
                const roleClass = user.role === 'admin' ? 'badge-danger' : 'badge-info';
                const statusClass = user.status === 'active' ? 'badge-success' : 'badge-secondary';
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.id}</td>
                    <td><strong>${user.name}</strong></td>
                    <td>${user.email}</td>
                    <td><span class="badge ${roleClass}">${user.role}</span></td>
                    <td><span class="badge ${statusClass}">${user.status}</span></td>
                    <td>${user.created_at}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-sm btn-edit" onclick="editUser(${user.id})"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn-sm btn-delete" onclick="deleteUser(${user.id})"><i class="fas fa-trash"></i> Delete</button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Search functionality
        const searchInput = document.getElementById('userSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#usersTableBody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }
    }

    function editUser(userId) {
        window.location.href = `<?= base_url('users/edit/') ?>${userId}`;
    }

    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            window.location.href = `<?= base_url('users/delete/') ?>${userId}`;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('usersTableBody')) {
            loadAdminDashboard();
        }

        // Set active admin sidebar link based on current URL
        const currentUrl = window.location.pathname;
        document.querySelectorAll('.admin-sidebar .nav-link').forEach(link => {
            const linkPath = link.getAttribute('href');
            if (linkPath && currentUrl.includes(linkPath.split('/').pop())) {
                link.classList.add('active');
            }
        });
    });
    <?php endif; ?>
</script>

<?= $this->endSection() ?>
