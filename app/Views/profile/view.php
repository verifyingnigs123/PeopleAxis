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

    .profile-container {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 25px;
    }

    @media (max-width: 768px) {
        .profile-container {
            grid-template-columns: 1fr;
        }
    }

    .profile-sidebar {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        padding: 25px;
        text-align: center;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
    }

    .profile-name {
        color: #1e3c72;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .profile-role {
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    .profile-status {
        display: inline-block;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        background: #d4edda;
        color: #155724;
        margin-bottom: 20px;
    }

    .profile-divider {
        border-top: 1px solid #e1e8ed;
        margin: 20px 0;
    }

    .profile-menu {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .profile-menu-item {
        padding: 12px 15px;
        text-align: left;
        color: #2c3e50;
        cursor: pointer;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        background: transparent;
        border: none;
        width: 100%;
        font-size: 0.9rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .profile-menu-item:hover {
        background: #f8f9ff;
        border-left-color: #2a5298;
        color: #2a5298;
    }

    .profile-menu-item.active {
        background: #f8f9ff;
        border-left-color: #2a5298;
        color: #2a5298;
        font-weight: 600;
    }

    .profile-main {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }

    @media (max-width: 768px) {
        .profile-main {
            grid-template-columns: 1fr;
        }
    }

    .profile-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 25px;
    }

    .profile-card h3 {
        color: #1e3c72;
        font-weight: 700;
        margin: 0 0 20px 0;
        padding-bottom: 15px;
        border-bottom: 2px solid #e1e8ed;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.15rem;
    }

    .profile-field {
        margin-bottom: 18px;
    }

    .profile-field-label {
        color: #7f8c8d;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .profile-field-value {
        color: #2c3e50;
        font-size: 1rem;
        font-weight: 500;
    }

    .edit-btn {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        justify-content: center;
    }

    .edit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.4);
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #e8ecff;
        color: #2a5298;
    }

    .btn-secondary {
        background: #e1e8ed;
        color: #2c3e50;
    }

    .btn-secondary:hover {
        background: #d5dce3;
        color: #2c3e50;
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Profile</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <h1><i class="fas fa-user-circle"></i> My Profile</h1>
</div>

<!-- Profile Container -->
<div class="profile-container">
    <!-- Sidebar -->
    <div class="profile-sidebar">
        <div class="profile-avatar">
            <i class="fas fa-user"></i>
        </div>

        <div class="profile-name"><?= esc($user->name ?? 'User') ?></div>
        <div class="profile-role"><?= esc($user->role_name ?? 'Employee') ?></div>
        
        <span class="profile-status">
            <i class="fas fa-circle"></i> Active
        </span>

        <div class="profile-divider"></div>

        <div class="profile-menu">
            <button class="profile-menu-item active" onclick="showSection('personal')">
                <i class="fas fa-user"></i> Personal Info
            </button>
            <button class="profile-menu-item" onclick="showSection('contact')">
                <i class="fas fa-phone"></i> Contact Info
            </button>
            <button class="profile-menu-item" onclick="showSection('employment')">
                <i class="fas fa-briefcase"></i> Employment
            </button>
            <button class="profile-menu-item" onclick="showSection('account')">
                <i class="fas fa-lock"></i> Account
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="profile-main">
        <!-- Personal Information Section -->
        <div id="personal-section" class="section-content">
            <div class="profile-card">
                <h3>
                    <i class="fas fa-user"></i> Personal Information
                </h3>

                <div class="profile-field">
                    <div class="profile-field-label">Full Name</div>
                    <div class="profile-field-value"><?= esc($user->name ?? 'N/A') ?></div>
                </div>

                <div class="profile-field">
                    <div class="profile-field-label">User ID</div>
                    <div class="profile-field-value"><?= esc($user->id ?? 'N/A') ?></div>
                </div>

                <div class="profile-field">
                    <div class="profile-field-label">Status</div>
                    <div class="profile-field-value">
                        <span class="badge">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                </div>

                <div class="profile-field">
                    <div class="profile-field-label">Date Joined</div>
                    <div class="profile-field-value"><?= date('M d, Y', strtotime($user->created_at ?? now())) ?></div>
                </div>
            </div>

            <div class="profile-card">
                <h3>
                    <i class="fas fa-shield-alt"></i> Role & Permissions
                </h3>

                <div class="profile-field">
                    <div class="profile-field-label">Role</div>
                    <div class="profile-field-value"><?= esc($user->role_name ?? 'N/A') ?></div>
                </div>

                <div class="profile-field">
                    <div class="profile-field-label">Permissions</div>
                    <div class="profile-field-value">
                        <span class="badge">View Dashboard</span>
                        <span class="badge">Manage Profile</span>
                        <span class="badge">View Reports</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div id="contact-section" class="section-content" style="display:none;">
            <div class="profile-card">
                <h3>
                    <i class="fas fa-phone"></i> Contact Information
                </h3>

                <div class="profile-field">
                    <div class="profile-field-label">Email Address</div>
                    <div class="profile-field-value"><?= esc($user->email ?? 'N/A') ?></div>
                </div>

                <div class="profile-field">
                    <div class="profile-field-label">Phone Number</div>
                    <div class="profile-field-value"><?= esc($user->phone ?? 'Not Provided') ?></div>
                </div>

                <div class="profile-field">
                    <div class="profile-field-label">Address</div>
                    <div class="profile-field-value"><?= esc($user->address ?? 'Not Provided') ?></div>
                </div>

                <button class="edit-btn btn-secondary">
                    <i class="fas fa-edit"></i> Edit Contact Info
                </button>
            </div>
        </div>

        <!-- Employment Section -->
        <div id="employment-section" class="section-content" style="display:none;">
            <div class="profile-card">
                <h3>
                    <i class="fas fa-briefcase"></i> Employment Details
                </h3>

                <div class="profile-field">
                    <div class="profile-field-label">Position</div>
                    <div class="profile-field-value"><?= esc($employee->position_name ?? 'N/A') ?></div>
                </div>

                <div class="profile-field">
                    <div class="profile-field-label">Department</div>
                    <div class="profile-field-value"><?= esc($employee->department_name ?? 'N/A') ?></div>
                </div>

                <div class="profile-field">
                    <div class="profile-field-label">Employee ID</div>
                    <div class="profile-field-value"><?= esc($employee->employee_id ?? 'N/A') ?></div>
                </div>

                <div class="profile-field">
                    <div class="profile-field-label">Date of Joining</div>
                    <div class="profile-field-value"><?= isset($employee->joining_date) ? date('M d, Y', strtotime($employee->joining_date)) : 'N/A' ?></div>
                </div>
            </div>
        </div>

        <!-- Account Section -->
        <div id="account-section" class="section-content" style="display:none;">
            <div class="profile-card">
                <h3>
                    <i class="fas fa-lock"></i> Account Settings
                </h3>

                <div class="profile-field">
                    <div class="profile-field-label">Last Login</div>
                    <div class="profile-field-value"><?= isset($user->last_login) ? date('M d, Y H:i', strtotime($user->last_login)) : 'Never' ?></div>
                </div>

                <div class="profile-field">
                    <div class="profile-field-label">Account Status</div>
                    <div class="profile-field-value">
                        <span class="badge">Verified</span>
                    </div>
                </div>

                <a href="<?= base_url('dashboard/updateProfile') ?>" class="edit-btn">
                    <i class="fas fa-key"></i> Change Password
                </a>
            </div>
        </div>

        <!-- Edit Profile Button -->
        <div class="profile-card" style="grid-column: 1 / -1;">
            <a href="<?= base_url('dashboard/updateProfile') ?>" class="edit-btn">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
        </div>
    </div>
</div>

<script>
function showSection(section) {
    // Hide all sections
    document.querySelectorAll('.section-content').forEach(el => {
        el.style.display = 'none';
    });

    // Show selected section
    const sectionId = section + '-section';
    const element = document.getElementById(sectionId);
    if (element) {
        element.style.display = 'grid';
    }

    // Update active menu
    document.querySelectorAll('.profile-menu-item').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.closest('.profile-menu-item').classList.add('active');
}
</script>

<?= $this->endSection() ?>
