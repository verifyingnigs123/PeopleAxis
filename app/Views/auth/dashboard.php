<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    :root {
        --dash-bg: #f0f7f2;
        --dash-surface: #ffffff;
        --dash-border: #dbe9e0;
        --dash-text: #1f362a;
        --dash-muted: #5f7b69;
        --dash-primary: #6ea988;
        --dash-primary-soft: #ecf7f0;
    }

    .dashboard-shell {
        max-width: 1220px;
        margin: 0 auto;
        padding: 4px 0 14px;
        color: var(--dash-text);
    }

    .admin-header,
    .page-header {
        background: var(--dash-surface);
        border: 1px solid var(--dash-border);
        border-radius: 12px;
        padding: 16px 18px;
        margin-bottom: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
    }

    .admin-header h1,
    .page-header h1 {
        margin: 0;
        color: #2f5f45;
        font-weight: 700;
        font-size: 1.9rem;
        line-height: 1.1;
    }

    .admin-header p,
    .page-header p {
        margin: 6px 0 0;
        color: var(--dash-muted);
        font-size: 0.92rem;
    }

    .admin-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .stat-box,
    .stat-card {
        background: var(--dash-surface);
        border: 1px solid var(--dash-border);
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-box {
        padding: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stat-card {
        text-align: center;
        padding: 16px;
    }

    .stat-box:hover,
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.09);
    }

    .stat-box i,
    .stat-card i {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        color: var(--dash-primary);
        background: var(--dash-primary-soft);
    }

    .stat-info h5 {
        margin: 0;
        color: var(--dash-muted);
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .stat-info h3 {
        margin: 6px 0 0;
        color: #0f172a;
        font-weight: 700;
        font-size: 1.55rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #0f172a;
        margin: 10px 0 4px;
    }

    .stat-label {
        color: var(--dash-muted);
        font-weight: 600;
        font-size: 0.88rem;
    }

    .admin-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 12px;
    }

    .action-card {
        background: var(--dash-surface);
        border: 1px solid var(--dash-border);
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.09);
    }

    .action-card i {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        color: var(--dash-primary);
        background: var(--dash-primary-soft);
        margin-bottom: 8px;
    }

    .action-card h3 {
        color: #0f172a;
        font-weight: 700;
        font-size: 1.02rem;
        margin: 2px 0 8px;
    }

    .action-card p {
        color: var(--dash-muted);
        font-size: 0.88rem;
        line-height: 1.45;
        margin-bottom: 12px;
    }

    .card {
        background: var(--dash-surface);
        border: 1px solid var(--dash-border);
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        margin-bottom: 14px;
        overflow: hidden;
    }

    .card-header {
        background: #f4faf6;
        border-bottom: 1px solid var(--dash-border);
        color: #355444;
        font-weight: 600;
        padding: 12px 14px;
    }

    .card-body {
        padding: 14px;
    }

    .card-body .btn {
        margin-right: 8px;
        margin-bottom: 8px;
    }

    .btn-outline-primary {
        border-color: #b8d8c6;
        color: #4d7f63;
        background: #ecf7f0;
    }

    .btn-outline-primary:hover {
        border-color: #9ac4ad;
        color: #2f5f45;
        background: #dfeee5;
    }

    .alert {
        border-radius: 10px;
        border: 1px solid transparent;
        margin-bottom: 12px;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .table {
        color: #355444;
    }

    .table thead th {
        background: #f4faf6;
        color: #355444;
        border-bottom: 1px solid var(--dash-border);
        font-weight: 600;
    }

    .table tbody tr:hover {
        background: #eef7f1;
    }

    @media (max-width: 992px) {
        .admin-header h1,
        .page-header h1 {
            font-size: 1.6rem;
        }
    }

    @media (max-width: 768px) {
        .dashboard-shell {
            padding-top: 0;
        }

        .admin-header,
        .page-header {
            padding: 14px;
        }

        .admin-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .stat-card {
            padding: 14px;
        }

        .stat-value {
            font-size: 1.6rem;
        }
    }

    @media (max-width: 576px) {
        .admin-stats {
            grid-template-columns: 1fr;
        }

        .admin-actions {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php $roleName = session()->get('role_name') ?? session()->get('role'); ?>

<div class="dashboard-shell">

<!-- Super Admin Dashboard -->
<?php if ($roleName === 'Super Admin'): ?>
    <div class="admin-header">
        <div>
            <h1><i class="fas fa-shield-alt"></i> Super Admin Dashboard</h1>
            <p>Operational overview for users, access control, and system governance</p>
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
                <h5>Total Employees</h5>
                <h3><?= $totalEmployees ?? 0 ?></h3>
            </div>
        </div>
        <div class="stat-box">
            <i class="fas fa-history"></i>
            <div class="stat-info">
                <h5>Audit Logs</h5>
                <h3><?= $auditCount ?? 0 ?></h3>
            </div>
        </div>
        <div class="stat-box">
            <i class="fas fa-check-circle"></i>
            <div class="stat-info">
                <h5>System Health</h5>
                <h3>Good</h3>
            </div>
        </div>
    </div>

    <!-- Super Admin Actions (Manager-style layout) -->
    <div class="admin-actions">
        <div class="action-card">
            <i class="fas fa-users-cog"></i>
            <h3>Manage Users</h3>
            <p><?= $totalUsers ?? 0 ?> accounts are registered. Create, update, and monitor user access.</p>
            <a href="<?= base_url('users') ?>" class="btn btn-outline-primary">Open Users</a>
        </div>
        <div class="action-card">
            <i class="fas fa-lock"></i>
            <h3>Roles & Permissions</h3>
            <p>Manage role assignments and keep permission boundaries aligned with policy.</p>
            <a href="<?= base_url('roles') ?>" class="btn btn-outline-primary">Manage Roles</a>
        </div>
        <div class="action-card">
            <i class="fas fa-history"></i>
            <h3>Audit Logs</h3>
            <p><?= $auditCount ?? 0 ?> log entries are available for compliance and activity review.</p>
            <a href="<?= base_url('activity-logs') ?>" class="btn btn-outline-primary">View Logs</a>
        </div>
        <div class="action-card">
            <i class="fas fa-cogs"></i>
            <h3>System Settings</h3>
            <p>Configure platform behavior and keep global settings consistent.</p>
            <a href="<?= base_url('settings') ?>" class="btn btn-outline-primary">Open Settings</a>
        </div>
    </div>

<!-- HR Admin Dashboard -->
<?php elseif ($roleName === 'HR Admin'): ?>
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
                <h5>Present Today</h5>
                <h3><?= $attendanceSummary['present'] ?? 0 ?></h3>
            </div>
        </div>
        <div class="stat-box">
            <i class="fas fa-download"></i>
            <div class="stat-info">
                <h5>Absent Today</h5>
                <h3><?= $attendanceSummary['absent'] ?? 0 ?></h3>
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

<!-- Manager Dashboard -->
<?php elseif ($roleName === 'Manager'): ?>
    <div class="admin-header">
        <div>
            <h1><i class="fas fa-users-cog"></i> Manager Dashboard</h1>
            <p>Operational overview across <?= $managedDepartmentCount ?? 0 ?> managed departments</p>
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
            <i class="fas fa-building"></i>
            <div class="stat-info">
                <h5>Departments</h5>
                <h3><?= $managedDepartmentCount ?? 0 ?></h3>
            </div>
        </div>
        <div class="stat-box">
            <i class="fas fa-clock"></i>
            <div class="stat-info">
                <h5>Present Today</h5>
                <h3><?= $teamAttendance['present'] ?? 0 ?></h3>
            </div>
        </div>
        <div class="stat-box">
            <i class="fas fa-calendar-check"></i>
            <div class="stat-info">
                <h5>Pending Leave Reviews</h5>
                <h3><?= $pendingTeamLeaves ?? 0 ?></h3>
            </div>
        </div>
    </div>

    <div class="admin-actions">
        <div class="action-card">
            <i class="fas fa-eye"></i>
            <h3>View Team Attendance</h3>
            <p><?= $teamAttendance['late'] ?? 0 ?> late or partial logs need attention today.</p>
            <a href="<?= base_url('attendance/team') ?>" class="btn btn-outline-primary">Team Attendance</a>
        </div>
        <div class="action-card">
            <i class="fas fa-check"></i>
            <h3>Approve Team Leaves</h3>
            <p><?= $pendingTeamLeaves ?? 0 ?> requests are waiting for your decision.</p>
            <a href="<?= base_url('leaves/team') ?>" class="btn btn-outline-primary">Open Queue</a>
        </div>
        <div class="action-card">
            <i class="fas fa-chart-pie"></i>
            <h3>Team Performance</h3>
            <p>Review monthly attendance trends and identify at-risk team members.</p>
            <a href="<?= base_url('reports/team') ?>" class="btn btn-outline-primary">View Dashboard</a>
        </div>
    </div>

<!-- Employee Dashboard -->
<?php else: ?>
    <!-- Main Content Area (Header & Sidebar provided by header.php) -->
    <!-- Alert Messages -->
    <?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?= session()->get('success') ?>
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
                    <div class="stat-value"><?= $employee ? 'Active' : 'Pending' ?></div>
                    <div class="stat-label">Profile Status</div>
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
                        <a href="<?= base_url('attendance') ?>" class="btn btn-outline-primary">Attendance Dashboard</a>
                        <a href="<?= base_url('attendance') ?>" class="btn btn-outline-primary">View History</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-calendar"></i> Leave Requests
                    </div>
                    <div class="card-body">
                        <a href="<?= base_url('leaves/create') ?>" class="btn btn-outline-primary">Request Leave</a>
                        <a href="<?= base_url('leaves') ?>" class="btn btn-outline-primary">Leave Status Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php endif; ?>

</div>

<?= $this->endSection() ?>
