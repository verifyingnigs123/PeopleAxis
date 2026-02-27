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

    .page-header p {
        color: #95a5a6;
        margin: 5px 0 0 0;
    }

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

    .form-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 30px;
    }

    .form-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 20px 25px;
        margin: -30px -30px 30px -30px;
        border-radius: 8px 8px 0 0;
    }

    .form-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.3rem;
    }

    .form-group label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .form-control {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 10px 15px;
        font-size: 0.95rem;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        border-color: #2a5298;
        box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border: none;
        padding: 10px 25px;
        border-radius: 6px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.4);
    }

    .btn-secondary {
        background: #6c757d;
        border: none;
        padding: 10px 25px;
        border-radius: 6px;
        font-weight: 600;
    }

    .alert {
        border-radius: 6px;
        border: none;
        margin-bottom: 20px;
        padding: 15px 20px;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .password-wrapper {
        position: relative;
    }

    .password-wrapper .password-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: #3498db;
        cursor: pointer;
        z-index: 10;
    }

    .required-star {
        color: #e74c3c;
    }
</style>

<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
    <span>/</span>
    <a href="<?= base_url('users') ?>">Users</a>
    <span>/</span>
    <span>Add User</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="fas fa-user-plus"></i> Add New User</h1>
        <p>Create a new user account with appropriate role and permissions</p>
    </div>
</div>

<div class="form-container">
    <div class="form-header">
        <h2><i class="fas fa-user-plus me-2"></i> User Information</h2>
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

    <form action="<?= base_url('users/store') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="name" class="form-label">
                        Full Name <span class="required-star">*</span>
                    </label>
                    <input type="text" class="form-control" id="name" name="name"
                           placeholder="Enter full name" required
                           value="<?= old('name') ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="email" class="form-label">
                        Email Address <span class="required-star">*</span>
                    </label>
                    <input type="email" class="form-control" id="email" name="email"
                           placeholder="Enter email address" required
                           value="<?= old('email') ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="password" class="form-label">
                        Password <span class="required-star">*</span>
                    </label>
                    <div class="password-wrapper">
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Enter password (min 6 characters)" required minlength="6"
                               value="HRmanage" style="padding-right: 40px;">
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password', this)">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                    <small class="text-muted">Default password: HRmanage (change as needed)</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="confirm_password" class="form-label">
                        Confirm Password <span class="required-star">*</span>
                    </label>
                    <div class="password-wrapper">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                               placeholder="Confirm password" required minlength="6"
                               value="HRmanage" style="padding-right: 40px;">
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('confirm_password', this)">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="role_id" class="form-label">
                        Role <span class="required-star">*</span>
                    </label>
                    <select class="form-select" id="role_id" name="role_id" required>
                        <option value="" disabled selected>Select role</option>
                        <option value="1" <?= old('role_id') == '1' ? 'selected' : '' ?>>Super Admin</option>
                        <option value="2" <?= old('role_id') == '2' ? 'selected' : '' ?>>HR Admin</option>
                        <option value="3" <?= old('role_id') == '3' ? 'selected' : '' ?>>Manager</option>
                        <option value="4" <?= old('role_id') == '4' ? 'selected' : '' ?>>Employee</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="is_active" class="form-label">
                        Account Status <span class="required-star">*</span>
                    </label>
                    <select class="form-select" id="is_active" name="is_active" required>
                        <option value="1" <?= old('is_active') == '1' ? 'selected' : '' ?> selected>Active</option>
                        <option value="0" <?= old('is_active') == '0' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="send_welcome" name="send_welcome" value="1">
                <label class="form-check-label" for="send_welcome">
                    Send welcome email to user
                </label>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?= base_url('users') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Users
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Create User
            </button>
        </div>
    </form>
</div>

<script>
function togglePasswordVisibility(fieldId, button) {
    const field = document.getElementById(fieldId);
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Password and confirm password do not match!');
        return false;
    }
    
    return true;
});
</script>

<?= $this->endSection() ?>
