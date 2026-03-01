<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .landing-hero {
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        color: white;
        padding: 4rem 2rem;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 3rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .landing-hero .hero-icon {
        font-size: 4rem;
        color: #e74c3c;
        margin-bottom: 1rem;
    }

    .landing-hero h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .landing-hero p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 2rem;
    }

    .landing-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .landing-buttons a {
        padding: 0.9rem 2rem;
        font-weight: 600;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary-alt {
        background: white;
        color: #3498db;
    }

    .btn-primary-alt:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .btn-secondary-alt {
        background: transparent;
        color: white;
        border: 2px solid white;
    }

    .btn-secondary-alt:hover {
        background: white;
        color: #3498db;
    }

    @media (max-width: 768px) {
        .landing-hero {
            padding: 2rem 1.5rem;
        }

        .landing-hero h1 {
            font-size: 1.8rem;
        }

        .landing-buttons {
            flex-direction: column;
        }

        .landing-buttons a {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<!-- Hero Section -->
<div class="landing-hero">
    <div class="hero-icon">
        <i class="fas fa-users"></i>
    </div>
    <h1>Welcome to PeopleAxis</h1>
    <p>Complete HR Management System</p>
    <p style="font-size: 0.95rem; opacity: 0.85;">
        Streamline your human resources management with our comprehensive platform
    </p>
    
    <div class="landing-buttons">
        <a href="<?= base_url('login') ?>" class="btn-primary-alt">
            <i class="fas fa-sign-in-alt"></i> Login to Dashboard
        </a>
    </div>
</div>

<!-- Features Section -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-star"></i> Key Features
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div style="text-align: center;">
                            <i class="fas fa-users-cog" style="font-size: 2.5rem; color: #3498db; margin-bottom: 1rem; display: block;"></i>
                            <h5 style="color: #2c3e50; font-weight: 600; margin-bottom: 0.5rem;">Employee Management</h5>
                            <p style="color: #7f8c8d; margin: 0; font-size: 0.95rem;">Manage employee information, profiles, and organizational structure efficiently</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div style="text-align: center;">
                            <i class="fas fa-calendar-check" style="font-size: 2.5rem; color: #3498db; margin-bottom: 1rem; display: block;"></i>
                            <h5 style="color: #2c3e50; font-weight: 600; margin-bottom: 0.5rem;">Leave Management</h5>
                            <p style="color: #7f8c8d; margin: 0; font-size: 0.95rem;">Track leave requests, approvals, and maintain attendance records</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div style="text-align: center;">
                            <i class="fas fa-chart-line" style="font-size: 2.5rem; color: #3498db; margin-bottom: 1rem; display: block;"></i>
                            <h5 style="color: #2c3e50; font-weight: 600; margin-bottom: 0.5rem;">Performance Tracking</h5>
                            <p style="color: #7f8c8d; margin: 0; font-size: 0.95rem;">Monitor employee performance and generate insightful reports</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div style="text-align: center;">
                            <i class="fas fa-users-tie" style="font-size: 2.5rem; color: #3498db; margin-bottom: 1rem; display: block;"></i>
                            <h5 style="color: #2c3e50; font-weight: 600; margin-bottom: 0.5rem;">Recruitment</h5>
                            <p style="color: #7f8c8d; margin: 0; font-size: 0.95rem;">Post jobs, manage candidates, and streamline hiring process</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div style="text-align: center;">
                            <i class="fas fa-shield-alt" style="font-size: 2.5rem; color: #3498db; margin-bottom: 1rem; display: block;"></i>
                            <h5 style="color: #2c3e50; font-weight: 600; margin-bottom: 0.5rem;">Security & Privacy</h5>
                            <p style="color: #7f8c8d; margin: 0; font-size: 0.95rem;">Enterprise-grade security with role-based access control</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

