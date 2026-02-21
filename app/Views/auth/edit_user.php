<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .edit-user-form {
        max-width: 600px;
        margin: 0 auto;
    }

    .form-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        padding: 30px;
        margin-top: 30px;
    }

    .form-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        margin: -30px -30px 30px -30px;
    }

    .form-header h1 {
        margin: 0;
        font-weight: 700;
        font-size: 1.8rem;
    }

    .form-header p {
        margin: 8px 0 0 0;
        opacity: 0.9;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 10px 12px;
        font-size: 0.95rem;
    }

    .form-control:focus {
        border-color: #1e3c72;
        box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.1);
    }

    .text-danger {
        color: #e74c3c;
    }

    .btn-group {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }

    .btn {
        flex: 1;
        padding: 10px 20px;
        font-size: 0.95rem;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
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
        border-radius: 8px;
        border: none;
        margin-bottom: 20px;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        padding: 12px 16px;
    }

    .alert-error ul {
        margin: 0;
        padding-left: 20px;
    }

    .row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    @media (max-width: 768px) {
        .row {
            grid-template-columns: 1fr;
        }

        .btn-group {
            flex-direction: column;
        }

        .form-card {
            margin-left: 15px;
            margin-right: 15px;
        }
    }
</style>

<div class="edit-user-form">
    <div class="form-card">
        <div class="form-header">
            <h1><i class="fas fa-user-edit me-2"></i> Edit User</h1>
            <p>Update user information and permissions</p>
        </div>

        <?php if (session()->has('errors')): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('users/update/' . $user->id) ?>" method="POST">
            <?= csrf_field() ?>

            <div class="row">
                <div class="form-group">
                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= old('name', esc($user->name)) ?>" placeholder="Enter full name" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= old('email', esc($user->email)) ?>" placeholder="Enter email address" required>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="user" <?= $user->role === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= $user->role === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="is_active" class="form-label">Status <span class="text-danger">*</span></label>
                    <select id="is_active" name="is_active" class="form-control" required>
                        <option value="1" <?= $user->is_active == 1 ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= $user->is_active == 0 ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                <small class="text-muted">Leave blank if you don't want to change the password</small>
            </div>

            <div class="btn-group">
                <a href="<?= base_url('users') ?>" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update User
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
