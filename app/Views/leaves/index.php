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

    .btn-add {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        border: none;
        padding: 10px 22px;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.4);
        color: white;
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

    .leaves-table {
        width: 100%;
        border-collapse: collapse;
    }

    .leaves-table thead th {
        background: #f8f9fa;
        color: #1e3c72;
        font-weight: 600;
        padding: 14px 20px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .leaves-table tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f3f5;
        font-size: 0.9rem;
        color: #495057;
    }

    .leaves-table tbody tr:hover {
        background: #f8f9ff;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .badge-pending {
        background: #fff3cd;
        color: #856404;
    }

    .badge-approved {
        background: #d4edda;
        color: #155724;
    }

    .badge-rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        padding: 6px 12px;
        font-size: 0.85rem;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-approve {
        background: #27ae60;
        color: white;
    }

    .btn-approve:hover {
        background: #229954;
    }

    .btn-reject {
        background: #e74c3c;
        color: white;
    }

    .btn-reject:hover {
        background: #c0392b;
    }

    .btn-edit {
        background: #3498db;
        color: white;
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

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span><?= isset($isHRAdmin) && $isHRAdmin ? 'Approve Leave' : 'Leave Requests' ?></span>
</div>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="fas fa-calendar-times"></i> <?= isset($isHRAdmin) && $isHRAdmin ? 'Approve Leave Requests' : 'Leave Requests' ?></h1>
        <p><?= isset($isHRAdmin) && $isHRAdmin ? 'Review and approve pending leave requests' : 'Manage employee leave requests' ?></p>
    </div>
    <?php if (!isset($isHRAdmin) || !$isHRAdmin): ?>
        <a href="<?= base_url('leaves/create') ?>" class="btn-add">
            <i class="fas fa-plus"></i> Apply for Leave
        </a>
    <?php endif; ?>
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
        <h2><i class="fas fa-list"></i> Leave Requests (<?= count($leaves ?? []) ?>)</h2>
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

<?= $this->endSection() ?>
