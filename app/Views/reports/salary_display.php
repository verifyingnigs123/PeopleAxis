<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .salary-report-shell {
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
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .panel-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.08rem;
    }

    .export-button {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.84rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .export-button:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        text-decoration: none;
        color: #ffffff;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table-legend {
        padding: 6px 14px;
        background: #f8f9fa;
        border-bottom: 1px solid #e8eef5;
        font-size: 0.72rem;
        color: #7f8c8d;
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .salary-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.78rem;
    }

    .salary-table thead th {
        background: #f7f9fc;
        color: #445b72;
        font-weight: 700;
        padding: 8px 10px;
        text-align: left;
        border-bottom: 1px solid #e1e9f2;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .salary-table thead th.th-deduct {
        background: #fff3f3;
        color: #a93226;
        border-left: 2px solid #f1c7cb;
    }

    .salary-table tbody td.td-deduct {
        color: #c0392b;
        font-size: 0.76rem;
        border-left: 2px solid #fce8e8;
    }

    .salary-table tbody td {
        padding: 7px 10px;
        border-bottom: 1px solid #edf2f7;
        font-size: 0.78rem;
        color: #495057;
        white-space: nowrap;
    }

    .salary-table tbody tr:hover {
        background: #fbfdff;
    }

    .currency {
        color: #27ae60;
        font-weight: 700;
    }

    .empty-state {
        text-align: center;
        padding: 54px 20px;
        color: #95a5a6;
    }

    @media (max-width: 768px) {
        .admin-header {
            padding: 14px;
        }

        .admin-header h1 {
            font-size: 1.55rem;
        }

        .panel-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .panel-header h2 {
            order: 1;
        }

        .export-button {
            order: 2;
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="salary-report-shell">

<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
    <span>/</span>
    <a href="<?= base_url('reports') ?>"><i class="fas fa-chart-bar"></i> Reports</a>
    <span>/</span>
    <span>Salary Report</span>
</div>

<!-- Page Header -->
<div class="admin-header">
    <div>
        <h1><i class="fas fa-dollar-sign"></i> Salary Report</h1>
        <p>Employee salary and payroll information with deductions</p>
    </div>
</div>

<!-- Stats -->
<?php
    $totalEmp = count($reportData['data'] ?? []);
    $withSalary = count(array_filter($reportData['data'] ?? [], fn($emp) => !empty($emp->salary_id)));
    $withoutSalary = max($totalEmp - $withSalary, 0);
    $coverageRate = $totalEmp > 0 ? round(($withSalary / $totalEmp) * 100) . '%' : '0%';
?>

<div class="admin-stats">
    <div class="stat-box">
        <i class="fas fa-users"></i>
        <div class="stat-info">
            <h5>Total Employees</h5>
            <h3><?= $totalEmp ?></h3>
        </div>
    </div>
    <div class="stat-box success">
        <i class="fas fa-file-invoice-dollar"></i>
        <div class="stat-info">
            <h5>Rates Set</h5>
            <h3><?= $withSalary ?></h3>
        </div>
    </div>
    <div class="stat-box">
        <i class="fas fa-chart-pie"></i>
        <div class="stat-info">
            <h5>Report Period</h5>
            <h3><?= date('M Y', strtotime($reportData['period'] ?? 'now')) ?></h3>
        </div>
    </div>
    <div class="stat-box">
        <i class="fas fa-calendar"></i>
        <div class="stat-info">
            <h5>Generated On</h5>
            <h3><?= date('M d', strtotime($reportData['generated_at'] ?? 'now')) ?></h3>
        </div>
    </div>
</div>

<!-- Salary Table -->
<div class="admin-panel">
    <div class="panel-header">
        <h2 style="margin:0;"><i class="fas fa-list"></i> Employee Salary Records (<?= count($reportData['data'] ?? []) ?>)</h2>
        <a href="<?= base_url('reports/export/salary-excel') ?>" class="export-button">
            <i class="fas fa-file-excel"></i> Download Excel
        </a>
    </div>

    <div class="table-responsive">
        <!-- Column legend -->
        <div class="table-legend">
            <span><strong>W. Tax</strong> = Withholding Tax</span>
            <span><strong>Extra Ded.</strong> = Custom/Other Deductions</span>
            <span style="color:#a93226;"><i class="fas fa-square" style="font-size:.55rem;"></i> Red columns = Statutory deductions (auto-computed)</span>
        </div>
        
        <?php if (!empty($reportData['data'])): ?>
            <table class="salary-table" id="salaryTable">
                <thead>
                    <tr>
                        <th>Emp. / Bio ID</th>
                        <th>Employee</th>
                        <th>Dept.</th>
                        <th>Position</th>
                        <th>Rate</th>
                        <th>Type</th>
                        <th>Base Salary</th>
                        <th>Allowances</th>
                        <th class="th-deduct">SSS</th>
                        <th class="th-deduct">PhilHealth</th>
                        <th class="th-deduct">Pag-IBIG</th>
                        <th class="th-deduct">W. Tax</th>
                        <th class="th-deduct">Extra Ded.</th>
                        <th>Gross</th>
                        <th>Net Pay</th>
                        <th>Effective</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reportData['data'] as $i => $emp):
                        $hasSalary   = !empty($emp->salary_id);
                        $empRate     = (float)($emp->rate ?? 0);
                        $empRateType = $emp->rate_type ?? 'monthly';
                        $rtLabels    = ['hourly' => 'Hourly', 'daily' => 'Daily', 'monthly' => 'Monthly'];
                        $rtColors    = ['hourly' => '#8e44ad', 'daily' => '#2980b9', 'monthly' => '#27ae60'];
                        $base        = (float)($emp->base_salary      ?? 0);
                        $allowances  = (float)($emp->allowances        ?? 0);
                        $sss         = (float)($emp->sss_contribution        ?? 0);
                        $philhealth  = (float)($emp->philhealth_contribution ?? 0);
                        $pagibig     = (float)($emp->pagibig_contribution    ?? 0);
                        $wTax        = (float)($emp->withholding_tax         ?? 0);
                        $extraDed    = (float)($emp->deductions        ?? 0);
                        $gross       = $base + $allowances;
                        $net         = (float)($emp->net_salary ?? ($gross - $sss - $philhealth - $pagibig - $wTax - $extraDed));
                    ?>
                        <tr class="salary-row">
                            <td>
                                <span style="font-family:monospace;font-weight:700;color:#2f5f45;font-size:.88rem;"><?= esc($emp->emp_code) ?></span>
                            </td>
                            <td>
                                <strong><?= esc($emp->employee_name) ?></strong>
                            </td>
                            <td><?= esc($emp->department_name ?? '—') ?></td>
                            <td><?= esc($emp->role_name  ?? '—') ?></td>

                            <!-- Salary Rate -->
                            <td>
                                <?php if ($empRate > 0): ?>
                                    <span style="color:#27ae60;font-weight:700;">&#8369;<?= number_format($empRate, 2) ?></span>
                                <?php else: ?>
                                    <span style="color:#bdc3c7;font-style:italic;font-size:.82rem;">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- Rate Type badge -->
                            <td>
                                <?php if ($empRate > 0): ?>
                                    <span style="display:inline-block;padding:2px 10px;border-radius:12px;font-size:.75rem;font-weight:700;
                                                 background:<?= $rtColors[$empRateType] ?? '#7f8c8d' ?>22;
                                                 color:<?= $rtColors[$empRateType] ?? '#7f8c8d' ?>;border:1px solid <?= $rtColors[$empRateType] ?? '#7f8c8d' ?>55;">
                                        <?= $rtLabels[$empRateType] ?? ucfirst($empRateType) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color:#bdc3c7;font-style:italic;font-size:.82rem;">—</span>
                                <?php endif; ?>
                            </td>

                            <?php if ($hasSalary): ?>
                                <td class="currency">&#8369;<?= number_format($base, 2) ?></td>
                                <td style="color:#2980b9;font-weight:600;">&#8369;<?= number_format($allowances, 2) ?></td>
                                <td class="td-deduct">&#8369;<?= number_format($sss, 2) ?></td>
                                <td class="td-deduct">&#8369;<?= number_format($philhealth, 2) ?></td>
                                <td class="td-deduct">&#8369;<?= number_format($pagibig, 2) ?></td>
                                <td class="td-deduct">&#8369;<?= number_format($wTax, 2) ?></td>
                                <td class="td-deduct">&#8369;<?= number_format($extraDed, 2) ?></td>
                                <td style="color:#8e44ad;font-weight:700;">&#8369;<?= number_format($gross, 2) ?></td>
                                <td style="color:#2f5f45;font-weight:800;">&#8369;<?= number_format($net, 2) ?></td>
                                <td style="font-size:.75rem;color:#7f8c8d;">
                                    <?= !empty($emp->effective_from) ? date('M d, Y', strtotime($emp->effective_from)) : '—' ?>
                                </td>
                            <?php else: ?>
                                <td colspan="10">
                                    <span style="color:#bdc3c7;font-style:italic;font-size:.78rem;">No rate set yet</span>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users" style="font-size:2.5rem;opacity:.3;"></i>
                <p style="margin-top:12px;">No active employees found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</div>

<?= $this->endSection() ?>
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

