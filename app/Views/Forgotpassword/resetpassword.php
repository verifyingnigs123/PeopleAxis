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
        color: #e74c3c;
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

    .password-wrapper {
        position: relative;
    }

    .password-wrapper input {
        padding-right: 3.5rem;
    }

    .password-toggle {
        background: transparent;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: calc(100% - 0.4rem);
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        position: absolute;
        cursor: pointer;
        color: #7f8c8d;
    }

    .password-toggle:hover {
        color: #2c3e50;
    }

    .strength-bar {
        height: 4px;
        border-radius: 2px;
        margin-top: 0.5rem;
        background: #dee2e6;
        overflow: hidden;
    }

    .strength-fill {
        height: 100%;
        border-radius: 2px;
        transition: width 0.3s ease, background 0.3s ease;
        width: 0%;
    }

    .strength-label {
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    .auth-btn {
        width: 100%;
        padding: 0.75rem;
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
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
        box-shadow: 0 5px 20px rgba(231, 76, 60, 0.3);
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

    .password-requirements {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 0.75rem 1rem;
        margin-top: 0.5rem;
        font-size: 0.85rem;
    }

    .req-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.15rem 0;
        color: #7f8c8d;
        transition: color 0.2s ease;
    }

    .req-item.met {
        color: #27ae60;
    }

    .req-item i {
        font-size: 0.75rem;
        width: 14px;
    }
</style>

<div class="auth-container">

    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-lock-open header-icon"></i>
            <h1>Reset Password</h1>
            <p>Create a new secure password for your account</p>
            <?php if (!empty($email)): ?>
            <small style="opacity:0.75;"><?= esc($email) ?></small>
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

            <form action="<?= base_url('reset-password') ?>" method="POST" id="resetForm">
                <?= csrf_field() ?>

                <!-- New Password -->
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> New Password
                    </label>
                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Enter new password"
                            required
                            autofocus
                            minlength="6"
                        >
                        <button type="button" class="password-toggle" onclick="togglePwd('password', this)" aria-label="Toggle password visibility">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                    <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                    <div class="strength-label" id="strengthLabel"></div>

                    <div class="password-requirements" id="requirements">
                        <div class="req-item" id="req-length"><i class="fas fa-circle"></i> At least 6 characters</div>
                        <div class="req-item" id="req-upper"><i class="fas fa-circle"></i> Contains uppercase letter</div>
                        <div class="req-item" id="req-number"><i class="fas fa-circle"></i> Contains a number</div>
                        <div class="req-item" id="req-nospecial"><i class="fas fa-circle"></i> No special characters (e.g. ! @ # $)</div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirm">
                        <i class="fas fa-lock"></i> Confirm Password
                    </label>
                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password_confirm"
                            name="password_confirm"
                            class="form-control"
                            placeholder="Re-enter new password"
                            required
                            minlength="6"
                        >
                        <button type="button" class="password-toggle" onclick="togglePwd('password_confirm', this)" aria-label="Toggle confirm password visibility">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                    <small id="matchMsg" style="font-size:0.8rem;margin-top:0.3rem;display:block;"></small>
                </div>

                <button type="submit" class="auth-btn" id="submitBtn">
                    <i class="fas fa-save"></i> Update Password
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
function togglePwd(fieldId, btn) {
    var input = document.getElementById(fieldId);
    var icon  = btn.querySelector('i');
    if (!input || !icon) return;
    var show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    icon.classList.toggle('fa-eye', show);
    icon.classList.toggle('fa-eye-slash', !show);
    input.focus();
}

var pwdInput     = document.getElementById('password');
var confirmInput = document.getElementById('password_confirm');
var matchMsg     = document.getElementById('matchMsg');
var submitBtn    = document.getElementById('submitBtn');

// Strength checker
pwdInput.addEventListener('input', function () {
    // Strip special characters in real-time
    var stripped = this.value.replace(/[^A-Za-z0-9]/g, '');
    if (this.value !== stripped) {
        var pos = this.selectionStart - (this.value.length - stripped.length);
        this.value = stripped;
        this.setSelectionRange(pos, pos);
    }

    var val   = this.value;
    var score = 0;

    var reqLength    = document.getElementById('req-length');
    var reqUpper     = document.getElementById('req-upper');
    var reqNumber    = document.getElementById('req-number');
    var reqNoSpecial = document.getElementById('req-nospecial');

    if (val.length >= 6)   { score++; reqLength.classList.add('met');    reqLength.querySelector('i').className    = 'fas fa-check-circle'; }
    else                   { reqLength.classList.remove('met');           reqLength.querySelector('i').className    = 'fas fa-circle'; }

    if (/[A-Z]/.test(val)) { score++; reqUpper.classList.add('met');     reqUpper.querySelector('i').className     = 'fas fa-check-circle'; }
    else                   { reqUpper.classList.remove('met');            reqUpper.querySelector('i').className     = 'fas fa-circle'; }

    if (/[0-9]/.test(val)) { score++; reqNumber.classList.add('met');    reqNumber.querySelector('i').className    = 'fas fa-check-circle'; }
    else                   { reqNumber.classList.remove('met');           reqNumber.querySelector('i').className    = 'fas fa-circle'; }

    // No special characters — always met because we strip them above
    reqNoSpecial.classList.add('met');
    reqNoSpecial.querySelector('i').className = 'fas fa-check-circle';

    var fill   = document.getElementById('strengthFill');
    var label  = document.getElementById('strengthLabel');
    var widths = ['0%', '33%', '66%', '100%'];
    var colors = ['#dee2e6', '#e74c3c', '#f39c12', '#2ecc71'];
    var labels = ['', '<span style="color:#e74c3c">Weak</span>', '<span style="color:#f39c12">Fair</span>', '<span style="color:#2ecc71">Strong</span>'];

    fill.style.width      = widths[score];
    fill.style.background = colors[score];
    label.innerHTML       = labels[score];

    checkMatch();
});

// Match checker
confirmInput.addEventListener('input', checkMatch);

function checkMatch() {
    if (confirmInput.value === '') { matchMsg.textContent = ''; return; }
    if (pwdInput.value === confirmInput.value) {
        matchMsg.innerHTML = '<i class="fas fa-check-circle" style="color:#27ae60"></i> <span style="color:#27ae60">Passwords match</span>';
    } else {
        matchMsg.innerHTML = '<i class="fas fa-times-circle" style="color:#e74c3c"></i> <span style="color:#e74c3c">Passwords do not match</span>';
    }
}

// Prevent submit if passwords don't match or contain special characters
document.getElementById('resetForm').addEventListener('submit', function (e) {
    if (/[^A-Za-z0-9]/.test(pwdInput.value)) {
        e.preventDefault();
        pwdInput.value = pwdInput.value.replace(/[^A-Za-z0-9]/g, '');
        pwdInput.focus();
        return;
    }
    if (pwdInput.value !== confirmInput.value) {
        e.preventDefault();
        matchMsg.innerHTML = '<i class="fas fa-times-circle" style="color:#e74c3c"></i> <span style="color:#e74c3c">Passwords do not match</span>';
        confirmInput.focus();
    }
});
</script>
<?= $this->endSection() ?>
