<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<?php
    $rows = $reportData['data'] ?? [];
    $totalRecords = count($rows);
    $periodValue = $reportData['period'] ?? date('Y-m');
    $periodLabel = date('F Y', strtotime($periodValue . '-01'));
?>

<style>
    .attendance-display-shell {
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

    .page-header,
    .report-info,
    .report-table-container {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 14px;
        padding: 16px 18px;
    }

    .page-header h1 {
        color: #2f5f45;
        font-weight: 700;
        margin: 0;
        font-size: 2rem;
        line-height: 1;
    }

    .page-header p {
        color: #6f8192;
        margin: 6px 0 0;
        font-size: 0.92rem;
    }

    .report-info {
        padding: 14px;
        margin-bottom: 14px;
    }

    .report-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
    }

    .summary-item {
        background: #f8fbff;
        border: 1px solid #e2ebf4;
        padding: 14px;
        border-radius: 8px;
        text-align: center;
    }

    .summary-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2f5f45;
        margin: 4px 0;
        line-height: 1.1;
    }

    .summary-label {
        font-size: 0.78rem;
        color: #6f8192;
        text-transform: uppercase;
        letter-spacing: 0.35px;
        font-weight: 700;
    }

    .report-table-container {
        overflow: hidden;
    }

    .table-header {
        background: #6ea988;
        color: #ffffff;
        padding: 12px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .table-header h3 {
        margin: 0;
        font-weight: 700;
        font-size: 1.05rem;
    }

    .table-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .table-period {
        font-size: 0.84rem;
        opacity: 0.92;
        font-weight: 600;
    }

    .btn-export {
        padding: 8px 14px;
        border: 1px solid #c9d8ef;
        background: #f1f5fb;
        color: #6ea988;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 700;
        transition: background 0.2s ease, border-color 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.84rem;
        text-decoration: none;
    }

    .btn-export:hover {
        background: #e7effa;
        border-color: #bdd2ee;
        text-decoration: none;
    }

    .btn-export.excel {
        color: #1d7a3f;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.86rem;
    }

    .report-table thead th {
        background: #f7f9fc;
        color: #445b72;
        font-weight: 700;
        padding: 12px 14px;
        text-align: left;
        border-bottom: 1px solid #e1e9f2;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.35px;
        white-space: nowrap;
    }

    .report-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #edf2f7;
        color: #42586e;
    }

    .report-table tbody tr:hover {
        background: #fbfdff;
    }

    .employee-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .employee-name {
        font-weight: 700;
        color: #2f5f45;
    }

    .employee-code {
        font-size: 0.78rem;
        color: #6f8192;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 700;
        border: 1px solid transparent;
        text-transform: capitalize;
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

    .badge-leave {
        background: #e8f4fd;
        color: #1e6fa5;
        border-color: #c7dff1;
    }

    .empty-state {
        text-align: center;
        padding: 40px 14px;
        color: #7f90a0;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 14px;
        opacity: 0.45;
        color: #8fa3b8;
    }

    .empty-note {
        font-size: 0.92rem;
        margin-top: 8px;
    }

    .report-footer {
        padding: 14px;
    }

    @media (max-width: 768px) {
        .report-summary {
            grid-template-columns: repeat(2, 1fr);
        }

        .page-header {
            padding: 14px;
        }

        .page-header h1 {
            font-size: 1.6rem;
        }

        .report-table {
            font-size: 0.78rem;
        }

        .report-table thead th,
        .report-table tbody td {
            padding: 8px 10px;
        }
    }

    @media (max-width: 480px) {
        .report-summary {
            grid-template-columns: 1fr;
        }

        .table-actions {
            width: 100%;
        }

        .btn-export {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="attendance-display-shell">
    <div class="breadcrumbs">
        <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
        <span>/</span>
        <a href="<?= base_url('reports') ?>"><i class="fas fa-chart-bar"></i> Reports</a>
        <span>/</span>
        <span>Attendance Report</span>
    </div>

    <div class="page-header">
        <div>
            <h1><i class="fas fa-chart-line"></i> Attendance Report</h1>
            <p>Detailed attendance records for your organization</p>
        </div>
    </div>

    <div class="report-info">
        <div class="report-summary">
            <div class="summary-item">
                <div class="summary-label">Report Period</div>
                <div class="summary-value"><?= esc($periodLabel) ?></div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Records</div>
                <div class="summary-value"><?= $totalRecords ?></div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Generated On</div>
                <div class="summary-value" style="font-size: 1.2rem;"><?= date('M d', strtotime($reportData['generated_at'] ?? 'now')) ?></div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Status</div>
                <div class="summary-value" style="color: #27ae60; font-size: 1.4rem;">✓</div>
            </div>
        </div>
    </div>

    <div class="report-table-container" data-print-root>
        <div class="table-header">
            <h3><i class="fas fa-table"></i> Attendance Records</h3>
            <div class="table-actions">
                <div class="table-period"><?= esc($reportData['period'] ?? date('Y-m')) ?></div>
                <a href="<?= base_url('reports/export/attendance-excel') ?>" class="btn-export excel">
                    <i class="fas fa-file-excel"></i> Download Excel
                </a>
            </div>
        </div>

        <?php if (!empty($rows)): ?>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Employee ID</th>
                        <th>RFID Number</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Break Out</th>
                        <th>Break In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $index => $record): ?>
                        <?php
                            $status = strtolower((string) ($record['status'] ?? 'pending'));
                            $employeeName = trim((string) (($record['first_name'] ?? '') . ' ' . ($record['last_name'] ?? '')));
                        ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <div class="employee-cell">
                                    <span class="employee-name"><?= esc($employeeName) ?></span>
                                </div>
                            </td>
                            <td><?= esc($record['emp_code'] ?? 'N/A') ?></td>
                            <td><?= esc($record['rfid_number'] ?? 'N/A') ?></td>
                            <td><?= esc($record['department_name'] ?? 'Unassigned') ?></td>
                            <td><?= !empty($record['date']) ? date('M d, Y', strtotime($record['date'])) : '-' ?></td>
                            <td><?= !empty($record['time_in']) ? date('h:i A', strtotime($record['time_in'])) : '-' ?></td>
                            <td><?= !empty($record['break_out']) ? date('h:i A', strtotime($record['break_out'])) : '-' ?></td>
                            <td><?= !empty($record['break_in']) ? date('h:i A', strtotime($record['break_in'])) : '-' ?></td>
                            <td><?= !empty($record['time_out']) ? date('h:i A', strtotime($record['time_out'])) : '-' ?></td>
                            <td>
                                <span class="status-badge badge-<?= esc($status) ?>">
                                    <?= esc(ucfirst($record['status'] ?? 'Pending')) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p><strong>No attendance records found</strong></p>
                <p class="empty-note">There are no attendance records for the selected period.</p>
            </div>
        <?php endif; ?>

        <div class="report-footer">
            <?= view('reports/_print_helpers') ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
