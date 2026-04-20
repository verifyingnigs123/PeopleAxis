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
        color: #64748b;
    }

    .toolbar {
        display: flex;
        gap: 12px;
        align-items: end;
        flex-wrap: wrap;
        background: white;
        padding: 16px 18px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .toolbar label {
        font-size: 0.82rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #6c757d;
        display: block;
        margin-bottom: 6px;
    }

    .toolbar select {
        min-width: 190px;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #d9e2ec;
    }

    .btn-primary-soft {
        background: linear-gradient(135deg, #6ea988 0%, #5b9474 100%);
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

    .stat-card.stat-total {
        border-left-color: #2563eb;
    }

    .stat-card.stat-total .stat-value {
        color: #2563eb;
    }

    .stat-card.stat-pending {
        border-left-color: #ea580c;
    }

    .stat-card.stat-pending .stat-value {
        color: #ea580c;
    }

    .stat-card.stat-awaiting-hr {
        border-left-color: #ca8a04;
    }

    .stat-card.stat-awaiting-hr .stat-value {
        color: #ca8a04;
    }

    .stat-card.stat-rejected {
        border-left-color: #b91c1c;
    }

    .stat-card.stat-rejected .stat-value {
        color: #b91c1c;
    }

    .stat-card.stat-ended {
        border-left-color: #111827;
    }

    .stat-card.stat-ended .stat-value {
        color: #111827;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 20px;
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
        background: linear-gradient(135deg, #6ea988 0%, #5b9474 100%);
        color: white;
        font-weight: 700;
    }

    .panel-body {
        padding: 18px 22px;
    }

    .metric-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #eef2f7;
    }

    .metric-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .metric-row:first-child {
        padding-top: 0;
    }

    .metric-label {
        color: #64748b;
        font-size: 0.9rem;
    }

    .metric-value {
        color: #2f5f45;
        font-weight: 700;
        text-align: right;
    }

    .alert {
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .leave-table {
        width: 100%;
        border-collapse: collapse;
    }

    .leave-table thead th {
        background: #f8fafc;
        color: #2f5f45;
        padding: 14px 18px;
        text-align: left;
        font-size: 0.82rem;
        text-transform: uppercase;
        font-weight: 700;
        border-bottom: 2px solid #e2e8f0;
    }

    .leave-table tbody td {
        padding: 14px 18px;
        border-bottom: 1px solid #eef2f7;
        color: #334155;
        vertical-align: top;
    }

    .leave-table tbody tr:hover {
        background: #eef7f1;
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

    .badge-pending {
        background: #fff4e4;
        color: #9f6310;
    }

    .badge-manager-approved {
        background: #e8f1fb;
        color: #1f5ea8;
    }

    .badge-approved {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-ended {
        background: #111827;
        color: #ffffff;
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
    <span>Leave Status</span>
</div>

<div class="page-header">
    <div>
        <h1><i class="fas fa-calendar-alt"></i> Leave Status Dashboard</h1>
        <p>Track your submitted leave requests and upcoming approved leave periods.</p>
    </div>

    <form action="<?= base_url('leaves') ?>" method="get" class="toolbar">
        <div>
            <label for="leave-status">Status</label>
            <select id="leave-status" name="status" onchange="this.form.submit()">
                <option value="" <?= ($statusFilter ?? '') === '' ? 'selected' : '' ?>>All Statuses</option>
                <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="manager_approved" <?= ($statusFilter ?? '') === 'manager_approved' ? 'selected' : '' ?>>Awaiting HR</option>
                <option value="approved" <?= ($statusFilter ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= ($statusFilter ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                <option value="ended" <?= ($statusFilter ?? '') === 'ended' ? 'selected' : '' ?>>Ended</option>
            </select>
        </div>
        <button type="submit" class="btn-primary-soft">
            <i class="fas fa-filter"></i> Apply
        </button>
        <?php if ($employee): ?>
            <a href="<?= base_url('leaves/create') ?>" class="btn-primary-soft">
                <i class="fas fa-plus"></i> New Request
            </a>
        <?php endif; ?>
    </form>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php
    $approvedCount = (int) ($leaveSummary['approved'] ?? 0);
    $endedCount = (int) ($leaveSummary['ended'] ?? 0);
    // Keep approved history visible even after requests move to ended.
    $approvedRecordedCount = $approvedCount + $endedCount;
?>

<div class="stats-grid">
    <div class="stat-card stat-total">
        <div class="stat-label">Total Requests</div>
        <div class="stat-value"><?= array_sum($leaveSummary ?? []) ?></div>
    </div>
    <div class="stat-card stat-pending">
        <div class="stat-label">Pending</div>
        <div class="stat-value"><?= $leaveSummary['pending'] ?? 0 ?></div>
    </div>
    <div class="stat-card stat-awaiting-hr">
        <div class="stat-label">Awaiting HR</div>
        <div class="stat-value"><?= $leaveSummary['manager_approved'] ?? 0 ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Approved</div>
        <div class="stat-value"><?= $approvedRecordedCount ?></div>
    </div>
    <div class="stat-card stat-rejected">
        <div class="stat-label">Rejected</div>
        <div class="stat-value"><?= $leaveSummary['rejected'] ?? 0 ?></div>
    </div>
    <div class="stat-card stat-ended">
        <div class="stat-label">Ended</div>
        <div class="stat-value"><?= $endedCount ?></div>
    </div>
</div>

<?php
    $hasLeaveSchedule = !empty($activeLeave) || !empty($nextLeave);
    $hasLeaveHistory = !empty($leaves);
?>

<?php if ($hasLeaveSchedule): ?>
<div class="dashboard-grid">
    <?php if ($hasLeaveSchedule): ?>
        <div class="panel">
            <div class="panel-header">
                <i class="fas fa-plane-departure"></i> Leave Schedule
            </div>
            <div class="panel-body">
                <?php if (!empty($activeLeave)): ?>
                <div class="metric-row">
                    <div class="metric-label">Currently On Leave</div>
                    <div class="metric-value"><?= esc($activeLeave->leave_type ?? 'Leave') ?></div>
                </div>
                <div class="metric-row">
                    <div class="metric-label">Period</div>
                    <div class="metric-value"><?= date('M d, Y', strtotime($activeLeave->start_date)) ?> to <?= date('M d, Y', strtotime($activeLeave->end_date)) ?></div>
                </div>
                <div style="margin-top: 14px;">
                    <form action="<?= base_url('leaves/emergency-back/' . (int) ($activeLeave->id ?? 0)) ?>" method="post" onsubmit="return confirm('Confirm emergency back? You will be marked as back to work immediately.');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-primary-soft" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">
                            <i class="fas fa-undo-alt"></i> Emergency Back
                        </button>
                    </form>
                </div>
            <?php elseif (!empty($nextLeave)): ?>
                <div class="metric-row">
                    <div class="metric-label">Next Approved Leave</div>
                    <div class="metric-value"><?= esc($nextLeave->leave_type ?? 'Leave') ?></div>
                </div>
                <div class="metric-row">
                    <div class="metric-label">Starts</div>
                    <div class="metric-value"><?= date('M d, Y', strtotime($nextLeave->start_date)) ?></div>
                </div>
                <div class="metric-row">
                    <div class="metric-label">Ends</div>
                    <div class="metric-value"><?= date('M d, Y', strtotime($nextLeave->end_date)) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($hasLeaveHistory): ?>
<div class="panel">
    <div class="panel-header">
        <i class="fas fa-list"></i> Leave Request History
    </div>

    <div class="table-responsive">
            <table class="leave-table">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>Period</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaves as $leave): ?>
                        <?php
                            $status = strtolower((string) ($leave->status ?? 'pending'));
                            $badgeClass = match ($status) {
                                'approved' => 'badge-approved',
                                'manager_approved' => 'badge-manager-approved',
                                'rejected' => 'badge-rejected',
                                'ended' => 'badge-ended',
                                default => 'badge-pending',
                            };
                            $days = $leave->number_of_days ?? null;
                            if ($days === null && !empty($leave->start_date) && !empty($leave->end_date)) {
                                $days = (int) ((strtotime($leave->end_date) - strtotime($leave->start_date)) / 86400) + 1;
                            }
                        ?>
                        <tr>
                            <td><?= esc($leave->leave_type ?? 'N/A') ?></td>
                            <td>
                                <strong><?= !empty($leave->start_date) ? date('M d, Y', strtotime($leave->start_date)) : 'N/A' ?></strong><br>
                                <span class="muted">to <?= !empty($leave->end_date) ? date('M d, Y', strtotime($leave->end_date)) : 'N/A' ?></span>
                            </td>
                            <td><?= $days !== null ? esc((string) $days) . ' day(s)' : 'N/A' ?></td>
                            <td>
                                <span class="badge <?= $badgeClass ?>"><?= esc(ucwords(str_replace('_', ' ', $leave->status ?? 'pending'))) ?></span>
                            </td>
                            <td><span class="muted"><?= esc($leave->reason ?? 'No reason provided') ?></span></td>
                            <td><?= !empty($leave->created_at) ? date('M d, Y', strtotime($leave->created_at)) : 'N/A' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
    </div>

    <?php if (isset($pager) && $pager): ?>
        <div class="pagination-wrap">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?= $this->endSection() ?>