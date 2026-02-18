<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .settings-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 30px 0;
    }

    .settings-header {
        margin-bottom: 30px;
    }

    .settings-header h1 {
        color: #2c3e50;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .settings-header p {
        color: #95a5a6;
        font-size: 0.95rem;
    }

    .settings-section {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 25px;
        overflow: hidden;
    }

    .settings-section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .settings-section-header i {
        font-size: 1.3rem;
    }

    .settings-section-header h2 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .settings-section-body {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #2c3e50;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .form-group small {
        display: block;
        margin-top: 5px;
        color: #95a5a6;
        font-size: 0.85rem;
    }

    .form-control, .form-select {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 10px 12px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .form-check input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #667eea;
    }

    .form-check label {
        margin: 0;
        font-weight: 500;
        cursor: pointer;
        color: #495057;
    }

    .button-group {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }

    .btn {
        padding: 10px 25px;
        border-radius: 6px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #f1f3ff;
        color: #667eea;
        border: 1px solid #667eea;
    }

    .btn-secondary:hover {
        background: #667eea;
        color: white;
    }

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

    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
        border-left: 4px solid #17a2b8;
    }

    .breadcrumbs {
        margin-bottom: 20px;
    }

    .breadcrumbs a {
        color: #667eea;
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

    @media (max-width: 768px) {
        .settings-container {
            padding: 20px;
        }

        .settings-section-body {
            padding: 20px;
        }

        .settings-grid {
            grid-template-columns: 1fr;
        }

        .button-group {
            flex-direction: column;
            justify-content: stretch;
        }

        .btn {
            width: 100%;
        }
    }
</style>

<!-- Check if user is logged in -->
<?php if (!session()->get('logged_in')): ?>
    <?= redirect()->to('/login') ?>
<?php endif; ?>

<?php $role = session()->get('role'); ?>

<!-- Settings Container -->
<div class="container settings-container">
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
        <span>/</span>
        <span>Settings</span>
    </div>

    <!-- Settings Header -->
    <div class="settings-header">
        <h1><i class="fas fa-cog"></i> Settings</h1>
        <p>Manage your application and account settings</p>
    </div>

    <!-- Display Flash Messages -->
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
            <i class="fas fa-exclamation-circle"></i> <strong>Validation Errors:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <?php foreach ($errors as $field => $message): ?>
                    <li><?= $message ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- General Settings -->
    <div class="settings-section">
        <div class="settings-section-header">
            <i class="fas fa-sliders-h"></i>
            <h2>General Settings</h2>
        </div>
        <div class="settings-section-body">
            <form action="<?= base_url('settings/update') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="settings-grid">
                    <div class="form-group">
                        <label for="site_name">Site Name</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" 
                               value="PeopleAxis" required>
                        <small>The name of your application</small>
                    </div>

                    <div class="form-group">
                        <label for="site_url">Site URL</label>
                        <input type="url" class="form-control" id="site_url" name="site_url" 
                               value="<?= base_url() ?>" required>
                        <small>Your application's main URL</small>
                    </div>

                    <div class="form-group">
                        <label for="timezone">Timezone</label>
                        <select class="form-select" id="timezone" name="timezone" required>
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">Eastern Time</option>
                            <option value="America/Chicago">Central Time</option>
                            <option value="America/Denver">Mountain Time</option>
                            <option value="America/Los_Angeles">Pacific Time</option>
                            <option value="Europe/London">London</option>
                            <option value="Europe/Paris">Paris</option>
                            <option value="Asia/Tokyo">Tokyo</option>
                            <option value="Australia/Sydney">Sydney</option>
                        </select>
                        <small>Select your timezone</small>
                    </div>

                    <div class="form-group">
                        <label for="date_format">Date Format</label>
                        <select class="form-select" id="date_format" name="date_format" required>
                            <option value="Y-m-d">YYYY-MM-DD</option>
                            <option value="m/d/Y">MM/DD/YYYY</option>
                            <option value="d/m/Y">DD/MM/YYYY</option>
                            <option value="Y/m/d">YYYY/MM/DD</option>
                        </select>
                        <small>Choose your preferred date format</small>
                    </div>

                    <div class="form-group">
                        <label for="items_per_page">Items Per Page</label>
                        <input type="number" class="form-control" id="items_per_page" name="items_per_page" 
                               value="15" min="5" max="100" required>
                        <small>Number of items to display per page</small>
                    </div>
                </div>

                <hr style="margin: 30px 0;">

                <!-- Maintenance Mode -->
                <div class="form-group">
                    <label>Maintenance Mode</label>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="maintenance_mode" 
                               name="maintenance_mode" value="1">
                        <label class="form-check-label" for="maintenance_mode">
                            Enable Maintenance Mode (Application will be offline)
                        </label>
                    </div>
                    <small>When enabled, only administrators can access the application</small>
                </div>

                <div class="button-group">
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notification Settings -->
    <div class="settings-section">
        <div class="settings-section-header">
            <i class="fas fa-bell"></i>
            <h2>Notification Settings</h2>
        </div>
        <div class="settings-section-body">
            <form action="<?= base_url('settings/update') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="enable_notifications" 
                           name="enable_notifications" value="1" checked>
                    <label class="form-check-label" for="enable_notifications">
                        <strong>Enable Notifications</strong><br>
                        <small style="color: #95a5a6;">Receive in-app notifications</small>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="enable_email_notifications" 
                           name="enable_email_notifications" value="1" checked>
                    <label class="form-check-label" for="enable_email_notifications">
                        <strong>Email Notifications</strong><br>
                        <small style="color: #95a5a6;">Receive email notifications about important events</small>
                    </label>
                </div>

                <div class="button-group">
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- System Information Section (Admin Only) -->
    <?php if ($role === 'admin'): ?>
        <div class="settings-section">
            <div class="settings-section-header">
                <i class="fas fa-info-circle"></i>
                <h2>System Information</h2>
            </div>
            <div class="settings-section-body">
                <div class="settings-grid">
                    <div>
                        <p style="color: #95a5a6; font-size: 0.9rem; margin-bottom: 5px;">PHP Version</p>
                        <p style="color: #2c3e50; font-weight: 600; font-size: 1rem;"><?= phpversion() ?></p>
                    </div>
                    <div>
                        <p style="color: #95a5a6; font-size: 0.9rem; margin-bottom: 5px;">CodeIgniter Version</p>
                        <p style="color: #2c3e50; font-weight: 600; font-size: 1rem;">4.x</p>
                    </div>
                    <div>
                        <p style="color: #95a5a6; font-size: 0.9rem; margin-bottom: 5px;">Environment</p>
                        <p style="color: #2c3e50; font-weight: 600; font-size: 1rem;"><?= ENVIRONMENT ?></p>
                    </div>
                    <div>
                        <p style="color: #95a5a6; font-size: 0.9rem; margin-bottom: 5px;">Database</p>
                        <p style="color: #2c3e50; font-weight: 600; font-size: 1rem;">CodeIgniter 4</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
