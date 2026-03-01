<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .page-header {
        margin-bottom: 30px;
    }

    .page-header h1 {
        color: #1e3c72;
        font-weight: 700;
        margin: 0;
    }

    .form-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 40px;
        max-width: 600px;
        margin: 0 auto;
    }

    .form-header {
        margin-bottom: 30px;
        text-align: center;
    }

    .form-header h2 {
        color: #1e3c72;
        font-weight: 700;
        margin: 0;
    }

    .form-header p {
        color: #95a5a6;
        margin-top: 5px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        color: #2c3e50;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e1e8ed;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .form-control:focus {
        outline: none;
        border-color: #2a5298;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
    }

    .form-control.is-invalid {
        border-color: #e74c3c;
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    .invalid-feedback {
        color: #e74c3c;
        font-size: 0.85rem;
        margin-top: 5px;
        display: block;
    }

    textarea.form-control {
        resize: vertical;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 35px;
    }

    .btn {
        padding: 12px 28px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-submit {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        flex: 1;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.4);
    }

    .btn-cancel {
        background: #e1e8ed;
        color: #2c3e50;
        flex: 0 1 auto;
    }

    .btn-cancel:hover {
        background: #d5dce3;
    }

    .alert {
        border-radius: 6px;
        padding: 15px 20px;
        margin-bottom: 20px;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .required-notice {
        color: #e74c3c;
    }

    .date-range-info {
        background: #f8f9ff;
        padding: 12px 15px;
        border-radius: 6px;
        font-size: 0.9rem;
        color: #2c3e50;
        margin-top: 10px;
    }

    .date-range-value {
        font-weight: 700;
        color: #2a5298;
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <a href="<?= base_url('leaves') ?>">Leave Requests</a> /
    <span>Apply for Leave</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <h1><i class="fas fa-plus-circle"></i> Apply for Leave</h1>
</div>

<!-- Form Container -->
<div class="form-container">
    <div class="form-header">
        <h2>Leave Request Form</h2>
        <p>Fill in the details for your leave request</p>
    </div>

    <!-- Validation Errors -->
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <strong>Please fix the following errors:</strong>
            <ul style="margin-bottom:0;margin-top:10px;">
                <?php foreach ($errors as $field => $message): ?>
                    <li><?= $message ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('leaves/store') ?>" method="POST">
        <?= csrf_field() ?>

        <!-- From Date Field -->
        <div class="form-group">
            <label for="from_date" class="form-label">
                From Date <span class="required-notice">*</span>
            </label>
            <input 
                type="date" 
                class="form-control <?= isset($errors['from_date']) ? 'is-invalid' : '' ?>" 
                id="from_date" 
                name="from_date"
                value="<?= old('from_date') ?>"
                required
                min="<?= date('Y-m-d') ?>"
            >
            <?php if (isset($errors['from_date'])): ?>
                <span class="invalid-feedback"><?= $errors['from_date'] ?></span>
            <?php endif; ?>
        </div>

        <!-- To Date Field -->
        <div class="form-group">
            <label for="to_date" class="form-label">
                To Date <span class="required-notice">*</span>
            </label>
            <input 
                type="date" 
                class="form-control <?= isset($errors['to_date']) ? 'is-invalid' : '' ?>" 
                id="to_date" 
                name="to_date"
                value="<?= old('to_date') ?>"
                required
                min="<?= date('Y-m-d') ?>"
            >
            <?php if (isset($errors['to_date'])): ?>
                <span class="invalid-feedback"><?= $errors['to_date'] ?></span>
            <?php endif; ?>
            <div class="date-range-info">
                <span>Total Days: </span>
                <span class="date-range-value" id="total-days">0</span>
            </div>
        </div>

        <!-- Leave Type Field -->
        <div class="form-group">
            <label for="leave_type" class="form-label">
                Leave Type <span class="required-notice">*</span>
            </label>
            <select 
                class="form-control <?= isset($errors['leave_type']) ? 'is-invalid' : '' ?>" 
                id="leave_type" 
                name="leave_type"
                required
            >
                <option value="">Select Leave Type</option>
                <option value="Casual" <?= old('leave_type') === 'Casual' ? 'selected' : '' ?>>Casual Leave</option>
                <option value="Sick" <?= old('leave_type') === 'Sick' ? 'selected' : '' ?>>Sick Leave</option>
                <option value="Earned" <?= old('leave_type') === 'Earned' ? 'selected' : '' ?>>Earned Leave</option>
                <option value="Maternity" <?= old('leave_type') === 'Maternity' ? 'selected' : '' ?>>Maternity Leave</option>
                <option value="Paternity" <?= old('leave_type') === 'Paternity' ? 'selected' : '' ?>>Paternity Leave</option>
                <option value="Unpaid" <?= old('leave_type') === 'Unpaid' ? 'selected' : '' ?>>Unpaid Leave</option>
            </select>
            <?php if (isset($errors['leave_type'])): ?>
                <span class="invalid-feedback"><?= $errors['leave_type'] ?></span>
            <?php endif; ?>
        </div>

        <!-- Reason Field -->
        <div class="form-group">
            <label for="reason" class="form-label">
                Reason <span class="required-notice">*</span>
            </label>
            <textarea 
                class="form-control <?= isset($errors['reason']) ? 'is-invalid' : '' ?>" 
                id="reason" 
                name="reason" 
                placeholder="Describe the reason for your leave"
                rows="4"
                required
            ><?= old('reason') ?></textarea>
            <?php if (isset($errors['reason'])): ?>
                <span class="invalid-feedback"><?= $errors['reason'] ?></span>
            <?php endif; ?>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="<?= base_url('leaves') ?>" class="btn btn-cancel">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-submit">
                <i class="fas fa-paper-plane"></i> Submit Leave Request
            </button>
        </div>
    </form>
</div>

<script>
    // Calculate days between from and to date
    function calculateDays() {
        const fromDate = new Date(document.getElementById('from_date').value);
        const toDate = new Date(document.getElementById('to_date').value);
        
        if (fromDate && toDate && toDate >= fromDate) {
            const diffTime = Math.abs(toDate - fromDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            document.getElementById('total-days').textContent = diffDays;
        } else {
            document.getElementById('total-days').textContent = '0';
        }
    }

    document.getElementById('from_date').addEventListener('change', calculateDays);
    document.getElementById('to_date').addEventListener('change', calculateDays);

    // Calculate on page load if dates are pre-filled
    window.addEventListener('load', calculateDays);
</script>

<?= $this->endSection() ?>
