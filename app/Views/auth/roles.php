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

    .btn-add-role {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
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
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 20px 25px;
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
        color: #1e3c72;
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
        color: #2a5298;
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

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
</style>

<!-- Breadcrumbs -->
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

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);color:white;border:none;">
                <h5 class="modal-title" id="addRoleModalLabel">
                    <i class="fas fa-plus me-2"></i> Add New Role
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('roles/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body" style="padding:30px;">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-600">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter role name" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-600">Description</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Enter role description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border:none;padding:15px 30px 25px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);border:none;padding:8px 24px;">
                        <i class="fas fa-save me-1"></i> Save Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);color:white;border:none;">
                <h5 class="modal-title" id="editRoleModalLabel">
                    <i class="fas fa-edit me-2"></i> Edit Role
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRoleForm" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body" style="padding:30px;">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label fw-600">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" placeholder="Enter role name" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label fw-600">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" placeholder="Enter role description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border:none;padding:15px 30px 25px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);border:none;padding:8px 24px;">
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

<script>
let pendingDeleteRoleId = null;
let pendingDeleteRoleName = null;
let pendingRestoreRoleId = null;
let pendingRestoreRoleName = null;

function editRole(roleId) {
    fetch('<?= base_url('roles/getRole') ?>/' + roleId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('edit_name').value = data.data.name;
                document.getElementById('edit_description').value = data.data.description || '';
                document.getElementById('editRoleForm').action = '<?= base_url('roles/update') ?>/' + roleId;
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
</script>

<?= $this->endSection() ?>
