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

    .salary-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.78rem;
    }

    .salary-table thead th {
        background: #f0f3f8;
        color: #1e3c72;
        font-weight: 700;
        padding: 7px 9px;
        text-align: left;
        border-bottom: 2px solid #dce3f0;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: .3px;
        white-space: nowrap;
    }

    .salary-table thead th.th-deduct {
        background: #fff0f0;
        color: #a93226;
        border-left: 2px solid #f5c6ca;
    }

    .salary-table tbody td.td-deduct {
        color: #c0392b;
        font-size: .76rem;
        border-left: 2px solid #fce8e8;
    }

    .salary-table tbody td {
        padding: 6px 9px;
        border-bottom: 1px solid #f1f3f5;
        font-size: 0.78rem;
        color: #495057;
        white-space: nowrap;
    }

    .salary-table tbody tr:hover {
        background: #f8f9ff;
    }

    .salary-input {
        padding: 6px 8px;
        border: 1px solid #e1e8ed;
        border-radius: 4px;
        font-size: 0.85rem;
        width: 100%;
        transition: all 0.3s ease;
    }

    .salary-input:focus {
        outline: none;
        border-color: #2a5298;
        box-shadow: 0 0 0 2px rgba(42, 82, 152, 0.1);
    }

    .currency {
        color: #27ae60;
        font-weight: 700;
    }

    .action-buttons {
        display: flex;
        gap: 6px;
    }

    .btn-save {
        padding: 6px 12px;
        background: #27ae60;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        background: #229954;
    }

    .btn-cancel {
        padding: 6px 12px;
        background: #e1e8ed;
        color: #2c3e50;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: #d5dce3;
    }

    .btn-edit {
        padding: 6px 12px;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-edit:hover {
        background: #2980b9;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #95a5a6;
    }

    .alert {
        border-radius: 6px;
        padding: 15px 20px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
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
        color: #2a5298;
        text-decoration: none;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .pagination a:hover {
        background: #2a5298;
        color: white;
        border-color: #2a5298;
    }

    .pagination .active {
        background: #2a5298;
        color: white;
        border-color: #2a5298;
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Salary Management</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="fas fa-money-bill-wave"></i> Salary Management</h1>
        <p>Manage employee salaries and compensation</p>
    </div>
</div>

<!-- Toast notification -->
<div id="salaryToast" style="display:none;position:fixed;top:24px;right:24px;z-index:9999;min-width:280px;
     background:#fff;border-radius:8px;box-shadow:0 4px 18px rgba(0,0,0,.18);padding:16px 22px;
     border-left:5px solid #27ae60;font-size:.9rem;color:#155724;">
    <i class="fas fa-check-circle"></i> <span id="salaryToastMsg"></span>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<!-- Summary Cards (Super Admin only) -->
<?php if (!empty($isAdmin)): ?>
<?php
    $totalEmp     = count($employees ?? []);
    $withSalary   = count(array_filter($employees ?? [], fn($e) => !empty($e->salary_id)));
    $withoutSalary = $totalEmp - $withSalary;
?>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
    <div style="background:#fff;border-radius:8px;padding:20px 22px;box-shadow:0 2px 8px rgba(0,0,0,.07);border-top:4px solid #1e3c72;">
        <div style="font-size:1.9rem;font-weight:800;color:#1e3c72;"><?= $totalEmp ?></div>
        <div style="font-size:.82rem;color:#7f8c8d;margin-top:4px;">Active Employees</div>
    </div>
    <div style="background:#fff;border-radius:8px;padding:20px 22px;box-shadow:0 2px 8px rgba(0,0,0,.07);border-top:4px solid #27ae60;">
        <div style="font-size:1.9rem;font-weight:800;color:#27ae60;"><?= $withSalary ?></div>
        <div style="font-size:.82rem;color:#7f8c8d;margin-top:4px;">With Salary Rate Set</div>
    </div>
    <div style="background:#fff;border-radius:8px;padding:20px 22px;box-shadow:0 2px 8px rgba(0,0,0,.07);border-top:4px solid #e74c3c;">
        <div style="font-size:1.9rem;font-weight:800;color:#e74c3c;"><?= $withoutSalary ?></div>
        <div style="font-size:.82rem;color:#7f8c8d;margin-top:4px;">No Rate Set Yet</div>
    </div>
</div>
<?php endif; ?>

<!-- Salary Table -->
<div class="admin-panel">
    <div class="panel-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
        <div>
            <h2 style="margin:0;"><i class="fas fa-list"></i> Employee Salary Rates (<?= count($employees ?? []) ?>)</h2>
            <?php if (empty($isAdmin)): ?>
            <p style="margin:6px 0 0;font-size:0.85rem;opacity:0.85;"><i class="fas fa-info-circle"></i> You have view-only access. Contact a Super Admin to modify salary records.</p>
            <?php endif; ?>
        </div>
        <!-- Search -->
        <div>
            <input type="text" id="salarySearch" placeholder="&#xf002; Search employee..." oninput="filterTable(this.value)"
                   style="padding:8px 14px;border:1px solid rgba(255,255,255,.4);border-radius:6px;background:rgba(255,255,255,.15);
                          color:#fff;font-size:.85rem;width:220px;outline:none;"
                   onfocus="this.style.background='rgba(255,255,255,.25)'" onblur="this.style.background='rgba(255,255,255,.15)'">
        </div>
    </div>

    <div class="table-responsive">
        <!-- Column legend -->
        <div style="padding:6px 14px;background:#f8f9fa;border-bottom:1px solid #e8eef5;font-size:.72rem;color:#7f8c8d;display:flex;gap:16px;flex-wrap:wrap;">
            <span><strong>W. Tax</strong> = Withholding Tax</span>
            <span><strong>Extra Ded.</strong> = Custom/Other Deductions</span>
            <span style="color:#a93226;"><i class="fas fa-square" style="font-size:.55rem;"></i> Red columns = Statutory deductions (auto-computed)</span>
        </div>
        <?php if (!empty($employees)): ?>
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
                        <?php if (!empty($isAdmin)): ?><th>Act.</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $i => $emp):
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
                                <span style="font-family:monospace;font-weight:700;color:#1e3c72;font-size:.88rem;"><?= esc($emp->emp_code) ?></span>
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
                                <td style="color:#1e3c72;font-weight:800;">&#8369;<?= number_format($net, 2) ?></td>
                                <td style="font-size:.75rem;color:#7f8c8d;">
                                    <?= !empty($emp->effective_from) ? date('M d, Y', strtotime($emp->effective_from)) : '—' ?>
                                </td>
                            <?php else: ?>
                                <td colspan="10">
                                    <span style="color:#bdc3c7;font-style:italic;font-size:.78rem;">No rate set yet</span>
                                </td>
                            <?php endif; ?>

                            <?php if (!empty($isAdmin)):
                                $btnSalaryJson = $hasSalary ? esc(json_encode([
                                    'base_salary'    => $base,
                                    'allowances'     => $allowances,
                                    'deductions'     => $extraDed,
                                    'effective_from' => $emp->effective_from ?? '',
                                ])) : '';
                            ?>
                            <td>
                                <button class="<?= $hasSalary ? 'btn-edit' : 'btn-set' ?>"
                                        style="padding:4px 9px;font-size:.75rem;"
                                        title="<?= $hasSalary ? 'Edit Salary' : 'Set Rate' ?>"
                                        data-eid="<?= $emp->employee_pk ?>"
                                        data-name="<?= esc($emp->employee_name) ?>"
                                        data-has="<?= $hasSalary ? '1' : '0' ?>"
                                        data-salary="<?= $btnSalaryJson ?>"
                                        onclick="openSalaryModal(this)">
                                    <i class="fas <?= $hasSalary ? 'fa-edit' : 'fa-plus' ?>"></i>
                                </button>
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

<style>
    .btn-set {
        padding: 6px 12px;
        background: #27ae60;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-set:hover { background: #229954; }

    /* Modal overlay */
    .salary-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 1200;
        background: rgba(0,0,0,.55);
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .salary-modal-overlay.open { display: flex; }
    .salary-modal-box {
        background: #fff;
        border-radius: 12px;
        width: 100%;
        max-width: 560px;
        max-height: 92vh;
        overflow-y: auto;
        box-shadow: 0 10px 40px rgba(0,0,0,.28);
        animation: smSlide .22s ease;
    }
    @keyframes smSlide { from{transform:translateY(-24px);opacity:0} to{transform:translateY(0);opacity:1} }
    .salary-modal-head {
        background: linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);
        color: #fff;
        padding: 18px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 1;
    }
    .salary-modal-head h5 { margin:0; font-size:1.05rem; font-weight:700; }
    .salary-modal-head small { opacity:.75; font-size:.78rem; display:block; margin-top:2px; font-weight:400; }
    .salary-modal-close {
        background: none; border: none; color: #fff;
        font-size: 1.6rem; cursor: pointer; line-height: 1;
        width:32px; height:32px; border-radius:50%;
        display:flex; align-items:center; justify-content:center;
        transition: background .2s;
    }
    .salary-modal-close:hover { background: rgba(255,255,255,.2); }
    .salary-modal-body  { padding: 22px 26px; }
    .salary-modal-footer {
        padding: 14px 26px 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 1px solid #eef0f5;
        position: sticky;
        bottom: 0;
        background: #fff;
    }
    .sm-form-group { margin-bottom: 14px; }
    .sm-label { display:block; font-weight:600; color:#2c3e50; font-size:.83rem; margin-bottom:5px; }
    .sm-input {
        width:100%; padding:8px 11px;
        border:1.5px solid #d5dce0; border-radius:6px;
        font-size:.88rem; box-sizing:border-box;
        transition: border-color .25s;
    }
    .sm-input:focus { outline:none; border-color:#2a5298; box-shadow:0 0 0 3px rgba(42,82,152,.1); }
    .sm-section-head {
        font-size:.72rem; font-weight:700; letter-spacing:.5px;
        text-transform:uppercase; padding:6px 10px;
        border-radius:5px; margin: 16px 0 8px;
    }
    .sm-section-head.earn { background:#f0faf4; color:#155724; }
    .sm-section-head.ded  { background:#fff0f0; color:#a93226; }
    .sm-row {
        display:flex; justify-content:space-between; align-items:center;
        padding:6px 10px; font-size:.84rem;
        border-radius:4px;
    }
    .sm-row.subtotal { background:#f8f9fa; font-weight:700; margin-top:4px; }
    .sm-row.earn-val  { color:#27ae60; }
    .sm-row.ded-val   { color:#c0392b; }
    .sm-row.blue-val  { color:#2980b9; }
    .sm-alert { display:none; padding:10px 14px; border-radius:6px;
                background:#fde8e8; color:#c0392b; font-size:.84rem; margin-bottom:14px; }
    .net-banner {
        background: linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);
        border-radius:8px; padding:14px 18px; margin-top:14px;
        display:flex; justify-content:space-between; align-items:center;
    }
    .net-banner .label { color:rgba(255,255,255,.75); font-size:.75rem; font-weight:600; letter-spacing:.4px; text-transform:uppercase; }
    .net-banner .amount { color:#fff; font-size:1.55rem; font-weight:800; }
    .semi-grid {
        display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:14px;
    }
    .semi-card {
        border-radius:8px; padding:12px 14px;
        border: 1.5px solid;
    }
    .semi-card.first  { background:#f0faf4; border-color:#a9dfbf; }
    .semi-card.second { background:#fdf0f0; border-color:#f5b7b1; }
    .semi-card .sc-title { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; margin-bottom:8px; }
    .semi-card.first  .sc-title { color:#1e8449; }
    .semi-card.second .sc-title { color:#a93226; }
    .semi-card .sc-row { display:flex; justify-content:space-between; font-size:.78rem; padding:2px 0; color:#555; }
    .semi-card .sc-net { display:flex; justify-content:space-between; font-size:.92rem; font-weight:800; border-top:1.5px solid; margin-top:6px; padding-top:6px; }
    .semi-card.first  .sc-net { border-color:#a9dfbf; color:#1e8449; }
    .semi-card.second .sc-net { border-color:#f5b7b1; color:#a93226; }
</style>

<!-- Salary Edit Modal -->
<div class="salary-modal-overlay" id="salaryModal">
    <div class="salary-modal-box">
        <div class="salary-modal-head">
            <div>
                <h5 id="modalTitle"><i class="fas fa-file-invoice-dollar" style="margin-right:8px;"></i> Salary Rate</h5>
                <small id="smEmployeeName"></small>
            </div>
            <button class="salary-modal-close" onclick="closeModal()">&times;</button>
        </div>

        <form id="salaryForm">
            <div class="salary-modal-body">
                <input type="hidden" id="sm_employee_id" name="employee_id">
                <?= csrf_field() ?>
                <div class="sm-alert" id="smAlert"></div>

                <!-- Inputs -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="sm-form-group">
                        <label class="sm-label">Base Salary (&#8369;) <span style="color:#e74c3c;">*</span></label>
                        <input type="number" class="sm-input" id="sm_base" name="base_salary"
                               min="0" step="0.01" placeholder="0.00" required oninput="calcNet()">
                    </div>
                    <div class="sm-form-group">
                        <label class="sm-label">Allowances (&#8369;)</label>
                        <input type="number" class="sm-input" id="sm_allow" name="allowances"
                               min="0" step="0.01" placeholder="0.00" value="0" oninput="calcNet()">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="sm-form-group">
                        <label class="sm-label">Extra Deductions (&#8369;)
                            <span style="font-weight:400;color:#999;">(non-statutory)</span></label>
                        <input type="number" class="sm-input" id="sm_deduct" name="deductions"
                               min="0" step="0.01" placeholder="0.00" value="0" oninput="calcNet()">
                    </div>
                    <div class="sm-form-group">
                        <label class="sm-label">Effective From <span style="color:#e74c3c;">*</span></label>
                        <input type="date" class="sm-input" id="sm_effective" name="effective_from" required>
                    </div>
                </div>

                <!-- Earnings section -->
                <div class="sm-section-head earn"><i class="fas fa-plus-circle"></i> Earnings</div>
                <div class="sm-row"><span style="color:#555;">Basic Salary</span>        <span class="earn-val" id="pr_base">&#8369;0.00</span></div>
                <div class="sm-row"><span style="color:#555;">Allowances</span>          <span class="blue-val" id="pr_allow">&#8369;0.00</span></div>
                <div class="sm-row subtotal"><span>Gross Pay</span>                      <span id="pr_gross">&#8369;0.00</span></div>

                <!-- Deductions section -->
                <div class="sm-section-head ded"><i class="fas fa-minus-circle"></i> Deductions <small style="font-weight:400;font-size:.68rem;">(auto-computed · 2025 rates)</small></div>
                <div class="sm-row"><span style="color:#555;">SSS <small style="color:#aaa;">(4.5% of MSC)</small></span>         <span class="ded-val" id="pr_sss">&#8369;0.00</span></div>
                <div class="sm-row"><span style="color:#555;">PhilHealth <small style="color:#aaa;">(2.5%)</small></span>          <span class="ded-val" id="pr_ph">&#8369;0.00</span></div>
                <div class="sm-row"><span style="color:#555;">Pag-IBIG / HDMF</span>                                               <span class="ded-val" id="pr_pi">&#8369;0.00</span></div>
                <div class="sm-row"><span style="color:#555;">Withholding Tax <small style="color:#aaa;">(BIR TRAIN)</small></span><span class="ded-val" id="pr_tax">&#8369;0.00</span></div>
                <div class="sm-row"><span style="color:#555;">Extra Deductions</span>                                              <span class="ded-val" id="pr_extra">&#8369;0.00</span></div>
                <div class="sm-row subtotal"><span>Total Deductions</span>               <span style="color:#a93226;" id="pr_totded">&#8369;0.00</span></div>

                <!-- Net Pay banner (Monthly) -->
                <div class="net-banner">
                    <div>
                        <div class="label">Monthly Net Pay (Take-Home)</div>
                        <div style="color:rgba(255,255,255,.55);font-size:.68rem;margin-top:2px;">Full month · all deductions applied</div>
                    </div>
                    <div class="amount" id="pr_net">&#8369;0.00</div>
                </div>

                <!-- Semi-Monthly (15-day) Breakdown -->
                <div class="sm-section-head" style="background:#f0f3ff;color:#1e3c72;margin-top:18px;">
                    <i class="fas fa-calendar-alt"></i> Semi-Monthly Pay <small style="font-weight:400;font-size:.68rem;">(PH standard · 15th &amp; end-of-month)</small>
                </div>
                <div class="semi-grid">
                    <div class="semi-card first">
                        <div class="sc-title"><i class="fas fa-1"></i> 1st Payroll &mdash; 15th</div>
                        <div class="sc-row"><span>Gross (half)</span>      <span id="sm1_gross">&#8369;0.00</span></div>
                        <div class="sc-row" style="color:#bbb;font-size:.72rem;"><span>No statutory deductions</span></div>
                        <div class="sc-net"><span>Take-Home</span>         <span id="sm1_net">&#8369;0.00</span></div>
                    </div>
                    <div class="semi-card second">
                        <div class="sc-title"><i class="fas fa-2"></i> 2nd Payroll &mdash; End of Month</div>
                        <div class="sc-row"><span>Gross (half)</span>      <span id="sm2_gross">&#8369;0.00</span></div>
                        <div class="sc-row"><span style="color:#c0392b;">All Deductions</span> <span id="sm2_ded" style="color:#c0392b;">&#8369;0.00</span></div>
                        <div class="sc-net"><span>Take-Home</span>         <span id="sm2_net">&#8369;0.00</span></div>
                    </div>
                </div>
                <div style="font-size:.68rem;color:#999;margin-top:6px;padding:0 2px;">
                    <i class="fas fa-info-circle"></i>
                    SSS, PhilHealth, Pag-IBIG &amp; Withholding Tax are deducted in full on the 2nd payroll (common PH practice).
                </div>
            </div>

            <div class="salary-modal-footer">
                <button type="button" onclick="closeModal()"
                        style="padding:8px 20px;border:1.5px solid #d5dce0;border-radius:6px;background:#fff;color:#2c3e50;font-weight:600;cursor:pointer;">
                    Cancel
                </button>
                <button type="submit" id="smSubmitBtn"
                        style="padding:8px 26px;border:none;border-radius:6px;background:linear-gradient(135deg,#1e3c72,#2a5298);color:#fff;font-weight:700;cursor:pointer;font-size:.9rem;">
                    <i class="fas fa-save" style="margin-right:6px;"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
<script>
const GET_URL    = '<?= base_url('employees/salary/get') ?>/';
const UPDATE_URL = '<?= base_url('employees/salary/update') ?>';
const CSRF_NAME  = '<?= csrf_token() ?>';
let   csrfHash   = '<?= csrf_hash() ?>';

// ── Philippine Statutory Deduction Formulas (mirrors PhDeductions.php) ──
function computeSSS(salary) {
    let msc;
    if (salary < 4250)        msc = 4000;
    else if (salary >= 34750) msc = 35000;
    else                      msc = Math.floor((salary - 3750) / 500) * 500 + 4000;
    return Math.round(msc * 0.045 * 100) / 100;
}
function computePhilHealth(salary) {
    let c = salary * 0.025;
    c = Math.max(250, Math.min(c, 2500));
    return Math.round(c * 100) / 100;
}
function computePagIbig(salary) {
    let rate = salary <= 1500 ? 0.01 : 0.02;
    return Math.round(Math.min(salary * rate, 200) * 100) / 100;
}
function computeWTax(salary, sss, ph, pi) {
    let taxable = salary - sss - ph - pi;
    if (taxable <= 0) return 0;
    let tax;
    if      (taxable <= 20833)  tax = 0;
    else if (taxable <= 33332)  tax = (taxable - 20833)   * 0.20;
    else if (taxable <= 66666)  tax = 2500   + (taxable - 33333)  * 0.25;
    else if (taxable <= 166666) tax = 10833  + (taxable - 66667)  * 0.30;
    else if (taxable <= 666666) tax = 40833  + (taxable - 166667) * 0.32;
    else                        tax = 200833 + (taxable - 666667) * 0.35;
    return Math.round(Math.max(tax, 0) * 100) / 100;
}
function peso(n) { return '\u20B1' + parseFloat(n).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2}); }

function calcNet() {
    const base     = parseFloat(document.getElementById('sm_base').value)   || 0;
    const allow    = parseFloat(document.getElementById('sm_allow').value)  || 0;
    const extraDed = parseFloat(document.getElementById('sm_deduct').value) || 0;

    const sss  = computeSSS(base);
    const ph   = computePhilHealth(base);
    const pi   = computePagIbig(base);
    const tax  = computeWTax(base, sss, ph, pi);
    const gross    = base + allow;
    const totalDed = sss + ph + pi + tax + extraDed;
    const net      = gross - totalDed;

    document.getElementById('pr_base').innerHTML   = peso(base);
    document.getElementById('pr_allow').innerHTML  = peso(allow);
    document.getElementById('pr_gross').innerHTML  = peso(gross);
    document.getElementById('pr_sss').innerHTML    = peso(sss);
    document.getElementById('pr_ph').innerHTML     = peso(ph);
    document.getElementById('pr_pi').innerHTML     = peso(pi);
    document.getElementById('pr_tax').innerHTML    = peso(tax);
    document.getElementById('pr_extra').innerHTML  = peso(extraDed);
    document.getElementById('pr_totded').innerHTML = peso(totalDed);
    document.getElementById('pr_net').innerHTML    = peso(net);

    // ── Semi-Monthly (15-day) breakdown ──
    // PH standard: 26 working days / 2 = 13 days per pay period
    // Statutory deductions are applied in FULL on the 2nd payroll (end of month)
    const halfGross = gross / 2;
    // 1st payroll (15th): half gross, zero deductions
    document.getElementById('sm1_gross').innerHTML  = peso(halfGross);
    document.getElementById('sm1_net').innerHTML    = peso(halfGross);
    // 2nd payroll (EOM): half gross minus ALL deductions
    const sm2Net = halfGross - totalDed;
    document.getElementById('sm2_gross').innerHTML  = peso(halfGross);
    document.getElementById('sm2_ded').innerHTML    = peso(totalDed);
    document.getElementById('sm2_net').innerHTML    = peso(sm2Net);
}

function openSalaryModal(btn) {
    const employeeId   = btn.dataset.eid;
    const employeeName = btn.dataset.name;
    const hasSalary    = btn.dataset.has === '1';
    const existingData = hasSalary && btn.dataset.salary ? JSON.parse(btn.dataset.salary) : null;

    document.getElementById('sm_employee_id').value        = employeeId;
    document.getElementById('smEmployeeName').textContent  = employeeName;
    document.getElementById('modalTitle').innerHTML =
        '<i class="fas fa-file-invoice-dollar" style="margin-right:8px;"></i>' +
        (hasSalary ? 'Edit Salary Rate' : 'Set Salary Rate');
    document.getElementById('smAlert').style.display = 'none';

    if (hasSalary && existingData) {
        // Fill directly from table data — instant, no AJAX
        document.getElementById('sm_base').value    = existingData.base_salary    || '';
        document.getElementById('sm_allow').value   = existingData.allowances     || 0;
        document.getElementById('sm_deduct').value  = existingData.deductions     || 0;
        document.getElementById('sm_effective').value = existingData.effective_from ||
            new Date().toISOString().split('T')[0];
        calcNet();
    } else {
        document.getElementById('sm_effective').value = new Date().toISOString().split('T')[0];
        resetForm();
    }

    document.getElementById('salaryModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('salaryModal').classList.remove('open');
    document.body.style.overflow = 'auto';
}

function resetForm() {
    document.getElementById('sm_base').value   = '';
    document.getElementById('sm_allow').value  = '0';
    document.getElementById('sm_deduct').value = '0';
    calcNet();
}

function filterTable(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#salaryTable .salary-row').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

document.getElementById('salaryModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

document.getElementById('salaryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('smSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:6px;"></i> Saving...';

    const fd = new FormData(this);
    fd.set(CSRF_NAME, csrfHash);

    fetch(UPDATE_URL, {
        method: 'POST',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => {
        if (res.csrf_hash) csrfHash = res.csrf_hash;
        if (res.success) {
            closeModal();
            showToast(res.message || 'Saved successfully.');
            setTimeout(() => location.reload(), 1200);
        } else {
            document.getElementById('smAlert').textContent = res.message || 'An error occurred.';
            document.getElementById('smAlert').style.display = 'block';
        }
    })
    .catch(() => {
        document.getElementById('smAlert').textContent = 'Network error. Please try again.';
        document.getElementById('smAlert').style.display = 'block';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save" style="margin-right:6px;"></i> Save';
    });
});

function showToast(msg) {
    const t = document.getElementById('salaryToast');
    document.getElementById('salaryToastMsg').textContent = msg;
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 3500);
}
</script>

<?= $this->endSection() ?>
