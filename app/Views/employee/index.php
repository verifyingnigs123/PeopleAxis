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
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 60, 114, 0.3);
    }

    .admin-panel {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .panel-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .panel-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.3rem;
    }

    .search-box {
        flex: 1;
        min-width: 200px;
    }

    .search-box input {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 0.9rem;
        width: 100%;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table thead th {
        background: #f8f9fa;
        color: #2c3e50;
        padding: 15px;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.9rem;
    }

    .admin-table tbody tr {
        border-bottom: 1px solid #dee2e6;
        transition: background 0.3s ease;
    }

    .admin-table tbody tr:hover {
        background: #f8f9fa;
    }

    .admin-table tbody td {
        padding: 15px;
        color: #495057;
        font-size: 0.95rem;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-view {
        background: #3498db;
        color: white;
    }

    .btn-view:hover {
        background: #2980b9;
    }

    .btn-edit {
        background: #27ae60;
        color: white;
    }

    .btn-edit:hover {
        background: #229954;
    }

    .btn-delete {
        background: #e74c3c;
        color: white;
    }

    .btn-delete:hover {
        background: #c0392b;
    }

    .btn-action i {
        font-size: 0.75rem;
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

    .badge-pending {
        background: #fff3cd;
        color: #856404;
    }

    .badge-account-active {
        background: #d4edda;
        color: #155724;
    }

    .badge-rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .btn-rejected {
        background: #e74c3c;
        color: white;
        cursor: default;
        opacity: 0.85;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #7f8c8d;
    }

    .empty-state i {
        font-size: 3rem;
        color: #bdc3c7;
        margin-bottom: 20px;
        display: block;
    }

    .empty-state p {
        font-size: 1.1rem;
        margin: 10px 0;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #155724;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border-left: 4px solid #721c24;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .btn-add {
            width: 100%;
            justify-content: center;
        }

        .search-box {
            width: 100%;
        }

        .admin-table {
            font-size: 0.85rem;
        }

        .admin-table thead th,
        .admin-table tbody td {
            padding: 10px;
        }

        .action-buttons {
            flex-direction: column;
            width: 100%;
        }

        .btn-action {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Employees</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <h1><i class="fas fa-users"></i> Employees</h1>
    <button class="btn-add" onclick="openAddEmployeeModal()">
        <i class="fas fa-plus-circle"></i> Add New Employee
    </button>
</div>

<!-- Alerts -->
<?php if (session()->has('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- Employees Table -->
<div class="admin-panel">
    <div class="panel-header">
        <h2><i class="fas fa-list"></i> Employee Records (<?= count($employees) ?>)</h2>
        <div class="search-box">
            <input type="text" id="employeeSearchInput" placeholder="Search employees..." oninput="blockSearchSpecialChars(this)" onkeyup="searchEmployees(this.value)">
            <div id="employeeSearchInputError" style="display:none;color:#dc3545;font-size:0.8rem;margin-top:4px;">Special characters are not allowed in the search.</div>
        </div>
    </div>

    <div class="table-responsive">
        <?php if (!empty($employees)): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $i => $employee): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= esc($employee->employee_id) ?></strong></td>
                            <td><?= esc(trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? ''))) ?></td>
                            <td><?= esc($employee->email ?? '') ?></td>
                            <td><?= esc($positionMap[$employee->position_id] ?? 'N/A') ?></td>
                            <td>
                                <?php
                                    $acctStatus = $employee->account_status ?? 'pending';
                                    $hasUserAccount = isset($userEmailSet[$employee->email]);
                                    if ($acctStatus === 'rejected') {
                                        $accountBadgeClass = 'badge-rejected';
                                        $accountBadgeText  = 'Rejected';
                                        $accountIcon       = 'fa-times-circle';
                                    } elseif ($hasUserAccount || $acctStatus === 'approved') {
                                        $accountBadgeClass = 'badge-account-active';
                                        $accountBadgeText  = 'Active';
                                        $accountIcon       = 'fa-user-check';
                                    } else {
                                        $accountBadgeClass = 'badge-pending';
                                        $accountBadgeText  = 'Pending';
                                        $accountIcon       = 'fa-user-clock';
                                    }
                                ?>
                                <span class="badge <?= $accountBadgeClass ?>">
                                    <i class="fas <?= $accountIcon ?>"></i> <?= $accountBadgeText ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= base_url('employee/show/' . $employee->id) ?>" class="btn-action btn-view" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <?php
                                        $currentRole     = session()->get('role_name') ?? session()->get('role');
                                        $isSuperAdminRow = in_array($currentRole, ['Super Admin', 'admin']);
                                        $isRejected      = ($employee->account_status ?? 'pending') === 'rejected';
                                    ?>
                                    <?php if ($isRejected): ?>
                                        <button class="btn-action btn-delete" title="Delete rejected employee"
                                            onclick="openDeleteModal(
                                                '<?= base_url('employee/delete/' . $employee->id) ?>',
                                                '<?= esc($employee->first_name . ' ' . $employee->last_name, 'js') ?>',
                                                true
                                            )">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    <?php else: ?>
                                        <?php if ($isSuperAdminRow): ?>
                                            <button class="btn-action btn-delete" title="Delete"
                                                onclick="openDeleteModal(
                                                    '<?= base_url('employee/delete/' . $employee->id) ?>',
                                                    '<?= esc($employee->first_name . ' ' . $employee->last_name, 'js') ?>',
                                                    false
                                                )">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        <?php endif; ?>
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
                <p>No employees found.</p>
                <button onclick="openAddEmployeeModal()" style="color: #2a5298; background: none; border: none; cursor: pointer; text-decoration: none; font-weight: 600;">
                    <i class="fas fa-plus"></i> Create the first employee
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="modal">
    <div class="modal-content" style="max-width:460px;">
        <div class="modal-header" style="background:linear-gradient(135deg,#c0392b 0%,#e74c3c 100%);">
            <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body" style="text-align:center; padding:35px 30px;">
            <div style="font-size:3rem; color:#e74c3c; margin-bottom:16px;">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3 id="deleteModalTitle" style="color:#2c3e50; margin:0 0 10px;"></h3>
            <p id="deleteModalMsg" style="color:#7f8c8d; margin:0 0 28px; font-size:.97rem;"></p>
            <form id="deleteConfirmForm" method="POST">
                <?= csrf_field() ?>
                <div style="display:flex; gap:12px; justify-content:center;">
                    <button type="submit" class="btn-modal btn-danger-modal">
                        <i class="fas fa-trash"></i> Yes, Delete
                    </button>
                    <button type="button" class="btn-modal btn-secondary" onclick="closeDeleteModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div id="addEmployeeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-user-plus"></i> Add New Employee</h2>
            <button class="modal-close" onclick="closeAddEmployeeModal()">&times;</button>
        </div>

        <div class="modal-body">
            <form id="addEmployeeForm" method="POST" action="<?= base_url('employee/store') ?>">
                <?= csrf_field() ?>

                <!-- Error Messages -->
                <div id="modalErrors" class="alert alert-danger" style="display: none; margin-bottom: 20px;">
                    <i class="fas fa-exclamation-circle"></i>
                    <div id="errorList"></div>
                </div>

                <!-- Success Message -->
                <div id="modalSuccess" class="alert alert-success" style="display: none; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i>
                    <div id="successMessage"></div>
                </div>

                <!-- First Name and Last Name Row -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label for="first_name" class="form-label">First Name <span class="required-star">*</span></label>
                        <input type="text" class="form-control-modal" id="first_name" name="first_name" 
                               placeholder="Enter first name" required
                               oninput="validateSpecialChars(this,'add_first_name_err','name')">
                        <span id="add_first_name_err" style="display:none;color:#dc3545;font-size:0.8rem;">First name cannot contain special characters.</span>
                    </div>
                    <div class="form-group-modal">
                        <label for="last_name" class="form-label">Last Name <span class="required-star">*</span></label>
                        <input type="text" class="form-control-modal" id="last_name" name="last_name" 
                               placeholder="Enter last name" required
                               oninput="validateSpecialChars(this,'add_last_name_err','name')">
                        <span id="add_last_name_err" style="display:none;color:#dc3545;font-size:0.8rem;">Last name cannot contain special characters.</span>
                    </div>
                </div>

                <!-- Email and Phone Row -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label for="email" class="form-label">Email <span class="required-star">*</span></label>
                        <input type="email" class="form-control-modal" id="email" name="email" 
                               placeholder="Enter email address" required>
                    </div>
                    <div class="form-group-modal">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control-modal" id="phone" name="phone" 
                               placeholder="Enter phone number"
                               oninput="validateSpecialChars(this,'add_phone_err','phone')">
                        <span id="add_phone_err" style="display:none;color:#dc3545;font-size:0.8rem;">Phone cannot contain special characters.</span>
                    </div>
                </div>

                <!-- Department and Position Row -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label for="department_id" class="form-label">Department</label>
                        <select class="form-control-modal" id="department_id" name="department_id">
                            <option value="">Select Department</option>
                            <?php foreach ($departments ?? [] as $dept): ?>
                                <option value="<?= $dept->id ?>">
                                    <?= esc($dept->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group-modal">
                        <label for="position_id" class="form-label">Position</label>
                        <select class="form-control-modal" id="position_id" name="position_id">
                            <option value="">Select Position</option>
                            <?php foreach ($positions ?? [] as $pos): ?>
                                <option value="<?= $pos->id ?>">
                                    <?= esc($pos->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Date of Birth and Date of Joining Row -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control-modal" id="date_of_birth" name="date_of_birth">
                    </div>
                    <div class="form-group-modal">
                        <label for="date_of_joining" class="form-label">Date of Joining <span class="required-star">*</span></label>
                        <input type="date" class="form-control-modal" id="date_of_joining" name="date_of_joining" required>
                    </div>
                </div>

                <!-- Status -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control-modal" id="status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="modal-actions">
                    <button type="submit" class="btn-modal btn-primary">
                        <i class="fas fa-save"></i> Add Employee
                    </button>
                    <button type="button" class="btn-modal btn-secondary" onclick="closeAddEmployeeModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Employee Modal -->
<div id="editEmployeeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header" style="background: linear-gradient(135deg,#1a6b39 0%,#27ae60 100%);">
            <h2><i class="fas fa-user-edit"></i> Edit Employee — <span id="editModalName"></span></h2>
            <button class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Error Messages -->
            <div id="editErrors" class="alert alert-danger" style="display:none; margin-bottom:20px;">
                <i class="fas fa-exclamation-circle"></i>
                <div id="editErrorList"></div>
            </div>
            <!-- Success Message -->
            <div id="editSuccess" class="alert alert-success" style="display:none; margin-bottom:20px;">
                <i class="fas fa-check-circle"></i>
                <div id="editSuccessMsg"></div>
            </div>
            <form id="editEmployeeForm">
                <?= csrf_field() ?>
                <!-- First & Last Name -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label class="form-label">First Name <span class="required-star">*</span></label>
                        <input type="text" class="form-control-modal" id="edit_first_name" name="first_name" placeholder="Enter first name" required
                               oninput="validateSpecialChars(this,'edit_first_name_err','name')">
                        <span id="edit_first_name_err" style="display:none;color:#dc3545;font-size:0.8rem;">First name cannot contain special characters.</span>
                    </div>
                    <div class="form-group-modal">
                        <label class="form-label">Last Name <span class="required-star">*</span></label>
                        <input type="text" class="form-control-modal" id="edit_last_name" name="last_name" placeholder="Enter last name" required
                               oninput="validateSpecialChars(this,'edit_last_name_err','name')">
                        <span id="edit_last_name_err" style="display:none;color:#dc3545;font-size:0.8rem;">Last name cannot contain special characters.</span>
                    </div>
                </div>
                <!-- Email & Phone -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label class="form-label">Email <span class="required-star">*</span></label>
                        <input type="email" class="form-control-modal" id="edit_email" name="email" placeholder="Enter email address" required>
                    </div>
                    <div class="form-group-modal">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control-modal" id="edit_phone" name="phone" placeholder="Enter phone number"
                               oninput="validateSpecialChars(this,'edit_phone_err','phone')">
                        <span id="edit_phone_err" style="display:none;color:#dc3545;font-size:0.8rem;">Phone cannot contain special characters.</span>
                    </div>
                </div>
                <!-- Department & Position -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label class="form-label">Department</label>
                        <select class="form-control-modal" id="edit_department_id" name="department_id">
                            <option value="">Select Department</option>
                            <?php foreach ($departments ?? [] as $dept): ?>
                                <option value="<?= $dept->id ?>"><?= esc($dept->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group-modal">
                        <label class="form-label">Position</label>
                        <select class="form-control-modal" id="edit_position_id" name="position_id">
                            <option value="">Select Position</option>
                            <?php foreach ($positions ?? [] as $pos): ?>
                                <option value="<?= $pos->id ?>"><?= esc($pos->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <!-- DOB & Date of Joining -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control-modal" id="edit_date_of_birth" name="date_of_birth">
                    </div>
                    <div class="form-group-modal">
                        <label class="form-label">Date of Joining <span class="required-star">*</span></label>
                        <input type="date" class="form-control-modal" id="edit_date_of_joining" name="date_of_joining" required>
                    </div>
                </div>
                <!-- Status -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label class="form-label">Status</label>
                        <select class="form-control-modal" id="edit_status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                <!-- Actions -->
                <div class="modal-actions">
                    <button type="submit" class="btn-modal btn-primary" style="background:linear-gradient(135deg,#1a6b39 0%,#27ae60 100%)">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <button type="button" class="btn-modal btn-secondary" onclick="closeEditModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: white;
        border-radius: 8px;
        width: 90%;
        max-width: 700px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 25px;
        border-bottom: 2px solid #e8eef5;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
    }

    .modal-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.3rem;
    }

    .modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        padding: 0;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background 0.3s ease;
    }

    .modal-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .modal-body {
        padding: 30px;
    }

    .form-row-modal {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group-modal {
        margin-bottom: 0;
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

    .form-control-modal {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d5dce0;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: border-color 0.3s ease;
        box-sizing: border-box;
        font-family: inherit;
    }

    .form-control-modal:focus {
        outline: none;
        border-color: #2a5298;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e8eef5;
    }

    .btn-modal {
        padding: 10px 25px;
        border: none;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-modal.btn-primary {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
    }

    .btn-modal.btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 60, 114, 0.3);
    }

    .btn-modal.btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .btn-modal.btn-secondary:hover {
        background: #7f8c8d;
    }

    .btn-danger-modal {
        background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
        color: white;
    }
    .btn-danger-modal:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(192,57,43,0.35);
    }

    @media (max-width: 600px) {
        .modal-content {
            width: 95%;
            max-height: 95vh;
        }

        .form-row-modal {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .modal-actions {
            flex-direction: column;
        }

        .btn-modal {
            width: 100%;
            justify-content: center;
        }

        .modal-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .modal-close {
            align-self: flex-end;
        }
    }
</style>

<script>
    // ── Delete Confirmation Modal ──
    function openDeleteModal(action, name, isRejected) {
        const modal = document.getElementById('deleteConfirmModal');
        document.getElementById('deleteConfirmForm').action = action;
        document.getElementById('deleteModalTitle').textContent = 'Delete "' + name + '"?';
        document.getElementById('deleteModalMsg').textContent = isRejected
            ? 'This employee application was rejected. Deleting will permanently remove their record. This cannot be undone.'
            : 'Are you sure you want to permanently delete this employee? This action cannot be undone.';
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeDeleteModal() {
        document.getElementById('deleteConfirmModal').classList.remove('show');
        document.body.style.overflow = 'auto';
    }
    document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    function openAddEmployeeModal() {
        const modal = document.getElementById('addEmployeeModal');
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeAddEmployeeModal() {
        const modal = document.getElementById('addEmployeeModal');
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
        document.getElementById('addEmployeeForm').reset();
        document.getElementById('modalErrors').style.display = 'none';
        // Clear search input
        document.getElementById('employeeSearchInput').value = '';
        // Show all rows again
        document.querySelectorAll('.admin-table tbody tr').forEach(row => {
            row.style.display = '';
        });
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const addModal  = document.getElementById('addEmployeeModal');
        const editModal = document.getElementById('editEmployeeModal');
        if (event.target === addModal)  closeAddEmployeeModal();
        if (event.target === editModal) closeEditModal();
    };

    // Close modals on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDeleteModal();
            closeAddEmployeeModal();
            closeEditModal();
        }
    });

    // ── Edit Employee Modal ────────────────────────────────────
    let currentEditId = null;

    function openEditModal(id) {
        currentEditId = id;
        const modal = document.getElementById('editEmployeeModal');
        document.getElementById('editErrors').style.display   = 'none';
        document.getElementById('editSuccess').style.display  = 'none';
        document.getElementById('editModalName').textContent  = 'Loading…';

        fetch('<?= base_url('employee/get/') ?>' + id, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) { alert('Could not load employee data.'); return; }
            const e = data.employee;
            document.getElementById('editModalName').textContent  = (e.first_name ?? '') + ' ' + (e.last_name ?? '');
            document.getElementById('edit_first_name').value      = e.first_name    ?? '';
            document.getElementById('edit_last_name').value       = e.last_name     ?? '';
            document.getElementById('edit_email').value           = e.email         ?? '';
            document.getElementById('edit_phone').value           = e.phone         ?? '';
            document.getElementById('edit_date_of_birth').value   = e.date_of_birth ?? '';
            document.getElementById('edit_date_of_joining').value = e.date_of_joining ?? '';
            document.getElementById('edit_department_id').value   = e.department_id ?? '';
            document.getElementById('edit_position_id').value     = e.position_id   ?? '';
            document.getElementById('edit_status').value          = e.status        ?? 'active';
        })
        .catch(() => alert('Failed to fetch employee data.'));

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        document.getElementById('editEmployeeModal').classList.remove('show');
        document.body.style.overflow = 'auto';
        document.getElementById('editEmployeeForm').reset();
        document.getElementById('editErrors').style.display  = 'none';
        document.getElementById('editSuccess').style.display = 'none';
        currentEditId = null;
    }

    // Shared special-character validator for form fields
    function validateSpecialChars(input, errId, type) {
        const namePattern  = /^[A-Za-z\s\-\']*$/;
        const phonePattern = /^[0-9\s\+\-\(\)]*$/;
        const pattern = (type === 'phone') ? phonePattern : namePattern;
        const errSpan = document.getElementById(errId);
        if (!errSpan) return true;
        if (input.value !== '' && !pattern.test(input.value)) {
            errSpan.style.display = 'block';
            input.classList.add('is-invalid');
            return false;
        } else {
            errSpan.style.display = 'none';
            input.classList.remove('is-invalid');
            return true;
        }
    }

    document.getElementById('editEmployeeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (!currentEditId) return;
        // Validate before submit
        const fnOk = validateSpecialChars(document.getElementById('edit_first_name'), 'edit_first_name_err', 'name');
        const lnOk = validateSpecialChars(document.getElementById('edit_last_name'),  'edit_last_name_err',  'name');
        const phOk = validateSpecialChars(document.getElementById('edit_phone'),       'edit_phone_err',      'phone');
        if (!fnOk || !lnOk || !phOk) return;

        const formData = new FormData(this);
        document.getElementById('editErrors').style.display  = 'none';
        document.getElementById('editSuccess').style.display = 'none';

        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';

        fetch('<?= base_url('employee/update/') ?>' + currentEditId, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json().then(data => ({ status: r.status, data })))
        .then(({ status, data }) => {
            // Refresh CSRF
            if (data.csrf_hash) {
                document.querySelectorAll('#editEmployeeForm input[name="<?= csrf_token() ?>"]').forEach(el => el.value = data.csrf_hash);
                document.querySelectorAll('#addEmployeeForm input[name="<?= csrf_token() ?>"]').forEach(el => el.value = data.csrf_hash);
            }
            if (data.success) {
                const successDiv = document.getElementById('editSuccess');
                const successMsg = document.getElementById('editSuccessMsg');
                successDiv.style.display = 'flex';
                successMsg.innerHTML = '<strong>Employee updated successfully!</strong>';
                setTimeout(() => { window.location.reload(); }, 1200);
            } else {
                const errDiv  = document.getElementById('editErrors');
                const errList = document.getElementById('editErrorList');
                errDiv.style.display = 'flex';
                if (data.errors) {
                    let html = '<strong>Validation Errors:</strong><ul style="margin:10px 0 0 0;padding-left:20px;">';
                    for (const [, msg] of Object.entries(data.errors)) html += `<li>${msg}</li>`;
                    html += '</ul>';
                    errList.innerHTML = html;
                } else {
                    errList.innerHTML = `<strong>${data.message ?? 'An error occurred.'}</strong>`;
                }
            }
        })
        .catch(() => {
            const errDiv = document.getElementById('editErrors');
            document.getElementById('editErrorList').innerHTML = '<strong>Network error. Please try again.</strong>';
            errDiv.style.display = 'flex';
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
        });
    });

    // Handle form submission
    document.getElementById('addEmployeeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // Validate before submit
        const fnOk = validateSpecialChars(document.getElementById('first_name'), 'add_first_name_err', 'name');
        const lnOk = validateSpecialChars(document.getElementById('last_name'),  'add_last_name_err',  'name');
        const phOk = validateSpecialChars(document.getElementById('phone'),      'add_phone_err',      'phone');
        if (!fnOk || !lnOk || !phOk) return;
        
        const formData = new FormData(this);
        const errorDiv = document.getElementById('modalErrors');
        const errorList = document.getElementById('errorList');
        const successDiv = document.getElementById('modalSuccess');
        const successMessage = document.getElementById('successMessage');
        
        // Hide previous messages
        errorDiv.style.display = 'none';
        successDiv.style.display = 'none';
        
        fetch('<?= base_url('employee/store') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.status === 422) {
                return response.json().then(data => {
                    throw {isValidationError: true, errors: data.errors};
                });
            }
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message with auto-generated ID
                successDiv.style.display = 'flex';
                successMessage.innerHTML = `
                    <strong>Employee Created Successfully!</strong>
                    <p style="margin: 8px 0 0 0;">
                        <strong>Employee ID:</strong> ${data.employee_id}
                    </p>
                `;
                
                // Reset form
                document.getElementById('addEmployeeForm').reset();
                
                // Reload page after 2 seconds
                setTimeout(() => {
                    window.location.href = '<?= base_url('employee') ?>';
                }, 2000);
            }
        })
        .catch(error => {
            errorDiv.style.display = 'flex';
            if (error.isValidationError) {
                // Display validation errors
                let errorHtml = '<strong>Validation Errors:</strong><ul style="margin: 10px 0 0 0; padding-left: 20px;">';
                for (const [field, messages] of Object.entries(error.errors)) {
                    if (Array.isArray(messages)) {
                        messages.forEach(msg => {
                            errorHtml += `<li>${msg}</li>`;
                        });
                    } else {
                        errorHtml += `<li>${messages}</li>`;
                    }
                }
                errorHtml += '</ul>';
                errorList.innerHTML = errorHtml;
            } else {
                errorList.innerHTML = '<strong>An error occurred</strong><p>Please try again</p>';
            }
        });
    });

    function blockSearchSpecialChars(input) {
        const SEARCH_ALLOWED = /^[a-zA-Z0-9\s@._\-]*$/;
        const errorDiv = document.getElementById(input.id + 'Error');
        if (!SEARCH_ALLOWED.test(input.value)) {
            input.value = input.value.replace(/[^a-zA-Z0-9\s@._\-]/g, '');
            if (errorDiv) {
                errorDiv.style.display = 'block';
                clearTimeout(input._errTimer);
                input._errTimer = setTimeout(() => { errorDiv.style.display = 'none'; }, 3000);
            }
        } else {
            if (errorDiv) errorDiv.style.display = 'none';
        }
    }

    // Search function
    function searchEmployees(searchValue) {
        const searchInput = searchValue.toLowerCase().trim();
        const tableRows = document.querySelectorAll('.admin-table tbody tr');
        let visibleCount = 0;

        tableRows.forEach(row => {
            // Get text content from each cell
            const cells = row.querySelectorAll('td');
            let rowText = '';
            
            // Combine text from all cells (skip the first # column)
            for (let i = 1; i < cells.length - 1; i++) {
                rowText += cells[i].textContent.toLowerCase() + ' ';
            }

            // Check if search term matches
            if (searchInput === '' || rowText.includes(searchInput)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>

<?= $this->endSection() ?>
