<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    /* ===== Dashboard Styles ===== */
    .dashboard-container {
        padding: 20px;
        background: #f8f9fa;
        min-height: 100vh;
    }

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
        font-size: 2rem;
    }

    .page-header p {
        color: #95a5a6;
        margin: 5px 0 0 0;
    }

    .user-info {
        background: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
    }

    /* ===== Statistics Cards ===== */
    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        text-align: center;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stat-card.primary { border-left-color: #667eea; }
    .stat-card.success { border-left-color: #28a745; }
    .stat-card.warning { border-left-color: #ffc107; }
    .stat-card.danger { border-left-color: #dc3545; }
    .stat-card.info { border-left-color: #17a2b8; }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 1.5rem;
        color: white;
    }

    .stat-card.primary .stat-icon { background: linear-gradient(135deg, #667eea, #764ba2); }
    .stat-card.success .stat-icon { background: linear-gradient(135deg, #28a745, #20c997); }
    .stat-card.warning .stat-icon { background: linear-gradient(135deg, #ffc107, #fd7e14); }
    .stat-card.danger .stat-icon { background: linear-gradient(135deg, #dc3545, #e83e8c); }
    .stat-card.info .stat-icon { background: linear-gradient(135deg, #17a2b8, #20c997); }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #95a5a6;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ===== Charts Section ===== */
    .chart-container {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .chart-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c3e50;
    }

    /* ===== Recent Activities ===== */
    .activity-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .activity-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #ecf0f1;
        transition: background 0.2s ease;
    }

    .activity-item:hover {
        background: #f8f9fa;
        margin: 0 -15px;
        padding: 15px;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 0.9rem;
        color: white;
        background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 3px;
    }

    .activity-time {
        font-size: 0.8rem;
        color: #95a5a6;
    }

    /* ===== Tables ===== */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .data-table th,
    .data-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ecf0f1;
    }

    .data-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .data-table tbody tr:hover {
        background: #f8f9fa;
    }

    /* ===== Status Badges ===== */
    .badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-success {
        background: #d4edda;
        color: #155724;
    }

    .badge-warning {
        background: #fff3cd;
        color: #856404;
    }

    .badge-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-info {
        background: #d1ecf1;
        color: #0c5460;
    }

    /* ===== Responsive Design ===== */
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 15px;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .user-info {
            width: 100%;
        }

        .stat-card {
            margin-bottom: 20px;
        }

        .stat-value {
            font-size: 2rem;
        }

        .chart-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .data-table {
            font-size: 0.9rem;
        }

        .data-table th,
        .data-table td {
            padding: 8px;
        }
    }

    @media (max-width: 576px) {
        .page-header h1 {
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 1.8rem;
        }

        .activity-item {
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
        }

        .activity-icon {
            margin-bottom: 10px;
            margin-right: 0;
        }
    }

    /* ===== Loading Animation ===== */
    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* ===== Quick Actions ===== */
    .quick-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-outline {
        background: white;
        color: #667eea;
        border: 2px solid #667eea;
    }

    .btn-outline:hover {
        background: #667eea;
        color: white;
    }
</style>

<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <p>Welcome back, <?= esc($user['name']) ?>! Here's your system overview.</p>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            </div>
            <div>
                <div style="font-weight: 600; color: #2c3e50;"><?= esc($user['name']) ?></div>
                <div style="font-size: 0.9rem; color: #95a5a6;"><?= ucfirst(esc($userRole)) ?></div>
            </div>
        </div>
    </div>

    <?php if ($userRole === 'admin'): ?>
        <!-- Admin Dashboard -->
        <div class="row">
            <!-- Statistics Cards -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value"><?= $totalUsers ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stat-value"><?= $totalEmployees ?></div>
                    <div class="stat-label">Employees</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-value"><?= $totalDepartments ?></div>
                    <div class="stat-label">Departments</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="stat-value"><?= $totalPositions ?></div>
                    <div class="stat-label">Positions</div>
                </div>
            </div>
        </div>

        <!-- Additional Statistics Row -->
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-value"><?= $presentThisMonth ?></div>
                    <div class="stat-label">Present This Month</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card danger">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <div class="stat-value"><?= $absentThisMonth ?></div>
                    <div class="stat-label">Absent This Month</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-value"><?= $pendingLeaves ?></div>
                    <div class="stat-label">Pending Leaves</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value"><?= $approvedLeaves ?></div>
                    <div class="stat-label">Approved Leaves</div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3 class="chart-title">Recent Users</h3>
                        <div class="quick-actions">
                            <a href="<?= site_url('users') ?>" class="btn-action btn-outline">
                                <i class="fas fa-eye"></i> View All
                            </a>
                        </div>
                    </div>
                    <ul class="activity-list">
                        <?php if (!empty($recentUsers)): ?>
                            <?php foreach ($recentUsers as $recentUser): ?>
                                <li class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title"><?= esc($recentUser->name) ?></div>
                                        <div class="activity-time">
                                            <?= esc($recentUser->email) ?> • 
                                            <?= date('M d, Y', strtotime($recentUser->created_at)) ?>
                                        </div>
                                    </div>
                                    <span class="badge badge-<?= $recentUser->is_active ? 'success' : 'danger' ?>">
                                        <?= $recentUser->is_active ? 'Active' : 'Inactive' ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="activity-item">
                                <div class="activity-content">
                                    <div class="activity-title">No recent users found</div>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3 class="chart-title">Recent Employees</h3>
                        <div class="quick-actions">
                            <a href="<?= site_url('employees') ?>" class="btn-action btn-outline">
                                <i class="fas fa-eye"></i> View All
                            </a>
                        </div>
                    </div>
                    <ul class="activity-list">
                        <?php if (!empty($recentEmployees)): ?>
                            <?php foreach ($recentEmployees as $employee): ?>
                                <li class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title"><?= esc($employee->first_name) ?> <?= esc($employee->last_name) ?></div>
                                        <div class="activity-time">
                                            <?= esc($employee->email) ?> • 
                                            <?= date('M d, Y', strtotime($employee->created_at)) ?>
                                        </div>
                                    </div>
                                    <span class="badge badge-<?= $employee->status === 'active' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($employee->status) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="activity-item">
                                <div class="activity-content">
                                    <div class="activity-title">No recent employees found</div>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- User Dashboard -->
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stat-value"><?= $totalEmployees ?></div>
                    <div class="stat-label">Total Employees</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-value"><?= $totalDepartments ?></div>
                    <div class="stat-label">Departments</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-value"><?= $myPendingLeaves ?? 0 ?></div>
                    <div class="stat-label">Pending Leaves</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value"><?= $myApprovedLeaves ?? 0 ?></div>
                    <div class="stat-label">Approved Leaves</div>
                </div>
            </div>
        </div>

        <!-- User's Attendance and Leave Information -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3 class="chart-title">My Recent Attendance</h3>
                    </div>
                    <?php if (!empty($myAttendance)): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($myAttendance as $attendance): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($attendance->attendance_date)) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $attendance->status === 'present' ? 'success' : 'danger' ?>">
                                                <?= ucfirst($attendance->status) ?>
                                            </span>
                                        </td>
                                        <td><?= $attendance->check_in ?? '-' ?></td>
                                        <td><?= $attendance->check_out ?? '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="text-align: center; color: #95a5a6; padding: 20px;">
                            No attendance records found.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3 class="chart-title">My Leave Requests</h3>
                    </div>
                    <?php if (!empty($myLeaves)): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($myLeaves as $leave): ?>
                                    <tr>
                                        <td><?= ucfirst(esc($leave->type)) ?></td>
                                        <td><?= date('M d, Y', strtotime($leave->start_date)) ?></td>
                                        <td><?= date('M d, Y', strtotime($leave->end_date)) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $leave->status === 'approved' ? 'success' : ($leave->status === 'pending' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($leave->status) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="text-align: center; color: #95a5a6; padding: 20px;">
                            No leave requests found.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- JavaScript for real-time updates -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh dashboard data every 30 seconds
    setInterval(function() {
        fetch('<?= site_url('dashboard/getStats') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update statistics with new data
                    updateStatistics(data.data);
                }
            })
            .catch(error => console.error('Error fetching dashboard stats:', error));
    }, 30000);

    function updateStatistics(stats) {
        // Update stat cards with animation
        const statCards = document.querySelectorAll('.stat-value');
        statCards.forEach(card => {
            card.style.transition = 'all 0.5s ease';
            card.style.transform = 'scale(1.1)';
            setTimeout(() => {
                card.style.transform = 'scale(1)';
            }, 200);
        });
    }

    // Add interactive hover effects
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.cursor = 'pointer';
        });
    });
});
</script>

<?= $this->endSection() ?>
