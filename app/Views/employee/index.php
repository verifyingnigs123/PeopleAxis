<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .hr-shell {
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

    .admin-header {
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

    .admin-header h1 {
        color: #2f5f45;
        font-weight: 700;
        margin: 0;
        font-size: 2rem;
        line-height: 1;
    }

    .admin-header p {
        color: #6f8192;
        margin: 6px 0 0;
        font-size: 0.92rem;
    }

    .btn-add {
        background: #6ea988;
        color: #ffffff;
        border: 1px solid #6ea988;
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease, border-color 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        line-height: 1;
    }

    .btn-add:hover {
        background: #21437c;
        border-color: #21437c;
    }

    .admin-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
        margin-bottom: 14px;
    }

    .stat-box {
        background: #ffffff;
        padding: 16px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stat-box i {
        font-size: 1.2rem;
        color: #6ea988;
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eef3ff;
        border: 1px solid #d8e4f7;
        border-radius: 8px;
        flex-shrink: 0;
    }

    .stat-box.warning i {
        color: #9d6108;
        background: #fff5e6;
        border-color: #f0d5ab;
    }

    .stat-box.danger i {
        color: #a43737;
        background: #fef0f0;
        border-color: #f3cccc;
    }

    .stat-info h5 {
        margin: 0;
        color: #7f90a0;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.35px;
    }

    .stat-info h3 {
        margin: 6px 0 0;
        color: #2f5f45;
        font-weight: 700;
        font-size: 1.55rem;
        line-height: 1.1;
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

    .admin-panel {
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        margin-bottom: 14px;
    }

    .panel-header {
        background: #ffffff;
        color: #1f3550;
        padding: 14px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        border-bottom: 1px solid #e7edf4;
    }

    .panel-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .search-box {
        margin-left: auto;
        width: 100%;
        max-width: 360px;
    }

    .search-box input {
        border: 1px solid #d9e2ec;
        border-radius: 8px;
        padding: 9px 11px;
        font-size: 0.9rem;
        width: 100%;
        outline: none;
    }

    .search-box input:focus {
        border-color: #6ea988;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .admin-table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
    }

    .admin-table thead th {
        background: #f7f9fc;
        color: #445b72;
        padding: 12px 16px;
        text-align: left;
        font-weight: 700;
        border-bottom: 1px solid #e1e9f2;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.35px;
        white-space: nowrap;
    }

    .admin-table tbody tr {
        border-bottom: 1px solid #edf2f7;
    }

    .admin-table tbody tr:hover {
        background: #fbfdff;
    }

    .admin-table tbody td {
        padding: 12px 16px;
        color: #42586e;
        font-size: 0.88rem;
        vertical-align: middle;
    }

    .action-buttons {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 6px 10px;
        border: 1px solid transparent;
        border-radius: 7px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease, border-color 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        line-height: 1.1;
    }

    .btn-view {
        background: #e9f3ff;
        color: #1f5ea8;
        border-color: #c9dcf6;
    }

    .btn-view:hover {
        background: #dbeafd;
        border-color: #b8d2f2;
    }

    .btn-edit {
        background: #e9f7ec;
        color: #1d7a3f;
        border-color: #cfead8;
    }

    .btn-edit:hover {
        background: #ddf1e2;
        border-color: #bfe2cb;
    }

    .btn-delete {
        background: #feecec;
        color: #ba3939;
        border-color: #f4cccc;
    }

    .btn-delete:hover {
        background: #fbdcdc;
        border-color: #efbbbb;
    }

    .btn-action i {
        font-size: 0.72rem;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .badge-active,
    .badge-account-active {
        background: #e9f7ec;
        color: #1d7a3f;
        border-color: #cfead8;
    }

    .badge-inactive,
    .badge-rejected {
        background: #fdecec;
        color: #b43a3a;
        border-color: #f4d3d3;
    }

    .badge-suspended,
    .badge-pending {
        background: #fff4e4;
        color: #9f6310;
        border-color: #f1debb;
    }

    .btn-rejected {
        background: #fef0f0;
        color: #a43737;
        border-color: #f3cccc;
        cursor: default;
    }

    .empty-state {
        text-align: center;
        padding: 54px 20px;
        color: #7f90a0;
    }

    .empty-state i {
        font-size: 2.6rem;
        color: #adb9c5;
        margin-bottom: 12px;
        display: block;
    }

    .empty-state p {
        font-size: 0.98rem;
        margin: 8px 0;
    }

    @media (max-width: 992px) {
        .search-box {
            margin-left: 0;
            max-width: none;
        }

        .admin-table thead th,
        .admin-table tbody td {
            padding: 11px 12px;
        }
    }

    @media (max-width: 768px) {
        .admin-header {
            padding: 14px;
        }

        .admin-header h1 {
            font-size: 1.55rem;
        }

        .btn-add {
            width: 100%;
            justify-content: center;
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

<div class="hr-shell">

<?php
    $totalEmployees = count($employees ?? []);
    $activeEmployees = 0;
    $pendingEmployees = 0;
    $rejectedEmployees = 0;

    foreach (($employees ?? []) as $employeeSummary) {
        $summaryAccountStatus = strtolower((string) ($employeeSummary->account_status ?? 'pending'));
        $summaryEmployeeStatus = strtolower((string) ($employeeSummary->status ?? 'inactive'));

        if ($summaryAccountStatus === 'pending') {
            $pendingEmployees++;
        } elseif ($summaryAccountStatus === 'rejected') {
            $rejectedEmployees++;
        } elseif ($summaryEmployeeStatus === 'active') {
            $activeEmployees++;
        }
    }
?>

<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
    <span>/</span>
    <span>Employees</span>
</div>

<!-- Page Header -->
<div class="admin-header">
    <div>
        <h1><i class="fas fa-users"></i> Employees</h1>
        <p>Operational overview for employee records, approval status, and workforce updates</p>
    </div>
    <button class="btn-add" onclick="openAddEmployeeModal()">
        <i class="fas fa-plus-circle"></i> Add New Employee
    </button>
</div>

<div class="admin-stats">
    <div class="stat-box">
        <i class="fas fa-id-badge"></i>
        <div class="stat-info">
            <h5>Total Employees</h5>
            <h3><?= $totalEmployees ?></h3>
        </div>
    </div>
    <div class="stat-box">
        <i class="fas fa-user-check"></i>
        <div class="stat-info">
            <h5>Active</h5>
            <h3><?= $activeEmployees ?></h3>
        </div>
    </div>
    <div class="stat-box warning">
        <i class="fas fa-user-clock"></i>
        <div class="stat-info">
            <h5>Pending Approval</h5>
            <h3><?= $pendingEmployees ?></h3>
        </div>
    </div>
    <div class="stat-box danger">
        <i class="fas fa-user-times"></i>
        <div class="stat-info">
            <h5>Rejected</h5>
            <h3><?= $rejectedEmployees ?></h3>
        </div>
    </div>
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
        <h2><i class="fas fa-list"></i> Employee Records (<?= $totalEmployees ?>)</h2>
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
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $i => $employee): ?>
                        <tr>
                            <td><strong><?= esc($employee->employee_id) ?></strong></td>
                            <td><?= esc(trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? ''))) ?></td>
                            <td><?= esc($employee->email ?? '') ?></td>
                            <td><?= esc($departmentMap[$employee->department_id] ?? 'N/A') ?></td>
                            <td><?= esc($employee->user_role_name ?? 'N/A') ?></td>
                            <td>
                                <?php
                                    $acctStatus = strtolower($employee->account_status ?? 'pending');
                                    $empStatus  = strtolower($employee->status ?? 'inactive');
                                    if ($acctStatus === 'pending') {
                                        $statusBadgeClass = 'badge-pending';
                                        $statusBadgeText  = 'Pending Approval';
                                        $statusIcon       = 'fa-user-clock';
                                    } elseif ($acctStatus === 'rejected') {
                                        $statusBadgeClass = 'badge-rejected';
                                        $statusBadgeText  = 'Rejected';
                                        $statusIcon       = 'fa-times-circle';
                                    } elseif ($empStatus === 'inactive') {
                                        $statusBadgeClass = 'badge-inactive';
                                        $statusBadgeText  = 'Inactive';
                                        $statusIcon       = 'fa-user-slash';
                                    } elseif ($empStatus === 'suspended') {
                                        $statusBadgeClass = 'badge-suspended';
                                        $statusBadgeText  = 'Suspended';
                                        $statusIcon       = 'fa-user-lock';
                                    } else {
                                        $statusBadgeClass = 'badge-account-active';
                                        $statusBadgeText  = 'Active';
                                        $statusIcon       = 'fa-user-check';
                                    }
                                ?>
                                <span class="badge <?= $statusBadgeClass ?>">
                                    <i class="fas <?= $statusIcon ?>"></i> <?= $statusBadgeText ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= base_url('employee/show/' . $employee->id) ?>" class="btn-action btn-view" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <?php
                                        $currentRole  = session()->get('role_name') ?? session()->get('role');
                                        $isSuperAdmin = in_array($currentRole, ['Super Admin', 'admin']);
                                        $isHRAdmin    = in_array($currentRole, ['HR Admin', 'hr']);
                                        $isRejected   = ($employee->account_status ?? 'pending') === 'rejected';
                                        $empFullName  = esc($employee->first_name . ' ' . $employee->last_name, 'js');
                                        $deleteUrl    = base_url('employee/delete/' . $employee->id);
                                        $requestUrl   = base_url('employee/request-delete/' . $employee->id);
                                    ?>
                                    <?php if ($isRejected): ?>
                                        <!-- Rejected: both SA and HR Admin can directly delete -->
                                        <button class="btn-action btn-delete" title="Delete rejected employee"
                                            onclick="openDeleteModal('<?= $deleteUrl ?>', '<?= $empFullName ?>', true, false)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    <?php elseif ($isSuperAdmin): ?>
                                        <!-- Super Admin: actual permanent delete -->
                                        <button class="btn-action btn-delete" title="Delete"
                                            onclick="openDeleteModal('<?= $deleteUrl ?>', '<?= $empFullName ?>', false, false)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    <?php elseif ($isHRAdmin): ?>
                                        <!-- HR Admin: request deletion (notify Super Admin) -->
                                        <button class="btn-action btn-delete" title="Request Deletion"
                                            onclick="openDeleteModal('<?= $requestUrl ?>', '<?= $empFullName ?>', false, true)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
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
                <button onclick="openAddEmployeeModal()" style="color: #6ea988; background: none; border: none; cursor: pointer; text-decoration: none; font-weight: 600;">
                    <i class="fas fa-plus"></i> Create the first employee
                </button>
            </div>
        <?php endif; ?>
    </div>
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
                    <button type="submit" id="deleteModalSubmitBtn" class="btn-modal btn-danger-modal">
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
    <div class="modal-content add-employee-modal-content">
        <div class="modal-header add-employee-modal-header">
            <h2><i class="fas fa-user-plus"></i> Add New Employee</h2>
            <button class="modal-close" onclick="closeAddEmployeeModal()">&times;</button>
        </div>

        <div class="modal-body add-employee-modal-body">
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

                <?php
                $employeeTypeOptions = ['Manager', 'Employee'];
                ?>

                <!-- First Name and Last Name Row -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label for="first_name" class="form-label">First Name <span class="required-star">*</span></label>
                        <input type="text" class="form-control-modal" id="first_name" name="first_name" 
                               placeholder="Enter first name" required value="<?= esc(old('first_name') ?? '') ?>"
                               oninput="validateSpecialChars(this,'add_first_name_err','name')">
                        <span id="add_first_name_err" style="display:none;color:#dc3545;font-size:0.8rem;">First name cannot contain special characters.</span>
                    </div>
                    <div class="form-group-modal">
                        <label for="last_name" class="form-label">Last Name <span class="required-star">*</span></label>
                        <input type="text" class="form-control-modal" id="last_name" name="last_name" 
                               placeholder="Enter last name" required value="<?= esc(old('last_name') ?? '') ?>"
                               oninput="validateSpecialChars(this,'add_last_name_err','name')">
                        <span id="add_last_name_err" style="display:none;color:#dc3545;font-size:0.8rem;">Last name cannot contain special characters.</span>
                    </div>
                </div>

                <!-- RFID Number and Email Row -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label for="rfid_number" class="form-label">RFID Number <span class="required-star">*</span></label>
                        <input type="text" class="form-control-modal" id="rfid_number" name="rfid_number"
                               placeholder="Enter RFID number" required value="<?= esc(old('rfid_number') ?? '') ?>">
                    </div>
                    <div class="form-group-modal">
                        <label for="email" class="form-label">Email <span class="required-star">*</span></label>
                        <input type="email" class="form-control-modal" id="email" name="email" 
                               placeholder="Enter email address" required value="<?= esc(old('email') ?? '') ?>">
                    </div>
                </div>

                <!-- Phone Row -->
                <div class="form-row-modal full-width-row">
                    <div class="form-group-modal">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control-modal" id="phone" name="phone"
                               placeholder="Enter phone number" value="<?= esc(old('phone') ?? '') ?>"
                               oninput="validateSpecialChars(this,'add_phone_err','phone')">
                        <span id="add_phone_err" style="display:none;color:#dc3545;font-size:0.8rem;"></span>
                    </div>
                </div>

                <!-- Department Row -->
                <div class="form-row-modal full-width-row">
                    <div class="form-group-modal">
                        <label for="department_id" class="form-label">Department</label>
                        <select class="form-control-modal" id="department_id" name="department_id">
                            <option value="" <?= old('department_id') ? '' : 'selected' ?>>Select department</option>
                            <?php foreach (($departments ?? []) as $dept): ?>
                                <?php
                                $deptId = is_object($dept) ? ($dept->id ?? '') : ($dept['id'] ?? '');
                                $deptName = is_object($dept) ? ($dept->name ?? '') : ($dept['name'] ?? '');
                                ?>
                                <option value="<?= (int) $deptId ?>" <?= (string) old('department_id') === (string) $deptId ? 'selected' : '' ?>>
                                    <?= esc($deptName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Type Row -->
                <div class="form-row-modal full-width-row">
                    <div class="form-group-modal">
                        <label for="employee_type" class="form-label">Type <span class="required-star">*</span></label>
                        <select class="form-control-modal" id="employee_type" name="employee_type" required>
                            <option value="" disabled <?= old('employee_type') ? '' : 'selected' ?>>Select type</option>
                            <?php foreach ($employeeTypeOptions as $option): ?>
                                <option value="<?= esc($option) ?>" <?= old('employee_type') === $option ? 'selected' : '' ?>><?= esc($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Date of Birth and Date Hired Row -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label for="date_of_birth" class="form-label">Date of Birth <span class="required-star">*</span></label>
                        <input type="date" class="form-control-modal" id="date_of_birth" name="date_of_birth" required value="<?= esc(old('date_of_birth') ?? '') ?>">
                    </div>
                    <div class="form-group-modal">
                        <label for="date_of_joining" class="form-label">Date of Hired <span class="required-star">*</span></label>
                        <input type="date" class="form-control-modal" id="date_of_joining" name="date_of_joining" required value="<?= esc(old('date_of_joining') ?? date('Y-m-d')) ?>">
                    </div>
                </div>

                <!-- Employment and Salary Row -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label for="add_employment_type" class="form-label">Employment Type</label>
                        <select class="form-control-modal" id="add_employment_type" name="employment_type">
                            <option value="" selected>Select employment type</option>
                            <option value="full_time">Full-Time</option>
                            <option value="part_time">Part-Time</option>
                            <option value="contractual">Contractual</option>
                            <option value="probationary">Probationary</option>
                        </select>
                    </div>
                    <div class="form-group-modal">
                        <label for="add_rate" class="form-label">Rate</label>
                        <input type="number" class="form-control-modal" id="add_rate" name="rate" placeholder="0.00" min="0" step="0.01">
                    </div>
                </div>

                <div class="form-row-modal full-width-row">
                    <div class="form-group-modal">
                        <label for="add_rate_type" class="form-label">Rate Type</label>
                        <select class="form-control-modal" id="add_rate_type" name="rate_type">
                            <option value="" selected>Select rate type</option>
                            <option value="hourly">Hourly</option>
                            <option value="daily">Daily</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </div>

                <!-- Status Row -->
                <div class="form-row-modal full-width-row">
                    <div class="form-group-modal">
                        <label for="status" class="form-label">Status <span class="required-star">*</span></label>
                        <select class="form-control-modal" id="status" name="status" required>
                            <option value="active" <?= old('status', 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            <option value="suspended" <?= old('status') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
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
                        <input type="tel" class="form-control-modal" id="edit_phone" name="phone" placeholder="09xxxxxxxxx or +639xxxxxxxxx" maxlength="15"
                               oninput="validatePhilippinePhoneEdit(this,'edit_phone_err')">
                        <span id="edit_phone_err" style="display:none;color:#dc3545;font-size:0.8rem;"></span>
                        <div style="font-size:0.75rem;color:#7f8c8d;margin-top:4px;">
                            <i class="fas fa-info-circle"></i> Format: 09XXXXXXXXX (11 digits) or +639XXXXXXXXX (13 chars)
                        </div>
                    </div>
                </div>
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label class="form-label">RFID Number <span class="required-star">*</span></label>
                        <input type="text" class="form-control-modal" id="edit_rfid_number" name="rfid_number" placeholder="Scan or enter RFID number" required>
                    </div>
                </div>
                <!-- RFID Number and Department -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label class="form-label">Department</label>
                        <select class="form-control-modal" id="edit_department_id" name="department_id">
                            <option value="">Select Department</option>
                            <?php foreach ($departments ?? [] as $dept): ?>
                                <option value="<?= $dept->id ?>"><?= esc($dept->name ?? 'N/A') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <!-- Position and Employment Type -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label class="form-label">Position</label>
                        <select class="form-control-modal" id="edit_role_id" name="role_id">
                            <option value="">Select Position</option>
                            <?php foreach ($positions ?? [] as $pos): ?>
                                <option value="<?= $pos->id ?>"><?= esc($pos->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group-modal">
                        <label class="form-label">Employment Type</label>
                        <select class="form-control-modal" id="edit_employment_type" name="employment_type">
                            <option value="">Select Type</option>
                            <option value="full_time">Full-Time</option>
                            <option value="part_time">Part-Time</option>
                            <option value="contractual">Contractual</option>
                            <option value="probationary">Probationary</option>
                        </select>
                    </div>
                </div>
                <!-- DOB & Date Hired -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control-modal" id="edit_date_of_birth" name="date_of_birth">
                    </div>
                    <div class="form-group-modal">
                        <label class="form-label">Date Hired <span class="required-star">*</span></label>
                        <input type="date" class="form-control-modal" id="edit_date_of_joining" name="date_of_joining" required>
                    </div>
                </div>
                <!-- Salary Rate & Rate Type -->
                <div class="form-row-modal">
                    <div class="form-group-modal">
                        <label class="form-label">Salary Rate</label>
                        <input type="number" class="form-control-modal" id="edit_rate" name="rate"
                               placeholder="0.00" min="0" step="0.01">
                    </div>
                    <div class="form-group-modal">
                        <label class="form-label">Rate Type</label>
                        <select class="form-control-modal" id="edit_rate_type" name="rate_type">
                            <option value="">Select Rate Type</option>
                            <option value="hourly">Hourly</option>
                            <option value="daily">Daily</option>
                            <option value="monthly">Monthly</option>
                        </select>
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
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
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
        border-color: #6ea988;
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
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
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

    /* Add Employee modal styling to match HR admin target layout */
    #addEmployeeModal .add-employee-modal-content {
        max-width: 760px;
        border-radius: 8px;
    }

    #addEmployeeModal .add-employee-modal-header {
        padding: 22px 26px;
        border-bottom: none;
        background: linear-gradient(135deg, #3e7c5f 0%, #5e9e7f 100%);
    }

    #addEmployeeModal .add-employee-modal-header h2 {
        font-size: 2rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    #addEmployeeModal .add-employee-modal-body {
        padding: 26px 28px 20px;
        background: #f4f5f7;
    }

    #addEmployeeModal .form-row-modal {
        gap: 22px;
        margin-bottom: 18px;
    }

    #addEmployeeModal .full-width-row {
        grid-template-columns: 1fr;
    }

    #addEmployeeModal .form-label {
        margin-bottom: 10px;
        color: #21374b;
        font-size: 1rem;
        font-weight: 700;
    }

    #addEmployeeModal .required-star {
        color: #e75b69;
        margin-left: 5px;
    }

    #addEmployeeModal .form-control-modal {
        height: 50px;
        padding: 0 14px;
        border: 1px solid #ced7df;
        border-radius: 8px;
        background: #ffffff;
        font-size: 1.02rem;
    }

    #addEmployeeModal select.form-control-modal {
        padding-right: 36px;
    }

    #addEmployeeModal .modal-actions {
        margin-top: 14px;
        padding-top: 20px;
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
    function openDeleteModal(action, name, isRejected, isRequest) {
        const modal     = document.getElementById('deleteConfirmModal');
        const submitBtn = document.getElementById('deleteModalSubmitBtn');
        document.getElementById('deleteConfirmForm').action = action;

        if (isRequest) {
            document.getElementById('deleteModalTitle').textContent = 'Request deletion of "' + name + '"?';
            document.getElementById('deleteModalMsg').textContent   = 'This will send a deletion request to the Super Admin for approval. The employee record will NOT be removed until the Super Admin confirms.';
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Request';
            submitBtn.style.background = 'linear-gradient(135deg,#e67e22,#d35400)';
        } else if (isRejected) {
            document.getElementById('deleteModalTitle').textContent = 'Delete "' + name + '"?';
            document.getElementById('deleteModalMsg').textContent   = 'This employee application was rejected. Deleting will permanently remove their record. This cannot be undone.';
            submitBtn.innerHTML = '<i class="fas fa-trash"></i> Yes, Delete';
            submitBtn.style.background = '';
        } else {
            document.getElementById('deleteModalTitle').textContent = 'Delete "' + name + '"?';
            document.getElementById('deleteModalMsg').textContent   = 'Are you sure you want to permanently delete this employee? This action cannot be undone.';
            submitBtn.innerHTML = '<i class="fas fa-trash"></i> Yes, Delete';
            submitBtn.style.background = '';
        }
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
            document.getElementById('edit_rfid_number').value     = e.rfid_number   ?? '';
            document.getElementById('edit_date_of_birth').value   = e.date_of_birth ?? '';
            document.getElementById('edit_date_of_joining').value = e.date_of_joining ?? '';
            document.getElementById('edit_department_id').value   = e.department_id ?? '';
            document.getElementById('edit_role_id').value          = e.role_id       ?? '';
            document.getElementById('edit_status').value           = e.status        ?? 'active';
            document.getElementById('edit_employment_type').value = e.employment_type ?? '';
            document.getElementById('edit_rate').value            = e.rate            ?? '';
            document.getElementById('edit_rate_type').value       = e.rate_type       ?? '';
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

    function validatePhilippinePhoneCreate(input, errId) {
        const value = input.value.trim();
        const errSpan = document.getElementById(errId);
        
        if (value === '') {
            // Optional field - no error
            errSpan.style.display = 'none';
            input.classList.remove('is-invalid');
            return true;
        }
        
        const digitsOnly = value.replace(/\D/g, '');
        // Valid formats: 09XXXXXXXXX (11 digits) OR 639XXXXXXXXX (12 digits from +639XXXXXXXXX)
        const isValid = /^09\d{9}$/.test(digitsOnly) || /^639\d{9}$/.test(digitsOnly);
        
        if (!isValid) {
            errSpan.textContent = 'Invalid Philippine number. Use 09XXXXXXXXX (11 digits) or +639XXXXXXXXX (13 chars).';
            errSpan.style.display = 'block';
            input.classList.add('is-invalid');
            return false;
        } else {
            errSpan.style.display = 'none';
            input.classList.remove('is-invalid');
            return true;
        }
    }

    function validatePhilippinePhoneEdit(input, errId) {
        const value = input.value.trim();
        const errSpan = document.getElementById(errId);
        
        if (value === '') {
            // Optional field - no error
            errSpan.style.display = 'none';
            input.classList.remove('is-invalid');
            return true;
        }
        
        const digitsOnly = value.replace(/\D/g, '');
        // Valid formats: 09XXXXXXXXX (11 digits) OR 639XXXXXXXXX (12 digits from +639XXXXXXXXX)
        const isValid = /^09\d{9}$/.test(digitsOnly) || /^639\d{9}$/.test(digitsOnly);
        
        if (!isValid) {
            errSpan.textContent = 'Invalid Philippine number. Use 09XXXXXXXXX (11 digits) or +639XXXXXXXXX (13 chars).';
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
        const phOk = validatePhilippinePhoneEdit(document.getElementById('edit_phone'),       'edit_phone_err');
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
        const phoneOk = validateSpecialChars(document.getElementById('phone'), 'add_phone_err', 'phone');
        const requiredFields = ['rfid_number', 'employee_type', 'date_of_birth', 'date_of_joining', 'status'];
        const missingRequired = requiredFields.some(function(fieldId) {
            const field = document.getElementById(fieldId);
            return !field || field.value.trim() === '';
        });
        if (!fnOk || !lnOk || !phoneOk || missingRequired) return;
        
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
