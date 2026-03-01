<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .page-header {
        margin-bottom: 30px;
    }

    .page-header h1 {
        color: #1e3c72;
        font-weight: 700;
        margin: 0;
    }

    .form-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 40px;
        max-width: 600px;
        margin: 0 auto;
    }

    .form-header {
        margin-bottom: 30px;
        text-align: center;
    }

    .form-header h2 {
        color: #1e3c72;
        font-weight: 700;
        margin: 0;
    }

    .form-header p {
        color: #95a5a6;
        margin-top: 5px;
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
        border-color: #2a5298;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
    }

    .form-control.is-invalid {
        border-color: #e74c3c;
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    .invalid-feedback {
        color: #e74c3c;
        font-size: 0.85rem;
        margin-top: 5px;
        display: block;
    }

    textarea.form-control {
        resize: vertical;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 35px;
    }

    .btn {
        padding: 12px 28px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-submit {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        flex: 1;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.4);
    }

    .btn-cancel {
        background: #e1e8ed;
        color: #2c3e50;
        flex: 0 1 auto;
    }

    .btn-cancel:hover {
        background: #d5dce3;
    }

    .alert {
        border-radius: 6px;
        padding: 15px 20px;
        margin-bottom: 20px;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .required-notice {
        color: #e74c3c;
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <a href="<?= base_url('roles') ?>">Roles</a> /
    <span>Create New Role</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <h1><i class="fas fa-plus-circle"></i> Create New Role</h1>
</div>

<!-- Form Container -->
<div class="form-container">
    <div class="form-header">
        <h2>New Role Details</h2>
        <p>Fill in the form below to create a new system role</p>
    </div>

    <!-- Validation Errors -->
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <strong>Please fix the following errors:</strong>
            <ul style="margin-bottom:0;margin-top:10px;">
                <?php foreach ($errors as $field => $message): ?>
                    <li><?= $message ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('roles/store') ?>" method="POST">
        <?= csrf_field() ?>

        <!-- Role Name Field -->
        <div class="form-group">
            <label for="name" class="form-label">
                Role Name <span class="required-notice">*</span>
            </label>
            <input 
                type="text" 
                class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                id="name" 
                name="name" 
                placeholder="e.g., Manager, Supervisor, Assistant"
                value="<?= old('name') ?>"
                required
            >
            <?php if (isset($errors['name'])): ?>
                <span class="invalid-feedback"><?= $errors['name'] ?></span>
            <?php endif; ?>
        </div>

        <!-- Description Field -->
        <div class="form-group">
            <label for="description" class="form-label">
                Description
            </label>
            <textarea 
                class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                id="description" 
                name="description" 
                placeholder="Describe what this role does and its responsibilities"
                rows="4"
            ><?= old('description') ?></textarea>
            <?php if (isset($errors['description'])): ?>
                <span class="invalid-feedback"><?= $errors['description'] ?></span>
            <?php endif; ?>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="<?= base_url('roles') ?>" class="btn btn-cancel">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-submit">
                <i class="fas fa-save"></i> Create Role
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
