<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .users-shell {
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
        font-size: 2rem;
        line-height: 1;
    }

    .page-header p {
        margin: 6px 0 0;
        color: #6f8192;
        font-size: 0.92rem;
    }

    .btn-add-user {
        background: #6ea988;
        color: #ffffff;
        border: 1px solid #6ea988;
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease, border-color 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        line-height: 1;
    }

    .btn-add-user:hover {
        background: #21437c;
        border-color: #21437c;
    }

    .alert {
        border-radius: 8px;
        border: none;
        margin-bottom: 14px;
        padding: 12px 14px;
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
    }

    .panel-header {
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        border-bottom: 1px solid #e7edf4;
        background: #ffffff;
    }

    .panel-header h2 {
        margin: 0;
        color: #1f3550;
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
        width: 100%;
        border: 1px solid #d9e2ec;
        border-radius: 8px;
        padding: 9px 11px;
        font-size: 0.9rem;
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
        min-width: 920px;
        border-collapse: collapse;
    }

    .admin-table thead th {
        background: #f7f9fc;
        color: #445b72;
        font-weight: 700;
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #e1e9f2;
        font-size: 0.77rem;
        text-transform: uppercase;
        letter-spacing: 0.38px;
        white-space: nowrap;
    }

    .admin-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid #edf2f7;
        font-size: 0.9rem;
        color: #42586e;
        vertical-align: middle;
    }

    .admin-table tbody tr:last-child td {
        border-bottom: none;
    }

    .admin-table tbody tr:hover {
        background: #fbfdff;
    }

    .admin-table tbody td:last-child {
        white-space: nowrap;
        width: 1%;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 180px;
    }

    .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #edf3ff;
        color: #6ea988;
        border: 1px solid #d3e0f4;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
        flex-shrink: 0;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: capitalize;
        border: 1px solid transparent;
    }

    .badge-admin {
        background: #ecf2ff;
        color: #2b4f8b;
        border-color: #d2dff6;
    }

    .badge-user {
        background: #e9f7ec;
        color: #1d7a3f;
        border-color: #cfead8;
    }

    .badge-active {
        background: #e9f7ec;
        color: #1d7a3f;
        border-color: #cfead8;
    }

    .badge-inactive {
        background: #fdecec;
        color: #b43a3a;
        border-color: #f4d3d3;
    }

    .badge-employee {
        background: #e8f4fd;
        color: #1e6fa5;
        border-color: #c7dff1;
    }

    .badge-no-employee {
        background: #f5f7fa;
        color: #778899;
        border-color: #dce5ee;
    }

    .emp-id-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f1f6ff;
        color: #6ea988;
        border: 1px solid #d3e0f4;
        border-radius: 999px;
        padding: 3px 10px;
        font-size: 0.76rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .emp-id-chip i {
        font-size: 0.68rem;
        opacity: 0.85;
    }

    .emp-id-none {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #97a7b6;
        font-size: 0.84rem;
    }

    .position-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f8f9fb;
        color: #486076;
        border: 1px solid #dce4ed;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 0.78rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .position-chip i {
        font-size: 0.7rem;
        opacity: 0.78;
    }

    .position-none {
        color: #9badbd;
        font-size: 0.84rem;
        font-style: italic;
    }

    .action-buttons {
        display: inline-flex;
        gap: 6px;
        flex-wrap: nowrap;
        align-items: center;
    }

    .btn-edit,
    .btn-delete,
    .btn-activate,
    .btn-deactivate,
    .btn-restore,
    .btn-disabled {
        padding: 6px 10px;
        font-size: 0.78rem;
        font-weight: 700;
        border-radius: 7px;
        border: 1px solid transparent;
        cursor: pointer;
        transition: background 0.2s ease, border-color 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-align: center;
        white-space: nowrap;
        line-height: 1.1;
    }

    .btn-edit {
        background: #e9f3ff;
        color: #1f5ea8;
        border-color: #c9dcf6;
    }

    .btn-edit:hover {
        background: #dbeafd;
        border-color: #b8d2f2;
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

    .btn-activate,
    .btn-restore {
        background: #e9f7ec;
        color: #1d7a3f;
        border-color: #cfead8;
    }

    .btn-activate:hover,
    .btn-restore:hover {
        background: #ddf1e2;
        border-color: #bfe2cb;
    }

    .btn-deactivate {
        background: #fff4e6;
        color: #ad6d16;
        border-color: #f1dfbf;
    }

    .btn-deactivate:hover {
        background: #ffecd3;
        border-color: #e8d0a5;
    }

    .btn-disabled {
        background: #f1f3f6;
        color: #8a98a6;
        border-color: #dbe2ea;
        cursor: not-allowed;
        opacity: 0.9;
    }

    .btn-disabled:hover {
        background: #f1f3f6;
        border-color: #dbe2ea;
    }

    .empty-state {
        text-align: center;
        padding: 54px 20px;
        color: #7f90a0;
    }

    .empty-state i {
        font-size: 2.6rem;
        margin-bottom: 10px;
        display: block;
        color: #9aacbd;
    }

    .fw-600 {
        font-weight: 600;
    }

    .password-wrapper {
        position: relative;
    }

    .password-wrapper .password-toggle {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        background: transparent;
        border: none;
        width: 2.2rem;
        height: 2.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b6aaf;
        cursor: pointer;
    }

    .password-wrapper .password-toggle i {
        pointer-events: auto;
        display: inline-block;
    }

    .add-user-modal-content {
        border-radius: 8px;
        overflow: hidden;
        border: none;
    }

    .add-user-modal-header {
        background: linear-gradient(135deg, #3e7c5f 0%, #5e9e7f 100%);
        color: #ffffff;
        border: none;
        padding: 20px 24px;
    }

    .add-user-modal-header .modal-title {
        font-size: 2rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .add-user-modal-body {
        background: #f4f5f7;
        padding: 24px 28px 20px;
    }

    .add-user-modal-body .form-label {
        margin-bottom: 10px;
        color: #21374b;
        font-size: 1rem;
        font-weight: 700;
    }

    .add-user-modal-body .form-control,
    .add-user-modal-body .form-select {
        height: 50px;
        border: 1px solid #ced7df;
        border-radius: 8px;
        font-size: 1.02rem;
        padding: 0 14px;
        background: #ffffff;
    }

    .add-user-modal-body .row {
        margin-bottom: 2px;
    }

    .add-user-modal-footer {
        border: none;
        padding: 15px 28px 24px;
        background: #f4f5f7;
    }

    @media (max-width: 992px) {
        .page-header h1 {
            font-size: 1.75rem;
        }

        .panel-header {
            align-items: stretch;
        }

        .search-box {
            margin-left: 0;
            max-width: none;
        }

        .admin-table thead th,
        .admin-table tbody td {
            padding: 11px 12px;
        }
    }

    @media (max-width: 640px) {
        .page-header {
            padding: 14px;
        }

        .page-header h1 {
            font-size: 1.5rem;
        }

        .btn-add-user {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="users-shell">

<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
    <span>/</span>
    <span>Users</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="fas fa-users"></i> Users</h1>
        <p>Manage all registered users in the system</p>
    </div>
    <button class="btn-add-user" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fas fa-user-plus"></i> Add Employee
    </button>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <strong>Please fix the following errors:</strong>
        <ul style="margin:8px 0 0 20px;">
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Users Table -->
<div class="admin-panel">
    <div class="panel-header">
        <h2><i class="fas fa-list"></i> User Management (<?= count($users) ?>)</h2>
        <div class="search-box">
            <input type="text" id="userSearch" placeholder="Search users..." oninput="blockSearchSpecialChars(this)" onkeyup="searchUsers(this.value)">
            <div id="userSearchError" style="display:none;color:#dc3545;font-size:0.8rem;margin-top:4px;">Special characters are not allowed in the search.</div>
        </div>
    </div>
    <?php
        // Load roles for select boxes (only active/non-deleted roles)
        $db = \Config\Database::connect();
        $rolesList = $db->table('roles')->select('id, name')->where('deleted_at', null)->orderBy('name','ASC')->get()->getResult();
        $isHrAdminUserManagement = strtolower((string) session()->get('role_name')) === 'hr admin';
    ?>
    <div class="table-responsive">
        <?php if (!empty($users)): ?>
            <table class="admin-table" id="usersTable">
                <thead>
                    <tr>
                        <th>Employee ID#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <?php if (!$isHrAdminUserManagement): ?>
                            <th>Status</th>
                        <?php endif; ?>
                        <th>Joined</th>
                        <?php if (!$isHrAdminUserManagement): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $i => $u): ?>
                        <?php
                            // Resolve linked employee early so Name column can use it
                            $linkedEmp = $employeeEmailMap[$u->email] ?? null;
                            $displayName = $linkedEmp
                                ? trim(($linkedEmp['first_name'] ?? '') . ' ' . ($linkedEmp['last_name'] ?? ''))
                                : $u->name;
                            $displayName = $displayName ?: $u->name;
                        ?>
                        <tr data-user-id="<?= $u->id ?>">
                            <td>
                                <?php if ($linkedEmp): ?>
                                    <span class="emp-id-chip" title="<?= esc($displayName) ?>">
                                        <i class="fas fa-hashtag"></i><?= esc($linkedEmp['employee_id'] ?? '') ?>
                                    </span>
                                <?php else: ?>
                                    <span class="emp-id-none">
                                        <i class="fas fa-minus"></i> &mdash;
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="user-cell">
                                    <div class="avatar"><?= strtoupper(substr($displayName, 0, 1)) ?></div>
                                    <?= esc($displayName) ?>
                                </div>
                            </td>
                            <td><?= esc($u->email) ?></td>
                            <td>
                                <?php
                                    $roleName = isset($u->role_name) ? $u->role_name : ($roleMap[$u->role_id] ?? (isset($u->role) ? $u->role : 'User'));
                                    $roleClass = (strtolower($roleName) === 'super admin' || strtolower($roleName) === 'admin') ? 'badge-admin' : 'badge-user';
                                ?>
                                <span class="badge <?= $roleClass ?>">
                                    <?= esc($roleName) ?>
                                </span>
                            </td>
                            <?php if (!$isHrAdminUserManagement): ?>
                                <td>
                                    <span class="badge <?= $u->is_active ? 'badge-active' : 'badge-inactive' ?>" id="status-<?= $u->id ?>">
                                        <?= $u->is_active ? 'ACTIVE' : 'INACTIVE' ?>
                                    </span>
                                </td>
                            <?php endif; ?>
                            <td><?= date('M d, Y', strtotime($u->created_at)) ?></td>
                            <?php if (!$isHrAdminUserManagement): ?>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-sm btn-edit" style="<?= !$u->is_active ? 'display: none;' : '' ?>" onclick="editUser(<?= $u->id ?>, '<?= esc($u->name) ?>', '<?= esc($u->email) ?>', <?= (int)($u->role_id ?? 0) ?>, <?= $u->is_active ?>)" title="Edit User">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <?php if ($u->is_active): ?>
                                            <?php if ($u->id != $currentUserId): ?>
                                                <button type="button" class="btn btn-sm btn-delete" data-user-id="<?= $u->id ?>" data-user-name="<?= esc($u->name) ?>" onclick="showDeleteModal(this, <?= $u->id ?>, '<?= esc($u->name) ?>')" title="Delete User">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-disabled" disabled title="You cannot delete your own account">
                                                    <i class="fas fa-shield-alt"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-restore" data-user-id="<?= $u->id ?>" onclick="showRestoreModal(<?= $u->id ?>, '<?= esc($u->name) ?>')" title="Restore User">
                                                <i class="fas fa-undo"></i> Restore
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users-slash"></i>
                <p>No users found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#e74c3c 0%,#c0392b 100%);color:white;border:none;">
                <h5 class="modal-title" id="deleteUserModalLabel">
                    <i class="fas fa-trash me-2"></i> Delete User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:30px;">
                <div style="text-align:center;">
                    <i class="fas fa-exclamation-circle" style="font-size:3rem;color:#e74c3c;margin-bottom:15px;display:block;"></i>
                    <h4 style="color:#2c3e50;margin-bottom:10px;">Delete User?</h4>
                    <p id="deleteUserMessage" style="color:#7f8c8d;margin-bottom:0;"></p>
                </div>
            </div>
            <div class="modal-footer" style="border:none;padding:15px 30px 25px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger" style="background:#e74c3c;border:none;padding:8px 24px;">
                    <i class="fas fa-trash me-1"></i> Delete User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Restore User Modal -->
<div class="modal fade" id="restoreUserModal" tabindex="-1" aria-labelledby="restoreUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#27ae60 0%,#229954 100%);color:white;border:none;">
                <h5 class="modal-title" id="restoreUserModalLabel">
                    <i class="fas fa-undo me-2"></i> Restore User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:30px;">
                <div style="text-align:center;">
                    <i class="fas fa-check-circle" style="font-size:3rem;color:#27ae60;margin-bottom:15px;display:block;"></i>
                    <h4 style="color:#2c3e50;margin-bottom:10px;">Restore User?</h4>
                    <p id="restoreUserMessage" style="color:#7f8c8d;margin-bottom:0;"></p>
                </div>
            </div>
            <div class="modal-footer" style="border:none;padding:15px 30px 25px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmRestoreBtn" class="btn btn-success" style="background:#27ae60;border:none;padding:8px 24px;">
                    <i class="fas fa-undo me-1"></i> Restore User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content add-user-modal-content">
            <div class="modal-header add-user-modal-header">
                <h5 class="modal-title" id="addUserModalLabel">
                    <i class="fas fa-user-plus me-2"></i> Add New User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm" action="<?= base_url('users/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body add-user-modal-body">
                    <!-- Validation Errors -->
                    <div id="addUserErrors" class="alert alert-danger" style="display:none;">
                        <strong>Please fix the following errors:</strong>
                        <ul style="margin-bottom:0;margin-top:10px;" id="addUserErrorsList"></ul>
                    </div>

                    <input type="hidden" id="addUserName" name="name">

                    <?php
                        $addUserPositionOptions = ['Front Counter', 'Kitchen/Prep', 'Drive-Thru', 'Dining Room'];
                        $addUserTypeOptions = [];
                        foreach ($rolesList as $roleOption) {
                            $roleName = strtolower((string) ($roleOption->name ?? ''));
                            if (in_array($roleName, ['manager', 'employee'], true)) {
                                $addUserTypeOptions[] = $roleOption;
                            }
                        }
                    ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addUserFirstName" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="addUserFirstName" name="first_name"
                                       placeholder="Enter first name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addUserLastName" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="addUserLastName" name="last_name"
                                       placeholder="Enter last name" required>
                                <small id="nameError" class="text-danger" style="display:none;"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addUserRfidNumber" class="form-label">RFID Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="addUserRfidNumber" name="rfid_number"
                                       placeholder="Enter RFID number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addUserEmail" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="addUserEmail" name="email"
                                       placeholder="Enter email address" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addUserPhone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="addUserPhone" name="phone"
                                       placeholder="Enter phone number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addUserDepartment" class="form-label">Department</label>
                                <select class="form-select" id="addUserDepartment" name="department_id">
                                    <option value="" selected>Select department</option>
                                    <?php if (!empty($departments)): ?>
                                        <?php foreach ($departments as $department): ?>
                                            <option value="<?= (int) $department->id ?>"><?= esc($department->name) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addUserPosition" class="form-label">Position <span class="text-danger">*</span></label>
                                <select class="form-select" id="addUserPosition" name="position" required>
                                    <option value="" disabled selected>Select position</option>
                                    <?php foreach ($addUserPositionOptions as $positionOption): ?>
                                        <option value="<?= esc($positionOption) ?>"><?= esc($positionOption) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addUserType" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="addUserType" name="role_id" required>
                                    <option value="" disabled selected>Select type</option>
                                    <?php foreach ($addUserTypeOptions as $typeOption): ?>
                                        <option value="<?= $typeOption->id ?>"><?= esc($typeOption->name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="addUserDateOfBirth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="addUserDateOfBirth" name="date_of_birth" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="addUserDateHired" class="form-label">Date of Hired <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="addUserDateHired" name="date_of_joining" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="addUserStatus" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="addUserStatus" name="is_active" required>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="modal-footer add-user-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#2f5f45 0%,#6ea988 100%);border:none;padding:8px 24px;">
                        <i class="fas fa-save me-1"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#2f5f45 0%,#6ea988 100%);color:white;border:none;">
                <h5 class="modal-title" id="editUserModalLabel">
                    <i class="fas fa-user-edit me-2"></i> Edit User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" action="#" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body" style="padding:30px;">
                    <input type="hidden" id="editUserId" name="user_id">

                    <!-- Inline alert for edit modal -->
                    <div id="editUserAlert" style="display:none;margin-bottom:15px;" class="alert"></div>

                    <div class="mb-3">
                        <label for="editName" class="form-label fw-600">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editName" name="name"
                               placeholder="Enter full name" required>
                        <small id="editNameError" class="text-danger" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label for="editEmail" class="form-label fw-600">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="editEmail" name="email"
                               placeholder="Enter email address" required>
                    </div>

                    <div class="mb-3">
                        <label for="editPassword" class="form-label fw-600">Password <span style="color:#999;font-size:0.85rem;">(Leave blank to keep current)</span></label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="editPassword" name="password"
                                   placeholder="Leave blank to keep current password" minlength="6" style="padding-right:2.8rem;">
                            <button type="button" id="toggleEditPassword" class="password-toggle" aria-pressed="false" aria-label="Show password" onclick="toggleEditPasswordVisibility(event)">
                                <i class="fas fa-eye-slash" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editRole" class="form-label fw-600">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="editRole" name="role_id" required>
                                <?php foreach ($rolesList as $r): ?>
                                    <option value="<?= $r->id ?>"><?= esc($r->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editIsActive" class="form-label fw-600">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="editIsActive" name="is_active" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="border:none;padding:15px 30px 25px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#2f5f45 0%,#6ea988 100%);border:none;padding:8px 24px;">
                        <i class="fas fa-save me-1"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function blockSearchSpecialChars(input) {
    const SEARCH_ALLOWED = /^[a-zA-Z0-9\s@._\-]*$/;
    const errorDiv = document.getElementById(input.id + 'Error') || document.getElementById('userSearchError');
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

function searchUsers(query) {
    const rows = document.querySelectorAll('#usersTable tbody tr');
    query = query.toLowerCase().trim();
    
    if (!query) {
        // If search is empty, show all rows
        rows.forEach(row => {
            row.style.display = '';
        });
        return;
    }
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        // Search in Name (index 1), Email (index 2), and Role (index 3)
        const name = cells[1]?.textContent.toLowerCase() || '';
        const email = cells[2]?.textContent.toLowerCase() || '';
        const role = cells[3]?.textContent.toLowerCase() || '';
        
        const matches = name.includes(query) || email.includes(query) || role.includes(query);
        row.style.display = matches ? '' : 'none';
    });
}

// Validation function to check for special characters
// Only allow letters (a-z, A-Z), numbers (0-9), and spaces
const ALLOWED_NAME_PATTERN = /^[a-zA-Z0-9\s]*$/;

function hasInvalidCharacters(str) {
    return !ALLOWED_NAME_PATTERN.test(str);
}

function validateNameField(value) {
    if (hasInvalidCharacters(value)) {
        return 'Name can only contain letters, numbers, and spaces. Special characters are not allowed.';
    }
    return null;
}

// Real-time validation for name fields
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('addUserName');
    const firstNameInput = document.getElementById('addUserFirstName');
    const lastNameInput = document.getElementById('addUserLastName');
    const nameErrorDiv = document.getElementById('nameError');
    const editNameInput = document.getElementById('editName');
    const editNameErrorDiv = document.getElementById('editNameError');

    function syncAddUserNameFromParts() {
        if (!nameInput) {
            return '';
        }
        const first = firstNameInput ? firstNameInput.value.trim() : '';
        const last = lastNameInput ? lastNameInput.value.trim() : '';
        const full = (first + ' ' + last).trim();
        nameInput.value = full;
        return full;
    }

    function validateAddNameParts() {
        const full = syncAddUserNameFromParts();
        if (!nameErrorDiv) {
            return;
        }
        const error = validateNameField(full);
        if (error) {
            nameErrorDiv.textContent = error;
            nameErrorDiv.style.display = 'block';
            if (firstNameInput) firstNameInput.classList.add('is-invalid');
            if (lastNameInput) lastNameInput.classList.add('is-invalid');
        } else {
            nameErrorDiv.style.display = 'none';
            if (firstNameInput) firstNameInput.classList.remove('is-invalid');
            if (lastNameInput) lastNameInput.classList.remove('is-invalid');
        }
    }
    
    // Add User first/last name field validation
    if (firstNameInput && lastNameInput) {
        firstNameInput.addEventListener('input', validateAddNameParts);
        lastNameInput.addEventListener('input', validateAddNameParts);
    }
    
    // Edit User name field validation
    if (editNameInput && editNameErrorDiv) {
        editNameInput.addEventListener('input', function() {
            const error = validateNameField(this.value);
            if (error) {
                editNameErrorDiv.textContent = error;
                editNameErrorDiv.style.display = 'block';
                this.classList.add('is-invalid');
            } else {
                editNameErrorDiv.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });
    }
});

function toggleUserStatus(userId, action) {
    let confirmMessage = '';
    if (action === 'activate') {
        confirmMessage = 'Are you sure you want to activate this user?';
    } else if (action === 'delete') {
        confirmMessage = 'Are you sure you want to delete this user? This action cannot be undone.';
    } else {
        confirmMessage = 'Are you sure you want to deactivate this user? The user will not be able to log in.';
    }
    
    // For delete action we show modal instead of immediate confirm
    if (action === 'delete') {
        // noop here; delete is handled via showDeleteModal -> performDelete
        return;
    }

    if (!confirm(confirmMessage)) {
        return;
    }

    // Show loading state on button
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    const url = `<?= base_url('users/') ?>${action}/${userId}`;
    
    const csrfHeader = document.querySelector('meta[name="<?= csrf_header() ?>"]').getAttribute('content');
    const headers = {
        'X-Requested-With': 'XMLHttpRequest'
    };
    headers['<?= csrf_header() ?>'] = csrfHeader;

    fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: headers
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update status badge in real-time
            const statusBadge = document.getElementById(`status-${userId}`);
            const actionCell = button.closest('td');
            
            if (data.status === 'ACTIVE') {
                statusBadge.className = 'badge badge-active';
                statusBadge.textContent = 'ACTIVE';
                
                // Replace button with delete button
                actionCell.querySelectorAll('.btn-delete, .btn-restore').forEach(n => n.remove());
                const delBtn = document.createElement('button');
                delBtn.type = 'button';
                delBtn.className = 'btn btn-sm btn-delete';
                delBtn.setAttribute('data-user-id', userId);
                delBtn.setAttribute('data-user-name', actionCell.closest('tr').querySelector('.user-cell').innerText.trim());
                delBtn.title = 'Delete User';
                delBtn.innerHTML = '<i class="fas fa-trash"></i> Delete';
                delBtn.addEventListener('click', function(e) { showDeleteModal(this, userId, this.getAttribute('data-user-name')); });
                const container = actionCell.querySelector('.action-buttons') || actionCell;
                container.appendChild(delBtn);
            } else if (data.status === 'INACTIVE') {
                statusBadge.className = 'badge badge-inactive';
                statusBadge.textContent = 'INACTIVE';
                
                // Replace button with activate button
                actionCell.querySelectorAll('.btn-delete, .btn-restore').forEach(n => n.remove());
                const actBtn = document.createElement('button');
                actBtn.type = 'button';
                actBtn.className = 'btn btn-sm btn-activate';
                actBtn.title = 'Activate User';
                actBtn.innerHTML = '<i class="fas fa-user-check"></i> Activate';
                actBtn.addEventListener('click', function(e) { toggleUserStatus(userId, 'activate'); });
                const container = actionCell.querySelector('.action-buttons') || actionCell;
                container.appendChild(actBtn);
            }

            // Update CSRF token in meta for future requests
            if (data.csrf_hash) {
                const meta = document.querySelector('meta[name="<?= csrf_header() ?>"]');
                if (meta) meta.setAttribute('content', data.csrf_hash);
            }

            // Show success message
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Operation failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An unexpected error occurred', 'error');
        
        // Restore button state
        button.disabled = false;
        button.innerHTML = originalContent;
    });
}

// Notification system for real-time feedback
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        padding: 15px 20px;
        border-radius: 6px;
        border: none;
        margin-bottom: 0;
        animation: slideInRight 0.3s ease;
    `;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Show delete confirmation modal
function showDeleteModal(button, userId, name) {
    const modalEl = document.getElementById('deleteUserModal');
    const msg = document.getElementById('deleteUserMessage');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    msg.innerHTML = `Are you sure you want to delete <strong>${name}</strong>?<br><small>This action is irreversible but can be restored later.</small>`;
    confirmBtn.dataset.userId = userId;
    // keep reference to source button to update UI after response
    confirmBtn._sourceButton = button;
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

// Perform delete (soft)
document.getElementById('confirmDeleteBtn').addEventListener('click', function(e){
    const btn = e.currentTarget;
    const userId = btn.dataset.userId;
    if (!userId) return;
    // show loading
    btn.disabled = true;
    const original = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';

    const csrfHeaderDel = document.querySelector('meta[name="<?= csrf_header() ?>"]').getAttribute('content');
    const headersDel = {'X-Requested-With': 'XMLHttpRequest'};
    headersDel['<?= csrf_header() ?>'] = csrfHeaderDel;

    fetch(`<?= base_url('users/delete') ?>/${userId}`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: headersDel
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = original;
        const modalEl = document.getElementById('deleteUserModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');

        if (data.success) {
            // update UI: status badge and replace delete with restore
            const statusBadge = document.getElementById(`status-${userId}`);
            if (statusBadge) {
                statusBadge.className = 'badge badge-inactive';
                statusBadge.textContent = 'DELETED';
            }
            
            // Find the row by user ID
            const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
            
            if (userRow) {
                const actionCell = userRow.querySelector('.action-buttons');
                if (actionCell) {
                    // Hide edit button when deleted
                    const editBtn = actionCell.querySelector('.btn-edit');
                    if (editBtn) {
                        editBtn.style.display = 'none';
                    }
                    
                    // remove existing delete button(s)
                    actionCell.querySelectorAll('.btn-delete').forEach(n => n.remove());
                    
                    // add restore button
                    const restoreBtn = document.createElement('button');
                    restoreBtn.type = 'button';
                    restoreBtn.className = 'btn btn-sm btn-restore';
                    restoreBtn.setAttribute('data-user-id', userId);
                    restoreBtn.innerHTML = '<i class="fas fa-undo"></i> Restore';
                    restoreBtn.addEventListener('click', function(e){ 
                        const userName = userRow.querySelector('.user-cell')?.innerText.trim() || '';
                        showRestoreModal(userId, userName); 
                    });
                    actionCell.appendChild(restoreBtn);
                }
            }

            if (data.csrf_hash) {
                const meta = document.querySelector('meta[name="<?= csrf_header() ?>"]');
                if (meta) meta.setAttribute('content', data.csrf_hash);
            }
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Failed to delete user', 'error');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = original;
        console.error(err);
        showNotification('An unexpected error occurred', 'error');
    });
});

let pendingRestoreUserId = null;
let pendingRestoreUserName = null;

function showRestoreModal(userId, name) {
    pendingRestoreUserId = userId;
    pendingRestoreUserName = name;
    const modalEl = document.getElementById('restoreUserModal');
    const msg = document.getElementById('restoreUserMessage');
    msg.innerHTML = `Are you sure you want to restore <strong>${name}</strong>?`;
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

// Perform restore
document.getElementById('confirmRestoreBtn').addEventListener('click', function(e){
    const btn = e.currentTarget;
    const userId = pendingRestoreUserId;
    if (!userId) return;
    
    // show loading
    btn.disabled = true;
    const original = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Restoring...';

    const csrfHeaderRestore = document.querySelector('meta[name="<?= csrf_header() ?>"]').getAttribute('content');
    const headersRestore = {'X-Requested-With': 'XMLHttpRequest'};
    headersRestore['<?= csrf_header() ?>'] = csrfHeaderRestore;

    fetch(`<?= base_url('users/restore') ?>/${userId}`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: headersRestore
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = original;
        const modalEl = document.getElementById('restoreUserModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');

        if (data.success) {
            // Update status badge
            const statusBadge = document.getElementById(`status-${userId}`);
            if (statusBadge) {
                statusBadge.className = 'badge badge-active';
                statusBadge.textContent = 'ACTIVE';
            }

            // Find the row by user ID
            const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
            
            if (userRow) {
                const actionCell = userRow.querySelector('.action-buttons');
                if (actionCell) {
                    // Show edit button when restored
                    const editBtn = actionCell.querySelector('.btn-edit');
                    if (editBtn) {
                        editBtn.style.display = 'inline-flex';
                    }
                    
                    // remove existing restore button(s)
                    actionCell.querySelectorAll('.btn-restore').forEach(n => n.remove());
                    
                    // add delete button
                    const userName = userRow.querySelector('.user-cell')?.innerText.trim() || pendingRestoreUserName;
                    const delBtn = document.createElement('button');
                    delBtn.type = 'button';
                    delBtn.className = 'btn btn-sm btn-delete';
                    delBtn.setAttribute('data-user-id', userId);
                    delBtn.setAttribute('data-user-name', userName);
                    delBtn.title = 'Delete User';
                    delBtn.innerHTML = '<i class="fas fa-trash"></i> Delete';
                    delBtn.addEventListener('click', function(e){ showDeleteModal(this, userId, userName); });
                    actionCell.appendChild(delBtn);
                }
            }
            
            if (data.csrf_hash) {
                const meta = document.querySelector('meta[name="<?= csrf_header() ?>"]');
                if (meta) meta.setAttribute('content', data.csrf_hash);
            }
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Failed to restore user', 'error');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = original;
        showNotification('An error occurred while restoring', 'error');
    });
    
    // Clear pending data
    pendingRestoreUserId = null;
    pendingRestoreUserName = null;
});

// Add CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

function showEditModalAlert(message, type) {
    const alertBox = document.getElementById('editUserAlert');
    if (!alertBox) return;
    alertBox.className = `alert alert-${type}`;
    alertBox.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
    alertBox.style.display = 'block';
}

function editUser(userId, name, email, roleId, isActive) {
    // Clear previous alerts
    const alertBox = document.getElementById('editUserAlert');
    if (alertBox) { alertBox.style.display = 'none'; alertBox.textContent = ''; }

    // Populate modal fields with user data
    document.getElementById('editUserId').value = userId;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editRole').value = roleId;
    document.getElementById('editIsActive').value = isActive;
    
    // Clear password field
    document.getElementById('editPassword').value = '';
    
    // Show the modal
    const editModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editUserModal'));
    editModal.show();
}

// Handle edit form submission
document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const userId = document.getElementById('editUserId').value;
    const name = document.getElementById('editName').value;
    const email = document.getElementById('editEmail').value;
    const password = document.getElementById('editPassword').value;
    const roleId = document.getElementById('editRole').value;
    const isActive = document.getElementById('editIsActive').value;
    
    console.log('Submitting form with:', {userId, name, email, roleId, isActive});
    
    // Validate form
    if (!name || !email || !roleId) {
        showEditModalAlert('Please fill in all required fields.', 'danger');
        return;
    }
    
    // Validate special characters in name
    const nameError = validateNameField(name);
    if (nameError) {
        showEditModalAlert(nameError, 'danger');
        return;
    }
    
    // Show loading spinner
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.classList.add('active');
    }
    
    // Prepare FormData
    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('password', password);
    formData.append('role_id', roleId);
    formData.append('is_active', isActive);
    // Always include current CSRF token in POST body
    const csrfMeta = document.querySelector('meta[name="<?= csrf_header() ?>"]');
    if (csrfMeta) formData.append('<?= csrf_token() ?>', csrfMeta.getAttribute('content'));
    
    const url = `<?= base_url('users/update') ?>/${userId}`;
    console.log('Fetching:', url);
    
    const csrfHeaderForm = csrfMeta ? csrfMeta.getAttribute('content') : '';
    const headersForm = {'X-Requested-With': 'XMLHttpRequest'};
    if (csrfHeaderForm) headersForm['<?= csrf_header() ?>'] = csrfHeaderForm;

    fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: headersForm,
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status, response.statusText);
        return response.json().then(data => ({status: response.status, data}));
    })
    .then(({status, data}) => {
        if (spinner) spinner.classList.remove('active');
        
        console.log('Response data:', data);
        
        if (data.success) {
            // Close modal immediately
            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editUserModal'));
            modal.hide();
            // Remove the lingering backdrop manually in case Bootstrap leaves it
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');

            // Update CSRF token
            if (data.csrf_hash) {
                const meta = document.querySelector('meta[name="<?= csrf_header() ?>"]');
                if (meta) meta.setAttribute('content', data.csrf_hash);
            }

            // Update the table row in-place (no page reload)
            const u = data.user;
            const statusBadge = document.getElementById('status-' + u.id);
            if (statusBadge) {
                const row = statusBadge.closest('tr');
                const cells = row.querySelectorAll('td');

                // Cell 1: Name (avatar + name text) — rebuild innerHTML cleanly
                const userCell = cells[1].querySelector('.user-cell');
                if (userCell) {
                    userCell.innerHTML = `<div class="avatar">${u.name.charAt(0).toUpperCase()}</div> ${u.name}`;
                }

                // Cell 2: Email
                cells[2].textContent = u.email;

                // Cell 3: Role badge
                const roleName  = u.role_name;
                const roleClass = (roleName.toLowerCase() === 'super admin' || roleName.toLowerCase() === 'admin')
                    ? 'badge badge-admin' : 'badge badge-user';
                cells[3].innerHTML = `<span class="${roleClass}">${roleName}</span>`;

                // Cell 4: Status badge
                if (u.is_active) {
                    statusBadge.className = 'badge badge-active';
                    statusBadge.textContent = 'ACTIVE';
                } else {
                    statusBadge.className = 'badge badge-inactive';
                    statusBadge.textContent = 'INACTIVE';
                }

                // Update edit button onclick with new values so re-opening the modal reflects changes
                const editBtn = row.querySelector('.btn-edit');
                if (editBtn) {
                    editBtn.setAttribute('onclick',
                        `editUser(${u.id}, '${u.name.replace(/'/g,"\\'").replace(/"/g,'&quot;')}', '${u.email}', ${u.role_id}, ${u.is_active})`);
                    editBtn.style.display = u.is_active ? '' : 'none';
                }
            }

            showNotification('User updated successfully!', 'success');
        } else {
            showEditModalAlert('Error: ' + (data.message || 'Failed to update user'), 'danger');
        }
    })
    .catch(error => {
        if (spinner) spinner.classList.remove('active');
        console.error('Fetch error:', error);
        showEditModalAlert('Error: ' + error.message, 'danger');
    });
});

