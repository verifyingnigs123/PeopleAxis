<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .login-container {
        max-width: 500px;
        margin: 3rem auto;
    }

    .login-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .login-header {
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .login-header h1 {
        margin: 0 0 0.5rem 0;
        font-size: 1.8rem;
        font-weight: 700;
    }

    .login-header i {
        font-size: 2.5rem;
        color: #e74c3c;
        margin-bottom: 0.5rem;
        display: block;
    }

    .login-header p {
        margin: 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .login-body {
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

    .form-check {
        margin-bottom: 1.5rem;
    }

    .form-check input {
        border-color: #dee2e6;
    }

    .form-check input:checked {
        background-color: #3498db;
        border-color: #3498db;
    }

    .form-check label {
        margin-left: 0.5rem;
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 0;
    }

    .login-btn {
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

    .login-btn:hover {
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

    .login-footer {
        padding: 0 2rem 2rem;
        text-align: center;
    }

    .login-footer p {
        margin: 0;
        font-size: 0.9rem;
        color: #7f8c8d;
    }

    .login-footer a {
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
    }

    .login-footer a:hover {
        text-decoration: underline;
    }

    .divider {
        display: flex;
        align-items: center;
        margin: 1.5rem 0;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #dee2e6;
    }

    .divider span {
        margin: 0 0.75rem;
        color: #7f8c8d;
        font-size: 0.85rem;
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
</style>

<div class="login-container">
    <a href="<?= base_url() ?>" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Home
    </a>

    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-users"></i>
            <h1>PeopleAxis</h1>
            <p>HR Management System</p>
        </div>

        <div class="login-body">
            <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?= session()->get('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?= session()->get('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('login') ?>" method="POST">
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
                        placeholder="Enter your email"
                        value="<?= old('email') ?>"
                        required
                    >
                    <?php if ($errors = session('validation')) : ?>
                        <?php if (is_array($errors) && isset($errors['email'])): ?>
                            <small class="text-danger d-block mt-2">
                                <i class="fas fa-exclamation-triangle"></i> <?= $errors['email'] ?>
                            </small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Enter your password"
                        required
                    >
                    <?php if ($errors = session('validation')) : ?>
                        <?php if (is_array($errors) && isset($errors['password'])): ?>
                            <small class="text-danger d-block mt-2">
                                <i class="fas fa-exclamation-triangle"></i> <?= $errors['password'] ?>
                            </small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="form-check">
                    <input 
                        type="checkbox" 
                        class="form-check-input" 
                        id="remember" 
                        name="remember"
                    >
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="divider">
                <span>OR</span>
            </div>

            <p style="text-align: center; color: #7f8c8d; font-size: 0.9rem; margin: 1.5rem 0 0 0;">
                Don't have an account? <a href="<?= base_url('register') ?>" style="color: #3498db; font-weight: 600; text-decoration: none;">Sign Up</a>
            </p>
        </div>
    </div>

    <div class="login-footer">
        <p>
            <a href="<?= base_url('forgot-password') ?>">Forgot Password?</a> | 
            <a href="<?= base_url('contact') ?>">Contact Support</a>
        </p>
    </div>
</div>

<?= $this->endSection() ?>
