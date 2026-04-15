<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: end;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .page-header h1 {
        margin: 0;
        color: #2f5f45;
        font-weight: 700;
    }

    .page-header p {
        margin: 8px 0 0;
        color: #64748b;
    }

    .toolbar {
        display: flex;
        gap: 12px;
        align-items: end;
        flex-wrap: wrap;
        background: white;
        padding: 16px 18px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .toolbar label {
        font-size: 0.82rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #6c757d;
        display: block;
        margin-bottom: 6px;
    }

    .toolbar input {
        min-width: 180px;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #d9e2ec;
    }

    .btn-primary-soft {
        background: linear-gradient(135deg, #6ea988 0%, #5b9474 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 16px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #6ea988;
    }

    .stat-label {
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #6c757d;
    }

    .stat-value {
        margin-top: 10px;
        font-size: 2rem;
        font-weight: 700;
        color: #2f5f45;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.4fr);
        gap: 22px;
        margin-bottom: 28px;
    }

    .panel {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .panel-header {
        padding: 18px 22px;
        background: linear-gradient(135deg, #6ea988 0%, #5b9474 100%);
        color: white;
    }

    .panel-header h2 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
    }

    .panel-body {
        padding: 18px 22px;
    }

    .metric-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #eef2f7;
    }

    .metric-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .metric-row:first-child {
        padding-top: 0;
    }

    .metric-title {
        font-weight: 600;
        color: #2c3e50;
    }

    .metric-meta {
        color: #64748b;
        font-size: 0.88rem;
    }

    .metric-value {
        font-weight: 700;
        color: #2f5f45;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .performance-table {
        width: 100%;
        border-collapse: collapse;
    }

    .performance-table thead th {
        background: #f8fafc;
        color: #2f5f45;
        padding: 14px 18px;
        text-align: left;
        font-size: 0.82rem;
        text-transform: uppercase;
        font-weight: 700;
        border-bottom: 2px solid #e2e8f0;
    }

    .performance-table tbody td {
        padding: 14px 18px;
        border-bottom: 1px solid #eef2f7;
        color: #334155;
        vertical-align: top;
    }

    .performance-table tbody tr:hover {
        background: #eef7f1;
    }

    .muted {
        color: #64748b;
        font-size: 0.88rem;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge-strong {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-stable {
        background: #e1efe6;
        color: #4d7f63;
    }

    .badge-watch {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-risk {
        background: #fee2e2;
        color: #991b1b;
    }

    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #94a3b8;
    }

    .daily-panel {
        margin-top: 6px;
    }

    .monthly-panel {
        margin-top: 20px;
    }

    .daily-toolbar {
        display: flex;
        gap: 10px;
        align-items: end;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .daily-toolbar label {
        font-size: 0.78rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #6c757d;
        display: block;
        margin-bottom: 6px;
    }

    .daily-toolbar input,
    .daily-toolbar select {
        min-width: 160px;
        padding: 8px 10px;
        border-radius: 8px;
        border: 1px solid #d9e2ec;
        background: #fff;
    }

    .daily-table {
        width: 100%;
        border-collapse: collapse;
    }

    .daily-table thead th {
        background: #f8fafc;
        color: #2f5f45;
        padding: 12px 14px;
        text-align: left;
        font-size: 0.8rem;
        text-transform: uppercase;
        font-weight: 700;
        border-bottom: 2px solid #e2e8f0;
    }

    .daily-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #eef2f7;
        color: #334155;
        vertical-align: top;
    }

    .daily-table tbody tr:hover {
        background: #eef7f1;
    }

    .status-chip {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-present {
        background: #d1fae5;
        color: #065f46;
    }

    .status-late {
        background: #fef3c7;
        color: #92400e;
    }

    .status-absent {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-leave {
        background: #dbeafe;
        color: #1d4ed8;
    }

    @media (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Team Performance</span>
</div>

<div class="page-header">
    <div>
        <h1><i class="fas fa-chart-line"></i> Team Performance Dashboard</h1>
        <p>Attendance and leave signals for <?= esc($periodLabel) ?> across your managed departments.</p>
    </div>

    <form action="<?= base_url('reports/team') ?>" method="get" class="toolbar">
        <div>
            <label for="report-month">Reporting Month</label>
            <input id="report-month" type="month" name="month" value="<?= esc($selectedMonth) ?>">
        </div>
        <button type="submit" class="btn-primary-soft">
            <i class="fas fa-filter"></i> Apply
        </button>
    </form>
</div>

<?php if (empty($managedDepartments)): ?>
    <div class="panel">
        <div class="empty-state">
            <i class="fas fa-building" style="font-size: 2.8rem;"></i>
            <p>No departments are assigned to your manager account yet.</p>
        </div>
    </div>
<?php else: ?>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Team Members</div>
            <div class="stat-value"><?= $summary['team_members'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Average Score</div>
            <div class="stat-value"><?= $summary['average_score'] ?? 0 ?>%</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pending Leave Reviews</div>
            <div class="stat-value"><?= $summary['pending_leaves'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">At Risk Members</div>
            <div class="stat-value"><?= $summary['at_risk_members'] ?? 0 ?></div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="panel">
            <div class="panel-header">
                <h2><i class="fas fa-sitemap"></i> Department Snapshot</h2>
            </div>
            <div class="panel-body">
                <?php foreach ($departmentBreakdown as $department): ?>
                    <div class="metric-row">
                        <div>
                            <div class="metric-title"><?= esc($department['name']) ?></div>
                            <div class="metric-meta"><?= $department['members'] ?> members, <?= $department['pending_leaves'] ?> pending leave items</div>
                        </div>
                        <div class="metric-value"><?= $department['average_score'] ?>%</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h2><i class="fas fa-users"></i> Team Scoreboard</h2>
            </div>
            <div class="table-responsive">
                <?php if (!empty($performanceRows)): ?>
                    <table class="performance-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Present</th>
                                <th>Late</th>
                                <th>Absent</th>
                                <th>Leave Requests</th>
                                <th>Score</th>
                                <th>Status</th>
                                <th>Last Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($performanceRows as $row): ?>
                                <?php
                                    $badgeClass = match ($row['performance_label']) {
                                        'Strong' => 'badge-strong',
                                        'Stable' => 'badge-stable',
                                        'Watch' => 'badge-watch',
                                        default => 'badge-risk',
                                    };
                                ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($row['employee_name']) ?></strong><br>
                                        <span class="muted"><?= esc($row['employee_code']) ?></span>
                                    </td>
                                    <td><?= esc($row['department_name']) ?></td>
                                    <td><?= $row['present_days'] ?></td>
                                    <td><?= $row['late_days'] ?></td>
                                    <td><?= $row['absent_days'] ?></td>
                                    <td>
                                        <?= $row['leave_requests'] ?> total<br>
                                        <span class="muted"><?= $row['pending_leave_requests'] ?> pending</span>
                                    </td>
                                    <td><strong><?= $row['score'] ?>%</strong></td>
                                    <td>
                                        <span class="badge <?= $badgeClass ?>"><?= esc($row['performance_label']) ?></span>
                                    </td>
                                    <td><?= !empty($row['last_attendance_date']) ? date('M d, Y', strtotime($row['last_attendance_date'])) : 'No records' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox" style="font-size: 2.6rem;"></i>
                        <p>No team attendance or leave data is available for this month.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="panel daily-panel">
        <div class="panel-header">
            <h2><i class="fas fa-table"></i> Team Daily Attendance</h2>
        </div>
        <div class="panel-body">
            <form action="<?= base_url('reports/team') ?>" method="get" class="daily-toolbar">
                <input type="hidden" name="month" value="<?= esc($selectedMonth) ?>">
                <div>
                    <label for="daily-date">Date</label>
                    <input id="daily-date" type="date" name="date" value="<?= esc($selectedDate ?? date('Y-m-d')) ?>">
                </div>
                <div>
                    <label for="daily-sort-by">Sort By</label>
                    <select id="daily-sort-by" name="daily_sort_by">
                        <option value="date" <?= ($dailySortBy ?? 'date') === 'date' ? 'selected' : '' ?>>Date</option>
                        <option value="employee_name" <?= ($dailySortBy ?? 'date') === 'employee_name' ? 'selected' : '' ?>>Employee Name</option>
                    </select>
                </div>
                <div>
                    <label for="daily-sort-dir">Order</label>
                    <select id="daily-sort-dir" name="daily_sort_dir">
                        <option value="desc" <?= ($dailySortDir ?? 'desc') === 'desc' ? 'selected' : '' ?>>Descending</option>
                        <option value="asc" <?= ($dailySortDir ?? 'desc') === 'asc' ? 'selected' : '' ?>>Ascending</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary-soft">
                    <i class="fas fa-filter"></i> Apply
                </button>
            </form>

            <div class="table-responsive">
                <?php if (!empty($dailyAttendanceRows)): ?>
                    <table class="daily-table">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Department</th>
                                <th>Date</th>
                                <th>In Time</th>
                                <th>Out Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dailyAttendanceRows as $row): ?>
                                <?php
                                    $status = (string) ($row['status'] ?? 'Absent');
                                    $statusClass = 'status-absent';
                                    if ($status === 'Present') {
                                        $statusClass = 'status-present';
                                    } elseif ($status === 'Late') {
                                        $statusClass = 'status-late';
                                    } elseif ($status === 'Leave') {
                                        $statusClass = 'status-leave';
                                    }
                                ?>
                                <tr>
                                    <td><?= esc((string) ($row['employee_name'] ?? '-')) ?></td>
                                    <td><?= esc((string) ($row['department_name'] ?? 'Unassigned')) ?></td>
                                    <td><?= esc((string) ($row['date'] ?? ($selectedDate ?? date('Y-m-d')))) ?></td>
                                    <td><?= !empty($row['time_in']) ? esc(date('h:i A', strtotime((string) $row['time_in']))) : 'No Log' ?></td>
                                    <td><?= !empty($row['time_out']) ? esc(date('h:i A', strtotime((string) $row['time_out']))) : 'No Log' ?></td>
                                    <td><span class="status-chip <?= $statusClass ?>"><?= esc($status) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox" style="font-size: 2.4rem;"></i>
                        <p>No attendance records available for the selected date.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="panel monthly-panel">
        <div class="panel-header">
            <h2><i class="fas fa-calendar-alt"></i> Monthly Attendance Records</h2>
        </div>
        <div class="panel-body">
            <p class="muted" style="margin-top:0; margin-bottom: 12px;">Showing attendance logs for <?= esc($periodLabel) ?>.</p>
            <div class="table-responsive">
                <?php if (!empty($monthlyAttendanceRows)): ?>
                    <table class="daily-table">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Date</th>
                                <th>In Date</th>
                                <th>In Time</th>
                                <th>Out Date</th>
                                <th>Out Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($monthlyAttendanceRows as $row): ?>
                                <tr>
                                    <td><?= esc((string) ($row['employee_id'] ?? 'N/A')) ?></td>
                                    <td><?= esc((string) ($row['employee_name'] ?? '-')) ?></td>
                                    <td><?= !empty($row['date']) ? esc(date('Y-m-d', strtotime((string) $row['date']))) : 'No Log' ?></td>
                                    <td><?= !empty($row['in_date']) ? esc(date('Y-m-d', strtotime((string) $row['in_date']))) : 'No Log' ?></td>
                                    <td><?= !empty($row['in_time']) ? esc(date('h:i A', strtotime((string) $row['in_time']))) : 'No Log' ?></td>
                                    <td><?= !empty($row['out_date']) ? esc(date('Y-m-d', strtotime((string) $row['out_date']))) : 'No Log' ?></td>
                                    <td><?= !empty($row['out_time']) ? esc(date('h:i A', strtotime((string) $row['out_time']))) : 'No Log' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times" style="font-size: 2.4rem;"></i>
                        <p>No monthly attendance logs found for <?= esc($periodLabel) ?>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>