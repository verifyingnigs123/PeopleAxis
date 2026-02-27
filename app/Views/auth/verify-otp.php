<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .verify-otp-container {
        max-width: 500px;
        margin: 3rem auto;
    }

    .verify-otp-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .verify-otp-header {
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .verify-otp-header h1 {
        margin: 0 0 0.5rem 0;
        font-size: 1.6rem;
        font-weight: 700;
    }

    .verify-otp-header i {
        font-size: 2.5rem;
        color: #2ecc71;
        margin-bottom: 0.5rem;
        display: block;
    }

    .verify-otp-header p {
        margin: 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .verify-otp-body {
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

    .otp-input {
        letter-spacing: 8px;
        font-size: 2rem;
        text-align: center;
        font-weight: bold;
    }

    .verify-otp-btn {
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

    .verify-otp-btn:hover {
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

    .verify-otp-footer {
        padding: 0 2rem 2rem;
        text-align: center;
    }

    .verify-otp-footer p {
        margin: 0.5rem 0;
        font-size: 0.9rem;
        color: #7f8c8d;
    }

    .verify-otp-footer a {
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
    }

    .verify-otp-footer a:hover {
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

    .email-display {
        background-color: #f5f5f5;
        padding: 0.75rem 1rem;
        border-radius: 6px;
        margin-bottom: 1rem;
        word-break: break-all;
        font-weight: 500;
    }
</style>

<div class="verify-otp-container">
    <div class="verify-otp-card">
        <div class="verify-otp-header">
            <i class="fas fa-shield-alt"></i>
            <h1>Verify OTP</h1>
            <p>Enter the OTP sent to your email</p>
        </div>

        <div class="verify-otp-body">
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
                An OTP has been sent to your email. Please enter it below.
            </div>

            <form method="POST" action="/verify-otp">
                <?= csrf_field() ?>

                <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">

                <div class="form-group">
                    <label for="otp">Email Address</label>
                    <div class="email-display">
                        <i class="fas fa-envelope"></i> <?= htmlspecialchars($email ?? '') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="otp">Enter OTP (6 digits)</label>
                    <input 
                        type="text" 
                        id="otp" 
                        name="otp" 
                        required 
                        placeholder="000000"
                        maxlength="6"
                        class="otp-input"
                        pattern="[0-9]{6}"
                        inputmode="numeric"
                    >
                </div>

                <button type="submit" class="verify-otp-btn">
                    <i class="fas fa-check"></i> Verify OTP
                </button>
            </form>
        </div>

        <div class="verify-otp-footer">
            <p>
                Didn't receive the OTP? 
                <a href="/forgot-password">
                    <i class="fas fa-redo"></i> Request again
                </a>
            </p>
            <p>
                <a href="/login">
                    <i class="fas fa-sign-in-alt"></i> Back to login
                </a>
            </p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
