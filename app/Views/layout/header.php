<?php $role = session()->get('role'); ?>

<style>
    /* ===== Navbar Styles ===== */
    .navbar-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 1rem 0;
    }

    .navbar-custom .navbar-brand {
        color: white;
        font-weight: bold;
        font-size: 1.5rem;
    }

    .navbar-custom .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        margin-left: 1rem;
    }

    .navbar-custom .nav-link:hover {
        color: white !important;
    }

    .navbar-custom .user-menu-dropdown {
        color: white !important;
    }

    /* ===== Main Container ===== */
    .main-container {
        display: flex;
        min-height: calc(100vh - 70px);
        background: #f8f9fa;
    }

    .admin-container {
        display: flex;
        min-height: calc(100vh - 70px);
        background: #f5f6fa;
    }

    /* ===== Sidebar Styles ===== */
    .sidebar {
        width: 260px;
        background: white;
        padding: 20px 0;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
        position: fixed;
        height: calc(100vh - 70px);
        overflow-y: auto;
        z-index: 100;
    }

    .admin-sidebar {
        width: 260px;
        background: white;
        padding: 20px 0;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
        position: fixed;
        height: calc(100vh - 70px);
        overflow-y: auto;
        z-index: 100;
    }

    .sidebar nav {
        padding: 0;
    }

    .admin-sidebar nav {
        padding: 0;
    }

    .sidebar-title {
        padding: 15px 20px 10px;
        font-weight: 700;
        color: #667eea;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .admin-sidebar .sidebar-title {
        padding: 15px 20px 10px;
        font-weight: 700;
        color: #2a5298;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .sidebar .nav-link {
        display: block;
        padding: 12px 20px;
        color: #495057;
        text-decoration: none;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .admin-sidebar .nav-link {
        display: block;
        padding: 12px 20px;
        color: #495057;
        text-decoration: none;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .sidebar .nav-link:hover {
        background: #f1f3ff;
        border-left-color: #667eea;
        color: #667eea;
    }

    .admin-sidebar .nav-link:hover {
        background: #f1f4ff;
        border-left-color: #2a5298;
        color: #2a5298;
    }

    .sidebar .nav-link.active {
        background: #f1f3ff;
        border-left-color: #667eea;
        color: #667eea;
        font-weight: 600;
    }

    .admin-sidebar .nav-link.active {
        background: #f1f4ff;
        border-left-color: #2a5298;
        color: #2a5298;
        font-weight: 600;
    }

    .sidebar .nav-link i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    .admin-sidebar .nav-link i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    /* ===== Content Area ===== */
    .content-area {
        margin-left: 260px;
        padding: 30px;
        flex: 1;
        overflow-y: auto;
    }

    .admin-content {
        margin-left: 260px;
        padding: 30px;
        background: #f5f6fa;
        min-height: calc(100vh - 70px);
        flex: 1;
        overflow-y: auto;
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .sidebar {
            width: 0;
            position: fixed;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .admin-sidebar {
            width: 0;
            position: fixed;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .admin-sidebar.active {
            transform: translateX(0);
        }

        .content-area {
            margin-left: 0;
            padding: 15px;
        }

        .admin-content {
            margin-left: 0;
            padding: 15px;
        }
    }
</style>

<?php if (session()->get('logged_in')): ?>
<!-- Top Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= base_url() ?>">
            <i class="fas fa-users"></i>
            PeopleAxis
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('dashboard') ?>">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle user-menu-dropdown" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> 
                        <?= esc(session()->get('name') ?? 'User') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="fas fa-user"></i> My Profile</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('settings') ?>"><i class="fas fa-cog"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- User Dashboard Sidebar -->
<?php if ($role === 'user'): ?>
<div class="main-container">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <nav>
            <!-- Main Menu -->
            <div class="sidebar-title">Main</div>
            <a href="<?= base_url('dashboard') ?>" class="nav-link">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>

            <!-- Employee Management -->
            <div class="sidebar-title">Employee Management</div>
            <a href="<?= base_url('employees') ?>" class="nav-link">
                <i class="fas fa-users"></i> Employees
            </a>
            <a href="<?= base_url('departments') ?>" class="nav-link">
                <i class="fas fa-building"></i> Departments
            </a>
            <a href="<?= base_url('positions') ?>" class="nav-link">
                <i class="fas fa-briefcase"></i> Positions
            </a>
            <a href="<?= base_url('designations') ?>" class="nav-link">
                <i class="fas fa-certificate"></i> Designations
            </a>

            <!-- Payroll & Compensation -->
            <div class="sidebar-title">Payroll & Compensation</div>
            <a href="<?= base_url('salaries') ?>" class="nav-link">
                <i class="fas fa-money-bill-wave"></i> Salaries
            </a>
            <a href="<?= base_url('payroll') ?>" class="nav-link">
                <i class="fas fa-calculator"></i> Payroll
            </a>
            <a href="<?= base_url('allowances') ?>" class="nav-link">
                <i class="fas fa-coins"></i> Allowances
            </a>
            <a href="<?= base_url('deductions') ?>" class="nav-link">
                <i class="fas fa-minus-circle"></i> Deductions
            </a>

            <!-- Leave Management -->
            <div class="sidebar-title">Leave Management</div>
            <a href="<?= base_url('leaves') ?>" class="nav-link">
                <i class="fas fa-calendar-check"></i> Leaves
            </a>
            <a href="<?= base_url('leave-types') ?>" class="nav-link">
                <i class="fas fa-list"></i> Leave Types
            </a>
            <a href="<?= base_url('attendance') ?>" class="nav-link">
                <i class="fas fa-clock"></i> Attendance
            </a>

            <!-- Recruitment -->
            <div class="sidebar-title">Recruitment</div>
            <a href="<?= base_url('jobs') ?>" class="nav-link">
                <i class="fas fa-file-alt"></i> Job Postings
            </a>
            <a href="<?= base_url('candidates') ?>" class="nav-link">
                <i class="fas fa-user-tie"></i> Candidates
            </a>
            <a href="<?= base_url('interviews') ?>" class="nav-link">
                <i class="fas fa-handshake"></i> Interviews
            </a>

            <!-- Performance & Training -->
            <div class="sidebar-title">Performance & Training</div>
            <a href="<?= base_url('performance') ?>" class="nav-link">
                <i class="fas fa-star"></i> Performance
            </a>
            <a href="<?= base_url('training') ?>" class="nav-link">
                <i class="fas fa-book"></i> Training
            </a>

            <!-- Reports -->
            <div class="sidebar-title">Reports</div>
            <a href="<?= base_url('reports') ?>" class="nav-link">
                <i class="fas fa-file-pdf"></i> Reports
            </a>

            <!-- Administration -->
            <div class="sidebar-title">Administration</div>
            <a href="<?= base_url('users') ?>" class="nav-link">
                <i class="fas fa-user-shield"></i> Users
            </a>
            <a href="<?= base_url('roles') ?>" class="nav-link">
                <i class="fas fa-key"></i> Roles & Permissions
            </a>
            <a href="<?= base_url('settings') ?>" class="nav-link">
                <i class="fas fa-cog"></i> Settings
            </a>
        </nav>
    </aside>

    <!-- Content Area -->
    <div class="content-area">

<!-- Admin Dashboard Sidebar -->
<?php elseif ($role === 'admin'): ?>
<div class="admin-container">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <nav>
            <div class="sidebar-title">Main</div>
            <a href="<?= base_url('dashboard') ?>" class="nav-link">
                <i class="fas fa-home"></i> Dashboard
            </a>

            <div class="sidebar-title">User Management</div>
            <a href="<?= base_url('users') ?>" class="nav-link">
                <i class="fas fa-users"></i> User Management
            </a>
            
            <a href="<?= base_url('roles') ?>" class="nav-link">
                <i class="fas fa-lock"></i> Roles & Permissions
            </a>

            <div class="sidebar-title">System</div>
            <a href="<?= base_url('activity-logs') ?>" class="nav-link">
                <i class="fas fa-history"></i> Activity Logs
            </a>
            <a href="<?= base_url('settings') ?>" class="nav-link">
                <i class="fas fa-cog"></i> Settings
            </a>
            <a href="<?= base_url('backups') ?>" class="nav-link">
                <i class="fas fa-database"></i> Backups
            </a>

            <div class="sidebar-title">Reports</div>
            <a href="<?= base_url('reports') ?>" class="nav-link">
                <i class="fas fa-file-pdf"></i> Reports
            </a>
            <a href="<?= base_url('analytics') ?>" class="nav-link">
                <i class="fas fa-chart-bar"></i> Analytics
            </a>
        </nav>
    </aside>

    <!-- Admin Content Area -->
    <div class="admin-content" id="mainContent">

<?php endif; ?>

<script>
// AJAX Navigation for Sidebar
document.addEventListener('DOMContentLoaded', function() {
    const sidebarLinks = document.querySelectorAll('.sidebar .nav-link, .admin-sidebar .nav-link');
    
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = this.getAttribute('href');
            const contentArea = document.getElementById('mainContent');
            
            if (!contentArea) return;
            
            // Show loading indicator
            contentArea.innerHTML = '<div style="text-align: center; padding: 50px;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            
            // Add active class to clicked link
            document.querySelectorAll('.sidebar .nav-link, .admin-sidebar .nav-link').forEach(l => {
                l.classList.remove('active');
            });
            this.classList.add('active');
            
            // AJAX request
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Extract content section from the response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const mainContentDiv = doc.querySelector('[data-ajax-content]') || doc.querySelector('main');
                
                // If found, update content area with just the content
                if (mainContentDiv) {
                    contentArea.innerHTML = mainContentDiv.innerHTML;
                } else {
                    contentArea.innerHTML = html;
                }
                
                // Update browser history
                window.history.pushState({url: url}, '', url);
                
                // Re-initialize any scripts needed for the new content
                reinitializeScripts();
            })
            .catch(error => {
                console.error('Error loading content:', error);
                contentArea.innerHTML = '<div class="alert alert-danger" style="margin: 20px;"><i class="fas fa-exclamation-circle"></i> Error loading content. Please try again.</div>';
            });
        });
    });
});

// Function to reinitialize scripts after AJAX load
function reinitializeScripts() {
    // Reinitialize Bootstrap components
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Reinitialize form handling
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const spinner = document.getElementById('loadingSpinner');
            if (spinner) {
                spinner.classList.add('active');
            }
        });
    });
}
</script>
<?php endif; ?>
