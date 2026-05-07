<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .salary-display-shell {
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

    .page-header {
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
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        padding: 14px;
        margin-bottom: 14px;
    }

    .report-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
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
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
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

    .table-period {
        font-size: 0.84rem;
        opacity: 0.92;
        font-weight: 600;
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

    .salary-amount {
        font-weight: 700;
        color: #2f5f45;
        font-size: 1.1rem;
    }

    .export-buttons {
        display: flex;
        gap: 8px;
        margin-top: 14px;
        flex-wrap: wrap;
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

        .export-buttons {
            flex-direction: column;
        }

        .btn-export {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="salary-display-shell">

<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
    <span>/</span>
    <a href="<?= base_url('reports') ?>"><i class="fas fa-chart-bar"></i> Reports</a>
    <span>/</span>
    <span>Salary Report</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="fas fa-dollar-sign"></i> Salary Report</h1>
        <p>Employee salary and payroll information</p>
    </div>
</div>

<!-- Report Info -->
<div class="report-info">
    <div class="report-summary">
        <div class="summary-item">
            <div class="summary-label">Report Period</div>
            <div class="summary-value"><?= date('M Y', strtotime($reportData['period'] ?? 'now')) ?></div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Employees</div>
            <div class="summary-value"><?= count($reportData['data'] ?? []) ?></div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Generated On</div>
            <div class="summary-value" style="font-size: 1.2rem;"><?= date('M d', strtotime($reportData['generated_at'] ?? 'now')) ?></div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Status</div>
            <div class="summary-value" style="color: #27ae60; font-size: 1.4rem;">$</div>
        </div>
    </div>
</div>

<!-- Report Table -->
<div class="report-table-container" data-print-root>
    <div class="table-header">
        <h3><i class="fas fa-table"></i> Salary Records</h3>
        <div class="table-period"><?= $reportData['period'] ?? date('Y-m') ?></div>
    </div>
    
    <?php if (!empty($reportData['data'])): ?>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Email</th>
                    <th>Rate</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData['data'] as $record): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['employee_id'] ?? 'N/A') ?></td>
                        <td>
                            <strong><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($record['department_name'] ?? 'Unassigned') ?></td>
                        <td><?= htmlspecialchars($record['user_email'] ?? $record['email'] ?? 'N/A') ?></td>
                        <td>
                            <span class="salary-amount">
                                $<?= number_format($record['rate'] ?? 0, 2) ?>/<?= htmlspecialchars($record['rate_type'] ?? 'hour') ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge badge-<?= strtolower($record['account_status'] ?? 'pending') ?>">
                                <?= htmlspecialchars(ucfirst($record['account_status'] ?? 'Pending')) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p><strong>No salary records found</strong></p>
            <p class="empty-note">There are no salary records for the selected period.</p>
        </div>
    <?php endif; ?>
    
    <!-- Print Only -->
    <div style="padding:14px;">
        <?= view('reports/_print_helpers') ?>
    </div>
</div>

</div>

<?= $this->endSection() ?>
