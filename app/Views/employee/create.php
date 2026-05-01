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

        <?php
        $employeePositionOptions = ['Front Counter', 'Kitchen/Prep', 'Drive-Thru', 'Dining Room'];
        $employeeTypeOptions = ['Manager', 'Employee'];
        ?>

        <!-- First Name and Last Name Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="first_name" class="form-label">First Name <span class="required-star">*</span></label>
                <input type="text" class="form-control" id="first_name" name="first_name" 
                       placeholder="Enter first name" required value="<?= old('first_name') ?>"
                       oninput="validateSpecialChars(this,'err_first_name','name')">
                <span id="err_first_name" style="display:none;color:#dc3545;font-size:0.85rem;">First name cannot contain special characters.</span>
                <?php if (service('validation')->hasError('first_name')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('first_name')) ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="last_name" class="form-label">Last Name <span class="required-star">*</span></label>
                <input type="text" class="form-control" id="last_name" name="last_name" 
                       placeholder="Enter last name" required value="<?= old('last_name') ?>"
                       oninput="validateSpecialChars(this,'err_last_name','name')">
                <span id="err_last_name" style="display:none;color:#dc3545;font-size:0.85rem;">Last name cannot contain special characters.</span>
                <?php if (service('validation')->hasError('last_name')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('last_name')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- RFID Number and Email Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="rfid_number" class="form-label">RFID Number <span class="required-star">*</span></label>
                <input type="text" class="form-control" id="rfid_number" name="rfid_number"
                       placeholder="Enter RFID number" required value="<?= old('rfid_number') ?>">
                <?php if (service('validation')->hasError('rfid_number')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('rfid_number')) ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Email <span class="required-star">*</span></label>
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="Enter email address" required value="<?= old('email') ?>">
                <?php if (service('validation')->hasError('email')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('email')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Phone Row -->
        <div class="form-row full">
            <div class="form-group">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phone" name="phone"
                       placeholder="Enter phone number" value="<?= old('phone') ?>"
                       oninput="validateSpecialChars(this,'err_phone','phone')">
                <span id="err_phone" style="display:none;color:#dc3545;font-size:0.85rem;">Phone number can only contain digits and common separators.</span>
                <?php if (service('validation')->hasError('phone')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('phone')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Department Row -->
        <div class="form-row full">
            <div class="form-group">
                <label for="department_id" class="form-label">Department</label>
                <select class="form-control" id="department_id" name="department_id">
                    <option value="" <?= old('department_id') ? '' : 'selected' ?>>Select department</option>
                    <?php foreach (($departments ?? []) as $dept): ?>
                        <option value="<?= (int) $dept->id ?>" <?= (string) old('department_id') === (string) $dept->id ? 'selected' : '' ?>>
                            <?= esc($dept->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (service('validation')->hasError('department_id')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('department_id')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Position and Type Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="position" class="form-label">Position <span class="required-star">*</span></label>
                <select class="form-control" id="position" name="position" required>
                    <option value="" disabled <?= old('position') ? '' : 'selected' ?>>Select position</option>
                    <?php foreach ($employeePositionOptions as $option): ?>
                        <option value="<?= esc($option) ?>" <?= old('position') === $option ? 'selected' : '' ?>>
                            <?= esc($option) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="employee_type" class="form-label">Type <span class="required-star">*</span></label>
                <select class="form-control" id="employee_type" name="employee_type" required>
                    <option value="" disabled <?= old('employee_type') ? '' : 'selected' ?>>Select type</option>
                    <?php foreach ($employeeTypeOptions as $option): ?>
                        <option value="<?= esc($option) ?>" <?= old('employee_type') === $option ? 'selected' : '' ?>>
                            <?= esc($option) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Date of Birth and Date Hired Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="date_of_birth" class="form-label">Date of Birth <span class="required-star">*</span></label>
                <input type="date" class="form-control <?= service('validation')->hasError('date_of_birth') ? 'is-invalid' : '' ?>" id="date_of_birth" name="date_of_birth" 
                       required
                       value="<?= old('date_of_birth') ?>">
                <?php if (service('validation')->hasError('date_of_birth')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('date_of_birth')) ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="date_of_joining" class="form-label">Date of Hired <span class="required-star">*</span></label>
                <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" 
                       required value="<?= old('date_of_joining', date('Y-m-d')) ?>">
                <?php if (service('validation')->hasError('date_of_joining')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('date_of_joining')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Employment and Salary Row -->
        <div class="form-row">
            <div class="form-group">
                <label for="employment_type" class="form-label">Employment Type</label>
                <select class="form-control" id="employment_type" name="employment_type">
                    <option value="" <?= old('employment_type') ? '' : 'selected' ?>>Select employment type</option>
                    <option value="full_time" <?= old('employment_type') === 'full_time' ? 'selected' : '' ?>>Full-Time</option>
                    <option value="part_time" <?= old('employment_type') === 'part_time' ? 'selected' : '' ?>>Part-Time</option>
                    <option value="contractual" <?= old('employment_type') === 'contractual' ? 'selected' : '' ?>>Contractual</option>
                    <option value="probationary" <?= old('employment_type') === 'probationary' ? 'selected' : '' ?>>Probationary</option>
                </select>
                <?php if (service('validation')->hasError('employment_type')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('employment_type')) ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="rate" class="form-label">Rate</label>
                <input type="number" class="form-control" id="rate" name="rate" min="0" step="0.01" value="<?= old('rate') ?>" placeholder="0.00">
                <?php if (service('validation')->hasError('rate')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('rate')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-row full">
            <div class="form-group">
                <label for="rate_type" class="form-label">Rate Type</label>
                <select class="form-control" id="rate_type" name="rate_type">
                    <option value="" <?= old('rate_type') ? '' : 'selected' ?>>Select rate type</option>
                    <option value="hourly" <?= old('rate_type') === 'hourly' ? 'selected' : '' ?>>Hourly</option>
                    <option value="daily" <?= old('rate_type') === 'daily' ? 'selected' : '' ?>>Daily</option>
                    <option value="monthly" <?= old('rate_type') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                </select>
                <?php if (service('validation')->hasError('rate_type')): ?>
                    <div class="error-text"><?= esc(service('validation')->getError('rate_type')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Status -->
        <div class="form-row full">
            <div class="form-group">
                <label for="status" class="form-label">Status <span class="required-star">*</span></label>
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
    const phOk = validateSpecialChars(document.getElementById('phone'), 'err_phone', 'phone');
    const requiredFields = ['rfid_number', 'position', 'employee_type', 'date_of_birth', 'date_of_joining', 'status'];
    const missingRequired = requiredFields.some(function(fieldId) {
        const field = document.getElementById(fieldId);
        return !field || field.value.trim() === '';
    });
    if (!fnOk || !lnOk || !phOk || missingRequired) e.preventDefault();
});
</script>

<?= $this->endSection() ?>
