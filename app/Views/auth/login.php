<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    :root {
        --auth-ink: #1f2d3b;
        --auth-muted: #607182;
        --auth-accent: #0f766e;
        --auth-accent-dark: #0a5f59;
        --auth-warm: #f4efe7;
        --auth-line: #d5dee6;
        --auth-rail: #2d4454;
        --auth-rail-soft: #3f5d70;
    }

    .auth-stage {
        max-width: 1080px;
        margin: 18px auto 10px;
        padding: 0 10px;
        font-family: 'Gill Sans', 'Trebuchet MS', sans-serif;
        color: var(--auth-ink);
    }

    .auth-back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        color: #245f89;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .auth-back-link:hover {
        color: #174564;
    }

    .auth-card {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid var(--auth-line);
        box-shadow: 0 16px 38px rgba(23, 38, 52, 0.16);
        background: #ffffff;
        display: grid;
        grid-template-columns: 1fr 350px;
        animation: authEnter 0.5s ease;
    }

    .auth-form-panel {
        padding: 34px 34px 30px;
        background:
            linear-gradient(180deg, #ffffff 0%, var(--auth-warm) 100%);
        position: relative;
    }

    .auth-form-panel::before {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 1px;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.02), rgba(0, 0, 0, 0.12), rgba(0, 0, 0, 0.02));
    }

    .auth-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 4px 12px;
        background: #e8f5f3;
        color: var(--auth-accent-dark);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        margin-bottom: 12px;
    }

    .auth-title {
        margin: 0;
        font-family: Cambria, Cochin, Georgia, serif;
        font-size: 2rem;
        line-height: 1.15;
        color: #1c2a38;
    }

    .auth-subtitle {
        margin: 8px 0 18px;
        max-width: 560px;
        color: var(--auth-muted);
        font-size: 0.95rem;
        line-height: 1.55;
    }

    .auth-form-group {
        margin-bottom: 14px;
    }

    .auth-form-group label {
        display: block;
        margin-bottom: 6px;
        color: #2d3f50;
        font-size: 0.84rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.35px;
    }

    .auth-input {
        width: 100%;
        border: 1px solid #c8d4df;
        border-radius: 9px;
        background: #ffffff;
        color: #1f2d3b;
        padding: 10px 12px;
        font-size: 0.95rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .auth-input:focus {
        outline: none;
        border-color: var(--auth-accent);
        box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.14);
    }

    .auth-password-wrap {
        position: relative;
    }

    .auth-toggle-btn {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 8px;
        background: transparent;
        color: #5e6f7f;
        cursor: pointer;
    }

    .auth-toggle-btn:hover,
    .auth-toggle-btn:focus-visible {
        background: #ecf7f5;
        color: var(--auth-accent-dark);
        outline: none;
    }

    .auth-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin: 2px 0 16px;
    }

    .auth-row .form-check {
        margin: 0;
    }

    .auth-row .form-check-input:checked {
        background-color: var(--auth-accent);
        border-color: var(--auth-accent);
    }

    .auth-row .form-check-label {
        margin-left: 5px;
        color: #4d5e6e;
        font-size: 0.9rem;
    }

    .auth-forgot-link {
        color: #1a5f8f;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.86rem;
    }

    .auth-forgot-link:hover {
        color: #144565;
        text-decoration: underline;
    }

    .auth-submit-btn {
        width: 100%;
        border: none;
        border-radius: 10px;
        padding: 11px 16px;
        background: linear-gradient(135deg, var(--auth-accent-dark) 0%, var(--auth-accent) 100%);
        color: #ffffff;
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.2px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .auth-submit-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(10, 95, 89, 0.24);
    }

    .auth-note {
        margin-top: 12px;
        text-align: center;
        font-size: 0.83rem;
        color: #5f7081;
    }

    .auth-form-panel .alert {
        border: none;
        border-radius: 9px;
        margin-bottom: 14px;
        font-size: 0.92rem;
    }

    .auth-rail {
        padding: 28px 24px;
        background:
            radial-gradient(circle at 86% 12%, rgba(255, 255, 255, 0.18), transparent 40%),
            linear-gradient(160deg, var(--auth-rail) 0%, var(--auth-rail-soft) 100%);
        color: #ecf4fa;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 490px;
    }

    .auth-rail-title {
        margin: 0;
        font-size: 1.28rem;
        font-family: Cambria, Cochin, Georgia, serif;
    }

    .auth-rail-subtitle {
        margin: 8px 0 0;
        color: rgba(236, 244, 250, 0.86);
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .auth-rail-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 10px;
    }

    .auth-rail-list li {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.89rem;
    }

    .auth-rail-list i {
        color: #9fe3d7;
    }

    .auth-status-card {
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.08);
        padding: 12px;
    }

    .auth-status-title {
        margin: 0 0 4px;
        font-size: 0.78rem;
        letter-spacing: 0.45px;
        text-transform: uppercase;
        color: rgba(236, 244, 250, 0.82);
    }

    .auth-status-text {
        margin: 0;
        font-size: 0.88rem;
        line-height: 1.45;
    }

    @keyframes authEnter {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 980px) {
        .auth-card {
            grid-template-columns: 1fr;
        }

        .auth-form-panel::before {
            display: none;
        }

        .auth-rail {
            min-height: auto;
            gap: 16px;
        }
    }

    @media (max-width: 576px) {
        .auth-stage {
            margin-top: 12px;
            padding: 0;
        }

        .auth-form-panel {
            padding: 22px 18px 20px;
        }

        .auth-title {
            font-size: 1.55rem;
        }

        .auth-rail {
            padding: 20px 18px;
        }
    }
