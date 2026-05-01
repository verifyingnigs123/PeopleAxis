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

    .realtime-clock {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: white;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .realtime-clock-time {
        font-family: 'Courier New', monospace;
        font-size: 1.1rem;
        letter-spacing: 0.05em;
    }

    .elapsed-time-team {
        color: #f39c12;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
    }

    .active-indicator {
        display: inline-block;
        background: #27ae60;
        color: white;
        padding: 2px 8px;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 700;
        margin-left: 4px;
    }

    .filter-section {
        background: white;
        padding: 15px 18px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid #e0e0e0;
    }

    .filter-controls {
        display: flex;
        gap: 12px;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-group label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
    }

    .filter-group input,
    .filter-group select {
        padding: 10px 12px;
        border: 1px solid #d9e2ec;
        border-radius: 6px;
        font-size: 0.95rem;
        min-width: 200px;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: #6ea988;
        box-shadow: 0 0 0 3px rgba(110, 169, 136, 0.1);
    }

    .btn-filter {
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: white;
        border: none;
        padding: 10px 18px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: transform 0.2s;
    }

    .btn-filter:hover {
        color: white;
        transform: translateY(-1px);
    }

    .btn-reset {
        background: #e9ecef;
        color: #6c757d;
        border: none;
        padding: 10px 18px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s;
    }

    .btn-reset:hover {
        background: #dee2e6;
        color: #6c757d;
    }

    @media (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .filter-controls {
            flex-direction: column;
        }

        .filter-group input,
        .filter-group select {
            min-width: 100%;
        }

        .btn-filter,
        .btn-reset {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Team Attendance</span>
</div>

<h1 style="color: #2f5f45; font-weight: 700; margin-bottom: 20px;"><i class="fas fa-users-clock"></i> Team Attendance</h1>

<!-- Filter Section -->
<div class="filter-section">
    <form action="<?= base_url('attendance/team') ?>" method="get" id="filterForm" class="filter-controls">
        <div class="filter-group">
            <label for="search-name">Search Employee</label>
            <input type="text" id="search-name" name="search" placeholder="Employee name..." value="<?= esc($searchQuery ?? '') ?>">
        </div>

        <div class="filter-group">
            <label for="filter-status">Status</label>
            <select id="filter-status" name="status">
                <option value="">All Status</option>
                <option value="present" <?= ($statusFilter ?? '') === 'present' ? 'selected' : '' ?>>Present</option>
                <option value="late" <?= ($statusFilter ?? '') === 'late' ? 'selected' : '' ?>>Late</option>
                <option value="absent" <?= ($statusFilter ?? '') === 'absent' ? 'selected' : '' ?>>Absent</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="attendance-date">Date</label>
            <input id="attendance-date" type="date" name="date" value="<?= esc($selectedDate ?? date('Y-m-d')) ?>">
        </div>

        <button type="submit" class="btn-filter">
            <i class="fas fa-search"></i> Filter
        </button>

        <button type="button" class="btn-reset" onclick="document.location='<?= base_url('attendance/team') ?>'">
            <i class="fas fa-redo"></i> Reset
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
                            <th>Break Out</th>
                            <th>Break In</th>
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
                                <td><?= $record->break_out ? date('H:i', strtotime($record->break_out)) : '-' ?></td>
                                <td><?= $record->break_in ? date('H:i', strtotime($record->break_in)) : '-' ?></td>
                                <td>
                                    <?php if ($record->time_out): ?>
                                        <?= date('H:i', strtotime($record->time_out)) ?>
                                    <?php else: ?>
                                        <span class="active-indicator">ACTIVE</span>
                                        <span class="elapsed-time-team" data-checkin="<?= $record->time_in ?>"></span>
                                    <?php endif; ?>
                                </td>
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

<script>
// Realtime clock and elapsed time tracking for team attendance
const teamServerNow = new Date(<?= json_encode((new DateTimeImmutable('now', new DateTimeZone(app_timezone())))->format(DateTimeInterface::ATOM)) ?>);
const teamClockBase = Date.now();

function getTeamNow() {
    return new Date(teamServerNow.getTime() + (Date.now() - teamClockBase));
}

function updateTeamClock() {
    const clockEl = document.getElementById('teamAttendanceClock');
    if (clockEl) {
        const now = getTeamNow();
        clockEl.textContent = now.toLocaleTimeString(undefined, {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }
}

function updateTeamElapsedTimes() {
    const elapsedElements = document.querySelectorAll('.elapsed-time-team[data-checkin]');
    elapsedElements.forEach(el => {
        const checkInTime = el.getAttribute('data-checkin');
        if (!checkInTime) return;

        const checkInDate = new Date();
        const [hours, minutes, seconds] = checkInTime.split(':').map(Number);
        checkInDate.setHours(hours, minutes, seconds, 0);

        const now = getTeamNow();
        const diffMs = now - checkInDate;
        const diffSeconds = Math.floor(diffMs / 1000);
        const hrs = Math.floor(diffSeconds / 3600);
        const mins = Math.floor((diffSeconds % 3600) / 60);
        const secs = diffSeconds % 60;

        el.textContent = `${String(hrs).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    });
}

// Initialize and update every second
updateTeamClock();
updateTeamElapsedTimes();
setInterval(updateTeamClock, 1000);
setInterval(updateTeamElapsedTimes, 1000);
</script>

<?= $this->endSection() ?>