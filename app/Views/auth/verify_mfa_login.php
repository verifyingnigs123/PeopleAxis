<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-sm" style="width: 100%; max-width: 400px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                <h2 class="card-title">Two-Factor Authentication</h2>
                <p class="text-muted">We sent you a sign-in verification code via email. Enter the digits shown below to verify your identity.</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('mfa-login/verify'); ?>" method="POST">
                <?= csrf_field(); ?>

                <div class="mb-3">
                    <label for="otp" class="form-label">Verification Code</label>
                    <input 
                        type="text" 
                        id="otp" 
                        name="otp" 
                        class="form-control form-control-lg text-center <?= session('errors.otp') ? 'is-invalid' : ''; ?>" 
                        placeholder="000000" 
                        maxlength="6" 
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        required
                        autofocus
                    >
                    <?php if (session('errors.otp')): ?>
                        <div class="invalid-feedback d-block">
                            <?= session('errors.otp'); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-check mb-3">
                    <input 
                        class="form-check-input" 
                        type="checkbox" 
                        id="remember_device" 
                        name="remember_device"
                        value="1"
                    >
                    <label class="form-check-label" for="remember_device">
                        Trust this device for 30 days
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-lg">
                    <i class="fas fa-check-circle"></i> Verify
                </button>
            </form>

            <div class="text-center mt-3">
                <p class="text-muted small mb-0">
                    Didn't receive the code? <a href="<?= base_url('login'); ?>" class="text-decoration-none">Try logging in again</a>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control-lg {
        font-size: 1.5rem;
        letter-spacing: 0.5rem;
        font-weight: 600;
    }
    
    .card {
        border: none;
        border-radius: 0.5rem;
    }
</style>

<script>
    // Auto-format OTP input
    document.getElementById('otp').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
    });
</script>
<?= $this->endSection() ?>