// Handle add user form submission
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    const firstNameInput = document.getElementById('addUserFirstName');
    const lastNameInput = document.getElementById('addUserLastName');
    const nameInput = document.getElementById('addUserName');
    const emailInput = document.getElementById('addUserEmail');
    const rfidInput = document.getElementById('addUserRfidNumber');
    const positionInput = document.getElementById('addUserPosition');
    const typeInput = document.getElementById('addUserType');
    const phoneInput = document.getElementById('addUserPhone');
    const dobInput = document.getElementById('addUserDateOfBirth');
    const hiredInput = document.getElementById('addUserDateHired');
    const statusInput = document.getElementById('addUserStatus');
    const firstName = firstNameInput ? firstNameInput.value.trim() : '';
    const lastName = lastNameInput ? lastNameInput.value.trim() : '';
    const fullName = (firstName + ' ' + lastName).trim();
    if (nameInput) {
        nameInput.value = fullName;
    }
    const name = nameInput ? nameInput.value.trim() : '';
    const email = emailInput ? emailInput.value.trim() : '';
    const errorsBox = document.getElementById('addUserErrors');
    const errorsList = document.getElementById('addUserErrorsList');

    if (errorsBox) errorsBox.style.display = 'none';
    if (errorsList) errorsList.innerHTML = '';

    const errors = [];

    if (!firstName || !lastName || !rfidInput?.value.trim() || !positionInput?.value.trim() || !typeInput?.value.trim() || !dobInput?.value.trim() || !hiredInput?.value.trim() || !statusInput?.value.trim() || !email) {
        errors.push('Please fill in all required fields.');
    }
    
    // Validate special characters in name
    const nameError = validateNameField(name);
    if (nameError) {
        errors.push(nameError);
    }

    if (errors.length > 0) {
        e.preventDefault();
        if (errorsList) {
            errorsList.innerHTML = errors.map(msg => `<li>${msg}</li>`).join('');
        }
        if (errorsBox) {
            errorsBox.style.display = 'block';
        }
        return false;
    }
});
</script>

<script>
// Toggle for Edit User password
function toggleEditPasswordVisibility(e) {
    try {
        if (e && e.preventDefault) e.preventDefault();
        var pwd = document.getElementById('editPassword');
        var btn = document.getElementById('toggleEditPassword');
        if (!pwd || !btn) return;
        var show = pwd.getAttribute('type') === 'password';
        pwd.setAttribute('type', show ? 'text' : 'password');
        btn.setAttribute('aria-pressed', String(show));
        var icon = btn.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-eye', show);
            icon.classList.toggle('fa-eye-slash', !show);
        }
    } catch (err) {
        console.error(err);
    }
}

// Delegated listeners (fallback)
document.addEventListener('click', function (e) {
    var b = e.target.closest && e.target.closest('#toggleEditPassword');
    if (b) return toggleEditPasswordVisibility(e);
});

// Also attach directly to inner icons for reliability
var editEye = document.querySelector('#toggleEditPassword i');
if (editEye) editEye.addEventListener('click', function (e) { e.stopPropagation(); toggleEditPasswordVisibility(e); });
</script>

<?= $this->endSection() ?>
