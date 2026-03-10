<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .confirm-delete-wrapper {
        max-width: 560px;
        margin: 60px auto;
    }

    .confirm-delete-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.10);
        overflow: hidden;
    }

    .confirm-delete-header {
        background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
        color: #fff;
        padding: 28px 32px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .confirm-delete-header .icon-circle {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: rgba(255,255,255,0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
    }

    .confirm-delete-header h1 {
        font-size: 1.35rem;
        font-weight: 700;
        margin: 0 0 4px;
    }

    .confirm-delete-header p {
        margin: 0;
        font-size: 0.88rem;
        opacity: 0.88;
    }

    .confirm-delete-body {
        padding: 32px;
    }

    .request-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffc107;
        border-radius: 6px;
        padding: 8px 14px;
        font-size: 0.88rem;
        font-weight: 600;
        margin-bottom: 24px;
        width: 100%;
        box-sizing: border-box;
    }

    .employee-summary {
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #e74c3c;
        padding: 18px 20px;
        margin-bottom: 24px;
    }

    .employee-summary .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 0.9rem;
        border-bottom: 1px solid #e9ecef;
    }

    .employee-summary .summary-row:last-child {
        border-bottom: none;
    }

    .employee-summary .summary-label {
        color: #6c757d;
        font-weight: 500;
    }

    .employee-summary .summary-value {
        color: #2c3e50;
        font-weight: 600;
    }

    .warning-text {
        color: #c0392b;
        font-size: 0.9rem;
        background: #fdf0ef;
        border: 1px solid #f5c6c2;
        border-radius: 6px;
        padding: 12px 16px;
        margin-bottom: 28px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .warning-text i {
        font-size: 1.1rem;
        margin-top: 1px;
        flex-shrink: 0;
    }

    .confirm-delete-actions {
        display: flex;
        gap: 12px;
    }

    .btn-confirm-delete {
        flex: 1;
        background: linear-gradient(135deg, #c0392b, #e74c3c);
        color: #fff;
        border: none;
        padding: 12px 20px;
        border-radius: 7px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-confirm-delete:hover {
        opacity: 0.88;
    }

    .btn-cancel {
        flex: 1;
        background: #6c757d;
        color: #fff;
        border: none;
        padding: 12px 20px;
        border-radius: 7px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: opacity 0.2s;
    }

    .btn-cancel:hover {
        opacity: 0.88;
        color: #fff;
        text-decoration: none;
    }
</style>

<div class="confirm-delete-wrapper">

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" style="margin-bottom:18px;">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:18px;">
            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="confirm-delete-card">
        <div class="confirm-delete-header">
            <div class="icon-circle">
                <i class="fas fa-user-times"></i>
            </div>
            <div>
                <h1>Confirm Employee Deletion</h1>
                <p>Requested by HR Admin — Super Admin approval required</p>
            </div>
        </div>

        <div class="confirm-delete-body">

            <div class="request-badge">
                <i class="fas fa-bell"></i>
                HR Admin has requested deletion of this employee record.
            </div>

            <div class="employee-summary">
                <div class="summary-row">
                    <span class="summary-label">Employee ID</span>
                    <span class="summary-value"><?= esc($employee->employee_id ?? 'N/A') ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Full Name</span>
                    <span class="summary-value"><?= esc(trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? ''))) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Email</span>
                    <span class="summary-value"><?= esc($employee->email ?? 'N/A') ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Department</span>
                    <span class="summary-value"><?= esc($departmentMap[$employee->department_id] ?? 'N/A') ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Status</span>
                    <span class="summary-value"><?= esc(ucfirst($employee->account_status ?? 'pending')) ?></span>
                </div>
            </div>

            <div class="warning-text">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Approving this request will <strong>deactivate the employee's user account</strong> and hide them from the HR Admin employee table. Their record and Employee ID are preserved and can be fully restored from <strong>Manage Users</strong> at any time.</span>
            </div>

            <div class="confirm-delete-actions">
                <form method="POST" action="<?= base_url('employee/confirm-delete/' . $employee->id) ?>" style="flex:1; display:flex;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-confirm-delete" style="width:100%;">
                        <i class="fas fa-trash-alt"></i> Approve &amp; Delete
                    </button>
                </form>
                <form method="POST" action="<?= base_url('employee/reject-delete/' . $employee->id) ?>" style="flex:1; display:flex;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-cancel" style="width:100%; cursor:pointer; border:none;">
                        <i class="fas fa-times-circle"></i> Reject Request
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
