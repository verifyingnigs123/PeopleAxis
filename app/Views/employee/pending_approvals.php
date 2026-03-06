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
    .page-header h1 { color: #1e3c72; font-weight: 700; margin: 0; }

    .section-panel {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,.08);
        overflow: hidden;
        margin-bottom: 32px;
    }
    .panel-header {
        padding: 18px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: white;
        font-weight: 700;
        font-size: 1.05rem;
    }
    .panel-header.pending-hdr  { background: linear-gradient(135deg,#f39c12 0%,#e67e22 100%); }
    .panel-header.rejected-hdr { background: linear-gradient(135deg,#c0392b 0%,#e74c3c 100%); }

    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
        gap: 20px;
        padding: 24px;
    }

    .emp-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
        transition: box-shadow .2s;
    }
    .emp-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.12); }

    .emp-card-header {
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .emp-card-header.pending-card  { background: #fff8ec; border-bottom: 3px solid #f39c12; }
    .emp-card-header.rejected-card { background: #fff5f5; border-bottom: 3px solid #e74c3c; }

    .emp-avatar {
        width: 46px; height: 46px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; font-weight: 700; color: white; flex-shrink: 0;
    }
    .avatar-pending  { background: linear-gradient(135deg,#f39c12,#e67e22); }
    .avatar-rejected { background: linear-gradient(135deg,#c0392b,#e74c3c); }

    .emp-name  { font-weight: 700; color: #2c3e50; font-size: 1rem; margin-bottom: 2px; }
    .emp-id    { font-size: .8rem; color: #7f8c8d; }

    .emp-card-body { padding: 14px 18px; }
    .emp-detail-row {
        display: flex; gap: 8px;
        margin-bottom: 8px; font-size: .88rem; color: #555;
        align-items: flex-start;
    }
    .emp-detail-row i { color: #7f8c8d; width: 16px; flex-shrink: 0; margin-top: 2px; }

    .rejection-reason {
        margin-top: 8px;
        padding: 8px 12px;
        background: #fff0f0;
        border-left: 3px solid #e74c3c;
        border-radius: 4px;
        font-size: .82rem;
        color: #721c24;
    }
    .rejection-reason strong { display: block; margin-bottom: 2px; }

    .emp-card-footer {
        padding: 12px 18px;
        border-top: 1px solid #f1f3f5;
        display: flex; gap: 10px; flex-wrap: wrap;
    }

    .btn-review {
        padding: 7px 16px; border: none; border-radius: 5px;
        font-size: .85rem; font-weight: 600; cursor: pointer;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        transition: all .25s;
    }
    .btn-review.review  { background: linear-gradient(135deg,#1e3c72,#2a5298); color: white; }
    .btn-review.review:hover  { box-shadow: 0 4px 12px rgba(30,60,114,.35); transform: translateY(-1px); }
    .btn-review.del     { background: #e74c3c; color: white; }
    .btn-review.del:hover     { background: #c0392b; transform: translateY(-1px); }

    .badge-pill {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px; font-size: .78rem; font-weight: 600;
    }
    .badge-pending  { background: #fff3cd; color: #856404; }
    .badge-rejected { background: #f8d7da; color: #721c24; }

    .empty-state {
        text-align: center; padding: 50px 20px; color: #95a5a6;
    }
    .empty-state i { font-size: 2.5rem; display: block; margin-bottom: 14px; color: #bdc3c7; }

    .alert {
        padding: 14px 20px; border-radius: 7px; margin-bottom: 20px;
        display: flex; align-items: center; gap: 12px;
    }
    .alert-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
    .alert-danger  { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }

    .submitted-on { font-size: .78rem; color: #aaa; margin-top: 6px; }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom:20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Employee Approvals</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <h1><i class="fas fa-user-shield"></i> Employee Approval Dashboard</h1>
</div>

<!-- Flash alerts -->
<?php if (session()->has('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>
<?php if (session()->has('error')): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- ═══ PENDING SECTION ═══ -->
<div class="section-panel">
    <div class="panel-header pending-hdr">
        <i class="fas fa-hourglass-half fa-lg"></i>
        Waiting for Approval
        <span style="margin-left:auto; background:rgba(255,255,255,.25); padding:2px 12px; border-radius:20px; font-size:.85rem;">
            <?= count($pending) ?>
        </span>
    </div>

    <?php if (!empty($pending)): ?>
        <div class="cards-grid">
            <?php foreach ($pending as $emp): ?>
                <div class="emp-card">
                    <div class="emp-card-header pending-card">
                        <div class="emp-avatar avatar-pending">
                            <?= strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1)) ?>
                        </div>
                        <div>
                            <div class="emp-name"><?= esc(trim($emp->first_name . ' ' . $emp->last_name)) ?></div>
                            <div class="emp-id"><?= esc($emp->employee_id) ?></div>
                        </div>
                        <span class="badge-pill badge-pending" style="margin-left:auto;">
                            <i class="fas fa-clock"></i> Pending
                        </span>
                    </div>
                    <div class="emp-card-body">
                        <div class="emp-detail-row">
                            <i class="fas fa-envelope"></i> <?= esc($emp->email) ?>
                        </div>
                        <?php if ($emp->phone): ?>
                        <div class="emp-detail-row">
                            <i class="fas fa-phone"></i> <?= esc($emp->phone) ?>
                        </div>
                        <?php endif; ?>
                        <div class="emp-detail-row">
                            <i class="fas fa-building"></i>
                            <?= esc($departmentMap[$emp->department_id] ?? 'No Department') ?>
                        </div>
                        <div class="emp-detail-row">
                            <i class="fas fa-briefcase"></i>
                            <?= esc($positionMap[$emp->position_id] ?? 'No Position') ?>
                        </div>
                        <div class="emp-detail-row">
                            <i class="fas fa-calendar-plus"></i>
                            Joined: <?= $emp->date_of_joining ? date('M j, Y', strtotime($emp->date_of_joining)) : 'N/A' ?>
                        </div>
                        <div class="submitted-on">
                            <i class="fas fa-clock"></i>
                            Submitted <?= $emp->created_at ? date('M j, Y g:i A', strtotime($emp->created_at)) : '' ?>
                        </div>
                    </div>
                    <div class="emp-card-footer">
                        <a href="<?= base_url('employee/review/' . $emp->id) ?>" class="btn-review review">
                            <i class="fas fa-eye"></i> Review &amp; Decide
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-check-double"></i>
            <p>No employees are waiting for approval.</p>
        </div>
    <?php endif; ?>
</div>

<!-- ═══ REJECTED SECTION ═══ -->
<div class="section-panel">
    <div class="panel-header rejected-hdr">
        <i class="fas fa-times-circle fa-lg"></i>
        Rejected Applications
        <span style="margin-left:auto; background:rgba(255,255,255,.25); padding:2px 12px; border-radius:20px; font-size:.85rem;">
            <?= count($rejected) ?>
        </span>
    </div>

    <?php if (!empty($rejected)): ?>
        <div class="cards-grid">
            <?php foreach ($rejected as $emp): ?>
                <div class="emp-card">
                    <div class="emp-card-header rejected-card">
                        <div class="emp-avatar avatar-rejected">
                            <?= strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1)) ?>
                        </div>
                        <div>
                            <div class="emp-name"><?= esc(trim($emp->first_name . ' ' . $emp->last_name)) ?></div>
                            <div class="emp-id"><?= esc($emp->employee_id) ?></div>
                        </div>
                        <span class="badge-pill badge-rejected" style="margin-left:auto;">
                            <i class="fas fa-ban"></i> Rejected
                        </span>
                    </div>
                    <div class="emp-card-body">
                        <div class="emp-detail-row">
                            <i class="fas fa-envelope"></i> <?= esc($emp->email) ?>
                        </div>
                        <?php if ($emp->phone): ?>
                        <div class="emp-detail-row">
                            <i class="fas fa-phone"></i> <?= esc($emp->phone) ?>
                        </div>
                        <?php endif; ?>
                        <div class="emp-detail-row">
                            <i class="fas fa-building"></i>
                            <?= esc($departmentMap[$emp->department_id] ?? 'No Department') ?>
                        </div>
                        <div class="emp-detail-row">
                            <i class="fas fa-briefcase"></i>
                            <?= esc($positionMap[$emp->position_id] ?? 'No Position') ?>
                        </div>
                        <?php if (!empty($emp->approval_notes)): ?>
                        <div class="rejection-reason">
                            <strong><i class="fas fa-comment-alt"></i> Rejection Reason:</strong>
                            <?= esc($emp->approval_notes) ?>
                        </div>
                        <?php endif; ?>
                        <div class="submitted-on" style="margin-top:10px;">
                            <i class="fas fa-times-circle" style="color:#e74c3c;"></i>
                            Rejected <?= $emp->updated_at ? date('M j, Y g:i A', strtotime($emp->updated_at)) : '' ?>
                        </div>
                    </div>
                    <div class="emp-card-footer">
                        <form method="POST" action="<?= base_url('employee/delete/' . $emp->id) ?>"
                              onsubmit="return confirm('Permanently delete this rejected record?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-review del">
                                <i class="fas fa-trash"></i> Delete Record
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <p>No rejected applications.</p>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
