<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<style>
    .approvals-shell {
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
        margin: 0;
        color: #2f5f45;
        font-weight: 700;
        font-size: 1.85rem;
        line-height: 1;
    }

    .page-header p {
        margin: 6px 0 0;
        color: #6f8192;
        font-size: 0.92rem;
    }

    .header-pills {
        display: inline-flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .header-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .header-pill.pending {
        background: #fff5e6;
        color: #9d6108;
        border-color: #f0d5ab;
    }

    .header-pill.rejected {
        background: #fef0f0;
        color: #a43737;
        border-color: #f3cccc;
    }

    .alert {
        padding: 12px 14px;
        border-radius: 8px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
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

    .section-panel {
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        margin-bottom: 14px;
    }

    .panel-header {
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        font-size: 1rem;
        border-bottom: 1px solid #e7edf4;
    }

    .panel-header.pending-hdr {
        background: #fff8ec;
        color: #8b5708;
    }

    .panel-header.rejected-hdr {
        background: #fff2f2;
        color: #9c3434;
    }

    .panel-count {
        margin-left: auto;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .pending-hdr .panel-count {
        background: #fff0d4;
        border-color: #efd3a4;
        color: #8b5708;
    }

    .rejected-hdr .panel-count {
        background: #ffe1e1;
        border-color: #f0bebe;
        color: #9c3434;
    }

    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(295px, 1fr));
        gap: 12px;
        padding: 14px;
    }

    .emp-card {
        border: 1px solid #e5ecf3;
        border-radius: 9px;
        overflow: hidden;
        background: #ffffff;
    }

    .emp-card-header {
        padding: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid #edf2f7;
    }

    .emp-card-header.pending-card {
        background: #fffbf4;
    }

    .emp-card-header.rejected-card {
        background: #fff8f8;
    }

    .emp-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.86rem;
        font-weight: 700;
        flex-shrink: 0;
        border: 1px solid transparent;
    }

    .avatar-pending {
        background: #ffe6be;
        color: #8d5606;
        border-color: #f2d3a2;
    }

    .avatar-rejected {
        background: #ffdcdc;
        color: #a63737;
        border-color: #f3c0c0;
    }

    .emp-name {
        font-weight: 700;
        color: #26415d;
        font-size: 0.95rem;
        margin-bottom: 2px;
    }

    .emp-id {
        font-size: 0.78rem;
        color: #7f90a0;
    }

    .badge-pill {
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .badge-pending {
        background: #fff3d9;
        color: #8d5606;
        border-color: #efd3a4;
    }

    .badge-rejected {
        background: #ffe3e3;
        color: #a63737;
        border-color: #f4c2c2;
    }

    .emp-card-body {
        padding: 11px 12px;
    }

    .emp-detail-row {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        margin-bottom: 7px;
        font-size: 0.85rem;
        color: #476078;
        line-height: 1.35;
    }

    .emp-detail-row i {
        color: #8093a5;
        width: 14px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .rejection-reason {
        margin-top: 8px;
        padding: 8px 10px;
        background: #fff3f3;
        border-left: 3px solid #dd6262;
        border-radius: 6px;
        font-size: 0.8rem;
        color: #842f2f;
    }

    .rejection-reason strong {
        display: block;
        margin-bottom: 3px;
    }

    .submitted-on {
        font-size: 0.76rem;
        color: #90a0af;
        margin-top: 8px;
    }

    .emp-card-footer {
        padding: 10px 12px;
        border-top: 1px solid #edf2f7;
        display: flex;
        gap: 8px;
    }

    .emp-card-footer form {
        margin: 0;
    }

    .btn-review {
        padding: 7px 11px;
        border-radius: 8px;
        border: 1px solid transparent;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        transition: background 0.2s ease, border-color 0.2s ease;
        line-height: 1;
    }

    .btn-review.review {
        background: #6ea988;
        color: #ffffff;
        border-color: #6ea988;
    }

    .btn-review.review:hover {
        background: #21437c;
        border-color: #21437c;
    }

    .btn-review.del {
        background: #fff1f1;
        color: #ad3d3d;
        border-color: #efc5c5;
    }

    .btn-review.del:hover {
        background: #ffe3e3;
        border-color: #e9acac;
    }

    .empty-state {
        text-align: center;
        padding: 44px 20px;
        color: #7f90a0;
    }

    .empty-state i {
        font-size: 2.3rem;
        display: block;
        margin-bottom: 12px;
        color: #adb9c5;
    }

    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 1.55rem;
        }
    }

    @media (max-width: 640px) {
        .cards-grid {
            grid-template-columns: 1fr;
            padding: 12px;
            gap: 10px;
        }

        .page-header {
            padding: 14px;
        }
    }
