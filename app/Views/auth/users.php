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

    /* ===== Action Buttons ===== */
    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-edit,
    .btn-delete {
        padding: 6px 12px;
        font-size: 0.85rem;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
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
                                <span class="badge <?= $u->role === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                                    <?= esc($u->role) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $u->is_active ? 'badge-active' : 'badge-inactive' ?>">
                                    <?= $u->is_active ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($u->created_at)) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-sm btn-edit" onclick="editUser(<?= $u->id ?>, '<?= esc($u->name) ?>', '<?= esc($u->email) ?>', '<?= $u->role ?>', <?= $u->is_active ?>)" title="Edit User">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-delete" onclick="deleteUser(<?= $u->id ?>)" title="Delete User">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
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
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Enter password (min 6 characters)" required minlength="6">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label fw-600">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="" disabled selected>Select role</option>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
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
                        <input type="password" class="form-control" id="editPassword" name="password"
                               placeholder="Leave blank to keep current password" minlength="6">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editRole" class="form-label fw-600">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="editRole" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
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

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        fetch(`<?= base_url('users/delete') ?>/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_header() ?>': '<?= csrf_token() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User deleted successfully!');
                location.reload();
            } else {
                alert('Error deleting user: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the user.');
        });
    }
}

function editUser(userId, name, email, role, isActive) {
    // Populate modal fields with user data
    document.getElementById('editUserId').value = userId;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editRole').value = role;
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
    const role = document.getElementById('editRole').value;
    const isActive = document.getElementById('editIsActive').value;
    
    console.log('Submitting form with:', {userId, name, email, role, isActive});
    
    // Validate form
    if (!name || !email || !role) {
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
    formData.append('role', role);
    formData.append('is_active', isActive);
    
    const url = `<?= base_url('users/update') ?>/${userId}`;
    console.log('Fetching:', url);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_header() ?>': '<?= csrf_token() ?>'
        },
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

<?= $this->endSection() ?>
