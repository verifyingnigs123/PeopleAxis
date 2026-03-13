<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: end;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .page-header h1 {
        color: #1e3c72;
        font-weight: 700;
        margin: 0;
    }

    .page-header p {
        margin: 8px 0 0;
        color: #64748b;
    }

    .filter-form {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: end;
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
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
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
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #2a5298;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-panel {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .info-panel-header {
        padding: 18px 22px;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        font-weight: 700;
    }

    .info-panel-body {
        padding: 18px 22px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #eef2f7;
    }

    .info-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .info-row:first-child {
        padding-top: 0;
    }

    .info-label {
        color: #64748b;
        font-size: 0.9rem;
    }

    .info-value {
        color: #1e3c72;
        font-weight: 700;
        text-align: right;
    }

    .stat-label {
        color: #7f8c8d;
        font-size: 0.85rem;
        text-transform: uppercase;
        font-weight: 600;
    }

    .stat-value {
        font-size: 2rem;
        color: #2a5298;
        font-weight: 700;
        margin-top: 8px;
    }

    .admin-panel {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .panel-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 20px 25px;
    }

    .panel-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.3rem;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .attendance-table {
        width: 100%;
        border-collapse: collapse;
    }

    .attendance-table thead th {
        background: #f8f9fa;
        color: #1e3c72;
        font-weight: 600;
        padding: 14px 20px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .attendance-table tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f3f5;
        font-size: 0.9rem;
        color: #495057;
    }

    .attendance-table tbody tr:hover {
        background: #f8f9ff;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .badge-present {
        background: #d4edda;
        color: #155724;
    }

    .badge-absent {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-late {
        background: #fff3cd;
        color: #856404;
    }

    .badge-half-day {
        background: #d1ecf1;
        color: #0c5460;
    }

    .time-badge {
        background: #f0f3f7;
        color: #2c3e50;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #95a5a6;
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

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Attendance</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="fas fa-clipboard-list"></i> Attendance Dashboard</h1>
        <p>Monthly attendance overview for <?= esc($monthLabel ?? date('F Y')) ?>.</p>
    </div>

    <form action="<?= base_url('attendance') ?>" method="GET" class="filter-form">
        <div class="filter-group">
            <label for="attendance-month">Month</label>
            <input id="attendance-month" type="month" name="month" value="<?= esc($selectedMonth ?? date('Y-m')) ?>">
        </div>
        <button type="submit" class="btn-primary-soft">
            <i class="fas fa-filter"></i> Apply
        </button>
    </form>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Working Days</div>
        <div class="stat-value"><?= $stats['total_days'] ?? 0 ?></div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Days Present</div>
        <div class="stat-value" style="color: #27ae60;"><?= $stats['present_days'] ?? 0 ?></div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Days Absent</div>
        <div class="stat-value" style="color: #e74c3c;"><?= $stats['absent_days'] ?? 0 ?></div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Late Days</div>
        <div class="stat-value" style="color: #f39c12;"><?= $stats['late_days'] ?? 0 ?></div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Attendance Rate</div>
        <div class="stat-value"><?= $stats['attendance_rate'] ?? 0 ?>%</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Worked Hours</div>
        <div class="stat-value"><?= $stats['worked_hours'] ?? 0 ?></div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="info-panel">
        <div class="info-panel-header">
            <i class="fas fa-id-badge"></i> Employee Snapshot
        </div>
        <div class="info-panel-body">
            <?php if ($employee): ?>
                <div class="info-row">
                    <div class="info-label">Employee</div>
                    <div class="info-value"><?= esc(trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? ''))) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Employee ID</div>
                    <div class="info-value"><?= esc($employee->employee_id ?? 'N/A') ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Current Month</div>
                    <div class="info-value"><?= esc($monthLabel ?? date('F Y')) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status</div>
                    <div class="info-value"><?= esc(ucfirst((string) ($employee->status ?? 'active'))) ?></div>
                </div>
            <?php else: ?>
                <div class="empty-state" style="padding: 20px 10px;">
                    <i class="fas fa-user-slash"></i>
                    <p>No employee profile data available yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="info-panel">
        <div class="info-panel-header">
            <i class="fas fa-clock"></i> Latest Attendance Record
        </div>
        <div class="info-panel-body">
            <?php if (!empty($latestRecord)): ?>
                <div class="info-row">
                    <div class="info-label">Date</div>
                    <div class="info-value"><?= date('M d, Y', strtotime($latestRecord->date)) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Check In</div>
                    <div class="info-value"><?= $latestRecord->time_in ? date('H:i', strtotime($latestRecord->time_in)) : '-' ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Check Out</div>
                    <div class="info-value"><?= $latestRecord->time_out ? date('H:i', strtotime($latestRecord->time_out)) : '-' ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status</div>
                    <div class="info-value"><?= esc(ucwords(str_replace('-', ' ', (string) ($latestRecord->status ?? 'present')))) ?></div>
                </div>
            <?php else: ?>
                <div class="empty-state" style="padding: 24px 10px;">
                    <i class="fas fa-inbox"></i>
                    <p>No attendance records found yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Attendance Table -->
<div class="admin-panel">
    <div class="panel-header">
        <h2><i class="fas fa-list"></i> Attendance Records (<?= count($records ?? []) ?>)</h2>
    </div>

    <div class="table-responsive">
        <?php if (!empty($records)): ?>
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Check-In Time</th>
                        <th>Check-Out Time</th>
                        <th>Status</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $i => $record): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <strong><?= date('M d, Y', strtotime($record->date ?? date('Y-m-d'))) ?></strong>
                                <br>
                                <small style="color: #7f8c8d;"><?= date('l', strtotime($record->date ?? date('Y-m-d'))) ?></small>
                            </td>
                            <td>
                                <?php if ($record->time_in): ?>
                                    <span class="time-badge"><?= date('H:i', strtotime($record->time_in)) ?></span>
                                <?php else: ?>
                                    <span style="color: #95a5a6;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($record->time_out): ?>
                                    <span class="time-badge"><?= date('H:i', strtotime($record->time_out)) ?></span>
                                <?php else: ?>
                                    <span style="color: #95a5a6;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $status = strtolower((string) ($record->status ?? 'absent'));
                                    $statusBadge = match($status) {
                                        'present' => 'badge-present',
                                        'late' => 'badge-late',
                                        'half-day', 'half day' => 'badge-half-day',
                                        default => 'badge-absent'
                                    };
                                ?>
                                <span class="badge <?= $statusBadge ?>"><?= esc(ucwords(str_replace('-', ' ', $status))) ?></span>
                            </td>
                            <td>
                                <?php
                                    if ($record->time_in && $record->time_out) {
                                        $start = strtotime($record->time_in);
                                        $end = strtotime($record->time_out);
                                        $duration = round(($end - $start) / 3600, 1);
                                        echo '<strong>' . $duration . ' hrs</strong>';
                                    } else {
                                        echo '<span style="color: #95a5a6;">-</span>';
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No attendance records found for the selected period.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($pager) && $pager): ?>
        <div class="pagination-wrap">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
