<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .auth-container {
        max-width: 500px;
        margin: 3rem auto;
    }

    .auth-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .auth-header {
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .auth-header h1 {
        margin: 0 0 0.5rem 0;
        font-size: 1.8rem;
        font-weight: 700;
    }

    .auth-header i.header-icon {
        font-size: 2.5rem;
        color: #f39c12;
        margin-bottom: 0.5rem;
        display: block;
    }

    .auth-header p {
        margin: 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .auth-body {
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.95rem;
    }

    .form-group input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .auth-btn {
        width: 100%;
        padding: 0.75rem;
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }

    .auth-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(52, 152, 219, 0.3);
    }

    .alert {
        border-radius: 6px;
        border: none;
        margin-bottom: 1.5rem;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    .auth-footer {
        padding: 0 2rem 2rem;
        text-align: center;
    }

    .auth-footer p {
        margin: 0;
        font-size: 0.9rem;
        color: #7f8c8d;
    }

    .auth-footer a {
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
    }

    .auth-footer a:hover {
        text-decoration: underline;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 2rem;
        color: #3498db;
        text-decoration: none;
        font-weight: 500;
    }

    .back-link:hover {
        color: #2980b9;
    }

    .info-box {
        background: #eaf4fb;
        border-left: 4px solid #3498db;
        border-radius: 6px;
        padding: 0.85rem 1rem;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        color: #2c3e50;
    }

    .info-box i {
        color: #3498db;
        margin-right: 0.4rem;
    }
</style>

<div class="auth-container">
    <a href="<?= base_url('login') ?>" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Login
    </a>

    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-key header-icon"></i>
            <h1>Forgot Password</h1>
            <p>Enter your email to receive a one-time password</p>
        </div>

        <div class="auth-body">
            <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?= esc(session()->get('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?= esc(session()->get('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                We will send a 6-digit OTP to your registered email address. The OTP is valid for <strong>10 minutes</strong>.
            </div>

            <form action="<?= base_url('forgot-password') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="Enter your registered email"
                        value="<?= esc(old('email')) ?>"
                        required
                        autofocus
                    >
                </div>

                <button type="submit" class="auth-btn">
                    <i class="fas fa-paper-plane"></i> Send OTP
                </button>
            </form>
        </div>

        <div class="auth-footer">
            <p>
                Remember your password? <a href="<?= base_url('login') ?>">Sign In</a>
            </p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
