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
        color: #2ecc71;
        margin-bottom: 0.5rem;
        display: block;
    }

    .auth-header p {
        margin: 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .email-badge {
        background: rgba(255,255,255,0.15);
        border-radius: 20px;
        padding: 0.25rem 0.75rem;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: inline-block;
        word-break: break-all;
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

    .otp-input {
        width: 100%;
        padding: 1rem;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        font-size: 1.8rem;
        font-weight: 700;
        text-align: center;
        letter-spacing: 0.5rem;
        transition: all 0.3s ease;
        color: #2c3e50;
    }

    .otp-input:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .auth-btn {
        width: 100%;
        padding: 0.75rem;
        background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
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
        box-shadow: 0 5px 20px rgba(46, 204, 113, 0.3);
    }

    .resend-btn {
        width: 100%;
        padding: 0.6rem;
        background: transparent;
        color: #3498db;
        border: 2px solid #3498db;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .resend-btn:hover {
        background: #3498db;
        color: white;
    }

    .resend-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
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

    .countdown {
        font-size: 0.85rem;
        color: #7f8c8d;
        margin-top: 0.5rem;
        text-align: center;
    }

    .countdown span {
        font-weight: 700;
        color: #e74c3c;
    }

    .divider {
        display: flex;
        align-items: center;
        margin: 1rem 0;
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
</style>

<div class="auth-container">
    <a href="<?= base_url('forgot-password') ?>" class="back-link">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-shield-alt header-icon"></i>
            <h1>Verify OTP</h1>
            <p>Enter the 6-digit code sent to</p>
            <?php if (!empty($email)): ?>
            <span class="email-badge"><i class="fas fa-envelope"></i> <?= esc($email) ?></span>
            <?php endif; ?>
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

            <form action="<?= base_url('verify-otp') ?>" method="POST" id="otpForm">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="otp">
                        <i class="fas fa-lock"></i> One-Time Password (OTP)
                    </label>
                    <input
                        type="text"
                        id="otp"
                        name="otp"
                        class="otp-input"
                        placeholder="• • • • • •"
                        maxlength="6"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        autocomplete="one-time-code"
                        required
                        autofocus
                    >
                </div>

                <div class="countdown" id="countdownDisplay">
                    OTP expires in: <span id="timer">10:00</span>
                </div>

                <br>

                <button type="submit" class="auth-btn">
                    <i class="fas fa-check-circle"></i> Verify OTP
                </button>
            </form>

            <div class="divider">
                <span>Didn't receive the code?</span>
            </div>

            <form action="<?= base_url('forgot-password') ?>" method="POST" id="resendForm">
                <?= csrf_field() ?>
                <input type="hidden" name="email" value="<?= esc($email ?? '') ?>">
                <button type="submit" class="resend-btn" id="resendBtn">
                    <i class="fas fa-redo"></i> Resend OTP
                </button>
            </form>
        </div>

        <div class="auth-footer">
            <p>
                <a href="<?= base_url('login') ?>"><i class="fas fa-arrow-left"></i> Back to Login</a>
            </p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// OTP: only allow digits
document.getElementById('otp').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
});

// Countdown timer (10 minutes)
(function () {
    var total = 10 * 60;
    var timerEl = document.getElementById('timer');
    var resendBtn = document.getElementById('resendBtn');
    if (!timerEl) return;

    var interval = setInterval(function () {
        total--;
        if (total <= 0) {
            clearInterval(interval);
            timerEl.textContent = '00:00';
            timerEl.style.color = '#e74c3c';
            if (resendBtn) resendBtn.removeAttribute('disabled');
            return;
        }
        var m = Math.floor(total / 60);
        var s = total % 60;
        timerEl.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }, 1000);
})();
</script>
<?= $this->endSection() ?>
