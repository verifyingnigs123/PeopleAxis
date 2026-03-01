<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    /* ===== Page Header ===== */
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

    .page-header p {
        color: #95a5a6;
        margin: 5px 0 0 0;
    }

    /* ===== Breadcrumbs ===== */
    .breadcrumbs {
        margin-bottom: 20px;
    }

    .breadcrumbs a {
        color: #2a5298;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .breadcrumbs a:hover {
        text-decoration: underline;
    }

    .breadcrumbs span {
        color: #95a5a6;
        margin: 0 5px;
    }

    /* ===== Add User Button ===== */
    .btn-add-user {
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

    .btn-add-user:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.4);
    }

    /* ===== Panel ===== */
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

    .panel-header i {
        margin-right: 8px;
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

    /* ===== Table ===== */
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
        letter-spacing: 0.5px;
    }

    .admin-table tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f3f5;
        font-size: 0.95rem;
        color: #495057;
        vertical-align: middle;
    }

    .admin-table tbody tr:last-child td {
        border-bottom: none;
    }

    .admin-table tbody tr:hover {
        background: #f8f9ff;
    }

    /* ===== Badges ===== */
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .badge-admin {
        background: #e8ecff;
        color: #2a5298;
    }

    .badge-user {
        background: #e8f5e9;
        color: #28a745;
    }

    .badge-active {
        background: #d4edda;
        color: #155724;
    }

    .badge-inactive {
        background: #f8d7da;
        color: #721c24;
    }

    /* ===== Avatar ===== */
    .avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        margin-right: 10px;
        flex-shrink: 0;
    }

    .user-cell {
        display: flex;
        align-items: center;
    }

    /* ===== Alerts ===== */
    .alert {
        border-radius: 6px;
        border: none;
        margin-bottom: 20px;
        padding: 15px 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    /* ===== Empty State ===== */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #95a5a6;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        display: block;
    }

    .fw-600 { font-weight: 600; }

    /* Password toggle styles */
    .password-wrapper { position: relative; }
    .password-wrapper .password-toggle {
        position: absolute;
        right: .5rem;
        top: 50%;
        transform: translateY(-50%);
        z-index: 9999;
        background: transparent;
        border: none;
        width: 2.2rem;
        height: 2.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3498db;
        cursor: pointer;
    }
    .password-wrapper .password-toggle i {
        pointer-events: auto;
        display: inline-block;
    }

    /* ===== Action Buttons ===== */
    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-edit,
    .btn-delete,
    .btn-activate,
    .btn-deactivate {
        padding: 6px 12px;
        font-size: 0.85rem;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-align: center;
    }

    .btn-edit {
        background: #3498db;
        color: white;
    }

    .btn-edit:hover {
        background: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(52, 152, 219, 0.3);
    }

    .btn-delete {
        background: #e74c3c;
        color: white;
    }

    .btn-delete:hover {
        background: #c0392b;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
    }

    .btn-activate {
        background: #27ae60;
        color: white;
    }

    .btn-activate:hover {
        background: #229954;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);
    }

    .btn-deactivate {
        background: #f39c12;
        color: white;
    }

    .btn-deactivate:hover {
        background: #e67e22;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(243, 156, 18, 0.3);
    }

    .btn-disabled {
        background: #95a5a6;
        color: white;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .btn-disabled:hover {
        background: #95a5a6;
        transform: none;
        box-shadow: none;
    }
</style>

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
        <i class="fas fa-user-plus"></i> Add User
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
            <input type="text" id="userSearch" placeholder="Search users..." onkeyup="searchUsers(this.value)">
        </div>
    </div>
    <?php
        // Load roles for select boxes
        $db = \Config\Database::connect();
        $rolesList = $db->table('roles')->select('id, name')->orderBy('name','ASC')->get()->getResult();
    ?>
    <div class="table-responsive">
        <?php if (!empty($users)): ?>
            <table class="admin-table" id="usersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $i => $u): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <div class="user-cell">
                                    <div class="avatar"><?= strtoupper(substr($u->name, 0, 1)) ?></div>
                                    <?= esc($u->name) ?>
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
                            <td>
                                <span class="badge <?= $u->is_active ? 'badge-active' : 'badge-inactive' ?>" id="status-<?= $u->id ?>">
                                    <?= $u->is_active ? 'ACTIVE' : 'INACTIVE' ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($u->created_at)) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-sm btn-edit" onclick="editUser(<?= $u->id ?>, '<?= esc($u->name) ?>', '<?= esc($u->email) ?>', <?= (int)($u->role_id ?? 0) ?>, <?= $u->is_active ?>)" title="Edit User">
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
                                        <button type="button" class="btn btn-sm btn-activate" onclick="toggleUserStatus(<?= $u->id ?>, 'activate')" title="Activate User">
                                            <i class="fas fa-user-check"></i> Activate
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
                <i class="fas fa-users-slash"></i>
                <p>No users found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete/Restore Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);color:white;border:none;">
                <h5 class="modal-title" id="deleteUserModalLabel"><i class="fas fa-exclamation-triangle me-2"></i> Confirm Action</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <p id="deleteUserMessage">Are you sure you want to delete this user? This action can be undone by restoring them.</p>
            </div>
            <div class="modal-footer" style="border:none;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);color:white;border:none;">
                <h5 class="modal-title" id="addUserModalLabel">
                    <i class="fas fa-user-plus me-2"></i> Add New User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('users/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body" style="padding:30px;">

                    <div class="mb-3">
                        <label for="name" class="form-label fw-600">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                               placeholder="Enter full name" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-600">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="Enter email address" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-600">Password <span class="text-danger">*</span></label>
                        <div class="password-wrapper">
                           <input type="password" class="form-control" id="password" name="password"
                               placeholder="Enter password (min 6 characters)" required minlength="6" value="HRmanage" style="padding-right:2.8rem;">
                           <button type="button" id="toggleAddPassword" class="password-toggle" aria-pressed="false" aria-label="Show password" onclick="toggleAddPasswordVisibility(event)">
                               <i class="fas fa-eye-slash" aria-hidden="true"></i>
                           </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label fw-600">Role <span class="text-danger">*</span></label>
                                <select class="form-select" id="role" name="role_id" required>
                                    <option value="" disabled selected>Select role</option>
                                    <?php foreach ($rolesList as $r): ?>
                                        <option value="<?= $r->id ?>"><?= esc($r->name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="is_active" class="form-label fw-600">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="is_active" name="is_active" required>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="border:none;padding:15px 30px 25px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);border:none;padding:8px 24px;">
                        <i class="fas fa-save me-1"></i> Save User
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
            <div class="modal-header" style="background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);color:white;border:none;">
                <h5 class="modal-title" id="editUserModalLabel">
                    <i class="fas fa-user-edit me-2"></i> Edit User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" action="#" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body" style="padding:30px;">
                    <input type="hidden" id="editUserId" name="user_id">

                    <div class="mb-3">
                        <label for="editName" class="form-label fw-600">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editName" name="name"
                               placeholder="Enter full name" required>
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
                    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);border:none;padding:8px 24px;">
                        <i class="fas fa-save me-1"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function searchUsers(query) {
    const rows = document.querySelectorAll('#usersTable tbody tr');
    query = query.toLowerCase();
    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
    });
}

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
    msg.textContent = `Are you sure you want to delete ${name}? You can restore this user later.`;
    confirmBtn.dataset.userId = userId;
    // keep reference to source button to update UI after response
    confirmBtn._sourceButton = button;
    // set button label
    confirmBtn.innerHTML = 'Delete';
    confirmBtn.className = 'btn btn-danger';
    const modal = new bootstrap.Modal(modalEl);
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
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        if (data.success) {
            // update UI: status badge and replace delete with restore
            const statusBadge = document.getElementById(`status-${userId}`);
            if (statusBadge) {
                statusBadge.className = 'badge badge-inactive';
                statusBadge.textContent = 'DELETED';
            }
            const sourceBtn = btn._sourceButton || btn._sourceButton;
            // find action-buttons container
            const actionCell = (sourceBtn && sourceBtn.closest('td')) ? sourceBtn.closest('td') : null;
            if (actionCell) {
                // remove existing delete button(s)
                actionCell.querySelectorAll('.btn-delete').forEach(n => n.remove());
                // add restore button
                const restoreBtn = document.createElement('button');
                restoreBtn.type = 'button';
                restoreBtn.className = 'btn btn-sm btn-restore';
                restoreBtn.setAttribute('data-user-id', userId);
                restoreBtn.innerHTML = '<i class="fas fa-undo"></i> Restore';
                restoreBtn.addEventListener('click', function(e){ performRestore(userId); });
                const container = actionCell.querySelector('.action-buttons') || actionCell;
                container.appendChild(restoreBtn);
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

function performRestore(userId) {
    const btn = document.querySelector(`.btn-restore[data-user-id="${userId}"]`);
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Restoring...'; }

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
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-undo"></i> Restore'; }
        if (data.success) {
            const statusBadge = document.getElementById(`status-${userId}`);
            if (statusBadge) {
                statusBadge.className = 'badge badge-active';
                statusBadge.textContent = 'ACTIVE';
            }

            // replace restore with delete
            const restoreBtn = document.querySelector(`.btn-restore[data-user-id="${userId}"]`);
            if (restoreBtn) {
                const actionCell = restoreBtn.closest('td');
                restoreBtn.remove();
                const delBtn = document.createElement('button');
                delBtn.type = 'button';
                delBtn.className = 'btn btn-sm btn-delete';
                delBtn.setAttribute('data-user-id', userId);
                const name = actionCell ? actionCell.closest('tr').querySelector('.user-cell').innerText.trim() : '';
                delBtn.setAttribute('data-user-name', name);
                delBtn.title = 'Delete User';
                delBtn.innerHTML = '<i class="fas fa-trash"></i> Delete';
                delBtn.addEventListener('click', function(e){ showDeleteModal(this, userId, name); });
                const container = actionCell.querySelector('.action-buttons') || actionCell;
                container.appendChild(delBtn);
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
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-undo"></i> Restore'; }
        console.error(err);
        showNotification('An unexpected error occurred', 'error');
    });
}

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

function editUser(userId, name, email, roleId, isActive) {
    // Populate modal fields with user data
    document.getElementById('editUserId').value = userId;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
<<<<<<< HEAD
    document.getElementById('editRole').value = roleId;
=======
    // role passed is role_id
    document.getElementById('editRole').value = roleId;
>>>>>>> 6af8e22 (another update)
    document.getElementById('editIsActive').value = isActive;
    
    // Clear password field
    document.getElementById('editPassword').value = '';
    
    // Show the modal
    const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
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
        alert('Please fill in all required fields');
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
<<<<<<< HEAD
    formData.append('role_id', roleId);
=======
    formData.append('role_id', roleId);
>>>>>>> 6af8e22 (another update)
    formData.append('is_active', isActive);
    
    const url = `<?= base_url('users/update') ?>/${userId}`;
    console.log('Fetching:', url);
    
    const csrfHeaderForm = document.querySelector('meta[name="<?= csrf_header() ?>"]').getAttribute('content');
    const headersForm = {'X-Requested-With': 'XMLHttpRequest'};
    headersForm['<?= csrf_header() ?>'] = csrfHeaderForm;

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
            alert('User updated successfully!');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
            if (modal) modal.hide();
            
            // Reload page
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error: ' + (data.message || 'Failed to update user'));
        }
    })
    .catch(error => {
        if (spinner) spinner.classList.remove('active');
        console.error('Fetch error:', error);
        alert('Error: ' + error.message);
    });
});
</script>

<script>
// Toggle for Add User password
function toggleAddPasswordVisibility(e) {
    try {
        if (e && e.preventDefault) e.preventDefault();
        var pwd = document.getElementById('password');
        var btn = document.getElementById('toggleAddPassword');
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
    var a = e.target.closest && e.target.closest('#toggleAddPassword');
    if (a) return toggleAddPasswordVisibility(e);
    var b = e.target.closest && e.target.closest('#toggleEditPassword');
    if (b) return toggleEditPasswordVisibility(e);
});

// Also attach directly to inner icons for reliability
var addEye = document.querySelector('#toggleAddPassword i');
if (addEye) addEye.addEventListener('click', function (e) { e.stopPropagation(); toggleAddPasswordVisibility(e); });
var editEye = document.querySelector('#toggleEditPassword i');
if (editEye) editEye.addEventListener('click', function (e) { e.stopPropagation(); toggleEditPasswordVisibility(e); });
</script>

<?= $this->endSection() ?>
