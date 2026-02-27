<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .reset-password-container {
        max-width: 500px;
        margin: 3rem auto;
    }

    .reset-password-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .reset-password-header {
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .reset-password-header h1 {
        margin: 0 0 0.5rem 0;
        font-size: 1.6rem;
        font-weight: 700;
    }

    .reset-password-header i {
        font-size: 2.5rem;
        color: #9b59b6;
        margin-bottom: 0.5rem;
        display: block;
    }

    .reset-password-header p {
        margin: 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .reset-password-body {
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

    .password-strength {
        margin-top: 0.5rem;
        padding: 0.75rem;
        border-radius: 4px;
        font-size: 0.85rem;
        display: none;
    }

    .password-strength.weak {
        background-color: #ffe0e0;
        color: #c41e3a;
        display: block;
    }

    .password-strength.fair {
        background-color: #fff4e0;
        color: #d69e00;
        display: block;
    }

    .password-strength.good {
        background-color: #d4edda;
        color: #155724;
        display: block;
    }

    .reset-password-btn {
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

    .reset-password-btn:hover {
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

    .reset-password-footer {
        padding: 0 2rem 2rem;
        text-align: center;
    }

    .reset-password-footer p {
        margin: 0;
        font-size: 0.9rem;
        color: #7f8c8d;
    }

    .reset-password-footer a {
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
    }

    .reset-password-footer a:hover {
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

    .password-requirements {
        background-color: #f9f9f9;
        border: 1px solid #dee2e6;
        padding: 1rem;
        border-radius: 6px;
        font-size: 0.85rem;
        margin-top: 1rem;
    }

    .password-requirements ul {
        margin: 0;
        padding-left: 1.5rem;
    }

    .password-requirements li {
        color: #7f8c8d;
        margin-bottom: 0.25rem;
    }

    .password-toggle {
        cursor: pointer;
        color: #3498db;
        margin-left: 0.5rem;
    }
</style>

<div class="reset-password-container">
    <div class="reset-password-card">
        <div class="reset-password-header">
            <i class="fas fa-lock"></i>
            <h1>Set New Password</h1>
            <p>Create a strong password for your account</p>
        </div>

        <div class="reset-password-body">
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
                Please enter a new password. Make sure it's strong and secure.
            </div>

            <form method="POST" action="/reset-password">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="password">New Password 
                        <span class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="Enter new password"
                        minlength="6"
                        onchange="checkPasswordStrength()"
                    >
                    <div class="password-strength" id="passwordStrength"></div>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirm Password 
                        <span class="password-toggle" onclick="togglePassword('password_confirm')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </label>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        required 
                        placeholder="Confirm new password"
                        minlength="6"
                    >
                </div>

                <div class="password-requirements">
                    <strong>Password Requirements:</strong>
                    <ul>
                        <li>At least 6 characters long</li>
                        <li>Use uppercase and lowercase letters</li>
                        <li>Include numbers and special characters</li>
                    </ul>
                </div>

                <button type="submit" class="reset-password-btn">
                    <i class="fas fa-save"></i> Reset Password
                </button>
            </form>
        </div>

        <div class="reset-password-footer">
            <p>
                <a href="/login">
                    <i class="fas fa-sign-in-alt"></i> Back to login
                </a>
            </p>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
    field.setAttribute('type', type);
}

function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthDiv = document.getElementById('passwordStrength');

    if (password.length < 6) {
        strengthDiv.className = 'password-strength weak';
        strengthDiv.innerHTML = '<i class="fas fa-times-circle"></i> Password is too weak';
        return;
    }

    let strength = 0;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;

    if (strength <= 1) {
        strengthDiv.className = 'password-strength weak';
        strengthDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Weak password';
    } else if (strength === 2) {
        strengthDiv.className = 'password-strength fair';
        strengthDiv.innerHTML = '<i class="fas fa-check-circle"></i> Fair password';
    } else {
        strengthDiv.className = 'password-strength good';
        strengthDiv.innerHTML = '<i class="fas fa-check-circle"></i> Strong password';
    }
}
</script>

<?= $this->endSection() ?>
