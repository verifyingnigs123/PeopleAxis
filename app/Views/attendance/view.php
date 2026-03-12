<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .page-header h1 {
        color: #1e3c72;
        font-weight: 700;
        margin: 0;
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

    .month-selector {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        align-items: center;
    }

    .btn-month {
        padding: 8px 16px;
        background: #e1e8ed;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-month:hover {
        background: #d5dce3;
    }

    .btn-month.active {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
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
        <h1><i class="fas fa-clipboard-list"></i> Attendance</h1>
        <p>View your attendance records</p>
    </div>
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
</div>

<!-- Month Selector -->
<div class="month-selector">
    <span style="color: #2c3e50; font-weight: 600;">Filter by Month:</span>
    <form action="<?= base_url('attendance') ?>" method="GET" style="display: flex; gap: 10px;">
        <input type="month" name="month" class="btn-month" value="<?= isset($_GET['month']) ? esc($_GET['month']) : date('Y-m') ?>">
        <button type="submit" class="btn-month active">Apply</button>
        <a href="<?= base_url('attendance/view') ?>" class="btn-month">Reset</a>
    </form>
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
                                <strong><?= date('M d, Y', strtotime($record->date)) ?></strong>
                                <br>
                                <small style="color: #7f8c8d;"><?= date('l', strtotime($record->date)) ?></small>
                            </td>
                            <td>
                                <?php if (!empty($record->time_in)): ?>
                                    <span class="time-badge"><?= date('H:i', strtotime($record->time_in)) ?></span>
                                <?php else: ?>
                                    <span style="color: #95a5a6;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($record->time_out)): ?>
                                    <span class="time-badge"><?= date('H:i', strtotime($record->time_out)) ?></span>
                                <?php else: ?>
                                    <span style="color: #95a5a6;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $status = strtolower($record->status ?? 'absent');
                                    $statusBadge = match($status) {
                                        'present' => 'badge-present',
                                        'late' => 'badge-late',
                                        'half-day' => 'badge-half-day',
                                        default => 'badge-absent'
                                    };
                                ?>
                                <span class="badge <?= $statusBadge ?>"><?= ucfirst($status) ?></span>
                            </td>
                            <td>
                                <?php
                                    if (!empty($record->time_in) && !empty($record->time_out)) {
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
</div>

<?= $this->endSection() ?>
