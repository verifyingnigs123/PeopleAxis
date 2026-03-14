<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .profile-shell {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 16px;
    }

    .profile-header {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 14px 18px;
        margin-bottom: 14px;
    }

    .profile-header h1 {
        color: #2f5f45;
        font-weight: 700;
        margin: 0;
        font-size: 2rem;
    }

    .identity-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 20px;
        text-align: center;
    }

    .profile-avatar {
        width: 88px;
        height: 88px;
        margin: 0 auto 14px;
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .profile-name {
        color: #2f5f45;
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .profile-role {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 12px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .status-pill.active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-pill.inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .identity-meta {
        margin-top: 16px;
        border-top: 1px solid #e2e8f0;
        padding-top: 14px;
        text-align: left;
    }

    .identity-meta-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .identity-meta-row:last-child {
        border-bottom: none;
    }

    .identity-label {
        color: #64748b;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        font-weight: 600;
    }

    .identity-value {
        color: #2f5f45;
        font-size: 0.87rem;
        font-weight: 700;
        text-align: right;
    }

    .details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .detail-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 18px;
    }

    .detail-card h3 {
        color: #2f5f45;
        font-weight: 700;
        margin: 0 0 14px 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1.05rem;
    }

    .detail-item {
        display: grid;
        grid-template-columns: 150px 1fr;
        gap: 12px;
        align-items: start;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding-top: 2px;
    }

    .detail-value {
        color: #2c3e50;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.2px;
        background: #e8ecff;
        color: #6ea988;
    }

    .full-width {
        grid-column: 1 / -1;
    }

    .muted {
        color: #64748b;
        font-size: 0.9rem;
    }

    @media (max-width: 992px) {
        .profile-shell {
            grid-template-columns: 1fr;
        }

        .details-grid {
            grid-template-columns: 1fr;
        }

        .detail-item {
            grid-template-columns: 1fr;
            gap: 4px;
        }
    }
</style>

<?php
    $isActive = isset($user->is_active) ? ((int) $user->is_active === 1) : true;
    $roleName = session()->get('role_name') ?? 'Employee';
    $initial = strtoupper(substr(trim((string) ($user->name ?? 'U')), 0, 1));
?>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Profile</span>
</div>

<!-- Page Header -->
<div class="profile-header">
    <h1><i class="fas fa-user-circle"></i> My Profile</h1>
</div>

<!-- Profile Container -->
<div class="profile-shell">
    <div class="identity-card">
        <div class="profile-avatar">
            <?= esc($initial !== '' ? $initial : 'U') ?>
        </div>

        <div class="profile-name"><?= esc($user->name ?? 'User') ?></div>
        <div class="profile-role"><?= esc($roleName) ?></div>

        <span class="status-pill <?= $isActive ? 'active' : 'inactive' ?>">
            <i class="fas fa-circle"></i> <?= $isActive ? 'Active' : 'Inactive' ?>
        </span>

        <div class="identity-meta">
            <div class="identity-meta-row">
                <div class="identity-label">User ID</div>
                <div class="identity-value"><?= esc($user->id ?? 'N/A') ?></div>
            </div>
            <div class="identity-meta-row">
                <div class="identity-label">Joined</div>
                <div class="identity-value"><?= !empty($user->created_at) ? date('M d, Y', strtotime((string) $user->created_at)) : 'N/A' ?></div>
            </div>
        </div>
    </div>

    <div class="details-grid">
        <div class="detail-card">
            <h3><i class="fas fa-id-card"></i> Personal Details</h3>

            <div class="detail-item">
                <div class="detail-label">Full Name</div>
                <div class="detail-value"><?= esc($user->name ?? 'N/A') ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Role</div>
                <div class="detail-value"><?= esc($roleName) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Status</div>
                <div class="detail-value"><span class="badge"><?= $isActive ? 'Active' : 'Inactive' ?></span></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Date Joined</div>
                <div class="detail-value"><?= !empty($user->created_at) ? date('M d, Y', strtotime((string) $user->created_at)) : 'N/A' ?></div>
            </div>
        </div>

        <div class="detail-card">
            <h3><i class="fas fa-address-book"></i> Contact Details</h3>

            <div class="detail-item">
                <div class="detail-label">Email Address</div>
                <div class="detail-value"><?= esc($user->email ?? 'N/A') ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Phone Number</div>
                <div class="detail-value"><?= esc($user->phone ?? 'Not Provided') ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Address</div>
                <div class="detail-value"><?= esc($user->address ?? 'Not Provided') ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Account ID</div>
                <div class="detail-value"><?= esc($user->id ?? 'N/A') ?></div>
            </div>
        </div>

        <div class="detail-card full-width">
            <h3><i class="fas fa-shield-alt"></i> Access Scope</h3>
            <p class="muted">Your current access is based on your role and company policy. Contact HR Admin if you need profile updates or permission changes.</p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
