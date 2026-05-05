<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    /* Toast notification styles */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
    }

    .toast {
        background: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        margin-bottom: 10px;
        animation: slideIn 0.3s ease-in-out;
        display: flex;
        align-items: center;
        gap: 12px;
        border-left: 4px solid;
    }

    .toast.success {
        border-left-color: #10b981;
        background: #f0fdf4;
    }

    .toast.success .toast-icon {
        color: #10b981;
        font-size: 1.2rem;
    }

    .toast.error {
        border-left-color: #ef4444;
        background: #fef2f2;
    }

    .toast.error .toast-icon {
        color: #ef4444;
        font-size: 1.2rem;
    }

    .toast-content {
        flex: 1;
        font-size: 0.95rem;
        color: #1f2937;
        font-weight: 500;
    }

    .toast-close {
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        font-size: 1.2rem;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .toast-close:hover {
        color: #1f2937;
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

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
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .profile-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(47, 95, 69, 0.3);
    }

    .profile-avatar::after {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0);
        border-radius: 50%;
        transition: background 0.3s ease;
    }

    .profile-avatar:hover::after {
        background: rgba(0, 0, 0, 0.1);
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

    /* Modal Styles */
    .profile-photo-modal {
        display: none;
        position: fixed;
        z-index: 9998;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        animation: fadeIn 0.3s ease;
    }

    .profile-photo-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: white;
        padding: 20px;
        border-radius: 12px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 10px;
    }

    .modal-header h2 {
        margin: 0;
        color: #2f5f45;
        font-size: 1.2rem;
        font-weight: 700;
    }

    .modal-close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #64748b;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .modal-close-btn:hover {
        color: #1f2937;
    }

    .modal-image-container {
        text-align: center;
        margin-bottom: 15px;
    }

    .modal-image {
        max-width: 100%;
        max-height: 400px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .modal-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .modal-btn-update {
        background: #2f5f45;
        color: white;
    }

    .modal-btn-update:hover {
        background: #1e3a2a;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(47, 95, 69, 0.3);
    }

    .modal-btn-delete {
        background: #ef4444;
        color: white;
    }

    .modal-btn-delete:hover {
        background: #dc2626;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    }

    .modal-btn-cancel {
        background: #e5e7eb;
        color: #374151;
    }

    .modal-btn-cancel:hover {
        background: #d1d5db;
        transform: translateY(-2px);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
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

<!-- Profile Photo Modal -->
<div id="profilePhotoModal" class="profile-photo-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-image"></i> Profile Photo</h2>
            <button class="modal-close-btn" onclick="closeProfilePhotoModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-image-container">
            <img id="modalProfileImage" src="" alt="Profile photo" class="modal-image">
        </div>
        <div class="modal-actions">
            <button class="modal-btn modal-btn-update" onclick="updateProfilePhotoFromModal()">
                <i class="fas fa-edit"></i> Update Photo
            </button>
            <button class="modal-btn modal-btn-delete" onclick="showDeleteConfirmModal()">
                <i class="fas fa-trash"></i> Delete
            </button>
            <button class="modal-btn modal-btn-cancel" onclick="closeProfilePhotoModal()">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="profile-photo-modal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h2><i class="fas fa-exclamation-circle" style="color: #ef4444;"></i> Confirm Delete</h2>
            <button class="modal-close-btn" onclick="closeDeleteConfirmModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="padding: 15px; text-align: center;">
            <p style="color: #374151; font-size: 0.95rem; margin: 0;">Are you sure you want to delete your profile photo? This action cannot be undone.</p>
        </div>
        <div class="modal-actions" style="margin-top: 20px;">
            <button class="modal-btn modal-btn-delete" onclick="confirmDeleteProfilePhoto()">
                <i class="fas fa-trash"></i> Yes, Delete
            </button>
            <button class="modal-btn modal-btn-cancel" onclick="closeDeleteConfirmModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
        </div>
    </div>
</div>

<!-- Hidden file input for modal update -->
<input type="file" id="hiddenProfilePhotoInput" name="profile_photo" accept="image/*" style="display: none;">

<!-- Hidden form for CSRF token -->
<form id="deletePhotoForm" method="POST" action="<?= base_url('profile/remove-photo') ?>" style="display: none;">
    <?= csrf_field() ?>
</form>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Profile</span>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="toast-container"></div>

<!-- Page Header -->
<div class="profile-header">
    <h1><i class="fas fa-user-circle"></i> My Profile</h1>
</div>

<!-- Profile Container -->
<div class="profile-shell">
    <div class="identity-card">
        <div class="profile-avatar" id="profileAvatar" data-initial="<?= esc($initial !== '' ? $initial : 'U') ?>" data-photo-url="<?= esc($profilePhotoUrl) ?>" onclick="openProfilePhotoModal()">
            <?php if (!empty($profilePhotoUrl)): ?>
                <img id="profileAvatarImage" src="<?= esc($profilePhotoUrl) ?>" alt="Profile photo" style="pointer-events: none;">
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
</script>

<script>
// Toast notification helper
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'toast ' + type;
    
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    toast.innerHTML = `
        <span class="toast-icon">
            <i class="fas ${icon}"></i>
        </span>
        <div class="toast-content">${message}</div>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    container.appendChild(toast);

    // Auto remove after 4 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.style.animation = 'slideOut 0.3s ease-in-out';
            setTimeout(() => toast.remove(), 300);
        }
    }, 4000);
}

// Profile Photo Modal Functions
function openProfilePhotoModal() {
    const profileAvatar = document.getElementById('profileAvatar');
    const photoUrl = profileAvatar.dataset.photoUrl;
    
    console.log('Opening photo modal. Photo URL:', photoUrl);
    
    if (!photoUrl || photoUrl.trim() === '') {
        // No photo - directly open file picker to upload
        console.log('No photo found. Opening file picker to upload...');
        showToast('Click to upload a profile photo', 'success');
        updateProfilePhotoFromModal();
        return;
    }
    
    const modalImage = document.getElementById('modalProfileImage');
    const modal = document.getElementById('profilePhotoModal');
    
    modalImage.src = photoUrl;
    modalImage.onload = function() {
        console.log('Image loaded successfully');
    };
    modalImage.onerror = function() {
        console.error('Failed to load image:', photoUrl);
        showToast('Failed to load image', 'error');
    };
    
    modal.classList.add('active');
}

function closeProfilePhotoModal() {
    const modal = document.getElementById('profilePhotoModal');
    modal.classList.remove('active');
}

function updateProfilePhotoFromModal() {
    const hiddenInput = document.getElementById('hiddenProfilePhotoInput');
    hiddenInput.click(); // Trigger file picker
}

function showDeleteConfirmModal() {
    const modal = document.getElementById('deleteConfirmModal');
    modal.classList.add('active');
}

function closeDeleteConfirmModal() {
    const modal = document.getElementById('deleteConfirmModal');
    modal.classList.remove('active');
}

function confirmDeleteProfilePhoto() {
    closeDeleteConfirmModal();
    closeProfilePhotoModal();
    
    showToast('Deleting profile photo...', 'success');
    
    // Get CSRF token from the hidden form
    const deleteForm = document.getElementById('deletePhotoForm');
    const csrfInput = deleteForm.querySelector('input[name^="csrf"]');
    const csrfHeader = document.querySelector('meta[name="csrf-token"]');
    
    let headers = {
        'X-Requested-With': 'XMLHttpRequest'
    };
    
    let body = null;
    
    // If CSRF token exists, add it to request
    if (csrfInput) {
        const tokenName = csrfInput.name;
        const tokenValue = csrfInput.value;
        headers[tokenName] = tokenValue;
        
        body = new FormData();
        body.append(tokenName, tokenValue);
    }
    
    console.log('Sending delete request to: <?= base_url('profile/remove-photo') ?>');
    console.log('Headers:', headers);
    
    // Create a fetch request to remove profile photo
    fetch('<?= base_url('profile/remove-photo') ?>', {
        method: 'POST',
        headers: headers,
        body: body || undefined
    })
    .then(response => {
        console.log('Response status:', response.status);
        const contentType = response.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
            return response.json().then(data => ({
                status: response.status,
                data: data
            }));
        } else {
            return response.text().then(text => {
                console.error('Non-JSON response:', text);
                return {
                    status: response.status,
                    data: { success: false, message: 'Invalid response from server' }
                };
            });
        }
    })
    .then(result => {
        console.log('Delete response:', result);
        
        if (result.data && result.data.success) {
            showToast('✓ Profile photo deleted successfully!', 'success');
            
            // Update avatar to show placeholder
            const avatar = document.getElementById('profileAvatar');
            const initial = avatar.dataset.initial || 'U';
            avatar.innerHTML = '<div id="profileAvatarPlaceholder" class="profile-avatar-placeholder">' + initial + '</div>';
            avatar.dataset.photoUrl = '';
            
            // Re-add click handler to avatar
            avatar.onclick = function() { openProfilePhotoModal(); };
        } else {
            const errorMsg = (result.data && result.data.message) || 'Failed to delete profile photo';
            showToast(errorMsg, 'error');
            console.error('Delete failed:', result.data);
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        showToast('Error: ' + (error.message || 'Failed to delete profile photo'), 'error');
    });
}

// Close modal when clicking outside of it
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('profilePhotoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeProfilePhotoModal();
        }
    });

    document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteConfirmModal();
        }
    });

    // Handle file selection from hidden input
    const hiddenInput = document.getElementById('hiddenProfilePhotoInput');
    if (hiddenInput) {
        hiddenInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                console.log('File selected:', file.name);
                uploadProfilePhoto(file);
            }
        });
    }
});

// Upload profile photo
function uploadProfilePhoto(file) {
    // Validate file
    const maxSize = 2 * 1024 * 1024; // 2MB
    const allowedTypes = ['image/png', 'image/jpeg', 'image/webp'];
    
    if (file.size > maxSize) {
        showToast('File is too large. Maximum size is 2MB.', 'error');
        return;
    }
    
    if (!allowedTypes.includes(file.type)) {
        showToast('Invalid file type. Allowed types: PNG, JPG, WEBP.', 'error');
        return;
    }
    
    showToast('Uploading profile photo...', 'success');
    
    // Get current user name and email from the page
    const nameElement = document.getElementById('profileNameDetail');
    const emailElement = document.getElementById('profileEmailDetail');
    const currentName = nameElement ? nameElement.textContent.trim() : 'User';
    const currentEmail = emailElement ? emailElement.textContent.trim() : 'user@example.com';
    
    // Create FormData with file, name, email and CSRF token
    const formData = new FormData();
    formData.append('profile_photo', file);
    formData.append('name', currentName);
    formData.append('email', currentEmail);
    
    // Get CSRF token
    const csrfInput = document.querySelector('input[name^="csrf"]');
    if (csrfInput) {
        formData.append(csrfInput.name, csrfInput.value);
    }
    
    console.log('Uploading to: <?= base_url('profile/update') ?>');
    console.log('Name:', currentName);
    console.log('Email:', currentEmail);
    
    // Upload the file
    fetch('<?= base_url('profile/update') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Upload response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Upload response:', data);
        
        if (data.success) {
            showToast('✓ Profile photo uploaded successfully!', 'success');
            
            // Update avatar with new photo
            if (data.user && data.user.profile_photo) {
                const photoUrl = data.user.profile_photo + '?v=' + Date.now();
                const avatar = document.getElementById('profileAvatar');
                avatar.dataset.photoUrl = photoUrl;
                
                // Replace placeholder with image
                const placeholder = avatar.querySelector('.profile-avatar-placeholder');
                if (placeholder) {
                    placeholder.remove();
                }
                
                const img = document.createElement('img');
                img.id = 'profileAvatarImage';
                img.src = photoUrl;
                img.alt = 'Profile photo';
                img.style.pointerEvents = 'none';
                avatar.appendChild(img);
            }
            
            // Reset file input
            document.getElementById('hiddenProfilePhotoInput').value = '';
        } else {
            const errorMsg = (data.errors && data.errors.profile_photo) || data.message || 'Failed to upload profile photo';
            showToast(errorMsg, 'error');
            console.error('Upload failed:', data);
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        showToast('Error: ' + (error.message || 'Failed to upload profile photo'), 'error');
    });
}
</script>

<?= $this->endSection() ?>
