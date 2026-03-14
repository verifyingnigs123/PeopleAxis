<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .hr-shell {
        max-width: 1240px;
        margin: 0 auto;
        padding: 8px 0 18px;
    }

    .breadcrumbs {
        margin-bottom: 14px;
        font-size: 0.9rem;
    }

    .breadcrumbs a {
        color: #6ea988;
        text-decoration: none;
    }

    .breadcrumbs a:hover {
        text-decoration: underline;
    }

    .breadcrumbs span {
        color: #6f8192;
        margin: 0 6px;
    }

    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 14px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 18px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
    }

    .admin-header h1 {
        color: #2f5f45;
        font-weight: 700;
        margin: 0;
        font-size: 2rem;
        line-height: 1;
    }

    .admin-header p {
        color: #6f8192;
        margin: 6px 0 0;
        font-size: 0.92rem;
    }

    .admin-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
        margin-bottom: 14px;
    }

    .stat-box {
        background: #ffffff;
        padding: 16px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stat-box i {
        font-size: 1.2rem;
        color: #6ea988;
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eef3ff;
        border: 1px solid #d8e4f7;
        border-radius: 8px;
        flex-shrink: 0;
    }

    .stat-box.success i {
        color: #1d7a3f;
        background: #e9f7ec;
        border-color: #cfead8;
    }

    .stat-box.warning i {
        color: #9d6108;
        background: #fff5e6;
        border-color: #f0d5ab;
    }

    .stat-box.danger i {
        color: #a43737;
        background: #fef0f0;
        border-color: #f3cccc;
    }

    .stat-info h5 {
        margin: 0;
        color: #7f90a0;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.35px;
    }

    .stat-info h3 {
        margin: 6px 0 0;
        color: #2f5f45;
        font-weight: 700;
        font-size: 1.55rem;
        line-height: 1.1;
    }

    .admin-panel {
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .panel-header {
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: #ffffff;
        padding: 14px 16px;
    }

    .panel-header h2 {
        margin: 0;
        font-size: 1.08rem;
        font-weight: 700;
    }

    .table-responsive {
        padding: 14px;
        overflow-x: auto;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 760px;
    }

    .admin-table th {
        background: #f7f9fc;
        color: #445b72;
        font-weight: 700;
        padding: 10px 12px;
        text-align: left;
        border-bottom: 1px solid #e1e9f2;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.35px;
        white-space: nowrap;
    }

    .admin-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #edf2f7;
        font-size: 0.86rem;
        color: #42586e;
        white-space: nowrap;
    }

    .admin-table tbody tr:hover {
        background: #fbfdff;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .badge-present {
        background: #e9f7ec;
        color: #1d7a3f;
        border-color: #cfead8;
    }

    .badge-absent {
        background: #fdecec;
        color: #b43a3a;
        border-color: #f4d3d3;
    }

    .badge-late {
        background: #fff4e4;
        color: #9f6310;
        border-color: #f1debb;
    }

    .empty-msg {
        text-align: center;
        padding: 40px 16px;
        color: #7f8c8d;
    }

    .empty-msg i {
        font-size: 2.4rem;
        opacity: 0.45;
        margin-bottom: 10px;
        color: #95a8bb;
    }

    .pagination-wrap {
        margin-top: 14px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .admin-header {
            padding: 14px;
        }

        .admin-header h1 {
            font-size: 1.55rem;
        }

        .table-responsive {
            padding: 12px;
        }
    }
</style>

<div class="hr-shell">

<?php
    $pageLogs = $logs ?? [];
    $totalLogs = count($pageLogs);
    $presentLogs = 0;
    $lateLogs = 0;
    $absentLogs = 0;

    foreach ($pageLogs as $logSummary) {
        $summaryStatus = strtolower((string) ($logSummary->status ?? 'absent'));
        if ($summaryStatus === 'present') {
            $presentLogs++;
        } elseif ($summaryStatus === 'late') {
            $lateLogs++;
        } else {
            $absentLogs++;
        }
    }
?>

<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
    <span>/</span>
    <span><i class="fas fa-fingerprint"></i> Biometric Logs</span>
</div>

<!-- Header -->
<div class="admin-header">
    <div>
        <h1><i class="fas fa-fingerprint"></i> Biometric Attendance Logs</h1>
        <p>Attendance records captured from the biometric device</p>
    </div>
</div>

<div class="admin-stats">
    <div class="stat-box">
        <i class="fas fa-table"></i>
        <div class="stat-info">
            <h5>Records on Page</h5>
            <h3><?= $totalLogs ?></h3>
        </div>
    </div>
    <div class="stat-box success">
        <i class="fas fa-check-circle"></i>
        <div class="stat-info">
            <h5>Present</h5>
            <h3><?= $presentLogs ?></h3>
        </div>
    </div>
    <div class="stat-box warning">
        <i class="fas fa-clock"></i>
        <div class="stat-info">
            <h5>Late</h5>
            <h3><?= $lateLogs ?></h3>
        </div>
    </div>
    <div class="stat-box danger">
        <i class="fas fa-user-times"></i>
        <div class="stat-info">
            <h5>Absent</h5>
            <h3><?= $absentLogs ?></h3>
        </div>
    </div>
</div>

<!-- Table Panel -->
<div class="admin-panel">
    <div class="panel-header">
        <h2><i class="fas fa-table"></i> Records (<?= $totalLogs ?>)</h2>
    </div>
    <div class="table-responsive">
        <?php if (!empty($logs)): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee Name</th>
                        <th>Employee ID</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $index => $log): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><strong><?= esc($log->name ?? 'N/A') ?></strong></td>
                            <td><?= esc($log->employee_id ?? 'N/A') ?></td>
                            <td><?= !empty($log->date) ? date('M d, Y', strtotime($log->date)) : 'N/A' ?></td>
                            <td class="<?= !empty($log->time_in) ? 'text-success' : 'text-muted' ?>">
                                <?= !empty($log->time_in) ? '<i class="fas fa-arrow-right"></i> ' . date('H:i', strtotime($log->time_in)) : '—' ?>
                            </td>
                            <td class="<?= !empty($log->time_out) ? 'text-danger' : 'text-muted' ?>">
                                <?= !empty($log->time_out) ? '<i class="fas fa-arrow-left"></i> ' . date('H:i', strtotime($log->time_out)) : '—' ?>
                            </td>
                            <td>
                                <?php 
                                    $status = $log->status ?? 'Absent';
                                    $badgeClass = match(strtolower($status)) {
                                        'present' => 'badge-present',
                                        'late' => 'badge-late',
                                        default => 'badge-absent'
                                    };
                                ?>
                                <span class="status-badge <?= $badgeClass ?>"><?= esc(ucfirst($status)) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-msg">
                <i class="fas fa-inbox" style="font-size: 2.5rem; opacity: 0.4; margin-bottom: 10px;"></i>
                <p><strong>No attendance records found</strong></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pagination -->
<?php if (!empty($logs) && isset($pager)): ?>
    <div class="pagination-wrap">
        <?= $pager->links('default_full', 'default_full') ?>
    </div>
<?php endif; ?>

</div>

<?= $this->endSection() ?>
