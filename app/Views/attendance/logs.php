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
        color: #2f5f45;
        font-weight: 700;
        margin: 0;
    }

    .filters-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .filter-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #2c3e50;
        text-transform: uppercase;
    }

    .filter-input {
        padding: 10px 12px;
        border: 2px solid #e1e8ed;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .filter-input:focus {
        outline: none;
        border-color: #6ea988;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
    }

    .filter-btn {
        padding: 10px 20px;
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.4);
    }

    .admin-panel {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .panel-header {
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
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
        color: #2f5f45;
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

    .pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        padding: 20px;
        background: #f8f9fa;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border-radius: 4px;
        background: white;
        color: #6ea988;
        text-decoration: none;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .pagination a:hover {
        background: #6ea988;
        color: white;
        border-color: #6ea988;
    }

    .pagination .active {
        background: #6ea988;
        color: white;
        border-color: #6ea988;
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Attendance Logs</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="fas fa-history"></i> Attendance Logs</h1>
        <p>Track all employee attendance records</p>
    </div>
</div>

<!-- Filters -->
<div class="filters-section">
    <form action="<?= base_url('attendance/logs') ?>" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; width: 100%;">
        <div class="filter-group">
            <label class="filter-label">Employee</label>
            <input type="text" name="employee" class="filter-input" placeholder="Employee name or email" value="<?= isset($_GET['employee']) ? esc($_GET['employee']) : '' ?>" style="min-width: 200px;">
        </div>

        <div class="filter-group">
            <label class="filter-label">Status</label>
            <select name="status" class="filter-input">
                <option value="">All Status</option>
                <option value="present" <?= isset($_GET['status']) && $_GET['status'] === 'present' ? 'selected' : '' ?>>Present</option>
                <option value="absent" <?= isset($_GET['status']) && $_GET['status'] === 'absent' ? 'selected' : '' ?>>Absent</option>
                <option value="late" <?= isset($_GET['status']) && $_GET['status'] === 'late' ? 'selected' : '' ?>>Late</option>
                <option value="half-day" <?= isset($_GET['status']) && $_GET['status'] === 'half-day' ? 'selected' : '' ?>>Half Day</option>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">Date From</label>
            <input type="date" name="date_from" class="filter-input" value="<?= isset($_GET['date_from']) ? esc($_GET['date_from']) : '' ?>">
        </div>

        <div class="filter-group">
            <label class="filter-label">Date To</label>
            <input type="date" name="date_to" class="filter-input" value="<?= isset($_GET['date_to']) ? esc($_GET['date_to']) : '' ?>">
        </div>

        <button type="submit" class="filter-btn">
            <i class="fas fa-search"></i> Filter
        </button>
        <a href="<?= base_url('attendance/logs') ?>" class="filter-btn" style="background: #95a5a6;text-decoration:none;">
            <i class="fas fa-times"></i> Reset
        </a>
    </form>
</div>

<!-- Attendance Logs Table -->
<div class="admin-panel">
    <div class="panel-header">
        <h2><i class="fas fa-list"></i> All Attendance Records (<?= isset($total) ? $total : 0 ?> records)</h2>
    </div>

    <div class="table-responsive">
        <?php if (!empty($logs)): ?>
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Check-In Time</th>
                        <th>Check-Out Time</th>
                        <th>Status</th>
                        <th>Duration</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $i => $record): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <strong><?= esc($record->employee_name ?? 'N/A') ?></strong>
                                <br>
                                <small style="color: #7f8c8d;"><?= esc($record->employee_id ?? 'N/A') ?></small>
                            </td>
                            <td>
                                <strong><?= date('M d, Y', strtotime($record->attendance_date ?? now())) ?></strong>
                                <br>
                                <small style="color: #7f8c8d;"><?= date('l', strtotime($record->attendance_date ?? now())) ?></small>
                            </td>
                            <td>
                                <?php if ($record->check_in_time): ?>
                                    <span class="time-badge"><?= date('H:i', strtotime($record->check_in_time)) ?></span>
                                <?php else: ?>
                                    <span style="color: #95a5a6;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($record->check_out_time): ?>
                                    <span class="time-badge"><?= date('H:i', strtotime($record->check_out_time)) ?></span>
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
                                    if ($record->check_in_time && $record->check_out_time) {
                                        $start = strtotime($record->check_in_time);
                                        $end = strtotime($record->check_out_time);
                                        $duration = round(($end - $start) / 3600, 1);
                                        echo '<strong>' . $duration . ' hrs</strong>';
                                    } else {
                                        echo '<span style="color: #95a5a6;">-</span>';
                                    }
                                ?>
                            </td>
                            <td>
                                <small><?= esc($record->notes ?? '-') ?></small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No attendance records found.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($pager) && $pager): ?>
        <div class="pagination">
            <?= $pager->links('default', 'default_full') ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
