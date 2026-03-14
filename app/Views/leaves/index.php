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

    .btn-add {
        background: #6ea988;
        color: #ffffff;
        border: 1px solid #6ea988;
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease, border-color 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        text-decoration: none;
        line-height: 1;
    }

    .btn-add:hover {
        background: #21437c;
        border-color: #21437c;
        color: #ffffff;
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

    .alert {
        border-radius: 8px;
        padding: 12px 14px;
        margin-bottom: 14px;
        border: none;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
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
        font-weight: 700;
        font-size: 1.08rem;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .leaves-table {
        width: 100%;
        min-width: 940px;
        border-collapse: collapse;
    }

    .leaves-table thead th {
        background: #f7f9fc;
        color: #445b72;
        font-weight: 700;
        padding: 11px 14px;
        text-align: left;
        border-bottom: 1px solid #e1e9f2;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.35px;
        white-space: nowrap;
    }

    .leaves-table tbody td {
        padding: 11px 14px;
        border-bottom: 1px solid #edf2f7;
        font-size: 0.86rem;
        color: #42586e;
        vertical-align: middle;
    }

    .leaves-table tbody tr:hover {
        background: #fbfdff;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .badge-pending {
        background: #fff4e4;
        color: #9f6310;
        border-color: #f1debb;
    }

    .badge-approved {
        background: #e9f7ec;
        color: #1d7a3f;
        border-color: #cfead8;
    }

    .badge-rejected {
        background: #fdecec;
        color: #b43a3a;
        border-color: #f4d3d3;
    }

    .action-buttons {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .action-buttons form {
        margin: 0;
    }

    .btn-action {
        padding: 6px 10px;
        font-size: 0.78rem;
        font-weight: 700;
        border-radius: 7px;
        border: 1px solid transparent;
        cursor: pointer;
        transition: background 0.2s ease, border-color 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        line-height: 1.1;
        text-decoration: none;
    }

    .btn-approve {
        background: #e9f7ec;
        color: #1d7a3f;
        border-color: #cfead8;
    }

    .btn-approve:hover {
        background: #ddf1e2;
        border-color: #bfe2cb;
    }

    .btn-reject {
        background: #feecec;
        color: #ba3939;
        border-color: #f4cccc;
    }

    .btn-reject:hover {
        background: #fbdcdc;
        border-color: #efbbbb;
    }

    .btn-edit {
        background: #e9f3ff;
        color: #1f5ea8;
        border-color: #c9dcf6;
    }

    .btn-edit:hover {
        background: #dbeafd;
        border-color: #b8d2f2;
    }

    .empty-state {
        text-align: center;
        padding: 54px 20px;
        color: #7f90a0;
    }

    .empty-state i {
        font-size: 2.5rem;
        color: #adb9c5;
        margin-bottom: 12px;
        display: block;
    }

    @media (max-width: 768px) {
        .admin-header {
            padding: 14px;
        }

        .admin-header h1 {
            font-size: 1.55rem;
        }

        .btn-add {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="hr-shell">

<?php
    $leaveTotal = count($leaves ?? []);
    $pendingReviewCount = 0;
    $approvedCount = 0;
    $rejectedCount = 0;

    foreach (($leaves ?? []) as $leaveSummary) {
        $leaveStatusSummary = strtolower((string) ($leaveSummary->status ?? 'pending'));

        if (in_array($leaveStatusSummary, ['pending', 'manager_approved'], true)) {
            $pendingReviewCount++;
        } elseif ($leaveStatusSummary === 'approved') {
            $approvedCount++;
        } elseif ($leaveStatusSummary === 'rejected') {
            $rejectedCount++;
        }
    }

    $leavePageTitle = isset($isHRAdmin) && $isHRAdmin ? 'Approve Leave Requests' : 'Leave Requests';
    $leavePageDescription = isset($isHRAdmin) && $isHRAdmin
        ? 'Operational overview for review queues, approvals, and leave outcomes'
        : 'Track, review, and manage employee leave requests';
?>

<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
    <span>/</span>
    <span><?= isset($isHRAdmin) && $isHRAdmin ? 'Approve Leave' : 'Leave Requests' ?></span>
</div>

<!-- Page Header -->
<div class="admin-header">
    <div>
        <h1><i class="fas fa-calendar-times"></i> <?= $leavePageTitle ?></h1>
        <p><?= $leavePageDescription ?></p>
    </div>
    <?php if (!isset($isHRAdmin) || !$isHRAdmin): ?>
        <a href="<?= base_url('leaves/create') ?>" class="btn-add">
            <i class="fas fa-plus"></i> Apply for Leave
        </a>
    <?php endif; ?>
</div>

<div class="admin-stats">
    <div class="stat-box">
        <i class="fas fa-file-alt"></i>
        <div class="stat-info">
            <h5>Total Requests</h5>
            <h3><?= $leaveTotal ?></h3>
        </div>
    </div>
    <div class="stat-box warning">
        <i class="fas fa-hourglass-half"></i>
        <div class="stat-info">
            <h5><?= isset($isHRAdmin) && $isHRAdmin ? 'Awaiting Review' : 'Pending' ?></h5>
            <h3><?= $pendingReviewCount ?></h3>
        </div>
    </div>
    <div class="stat-box success">
        <i class="fas fa-check-circle"></i>
        <div class="stat-info">
            <h5>Approved</h5>
            <h3><?= $approvedCount ?></h3>
        </div>
    </div>
    <div class="stat-box danger">
        <i class="fas fa-times-circle"></i>
        <div class="stat-info">
            <h5>Rejected</h5>
            <h3><?= $rejectedCount ?></h3>
        </div>
    </div>
</div>

<!-- Flash Messages -->
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

<!-- Leaves Table -->
<div class="admin-panel">
    <div class="panel-header">
        <h2><i class="fas fa-list"></i> Leave Requests (<?= $leaveTotal ?>)</h2>
    </div>

    <div class="table-responsive">
        <?php if (!empty($leaves)): ?>
            <table class="leaves-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Leave Type</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaves as $i => $leave): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($leave->name ?? 'N/A') ?></td>
                            <td><?= date('M d, Y', strtotime($leave->start_date ?? now())) ?></td>
                            <td><?= date('M d, Y', strtotime($leave->end_date ?? now())) ?></td>
                            <td><?= esc($leave->leave_type ?? 'N/A') ?></td>
                            <td>
                                <strong><?= $leave->number_of_days ?? 0 ?> days</strong>
                            </td>
                            <td>
                                <?php 
                                    $statusBadge = match(strtolower($leave->status ?? 'pending')) {
                                        'approved' => 'badge-approved',
                                        'rejected' => 'badge-rejected',
                                        default => 'badge-pending'
                                    };
                                ?>
                                <span class="badge <?= $statusBadge ?>"><?= esc(ucfirst($leave->status ?? 'Pending')) ?></span>
                            </td>
                            <td>
                                <small><?= esc($leave->reason ?? 'N/A') ?></small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php 
                                        $canApproveLeave = isset($canApprove) && $canApprove;
                                        $isApprovalRequired = in_array($leave->status ?? 'pending', ['pending', 'manager_approved']);
                                    ?>
                                    
                                    <?php if ($canApproveLeave && $isApprovalRequired): ?>
                                        <form action="<?= base_url('leaves/approveByHR/' . ($leave->id ?? 0)) ?>" method="POST" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn-action btn-approve" title="Approve this leave request">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <form action="<?= base_url('leaves/reject/' . ($leave->id ?? 0)) ?>" method="POST" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn-action btn-reject" title="Reject this leave request">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </form>
                                    <?php elseif (!$canApproveLeave && (($leave->status ?? 'pending') === 'pending')): ?>
                                        <a href="<?= base_url('leaves/edit/' . ($leave->id ?? 0)) ?>" class="btn-action btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No leave requests found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</div>

<?= $this->endSection() ?>
