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
        overflow: hidden;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .profile-avatar-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
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

    .profile-message {
        display: none;
        padding: 10px 12px;
        border-radius: 8px;
        margin-bottom: 14px;
        font-size: 0.92rem;
        font-weight: 600;
    }

    .profile-message.success {
        background: #d1fae5;
        color: #065f46;
    }

    .profile-message.error {
        background: #fee2e2;
        color: #991b1b;
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
    $profilePhotoUrl = !empty($user->profile_photo) ? base_url($user->profile_photo) . '?v=' . strtotime((string) ($user->updated_at ?? 'now')) : '';
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
        <div class="profile-avatar" id="profileAvatar" data-initial="<?= esc($initial !== '' ? $initial : 'U') ?>" data-photo-url="<?= esc($profilePhotoUrl) ?>">
            <?php if (!empty($profilePhotoUrl)): ?>
                <img id="profileAvatarImage" src="<?= esc($profilePhotoUrl) ?>" alt="Profile photo">
            <?php else: ?>
                <div id="profileAvatarPlaceholder" class="profile-avatar-placeholder"><?= esc($initial !== '' ? $initial : 'U') ?></div>
            <?php endif; ?>
        </div>

        <div class="profile-name" id="profileNameDisplay"><?= esc($user->name ?? 'User') ?></div>
        <div class="profile-role" id="profileRoleDisplay"><?= esc($roleName) ?></div>

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

    <div class="detail-card">
        <h3><i class="fas fa-edit"></i> Edit Profile</h3>
        <div id="profileMessage" class="profile-message"></div>

        <form method="POST" action="<?= base_url('profile/update') ?>" enctype="multipart/form-data" id="profileForm">
            <?= csrf_field() ?>
            <div class="detail-item">
                <div class="detail-label">Full Name</div>
                <div class="detail-value"><input type="text" id="profileNameInput" name="name" value="<?= esc($user->name ?? '') ?>" required></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Email</div>
                <div class="detail-value"><input type="email" id="profileEmailInput" name="email" value="<?= esc($user->email ?? '') ?>" required></div>
            </div>
            <!-- Password removed from profile edit to avoid inline password changes -->
            <div class="detail-item">
                <div class="detail-label">Profile Photo</div>
                <div class="detail-value">
                    <input type="file" id="profilePhotoInput" name="profile_photo" accept="image/*">
                </div>
            </div>
            <div style="margin-top:12px;">
                <button type="submit" class="btn-add">Save Profile</button>
            </div>
        </form>
    </div>

    <div class="details-grid">
        <div class="detail-card">
            <h3><i class="fas fa-id-card"></i> Personal Details</h3>

            <div class="detail-item">
                <div class="detail-label">Full Name</div>
                <div class="detail-value" id="profileNameDetail"><?= esc($user->name ?? 'N/A') ?></div>
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
                <div class="detail-value" id="profileEmailDetail"><?= esc($user->email ?? 'N/A') ?></div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('profileForm');
    const fileInput = document.getElementById('profilePhotoInput');
    const avatar = document.getElementById('profileAvatar');
    const message = document.getElementById('profileMessage');
    const nameDisplay = document.getElementById('profileNameDisplay');
    const roleDisplay = document.getElementById('profileRoleDisplay');
    const profileNameInput = document.getElementById('profileNameInput');
    const profileEmailInput = document.getElementById('profileEmailInput');
    const profileNameDetail = document.getElementById('profileNameDetail');
    const profileEmailDetail = document.getElementById('profileEmailDetail');

    function showMessage(text, type) {
        if (!message) return;
        message.className = 'profile-message ' + type;
        message.textContent = text;
        message.style.display = 'block';
    }

    function renderAvatarImage(src) {
        if (!avatar) return;
        avatar.innerHTML = '<img id="profileAvatarImage" src="' + src + '" alt="Profile photo">';
    }

    function cacheBustedUrl(src) {
        if (!src) return src;
        const separator = src.includes('?') ? '&' : '?';
        return src + separator + 'v=' + Date.now();
    }

    function renderAvatarInitial(initial) {
        if (!avatar) return;
        avatar.innerHTML = '<div id="profileAvatarPlaceholder" class="profile-avatar-placeholder">' + initial + '</div>';
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) {
                const currentUrl = avatar ? avatar.dataset.photoUrl || '' : '';
                const initial = avatar ? avatar.dataset.initial || 'U' : 'U';
                if (currentUrl) {
                    renderAvatarImage(currentUrl);
                } else {
                    renderAvatarInitial(initial);
                }
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                renderAvatarImage(e.target.result);
            };
            reader.readAsDataURL(file);
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            showMessage('Saving profile…', 'success');

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw data;
                }
                return data;
            })
            .then(data => {
                if (!data.success) {
                    throw data;
                }

                const user = data.user || {};
                if (nameDisplay && user.name) nameDisplay.textContent = user.name;
                if (profileNameDetail && user.name) profileNameDetail.textContent = user.name;
                if (profileNameInput && user.name) profileNameInput.value = user.name;
                if (profileEmailInput && user.email) profileEmailInput.value = user.email;
                if (profileEmailDetail && user.email) profileEmailDetail.textContent = user.email;

                if (user.profile_photo) {
                    const photoUrl = cacheBustedUrl(user.profile_photo);
                    if (avatar) avatar.dataset.photoUrl = photoUrl;
                    renderAvatarImage(photoUrl);
                }

                if (message) {
                    message.className = 'profile-message success';
                    message.textContent = data.message || 'Profile updated successfully';
                    message.style.display = 'block';
                }
            })
            .catch(err => {
                const errors = err && err.errors ? err.errors : null;
                const errorText = errors ? Object.values(errors).flat().join(' ') : 'Unable to update profile.';
                if (message) {
                    message.className = 'profile-message error';
                    message.textContent = errorText;
                    message.style.display = 'block';
                }
            });
        });
    }
});
</script>

<?= $this->endSection() ?>