</style>

<div class="approvals-shell">

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
        <span>/</span>
        <span>Employee Approvals</span>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1><i class="fas fa-user-shield"></i> Employee Approval Dashboard</h1>
            <p>Review pending employee submissions and manage rejected applications</p>
            <div class="header-pills">
                <span class="header-pill pending"><i class="fas fa-hourglass-half"></i> Pending: <?= count($pending) ?></span>
                <span class="header-pill rejected"><i class="fas fa-times-circle"></i> Rejected: <?= count($rejected) ?></span>
            </div>
        </div>
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

    <!-- Pending Section -->
    <div class="section-panel">
        <div class="panel-header pending-hdr">
            <i class="fas fa-hourglass-half"></i>
            Waiting for Approval
            <span class="panel-count"><?= count($pending) ?></span>
        </div>

        <?php if (!empty($pending)): ?>
            <div class="cards-grid">
                <?php foreach ($pending as $emp): ?>
                    <div class="emp-card">
                        <div class="emp-card-header pending-card">
                            <div class="emp-avatar avatar-pending">
                                <?= strtoupper(substr((string) $emp->first_name, 0, 1) . substr((string) $emp->last_name, 0, 1)) ?>
                            </div>
                            <div>
                                <div class="emp-name"><?= esc(trim($emp->first_name . ' ' . $emp->last_name)) ?></div>
                                <div class="emp-id"><?= esc($emp->employee_id) ?></div>
                            </div>
                            <span class="badge-pill badge-pending">
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
                                <?= esc($positionMap[$emp->role_id] ?? 'No Role') ?>
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

    <!-- Rejected Section -->
    <div class="section-panel">
        <div class="panel-header rejected-hdr">
            <i class="fas fa-times-circle"></i>
            Rejected Applications
            <span class="panel-count"><?= count($rejected) ?></span>
        </div>

        <?php if (!empty($rejected)): ?>
            <div class="cards-grid">
                <?php foreach ($rejected as $emp): ?>
                    <div class="emp-card">
                        <div class="emp-card-header rejected-card">
                            <div class="emp-avatar avatar-rejected">
                                <?= strtoupper(substr((string) $emp->first_name, 0, 1) . substr((string) $emp->last_name, 0, 1)) ?>
                            </div>
                            <div>
                                <div class="emp-name"><?= esc(trim($emp->first_name . ' ' . $emp->last_name)) ?></div>
                                <div class="emp-id"><?= esc($emp->employee_id) ?></div>
                            </div>
                            <span class="badge-pill badge-rejected">
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
                                <?= esc($positionMap[$emp->role_id] ?? 'No Role') ?>
                            </div>
                            <?php if (!empty($emp->approval_notes)): ?>
                                <div class="rejection-reason">
                                    <strong><i class="fas fa-comment-alt"></i> Rejection Reason:</strong>
                                    <?= esc($emp->approval_notes) ?>
                                </div>
                            <?php endif; ?>
                            <div class="submitted-on">
                                <i class="fas fa-times-circle"></i>
                                Rejected <?= $emp->updated_at ? date('M j, Y g:i A', strtotime($emp->updated_at)) : '' ?>
                            </div>
                        </div>

                        <div class="emp-card-footer">
                            <form method="POST" action="<?= base_url('employee/delete/' . $emp->id) ?>" onsubmit="return confirm('Permanently delete this rejected record?');">
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

</div>

<?= $this->endSection() ?>
