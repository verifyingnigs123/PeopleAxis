<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .forgot-password-container {
        max-width: 500px;
        margin: 3rem auto;
    }

    .forgot-password-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .forgot-password-header {
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .forgot-password-header h1 {
        margin: 0 0 0.5rem 0;
        font-size: 1.6rem;
        font-weight: 700;
    }

    .forgot-password-header i {
        font-size: 2.5rem;
        color: #f39c12;
        margin-bottom: 0.5rem;
        display: block;
    }

    .forgot-password-header p {
        margin: 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .forgot-password-body {
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
        box-sizing: border-box;
    }

    .form-group input:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .forgot-password-btn {
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

    .forgot-password-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(52, 152, 219, 0.3);
    }

    .alert {
        border-radius: 6px;
        border: none;
        margin-bottom: 1.5rem;
        padding: 1rem;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    .alert-info {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .forgot-password-footer {
        padding: 0 2rem 2rem;
        text-align: center;
    }

    .forgot-password-footer p {
        margin: 0;
        font-size: 0.9rem;
        color: #7f8c8d;
    }

    .forgot-password-footer a {
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
    }

    .forgot-password-footer a:hover {
        text-decoration: underline;
    }

    .info-text {
        background-color: #e3f2fd;
        border-left: 4px solid #2196F3;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        color: #1565c0;
    }
</style>

<div class="forgot-password-container">
    <div class="forgot-password-card">
        <div class="forgot-password-header">
            <i class="fas fa-key"></i>
            <h1>Forgot Password?</h1>
            <p>No worries! We'll send you an OTP to reset your password</p>
        </div>

        <div class="forgot-password-body">
            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= session('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->has('success')): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <?= session('success') ?>
                </div>
            <?php endif; ?>

            <div class="info-text">
                <i class="fas fa-info-circle"></i>
                Enter your email address below and we'll send you an OTP code to verify your identity.
            </div>

            <form method="POST" action="/forgot-password">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        placeholder="Enter your email address"
                        value="<?= old('email') ?>"
                    >
                </div>

                <button type="submit" class="forgot-password-btn">
                    <i class="fas fa-paper-plane"></i> Send OTP
                </button>
            </form>
        </div>

        <div class="forgot-password-footer">
            <p>
                Remember your password? 
                <a href="/login">
                    <i class="fas fa-sign-in-alt"></i> Go back to login
                </a>
            </p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
