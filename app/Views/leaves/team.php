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
        color: #1f2937;
    }

    .stat-pending {
        border-left-color: #d4a017;
    }

    .stat-pending .stat-value {
        color: #a16207;
    }

    .stat-awaiting {
        border-left-color: #f97316;
    }

    .stat-awaiting .stat-value {
        color: #c2410c;
    }

    .stat-approved {
        border-left-color: #16a34a;
    }

    .stat-approved .stat-value {
        color: #166534;
    }

    .stat-rejected {
        border-left-color: #dc2626;
    }

    .stat-rejected .stat-value {
        color: #b91c1c;
    }

    .stat-ended {
        border-left-color: #111827;
    }

    .stat-ended .stat-value {
        color: #111827;
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
    }

    .panel-header h2 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
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

    .leaves-table {
        width: 100%;
        border-collapse: collapse;
    }

    .leaves-table thead th {
        background: #f8fafc;
        color: #2f5f45;
        padding: 14px 18px;
        text-align: left;
        font-size: 0.82rem;
        text-transform: uppercase;
        font-weight: 700;
        border-bottom: 2px solid #e2e8f0;
    }

    .leaves-table tbody td {
        padding: 14px 18px;
        border-bottom: 1px solid #eef2f7;
        color: #334155;
        vertical-align: top;
    }

    .leaves-table tbody tr:hover {
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

    .action-stack {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-action {
        border: none;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-approve {
        background: #15803d;
        color: white;
    }

    .btn-reject {
        background: #b91c1c;
        color: white;
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
</style>

<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Team Leave Requests</span>
</div>

<div class="page-header">
    <div>
        <h1><i class="fas fa-calendar-check"></i> Team Leave Dashboard</h1>
        <p>Review leave requests submitted by employees in your managed departments.</p>
    </div>

    <form action="<?= base_url('leaves/team') ?>" method="get" class="toolbar">
        <div>
            <label for="leave-status">Status</label>
            <select id="leave-status" name="status">
                <option value="" <?= $statusFilter === '' ? 'selected' : '' ?>>All Statuses</option>
                <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="manager_approved" <?= $statusFilter === 'manager_approved' ? 'selected' : '' ?>>Awaiting HR</option>
                <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                <option value="ended" <?= $statusFilter === 'ended' ? 'selected' : '' ?>>Ended</option>
            </select>
        </div>
        <button type="submit" class="btn-primary-soft">
            <i class="fas fa-filter"></i> Apply
        </button>
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

<?php if (empty($managedDepartments)): ?>
    <div class="panel">
        <div class="empty-state">
            <i class="fas fa-building" style="font-size: 2.8rem;"></i>
            <p>No departments are assigned to your manager account yet.</p>
        </div>
    </div>
<?php else: ?>
    <?php
        $approvedCount = (int) ($leaveSummary['approved'] ?? 0);
        $endedCount = (int) ($leaveSummary['ended'] ?? 0);
        // Keep approved history visible even after requests move to ended.
        $approvedRecordedCount = $approvedCount + $endedCount;
    ?>
    <div class="stats-grid">
        <div class="stat-card stat-pending">
            <div class="stat-label">Pending Review</div>
            <div class="stat-value"><?= $leaveSummary['pending'] ?? 0 ?></div>
        </div>
        <div class="stat-card stat-awaiting">
            <div class="stat-label">Awaiting HR</div>
            <div class="stat-value"><?= $leaveSummary['manager_approved'] ?? 0 ?></div>
        </div>
        <div class="stat-card stat-approved">
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

    <div class="panel">
        <div class="panel-header">
            <h2><i class="fas fa-list"></i> Team Leave Requests</h2>
        </div>

        <div class="table-responsive">
            <?php if (!empty($teamLeaves)): ?>
                <table class="leaves-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Leave Type</th>
                            <th>Dates</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teamLeaves as $leave): ?>
                            <?php
                                $status = strtolower((string) ($leave->status ?? 'pending'));
                                $badgeClass = match ($status) {
                                    'approved' => 'badge-approved',
                                    'manager_approved' => 'badge-manager-approved',
                                    'rejected' => 'badge-rejected',
                                    'ended' => 'badge-ended',
                                    default => 'badge-pending',
                                };
                                $duration = $leave->number_of_days ?? null;
                                if ($duration === null && !empty($leave->start_date) && !empty($leave->end_date)) {
                                    $duration = (int) ((strtotime($leave->end_date) - strtotime($leave->start_date)) / 86400) + 1;
                                }
                            ?>
                            <tr>
                                <td>
                                    <strong><?= esc($leave->employee_name ?? 'Unknown Employee') ?></strong><br>
                                    <span class="muted"><?= esc($leave->staff_code ?? 'N/A') ?></span>
                                </td>
                                <td><?= esc($leave->department_name ?? 'Unassigned') ?></td>
                                <td><?= esc($leave->leave_type ?? 'N/A') ?></td>
                                <td>
                                    <strong><?= !empty($leave->start_date) ? date('M d, Y', strtotime($leave->start_date)) : 'N/A' ?></strong><br>
                                    <span class="muted">to <?= !empty($leave->end_date) ? date('M d, Y', strtotime($leave->end_date)) : 'N/A' ?></span>
                                </td>
                                <td><?= $duration !== null ? esc((string) $duration) . ' day(s)' : 'N/A' ?></td>
                                <td>
                                    <span class="badge <?= $badgeClass ?>"><?= esc(ucwords(str_replace('_', ' ', $leave->status ?? 'pending'))) ?></span>
                                </td>
                                <td><span class="muted"><?= esc($leave->reason ?? 'No reason provided') ?></span></td>
                                <td>
                                    <?php if ($status === 'pending'): ?>
                                        <div class="action-stack">
                                            <form action="<?= base_url('leaves/approve-manager/' . $leave->id) ?>" method="post">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn-action btn-approve">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <form action="<?= base_url('leaves/reject/' . $leave->id) ?>" method="post">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn-action btn-reject">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    <?php elseif ($status === 'manager_approved'): ?>
                                        <span class="muted">Waiting for HR review.</span>
                                    <?php else: ?>
                                        <span class="muted">No action required.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox" style="font-size: 2.6rem;"></i>
                    <p>No leave requests match the selected filter.</p>
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

<?= $this->endSection() ?>