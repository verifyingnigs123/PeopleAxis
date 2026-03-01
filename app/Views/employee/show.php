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

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 60, 114, 0.3);
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
    }

    .btn-danger {
        background: #e74c3c;
        color: white;
    }

    .btn-danger:hover {
        background: #c0392b;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .detail-section {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 25px;
    }

    .section-title {
        color: #1e3c72;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e8eef5;
    }

    .detail-row {
        margin-bottom: 18px;
    }

    .detail-label {
        display: block;
        color: #7f8c8d;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 5px;
        letter-spacing: 0.5px;
    }

    .detail-value {
        color: #2c3e50;
        font-size: 1rem;
        font-weight: 500;
    }

    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-active {
        background: #d4edda;
        color: #155724;
    }

    .badge-inactive {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-suspended {
        background: #fff3cd;
        color: #856404;
    }

    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .action-buttons {
            width: 100%;
        }

        .action-buttons .btn {
            flex: 1;
            justify-content: center;
        }
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <a href="<?= base_url('employee') ?>">Employees</a> /
    <span><?= esc($employee->first_name . ' ' . $employee->last_name) ?></span>
</div>

<!-- Page Header with Actions -->
<div class="page-header">
    <h1><i class="fas fa-user"></i> <?= esc($employee->first_name . ' ' . $employee->last_name) ?></h1>
    <div class="action-buttons">
        <a href="<?= base_url('employee/edit/' . $employee->id) ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="<?= base_url('employee') ?>" class="btn btn-secondary">
            <i class="fas fa-list"></i> Back to List
        </a>
    </div>
</div>

<!-- Employee Details -->
<div class="detail-grid">
    <!-- Personal Information -->
    <div class="detail-section">
        <h3 class="section-title"><i class="fas fa-address-card"></i> Personal Information</h3>
        
        <div class="detail-row">
            <span class="detail-label">Employee ID</span>
            <span class="detail-value"><?= esc($employee->employee_id) ?></span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Full Name</span>
            <span class="detail-value"><?= esc($employee->first_name . ' ' . $employee->last_name) ?></span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Email Address</span>
            <span class="detail-value">
                <a href="mailto:<?= esc($employee->email) ?>" style="color: #2a5298; text-decoration: none;">
                    <?= esc($employee->email) ?>
                </a>
            </span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Phone Number</span>
            <span class="detail-value"><?= esc($employee->phone ?? 'N/A') ?></span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Date of Birth</span>
            <span class="detail-value">
                <?php if ($employee->date_of_birth): ?>
                    <?= date('F d, Y', strtotime($employee->date_of_birth)) ?>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </span>
        </div>
    </div>

    <!-- Employment Information -->
    <div class="detail-section">
        <h3 class="section-title"><i class="fas fa-briefcase"></i> Employment Information</h3>

        <div class="detail-row">
            <span class="detail-label">Department</span>
            <span class="detail-value"><?= $department ? esc($department->name) : 'N/A' ?></span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Position</span>
            <span class="detail-value"><?= $position ? esc($position->name) : 'N/A' ?></span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Date of Joining</span>
            <span class="detail-value">
                <?php if ($employee->date_of_joining): ?>
                    <?= date('F d, Y', strtotime($employee->date_of_joining)) ?>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="detail-value">
                <?php 
                    $statusClass = 'badge-' . strtolower($employee->status);
                    $statusText = ucfirst($employee->status ?? 'active');
                ?>
                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
            </span>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