</style>

<div class="auth-stage">
    <a href="<?= base_url() ?>" class="auth-back-link">
        <i class="fas fa-arrow-left"></i> Back to Home
    </a>

    <div class="auth-card">
        <section class="auth-form-panel">
            <div>
                <div class="auth-kicker">
                    <i class="fas fa-key"></i>
                    Authorized Access
                </div>
                <h1 class="auth-title">Log In to Dashboard</h1>
                <p class="auth-subtitle">Sign in with your company credentials to continue to PeopleAxis HR workspace.</p>
            </div>

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

            <form action="<?= base_url('login') ?>" method="POST" autocomplete="on">
                <?= csrf_field() ?>

                <div class="auth-form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="auth-input"
                        placeholder="name@company.com"
                        value="<?= old('email') ?>"
                        autocomplete="username"
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

                <div class="auth-form-group">
                    <label for="password">Password</label>
                    <div class="auth-password-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="auth-input"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                            style="padding-right: 2.9rem;"
                        >
                        <button type="button" id="togglePassword" class="auth-toggle-btn" aria-pressed="false" aria-label="Show password">
                            <i class="fas fa-eye-slash" aria-hidden="true"></i>
                            <span class="visually-hidden">Show password</span>
                        </button>
                    </div>
                    <?php if ($errors = session('validation')) : ?>
                        <?php if (is_array($errors) && isset($errors['password'])): ?>
                            <small class="text-danger d-block mt-2">
                                <i class="fas fa-exclamation-triangle"></i> <?= $errors['password'] ?>
                            </small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="auth-row">
                    <div class="form-check">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="remember"
                            name="remember"
                        >
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>

                    <a href="<?= base_url('forgot-password') ?>" class="auth-forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="auth-submit-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Log In to Dashboard
                </button>
            </form>

            <p class="auth-note">For account updates, contact your HR administrator.</p>
        </section>

        <aside class="auth-rail">
            <div>
                <h2 class="auth-rail-title">PeopleAxis HR System</h2>
                <p class="auth-rail-subtitle">A centralized space for teams, approvals, attendance, and role-based reporting.</p>
            </div>

            <ul class="auth-rail-list">
                <li><i class="fas fa-check-circle"></i> Department and employee records</li>
                <li><i class="fas fa-check-circle"></i> Leave workflow and approval control</li>
                <li><i class="fas fa-check-circle"></i> Real-time attendance visibility</li>
                <li><i class="fas fa-check-circle"></i> Audit-ready dashboard operations</li>
            </ul>

            <div class="auth-status-card">
                <p class="auth-status-title">Session Security</p>
                <p class="auth-status-text">All sign-in requests use CSRF protection and role-based session policies.</p>
            </div>
        </aside>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    (function bindPasswordToggle() {
        function togglePasswordVisibility(event) {
            if (event) {
                event.preventDefault();
            }

            var toggle = document.getElementById('togglePassword');
            var passwordInput = document.getElementById('password');

            if (!toggle || !passwordInput) {
                return;
            }

            var isHidden = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
            toggle.setAttribute('aria-pressed', String(isHidden));
            toggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');

            var srLabel = toggle.querySelector('.visually-hidden');
            if (srLabel) {
                srLabel.textContent = isHidden ? 'Hide password' : 'Show password';
            }

            var icon = toggle.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-eye', isHidden);
                icon.classList.toggle('fa-eye-slash', !isHidden);
            }
        }

        function wireToggle() {
            var toggle = document.getElementById('togglePassword');
            if (!toggle) {
                return;
            }

            toggle.addEventListener('click', togglePasswordVisibility);
            toggle.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    togglePasswordVisibility(event);
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', wireToggle);
        } else {
            wireToggle();
        }
    })();
</script>
<?= $this->endSection() ?>
