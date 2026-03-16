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
        color: #6c757d;
    }

    .filter-form {
        display: flex;
        gap: 12px;
        align-items: end;
        flex-wrap: wrap;
        background: white;
        padding: 16px 18px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-group label {
        font-size: 0.82rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #6c757d;
    }

    .filter-group input {
        min-width: 180px;
        padding: 10px 12px;
        border: 1px solid #d9e2ec;
        border-radius: 8px;
    }

    .btn-primary-soft {
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 16px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary-soft:hover {
        color: white;
        transform: translateY(-1px);
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
        grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr);
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
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
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
        padding: 12px 0;
        border-bottom: 1px solid #eef2f7;
        gap: 12px;
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
        color: #6c757d;
        font-size: 0.88rem;
    }

    .metric-value {
        font-weight: 700;
        color: #2f5f45;
    }

    .alert {
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 20px;
    }

    .alert-danger {
        background: #f8d7da;
        color: #842029;
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

    .badge-present {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-late {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-absent {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-default {
        background: #e2e8f0;
        color: #334155;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .attendance-table {
        width: 100%;
        border-collapse: collapse;
    }

    .attendance-table thead th {
        background: #f8fafc;
        color: #2f5f45;
        padding: 14px 18px;
        text-align: left;
        font-size: 0.82rem;
        text-transform: uppercase;
        font-weight: 700;
        border-bottom: 2px solid #e2e8f0;
    }

    .attendance-table tbody td {
        padding: 14px 18px;
        border-bottom: 1px solid #eef2f7;
        color: #334155;
        vertical-align: top;
    }

    .attendance-table tbody tr:hover {
        background: #f8fbff;
    }

    .muted {
        color: #64748b;
        font-size: 0.88rem;
    }

    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #94a3b8;
    }

    .pagination-wrap {
        padding: 18px 22px;
        border-top: 1px solid #eef2f7;
        background: #f8fafc;
    }

    @media (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Team Attendance</span>
</div>

<div class="page-header">
    <div>
        <h1><i class="fas fa-users-clock"></i> Team Attendance Dashboard</h1>
        <p>Daily attendance visibility for your managed departments on <?= date('M d, Y', strtotime($selectedDate)) ?>.</p>
    </div>

    <form action="<?= base_url('attendance/team') ?>" method="get" class="filter-form">
        <div class="filter-group">
            <label for="attendance-date">Attendance Date</label>
            <input id="attendance-date" type="date" name="date" value="<?= esc($selectedDate) ?>">
        </div>
        <button type="submit" class="btn-primary-soft">
            <i class="fas fa-filter"></i> Apply
        </button>
    </form>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if ($teamCount === 0): ?>
    <div class="panel">
        <div class="empty-state">
            <i class="fas fa-users" style="font-size: 2.8rem;"></i>
            <p>No approved team members found yet. Employees approved by Super Admin will appear here.</p>
        </div>
    </div>
<?php else: ?>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Team Members</div>
            <div class="stat-value"><?= $teamCount ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Present</div>
            <div class="stat-value"><?= $attendanceSummary['present'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Late or Partial</div>
            <div class="stat-value"><?= $attendanceSummary['late'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Absent or Missing Logs</div>
            <div class="stat-value"><?= $attendanceSummary['absent'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">On Leave</div>
            <div class="stat-value"><?= $attendanceSummary['on_leave'] ?? 0 ?></div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="panel">
            <div class="panel-header">
                <h2><i class="fas fa-layer-group"></i> Department Coverage</h2>
            </div>
            <div class="panel-body">
                <?php foreach ($departmentSummary as $department): ?>
                    <div class="metric-row">
                        <div>
                            <div class="metric-title"><?= esc($department['name']) ?></div>
                            <div class="metric-meta"><?= $department['recorded'] ?> recorded, <?= $department['missing'] ?> missing, <?= $department['on_leave'] ?> on leave</div>
                        </div>
                        <div class="metric-value"><?= $department['members'] ?> members</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h2><i class="fas fa-user-clock"></i> Missing Attendance</h2>
            </div>
            <div class="panel-body">
                <?php if (!empty($missingMembers)): ?>
                    <?php foreach ($missingMembers as $member): ?>
                        <div class="metric-row">
                            <div>
                                <div class="metric-title"><?= esc($member['name']) ?></div>
                                <div class="metric-meta"><?= esc($member['department']) ?></div>
                            </div>
                            <span class="badge badge-absent">No Log</span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state" style="padding: 20px 10px;">
                        <i class="fas fa-check-circle" style="font-size: 2rem;"></i>
                        <p>Everyone has a log or approved leave for this date.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2><i class="fas fa-list"></i> Attendance Logs For <?= date('M d, Y', strtotime($selectedDate)) ?></h2>
        </div>

        <div class="table-responsive">
            <?php if (!empty($attendanceRecords)): ?>
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendanceRecords as $record): ?>
                            <?php
                                $status = strtolower((string) ($record->status ?? 'present'));
                                $badgeClass = 'badge-default';

                                if ($status === 'absent') {
                                    $badgeClass = 'badge-absent';
                                } elseif (in_array($status, ['late', 'half-day', 'half day'], true)) {
                                    $badgeClass = 'badge-late';
                                } else {
                                    $badgeClass = 'badge-present';
                                }
                            ?>
                            <tr>
                                <td>
                                    <strong><?= esc($record->employee_name ?? 'Unknown Employee') ?></strong><br>
                                    <span class="muted"><?= esc($record->staff_code ?? 'N/A') ?></span>
                                </td>
                                <td><?= esc($record->department_name ?? 'Unassigned') ?></td>
                                <td><?= $record->time_in ? date('H:i', strtotime($record->time_in)) : '-' ?></td>
                                <td><?= $record->time_out ? date('H:i', strtotime($record->time_out)) : '-' ?></td>
                                <td>
                                    <span class="badge <?= $badgeClass ?>"><?= esc(ucwords(str_replace('-', ' ', $record->status ?? 'present'))) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox" style="font-size: 2.6rem;"></i>
                    <p>No attendance logs were recorded for this date.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($pager): ?>
            <div class="pagination-wrap">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>