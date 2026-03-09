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
        max-width: 900px;
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
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        color: #2c3e50;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .required-star {
        color: #e74c3c;
        margin-left: 4px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d5dce0;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: border-color 0.3s ease;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: #2a5298;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row.full {
        grid-template-columns: 1fr;
    }

    .form-actions {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 35px;
    }

    .btn {
        padding: 10px 30px;
        border: none;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
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

    .alert {
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-danger {
        background: #fadbd8;
        color: #c0392b;
        border-left: 4px solid #c0392b;
    }

    .error-text {
        font-size: 0.85rem;
        color: #e74c3c;
        margin-top: 5px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .form-container {
            padding: 20px;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <a href="<?= base_url('employee') ?>">Employees</a> /
    <span>Add New Employee</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <h1><i class="fas fa-user-plus"></i> Add New Employee</h1>
</div>

<!-- Form Container -->
<div class="form-container">
    <div class="form-header">
        <h2>Employee Information</h2>
        <p>Fill in the details to add a new employee</p>
    </div>

    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <strong>Validation Error</strong>
                <ul style="margin: 5px 0 0 0; padding-left: 20px;">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('employee/store') ?>" id="employeeForm">
        <?= csrf_field() ?>

        <!-- First Name and Last Name Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="first_name" class="form-label">First Name <span class="required-star">*</span></label>
                <input type="text" class="form-control" id="first_name" name="first_name" 
                       placeholder="Enter first name" required value="<?= old('first_name') ?>"
                       oninput="validateSpecialChars(this,'err_first_name','name')">
                <span id="err_first_name" style="display:none;color:#dc3545;font-size:0.85rem;">First name cannot contain special characters.</span>
                <?php if (has_error('first_name')): ?>
                    <div class="error-text"><?= error('first_name') ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="last_name" class="form-label">Last Name <span class="required-star">*</span></label>
                <input type="text" class="form-control" id="last_name" name="last_name" 
                       placeholder="Enter last name" required value="<?= old('last_name') ?>"
                       oninput="validateSpecialChars(this,'err_last_name','name')">
                <span id="err_last_name" style="display:none;color:#dc3545;font-size:0.85rem;">Last name cannot contain special characters.</span>
                <?php if (has_error('last_name')): ?>
                    <div class="error-text"><?= error('last_name') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Email and Phone Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="email" class="form-label">Email <span class="required-star">*</span></label>
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="Enter email address" required value="<?= old('email') ?>">
                <?php if (has_error('email')): ?>
                    <div class="error-text"><?= error('email') ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phone" name="phone" 
                       placeholder="Enter phone number" value="<?= old('phone') ?>"
                       oninput="validateSpecialChars(this,'err_phone','phone')">
                <span id="err_phone" style="display:none;color:#dc3545;font-size:0.85rem;">Phone cannot contain special characters.</span>
            </div>
        </div>

        <!-- Department and Position Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="department_id" class="form-label">Department</label>
                <select class="form-control" id="department_id" name="department_id">
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept->id ?>" <?= old('department_id') == $dept->id ? 'selected' : '' ?>>
                            <?= esc($dept->name ?? 'N/A') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="position_id" class="form-label">Position</label>
                <select class="form-control" id="position_id" name="position_id">
                    <option value="">Select Position</option>
                    <?php foreach ($positions as $pos): ?>
                        <option value="<?= $pos->id ?>" <?= old('position_id') == $pos->id ? 'selected' : '' ?>>
                            <?= esc($pos->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Date of Joining Row -->
        <div class="form-row full">
            <div class="form-group">
                <label for="date_of_joining" class="form-label">Date of Joining <span class="required-star">*</span></label>
                <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" 
                       required value="<?= old('date_of_joining', date('Y-m-d')) ?>">
                <?php if (has_error('date_of_joining')): ?>
                    <div class="error-text"><?= error('date_of_joining') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Date of Birth and Status Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="date_of_birth" class="form-label">Date of Birth</label>
                <input type="date" class="form-control <?= has_error('date_of_birth') ? 'is-invalid' : '' ?>" id="date_of_birth" name="date_of_birth" 
                       value="<?= old('date_of_birth') ?>">
                <?php if (has_error('date_of_birth')): ?>
                    <div class="error-text"><?= error('date_of_birth') ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="suspended" <?= old('status') == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Employee
            </button>
            <a href="<?= base_url('employee') ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<script>
function validateSpecialChars(input, errId, type) {
    const namePattern  = /^[A-Za-z\s\-\']*$/;
    const phonePattern = /^[0-9\s\+\-\(\)]*$/;
    const pattern = (type === 'phone') ? phonePattern : namePattern;
    const errSpan = document.getElementById(errId);
    if (!errSpan) return true;
    if (input.value !== '' && !pattern.test(input.value)) {
        errSpan.style.display = 'block';
        input.style.borderColor = '#dc3545';
        return false;
    } else {
        errSpan.style.display = 'none';
        input.style.borderColor = '';
        return true;
    }
}

document.getElementById('employeeForm').addEventListener('submit', function(e) {
    const fnOk = validateSpecialChars(document.getElementById('first_name'), 'err_first_name', 'name');
    const lnOk = validateSpecialChars(document.getElementById('last_name'),  'err_last_name',  'name');
    const phOk = validateSpecialChars(document.getElementById('phone'),      'err_phone',      'phone');
    if (!fnOk || !lnOk || !phOk) e.preventDefault();
});
</script>

<?= $this->endSection() ?>
