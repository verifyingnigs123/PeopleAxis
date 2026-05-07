<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .page-header {
        margin-bottom: 30px;
    }

    .page-header h1 {
        color: #2f5f45;
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
        color: #2f5f45;
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
        border-color: #6ea988;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
    }

    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
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
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
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
    <span>Edit Employee</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <h1><i class="fas fa-user-edit"></i> Edit Employee</h1>
</div>

<!-- Form Container -->
<div class="form-container">
    <div class="form-header">
        <h2><?= esc($employee->first_name . ' ' . $employee->last_name) ?></h2>
        <p>Update employee information</p>
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

    <form method="POST" action="<?= base_url('employee/update/' . $employee->id) ?>" id="employeeForm">
        <?= csrf_field() ?>

        <!-- First Name and Last Name Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="first_name" class="form-label">First Name <span class="required-star">*</span></label>
                <input type="text" class="form-control" id="first_name" name="first_name" autocomplete="given-name"
                       placeholder="Enter first name" required value="<?= esc($employee->first_name) ?>"
                       oninput="validateSpecialChars(this,'err_first_name','name')">
                <span id="err_first_name" style="display:none;color:#dc3545;font-size:0.85rem;">First name cannot contain special characters.</span>
                <?php if (service('validation')->hasError('first_name')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('first_name')) ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="last_name" class="form-label">Last Name <span class="required-star">*</span></label>
                <input type="text" class="form-control" id="last_name" name="last_name" autocomplete="family-name"
                       placeholder="Enter last name" required value="<?= esc($employee->last_name) ?>"
                       oninput="validateSpecialChars(this,'err_last_name','name')">
                <span id="err_last_name" style="display:none;color:#dc3545;font-size:0.85rem;">Last name cannot contain special characters.</span>
                <?php if (service('validation')->hasError('last_name')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('last_name')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Email and Phone Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="email" class="form-label">Email <span class="required-star">*</span></label>
                <input type="email" class="form-control" id="email" name="email" autocomplete="email"
                       placeholder="Enter email address" required value="<?= esc($employee->email) ?>">
                <?php if (service('validation')->hasError('email')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('email')) ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phone" name="phone" autocomplete="tel"
                       placeholder="Enter phone number" value="<?= esc($employee->phone ?? '') ?>"
                       oninput="validateSpecialChars(this,'err_phone','phone')">
                <span id="err_phone" style="display:none;color:#dc3545;font-size:0.85rem;">Phone cannot contain special characters.</span>
            </div>
        </div>

        <div class="form-row full">
            <div class="form-group">
                <label for="rfid_number" class="form-label">RFID Number <span class="required-star">*</span></label>
                <input type="text" class="form-control" id="rfid_number" name="rfid_number" autocomplete="off"
                       placeholder="Scan or enter RFID number" required value="<?= esc($employee->rfid_number ?? '') ?>">
                <?php if (service('validation')->hasError('rfid_number')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('rfid_number')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Department Row -->
        <div class="form-row full">
            <div class="form-group">
                <label for="department_id" class="form-label">Department</label>
                <select class="form-control" id="department_id" name="department_id" autocomplete="off">
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept->id ?>" <?= ($employee->department_id == $dept->id) ? 'selected' : '' ?>>
                            <?= esc($dept->name ?? 'N/A') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Position and Employment Type Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="role_id" class="form-label">Position</label>
                <select class="form-control" id="role_id" name="role_id" autocomplete="off">
                    <option value="">Select Position</option>
                    <?php foreach ($positions as $pos): ?>
                        <option value="<?= $pos->id ?>" <?= $employee->role_id == $pos->id ? 'selected' : '' ?>>
                            <?= esc($pos->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="employment_type" class="form-label">Employment Type</label>
                <select class="form-control" id="employment_type" name="employment_type" autocomplete="off">
                    <option value="">Select Type</option>
                    <option value="full_time" <?= ($employee->employment_type ?? '') == 'full_time' ? 'selected' : '' ?>>Full-Time</option>
                    <option value="part_time" <?= ($employee->employment_type ?? '') == 'part_time' ? 'selected' : '' ?>>Part-Time</option>
                    <option value="contractual" <?= ($employee->employment_type ?? '') == 'contractual' ? 'selected' : '' ?>>Contractual</option>
                    <option value="probationary" <?= ($employee->employment_type ?? '') == 'probationary' ? 'selected' : '' ?>>Probationary</option>
                </select>
            </div>
        </div>

        <!-- Date of Birth and Date Hired Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="date_of_birth" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" autocomplete="bday"
                       value="<?= esc($employee->date_of_birth ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="date_of_joining" class="form-label">Date Hired <span class="required-star">*</span></label>
                <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" autocomplete="off"
                       required value="<?= esc($employee->date_of_joining ?? '') ?>">
                <?php if (service('validation')->hasError('date_of_joining')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('date_of_joining')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Salary Rate and Rate Type Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="rate" class="form-label">Salary Rate</label>
                <input type="number" class="form-control" id="rate" name="rate" autocomplete="off"
                       placeholder="0.00" min="0" step="0.01" value="<?= esc($employee->rate ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="rate_type" class="form-label">Rate Type</label>
                <select class="form-control" id="rate_type" name="rate_type" autocomplete="off">
                    <option value="">Select Rate Type</option>
                    <option value="hourly" <?= ($employee->rate_type ?? '') == 'hourly' ? 'selected' : '' ?>>Hourly</option>
                    <option value="daily" <?= ($employee->rate_type ?? '') == 'daily' ? 'selected' : '' ?>>Daily</option>
                    <option value="monthly" <?= ($employee->rate_type ?? '') == 'monthly' ? 'selected' : '' ?>>Monthly</option>
                </select>
            </div>
        </div>

        <!-- Status Row -->
        <div class="form-row full">
            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status" autocomplete="off">
                    <option value="active" <?= $employee->status == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $employee->status == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="suspended" <?= $employee->status == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Employee
            </button>
            <a href="<?= base_url('employee') ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<script>
function showNotification(message) {
    // Create notification container if it doesn't exist
    let notifContainer = document.getElementById('notificationContainer');
    if (!notifContainer) {
        notifContainer = document.createElement('div');
        notifContainer.id = 'notificationContainer';
        notifContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        document.body.appendChild(notifContainer);
    }
    
    // Create notification element
    const notif = document.createElement('div');
    notif.style.cssText = 'background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 16px 20px; margin-bottom: 10px; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: slideInRight 0.3s ease;';
    notif.innerHTML = `
        <i class="fas fa-exclamation-circle" style="color: #ff9800; font-size: 1.2rem; flex-shrink: 0;"></i>
        <span style="color: #333; flex: 1; font-size: 0.95rem;">${message}</span>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #999; cursor: pointer; font-size: 1.5rem; padding: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">&times;</button>
    `;
    notifContainer.appendChild(notif);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => notif.remove(), 5000);
}

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
        const dobField = document.getElementById('date_of_birth');
    if (dobField && dobField.value) {
        const dob = new Date(dobField.value + 'T00:00:00');
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        if (age < 18) {
            e.preventDefault();
            dobField.style.borderColor = '#e74c3c';
            dobField.style.boxShadow = '0 0 0 3px rgba(231, 76, 60, 0.2)';
            dobField.focus();
            showNotification('Employees below 18 years old are not allowed. Please update the date of birth.');
            return;
        }
    }
    if (!fnOk || !lnOk || !phOk) e.preventDefault();
});
</script>

<?php if (service('validation')->hasError('date_of_birth')): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dobField = document.getElementById('date_of_birth');
        if (dobField) {
            dobField.style.borderColor = '#e74c3c';
            dobField.style.boxShadow = '0 0 0 3px rgba(231, 76, 60, 0.2)';
            showNotification(<?= json_encode(service('validation')->getError('date_of_birth')) ?>);
        }
    });
</script>
<?php endif; ?>

<?= $this->endSection() ?>
