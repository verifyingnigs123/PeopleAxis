<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<!-- ============================================================
     ROLES MANAGEMENT MODULE
     Professional Structure with Clear Sections
     ============================================================ -->

<!-- ============================================================
     STYLESHEET SECTION
     ============================================================ -->
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
        color: #2f5f45;
        font-weight: 700;
        margin: 0;
    }

    .btn-add-role {
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: white;
        border: none;
        padding: 10px 22px;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-add-role:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.4);
    }

    .admin-panel {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .panel-header {
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
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

    .table-responsive {
        overflow-x: auto;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table thead th {
        background: #f8f9fa;
        color: #2f5f45;
        font-weight: 600;
        padding: 14px 20px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .admin-table tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f3f5;
        font-size: 0.95rem;
        color: #495057;
    }

    .admin-table tbody tr:hover {
        background: #f8f9ff;
    }

    .admin-table tbody tr.deleted-row {
        opacity: 0.6;
        background: #f8f8f8;
    }

    .admin-table tbody tr.deleted-row td {
        text-decoration: line-through;
        color: #999;
    }

    .badge-deleted {
        background: #f8d7da;
        color: #721c24;
        font-size: 0.7rem;
        margin-left: 5px;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .badge-primary {
        background: #e8ecff;
        color: #6ea988;
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
        background: white;
        color: #333;
    }

    .search-box input::placeholder {
        color: #999;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-edit, .btn-delete {
        padding: 6px 12px;
        font-size: 0.85rem;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-edit {
        background: #3498db;
        color: white;
    }

    .btn-edit:hover {
        background: #2980b9;
    }

    .btn-delete {
        background: #e74c3c;
        color: white;
    }

    .btn-delete:hover {
        background: #c0392b;
    }

    .alert {
        border-radius: 6px;
        padding: 15px 20px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #95a5a6;
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
        border-color: #6ea988;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
    }

    .form-control.is-invalid {
        border-color: #e74c3c;
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    .text-danger {
        color: #e74c3c;
    }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
</style>

<!-- ============================================================
     MARKUP SECTION
     ============================================================ -->

<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Roles</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="fas fa-lock"></i> Roles</h1>
        <p>Manage system roles and permissions</p>
    </div>
    <button class="btn-add-role" data-bs-toggle="modal" data-bs-target="#addRoleModal">
        <i class="fas fa-plus"></i> Add Role
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

<!-- Roles Table -->
<div class="admin-panel">
    <div class="panel-header">
        <h2><i class="fas fa-list"></i> System Roles (<?= count($roles) ?>)</h2>
        <div class="search-box">
            <input type="text" id="roleSearch" placeholder="Search roles..." oninput="blockSearchSpecialChars(this)" onkeyup="searchRoles(this.value)">
            <div id="roleSearchError" style="display:none;color:#dc3545;font-size:0.8rem;margin-top:4px;">Special characters are not allowed in the search.</div>
        </div>
    </div>

    <div class="table-responsive">
        <?php if (!empty($roles)): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $i => $role): ?>
                        <tr id="role-row-<?= $role->id ?>" class="<?= $role->deleted_at ? 'deleted-row' : '' ?>">
                            <td><?= $i + 1 ?></td>
                            <td>
                                <span class="badge badge-primary"><?= esc($role->name) ?></span>
                                <?php if ($role->deleted_at): ?>
                                    <span class="badge badge-deleted">DELETED</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($role->description ?? 'N/A') ?></td>
                            <td><?= date('M d, Y', strtotime($role->created_at)) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if (!$role->deleted_at): ?>
                                        <button class="btn-edit" onclick="editRole(<?= $role->id ?>)" data-bs-toggle="modal" data-bs-target="#editRoleModal">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <?php if (!in_array(strtolower($role->name), ['super admin', 'admin', 'employee', 'user'])): ?>
                                            <button class="btn-delete" onclick="deleteRole(<?= $role->id ?>, '<?= esc($role->name) ?>')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button class="btn-edit" onclick="restoreRole(<?= $role->id ?>, '<?= esc($role->name) ?>')" style="background: #27ae60;">
                                            <i class="fas fa-undo"></i> Restore
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
                <p>No roles found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ============================================================
     MODAL DIALOGS SECTION
     ============================================================ -->

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#2f5f45 0%,#6ea988 100%);color:white;border:none;">
                <h5 class="modal-title" id="addRoleModalLabel">
                    <i class="fas fa-plus me-2"></i> Add New Role
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('roles/store') ?>" method="POST" id="addRoleForm">
                <?= csrf_field() ?>
                <div class="modal-body" style="padding:30px;">
                    <!-- Form Header -->
                    <div style="margin-bottom: 25px; text-align: center;">
                        <h6 style="color: #2f5f45; font-weight: 700; margin: 0;">New Role Details</h6>
                        <p style="color: #95a5a6; margin-top: 5px; margin-bottom: 0;">Fill in the form below to create a new system role</p>
                    </div>

                    <!-- Validation Errors -->
                    <div id="addRoleErrors" class="alert alert-danger" style="display:none;">
                        <strong>Please fix the following errors:</strong>
                        <ul style="margin-bottom:0;margin-top:10px;" id="addRoleErrorsList"></ul>
                    </div>

                    <!-- Role Name Field -->
                    <div class="form-group mb-3">
                        <label for="addRoleName" class="form-label fw-600">
                            Role Name <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="addRoleName" 
                            name="name" 
                            placeholder="e.g., Manager, Supervisor, Assistant"
                            pattern="^[a-zA-Z0-9 ]+$"
                            title="Only letters, numbers, and spaces are allowed"
                            value=""
                            required
                        >
                        <small id="addNameError" class="text-danger" style="display:none;">Only letters, numbers, and spaces are allowed</small>
                    </div>

                    <!-- Description Field -->
                    <div class="form-group">
                        <label for="addRoleDescription" class="form-label fw-600">Description</label>
                        <textarea 
                            class="form-control" 
                            id="addRoleDescription" 
                            name="description" 
                            placeholder="Describe what this role does and its responsibilities"
                            rows="4"
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border:none;padding:15px 30px 25px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#2f5f45 0%,#6ea988 100%);border:none;padding:8px 24px;">
                        <i class="fas fa-save me-1"></i> Save Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#2f5f45 0%,#6ea988 100%);color:white;border:none;">
                <h5 class="modal-title" id="editRoleModalLabel">
                    <i class="fas fa-edit me-2"></i> Edit Role
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRoleForm" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body" style="padding:30px;">
                    <!-- Form Header -->
                    <div style="margin-bottom: 20px;">
                        <h6 id="editRoleTitle" style="color: #2f5f45; font-weight: 700; margin: 0;"></h6>
                        <p style="color: #95a5a6; margin-top: 5px; margin-bottom: 0;">Update role details below</p>
                    </div>

                    <!-- Role Information Card -->
                    <div style="background: #f8f9ff; padding: 15px; border-radius: 6px; margin-bottom: 25px; border-left: 4px solid #6ea988;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem;">
                            <span style="color: #7f8c8d; font-weight: 600;">Created Date:</span>
                            <span id="editRoleCreatedDate" style="color: #2c3e50; font-weight: 500;"></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0; font-size: 0.9rem;">
                            <span style="color: #7f8c8d; font-weight: 600;">Last Updated:</span>
                            <span id="editRoleUpdatedDate" style="color: #2c3e50; font-weight: 500;"></span>
                        </div>
                    </div>

                    <!-- Validation Errors -->
                    <div id="editRoleErrors" class="alert alert-danger" style="display:none;">
                        <strong>Please fix the following errors:</strong>
                        <ul style="margin-bottom:0;margin-top:10px;" id="editRoleErrorsList"></ul>
                    </div>

                    <!-- Role Name Field -->
                    <div class="form-group mb-3">
                        <label for="editRoleName" class="form-label fw-600">
                            Role Name <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="editRoleName" 
                            name="name" 
                            placeholder="e.g., Manager, Supervisor, Assistant"
                            pattern="^[a-zA-Z0-9 ]+$"
                            title="Only letters, numbers, and spaces are allowed"
                            required
                        >
                        <small id="editNameError" class="text-danger" style="display:none;">Only letters, numbers, and spaces are allowed</small>
                    </div>

                    <!-- Description Field -->
                    <div class="form-group">
                        <label for="editRoleDescription" class="form-label fw-600">Description</label>
                        <textarea 
                            class="form-control" 
                            id="editRoleDescription" 
                            name="description" 
                            placeholder="Describe what this role does and its responsibilities"
                            rows="4"
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border:none;padding:15px 30px 25px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#2f5f45 0%,#6ea988 100%);border:none;padding:8px 24px;">
                        <i class="fas fa-save me-1"></i> Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Role Confirmation Modal -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#e74c3c 0%,#c0392b 100%);color:white;border:none;">
                <h5 class="modal-title" id="deleteRoleModalLabel">
                    <i class="fas fa-trash me-2"></i> Delete Role
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:30px;">
                <div style="text-align:center;">
                    <i class="fas fa-exclamation-circle" style="font-size:3rem;color:#e74c3c;margin-bottom:15px;display:block;"></i>
                    <h4 style="color:#2c3e50;margin-bottom:10px;">Delete Role?</h4>
                    <p style="color:#7f8c8d;margin-bottom:0;">
                        Are you sure you want to delete the role <strong id="deleteRoleName"></strong>?
                        <br><small>This action is irreversible but can be restored later.</small>
                    </p>
                </div>
            </div>
            <div class="modal-footer" style="border:none;padding:15px 30px 25px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()" style="background:#e74c3c;border:none;padding:8px 24px;">
                    <i class="fas fa-trash me-1"></i> Delete Role
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Restore Role Confirmation Modal -->
<div class="modal fade" id="restoreRoleModal" tabindex="-1" aria-labelledby="restoreRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#27ae60 0%,#229954 100%);color:white;border:none;">
                <h5 class="modal-title" id="restoreRoleModalLabel">
                    <i class="fas fa-undo me-2"></i> Restore Role
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:30px;">
                <div style="text-align:center;">
                    <i class="fas fa-check-circle" style="font-size:3rem;color:#27ae60;margin-bottom:15px;display:block;"></i>
                    <h4 style="color:#2c3e50;margin-bottom:10px;">Restore Role?</h4>
                    <p style="color:#7f8c8d;margin-bottom:0;">
                        Are you sure you want to restore the role <strong id="restoreRoleName"></strong>?
                    </p>
                </div>
            </div>
            <div class="modal-footer" style="border:none;padding:15px 30px 25px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmRestore()" style="background:#27ae60;border:none;padding:8px 24px;">
                    <i class="fas fa-undo me-1"></i> Restore Role
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     JAVASCRIPT SECTION
     Role Management Functionality
     ============================================================ -->
<script>
    // ===== STATE MANAGEMENT =====
    let roleState = {
        deleteId: null,
        deleteName: null,
        restoreId: null,
        restoreName: null
    };

    // ===== EDIT ROLE FUNCTION =====
    fetch('<?= base_url('roles/getRole') ?>/' + roleId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const role = data.data;
                document.getElementById('editRoleTitle').textContent = role.name;
                document.getElementById('editRoleName').value = role.name;
                document.getElementById('editRoleDescription').value = role.description || '';
                
                // Format and display dates
                const createdDate = new Date(role.created_at);
                const updatedDate = new Date(role.updated_at || role.created_at);
                document.getElementById('editRoleCreatedDate').textContent = createdDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                document.getElementById('editRoleUpdatedDate').textContent = updatedDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                
                document.getElementById('editRoleForm').action = '<?= base_url('roles/update') ?>/' + roleId;
                
                // Clear previous errors
                document.getElementById('editRoleErrors').style.display = 'none';
                document.getElementById('editRoleErrorsList').innerHTML = '';
            } else {
                alert('Failed to load role data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading role data');
        });
}

function deleteRole(roleId, roleName) {
    // Store the role data in variables
    pendingDeleteRoleId = roleId;
    pendingDeleteRoleName = roleName;
    
    // Update modal content
    document.getElementById('deleteRoleName').textContent = roleName;
    
    // Show the modal
    new bootstrap.Modal(document.getElementById('deleteRoleModal')).show();
}

function confirmDelete() {
    if (!pendingDeleteRoleId) {
        return;
    }

    const roleId = pendingDeleteRoleId;
    const roleName = pendingDeleteRoleName;
    
    // Get fresh CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="<?= csrf_header() ?>"]').getAttribute('content');
    const headers = {'X-Requested-With': 'XMLHttpRequest'};
    headers['<?= csrf_header() ?>'] = csrfToken;

    fetch('<?= base_url('roles/delete') ?>/' + roleId, {
        method: 'POST',
        credentials: 'same-origin',
        headers: headers
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update CSRF token in meta tag
            if (data.csrf_hash) {
                const meta = document.querySelector('meta[name="<?= csrf_header() ?>"]');
                if (meta) meta.setAttribute('content', data.csrf_hash);
            }
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('deleteRoleModal')).hide();
            
            // Update the row with deleted styling
            const row = document.getElementById('role-row-' + roleId);
            if (row) {
                row.style.transition = 'opacity 0.3s ease';
                
                row.classList.add('deleted-row');
                const badgeSpan = row.querySelector('.badge-primary');
                if (badgeSpan) {
                    badgeSpan.insertAdjacentHTML('afterend', '<span class="badge badge-deleted">DELETED</span>');
                }
                
                const actionButtons = row.querySelector('.action-buttons');
                actionButtons.innerHTML = `
                    <button class="btn-edit" onclick="restoreRole(${roleId}, '${roleName}')" style="background: #27ae60;">
                        <i class="fas fa-undo"></i> Restore
                    </button>
                `;
            }
            
            // Show success notification
            showNotification('Role deleted (soft) successfully', 'success');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting role');
    });
    
    // Clear pending data
    pendingDeleteRoleId = null;
    pendingDeleteRoleName = null;
}

function restoreRole(roleId, roleName) {
    // Store the role data in variables
    pendingRestoreRoleId = roleId;
    pendingRestoreRoleName = roleName;
    
    // Update modal content
    document.getElementById('restoreRoleName').textContent = roleName;
    
    // Show the modal
    new bootstrap.Modal(document.getElementById('restoreRoleModal')).show();
}

function confirmRestore() {
    if (!pendingRestoreRoleId) {
        return;
    }

    const roleId = pendingRestoreRoleId;
    const roleName = pendingRestoreRoleName;
    
    // Get fresh CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="<?= csrf_header() ?>"]').getAttribute('content');
    const headers = {'X-Requested-With': 'XMLHttpRequest'};
    headers['<?= csrf_header() ?>'] = csrfToken;

    fetch('<?= base_url('roles/restore') ?>/' + roleId, {
        method: 'POST',
        credentials: 'same-origin',
        headers: headers
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update CSRF token in meta tag
            if (data.csrf_hash) {
                const meta = document.querySelector('meta[name="<?= csrf_header() ?>"]');
                if (meta) meta.setAttribute('content', data.csrf_hash);
            }
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('restoreRoleModal')).hide();
            
            // Update the row to remove deleted styling
            const row = document.getElementById('role-row-' + roleId);
            if (row) {
                row.style.transition = 'opacity 0.3s ease';
                
                row.classList.remove('deleted-row');
                const deletedbadge = row.querySelector('.badge-deleted');
                if (deletedbadge) {
                    deletedbadge.remove();
                }
                
                const actionButtons = row.querySelector('.action-buttons');
                actionButtons.innerHTML = `
                    <button class="btn-edit" onclick="editRole(${roleId})" data-bs-toggle="modal" data-bs-target="#editRoleModal">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-delete" onclick="deleteRole(${roleId}, '${roleName}')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                `;
            }
            
            // Show success notification
            showNotification('Role restored successfully', 'success');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error restoring role');
    });
    
    // Clear pending data
    pendingRestoreRoleId = null;
    pendingRestoreRoleName = null;
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

// Handle edit form submission
document.getElementById('editRoleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const action = this.action;

    fetch(action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update CSRF token
            document.querySelector('[name="<?= csrf_token() ?>"]').value = data.csrf_hash;
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating role');
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

// Search/filter roles functionality
function searchRoles(searchValue) {
    const tableRows = document.querySelectorAll('.admin-table tbody tr');
    const searchTerm = searchValue.toLowerCase().trim();
    let visibleCount = 0;

    tableRows.forEach(row => {
        const roleName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const roleDescription = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        
        if (roleName.includes(searchTerm) || roleDescription.includes(searchTerm)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Show "no results" message if no roles match
    let emptyStateDiv = document.querySelector('.search-empty-state');
    if (visibleCount === 0 && searchTerm !== '') {
        if (!emptyStateDiv) {
            emptyStateDiv = document.createElement('div');
            emptyStateDiv.className = 'empty-state search-empty-state';
            emptyStateDiv.style.padding = '40px 20px';
            emptyStateDiv.innerHTML = '<i class="fas fa-search"></i><p>No roles found matching "' + escapeHtml(searchValue) + '"</p>';
            document.querySelector('.table-responsive').appendChild(emptyStateDiv);
        } else {
            emptyStateDiv.innerHTML = '<i class="fas fa-search"></i><p>No roles found matching "' + escapeHtml(searchValue) + '"</p>';
            emptyStateDiv.style.display = '';
        }
    } else if (emptyStateDiv) {
        emptyStateDiv.style.display = 'none';
    }
}

// Helper function to escape HTML special characters
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Real-time validation for role name input
document.addEventListener('DOMContentLoaded', function() {
    const addRoleNameInput = document.getElementById('addRoleName');
    const addNameError = document.getElementById('addNameError');
    const editRoleNameInput = document.getElementById('editRoleName');
    const editNameError = document.getElementById('editNameError');
    const allowedPattern = /^[a-zA-Z0-9 ]*$/;
    
    // Validation for Add Role modal
    if (addRoleNameInput) {
        addRoleNameInput.addEventListener('input', function(e) {
            if (!allowedPattern.test(this.value)) {
                addNameError.style.display = 'block';
                this.classList.add('is-invalid');
                // Remove special characters
                this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '');
            } else {
                addNameError.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });
        
        // Reset error on modal close
        const addRoleModal = document.getElementById('addRoleModal');
        if (addRoleModal) {
            addRoleModal.addEventListener('hidden.bs.modal', function() {
                addNameError.style.display = 'none';
                addRoleNameInput.classList.remove('is-invalid');
                addRoleNameInput.value = '';
                document.getElementById('addRoleDescription').value = '';
                document.getElementById('addRoleErrors').style.display = 'none';
            });
        }
    }
    
    // Validation for Edit Role modal
    if (editRoleNameInput) {
        editRoleNameInput.addEventListener('input', function(e) {
            if (!allowedPattern.test(this.value)) {
                editNameError.style.display = 'block';
                this.classList.add('is-invalid');
                // Remove special characters
                this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '');
            } else {
                editNameError.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });
        
        // Reset error on modal close
        const editRoleModal = document.getElementById('editRoleModal');
        if (editRoleModal) {
            editRoleModal.addEventListener('hidden.bs.modal', function() {
                editNameError.style.display = 'none';
                editRoleNameInput.classList.remove('is-invalid');
                document.getElementById('editRoleErrors').style.display = 'none';
            });
        }
    }
});
</script>

<?= $this->endSection() ?>
