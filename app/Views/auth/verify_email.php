<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<div style="max-width:480px;margin:3rem auto">
    <div class="card">
        <div class="card-header" style="background:#3498db;color:#fff;padding:1rem;text-align:center">
            <h3>Verify your email</h3>
            <p style="margin:0;opacity:.9">Enter the verification code sent to your email</p>
        </div>
        <div class="card-body" style="padding:1rem">
            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger"><?= session()->get('error') ?></div>
            <?php endif; ?>
            <?php if (session()->has('warning')): ?>
                <div class="alert alert-warning"><?= session()->get('warning') ?></div>
            <?php endif; ?>
            <?php if (session()->has('success')): ?>
                <div class="alert alert-success"><?= session()->get('success') ?></div>
            <?php endif; ?>

            <form action="<?= base_url('verify-email') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="code">Verification code</label>
                    <input type="text" id="code" name="code" class="form-control" required maxlength="6" placeholder="e.g. 123456">
                </div>
                <button class="btn btn-primary" type="submit">Verify</button>
            </form>

            <p style="margin-top:1rem">Didn't receive email? Check spam or <a href="<?= base_url('contact') ?>">contact support</a>.</p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
