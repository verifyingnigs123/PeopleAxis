<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .settings-shell {
        max-width: 980px;
        margin: 0 auto;
        padding: 8px 0 18px;
    }

    .settings-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .settings-header h1 {
        margin: 0;
        color: #2f5f45;
        font-weight: 700;
    }

    .settings-header p {
        margin: 4px 0 0;
        color: #6f8192;
        font-size: 0.92rem;
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

    .settings-card {
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 16px;
    }

    .settings-card-header {
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: #ffffff;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .settings-card-header h2 {
        margin: 0;
        font-size: 1.06rem;
        font-weight: 700;
    }

    .settings-card-body {
        padding: 18px;
    }

    .settings-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .field.full {
        grid-column: 1 / -1;
    }

    .field label {
        color: #2a3e55;
        font-weight: 600;
        font-size: 0.9rem;
        margin: 0;
    }

    .field small {
        color: #7f90a0;
        font-size: 0.82rem;
    }

    .field .form-control,
    .field .form-select {
        border: 1px solid #d9e2ec;
        border-radius: 8px;
        padding: 9px 11px;
        font-size: 0.92rem;
    }

    .field .form-control:focus,
    .field .form-select:focus {
        border-color: #6ea988;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
    }

    .switch-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .switch-item {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        background: #f8fbff;
    }

    .switch-item .form-check {
        margin: 0;
        display: flex;
        align-items: start;
        gap: 9px;
    }

    .switch-item .form-check-input {
        margin-top: 3px;
        width: 17px;
        height: 17px;
        accent-color: #6ea988;
    }

    .switch-item .form-check-label {
        margin: 0;
        font-weight: 600;
        color: #2f4358;
    }

    .switch-item .hint {
        margin-top: 5px;
        color: #7f90a0;
        font-size: 0.82rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .info-item {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 11px;
        background: #fcfdff;
    }

    .info-item .label {
        margin: 0;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.35px;
        font-weight: 700;
        color: #7f90a0;
    }

    .info-item .value {
        margin: 5px 0 0;
        font-size: 0.98rem;
        font-weight: 700;
        color: #1f3550;
    }

    .form-actions {
        margin-top: 14px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-settings {
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border: 1px solid transparent;
    }

    .btn-settings-primary {
        background: #6ea988;
        color: #ffffff;
        border-color: #6ea988;
    }

    .btn-settings-primary:hover {
        background: #21437c;
        color: #ffffff;
    }

    .btn-settings-secondary {
        background: #f1f5fb;
        color: #6ea988;
        border-color: #c9d8ef;
    }

    .btn-settings-secondary:hover {
        background: #e7effa;
        color: #1f4077;
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

    @media (max-width: 992px) {
        .settings-grid,
        .switch-grid,
        .info-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 640px) {
        .settings-grid,
        .switch-grid,
        .info-grid {
            grid-template-columns: 1fr;
        }

        .settings-card-body {
            padding: 14px;
        }

        .form-actions {
            justify-content: stretch;
        }

        .btn-settings {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<?php
    $role = session()->get('role');
    $roleName = session()->get('role_name') ?? $role;
    $isAdminScope = in_array($roleName, ['Super Admin', 'admin'], true);
?>

<div class="settings-shell">
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
        <span>/</span>
        <span>Settings</span>
    </div>

    <!-- Settings Header -->
    <div class="settings-header">
        <h1><i class="fas fa-cog"></i> Settings</h1>
        <p>Configure application behavior, defaults, and notification preferences</p>
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

    <form action="<?= base_url('settings/update') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="settings-card">
            <div class="settings-card-header">
                <i class="fas fa-sliders-h"></i>
                <h2>General Settings</h2>
            </div>
            <div class="settings-card-body">
                <div class="settings-grid">
                    <div class="field">
                        <label for="site_name">Site Name</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" value="<?= old('site_name', 'PeopleAxis') ?>" required>
                        <small>The application name displayed across the system.</small>
                    </div>

                    <div class="field">
                        <label for="site_url">Site URL</label>
                        <input type="url" class="form-control" id="site_url" name="site_url" value="<?= old('site_url', base_url()) ?>" required>
                        <small>Primary application URL used for links and routing.</small>
                    </div>

                    <div class="field">
                        <label for="timezone">Timezone</label>
                        <?php $timezone = old('timezone', 'UTC'); ?>
                        <select class="form-select" id="timezone" name="timezone" required>
                            <option value="UTC" <?= $timezone === 'UTC' ? 'selected' : '' ?>>UTC</option>
                            <option value="America/New_York" <?= $timezone === 'America/New_York' ? 'selected' : '' ?>>Eastern Time</option>
                            <option value="America/Chicago" <?= $timezone === 'America/Chicago' ? 'selected' : '' ?>>Central Time</option>
                            <option value="America/Denver" <?= $timezone === 'America/Denver' ? 'selected' : '' ?>>Mountain Time</option>
                            <option value="America/Los_Angeles" <?= $timezone === 'America/Los_Angeles' ? 'selected' : '' ?>>Pacific Time</option>
                            <option value="Europe/London" <?= $timezone === 'Europe/London' ? 'selected' : '' ?>>London</option>
                            <option value="Europe/Paris" <?= $timezone === 'Europe/Paris' ? 'selected' : '' ?>>Paris</option>
                            <option value="Asia/Tokyo" <?= $timezone === 'Asia/Tokyo' ? 'selected' : '' ?>>Tokyo</option>
                            <option value="Australia/Sydney" <?= $timezone === 'Australia/Sydney' ? 'selected' : '' ?>>Sydney</option>
                        </select>
                        <small>Default timezone for reports and timestamps.</small>
                    </div>

                    <div class="field">
                        <label for="date_format">Date Format</label>
                        <?php $dateFormat = old('date_format', 'Y-m-d'); ?>
                        <select class="form-select" id="date_format" name="date_format" required>
                            <option value="Y-m-d" <?= $dateFormat === 'Y-m-d' ? 'selected' : '' ?>>YYYY-MM-DD</option>
                            <option value="m/d/Y" <?= $dateFormat === 'm/d/Y' ? 'selected' : '' ?>>MM/DD/YYYY</option>
                            <option value="d/m/Y" <?= $dateFormat === 'd/m/Y' ? 'selected' : '' ?>>DD/MM/YYYY</option>
                            <option value="Y/m/d" <?= $dateFormat === 'Y/m/d' ? 'selected' : '' ?>>YYYY/MM/DD</option>
                        </select>
                        <small>Preferred display format for dates.</small>
                    </div>

                    <div class="field">
                        <label for="items_per_page">Items Per Page</label>
                        <input type="number" class="form-control" id="items_per_page" name="items_per_page" value="<?= old('items_per_page', '15') ?>" min="5" max="100" required>
                        <small>How many rows to show in paginated lists.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-card">
            <div class="settings-card-header">
                <i class="fas fa-bell"></i>
                <h2>Notifications & Maintenance</h2>
            </div>
            <div class="settings-card-body">
                <div class="switch-grid">
                    <div class="switch-item">
                        <input type="hidden" name="enable_notifications" value="0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="enable_notifications" name="enable_notifications" value="1" <?= old('enable_notifications', '1') == '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="enable_notifications">Enable In-App Notifications</label>
                        </div>
                        <div class="hint">Show alerts and activity updates inside the app.</div>
                    </div>

                    <div class="switch-item">
                        <input type="hidden" name="enable_email_notifications" value="0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="enable_email_notifications" name="enable_email_notifications" value="1" <?= old('enable_email_notifications', '1') == '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="enable_email_notifications">Enable Email Notifications</label>
                        </div>
                        <div class="hint">Send important notifications via email.</div>
                    </div>

                    <div class="switch-item full" style="grid-column: 1 / -1;">
                        <input type="hidden" name="maintenance_mode" value="0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" <?= old('maintenance_mode', '0') == '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="maintenance_mode">Enable Maintenance Mode</label>
                        </div>
                        <div class="hint">When enabled, only administrators can access the application.</div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($isAdminScope): ?>
            <div class="settings-card">
                <div class="settings-card-header">
                    <i class="fas fa-info-circle"></i>
                    <h2>System Information</h2>
                </div>
                <div class="settings-card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <p class="label">PHP Version</p>
                            <p class="value"><?= phpversion() ?></p>
                        </div>
                        <div class="info-item">
                            <p class="label">CodeIgniter Version</p>
                            <p class="value">4.x</p>
                        </div>
                        <div class="info-item">
                            <p class="label">Environment</p>
                            <p class="value"><?= ENVIRONMENT ?></p>
                        </div>
                        <div class="info-item">
                            <p class="label">Database Driver</p>
                            <p class="value">MySQLi</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-actions">
            <a href="<?= base_url('dashboard') ?>" class="btn-settings btn-settings-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <button type="submit" class="btn-settings btn-settings-primary">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
