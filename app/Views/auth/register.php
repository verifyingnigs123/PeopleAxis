<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
	.register-container { max-width: 600px; margin: 3rem auto; }
	.card { background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,.06); overflow: hidden; }
	.card-header { background: linear-gradient(135deg,#2c3e50 0%,#3498db 100%); color: #fff; padding: 1.5rem; text-align:center }
	.card-body { padding: 1.5rem; }
	.form-group { margin-bottom: 1rem; }
	.form-group label { font-weight:600; display:block; margin-bottom:.5rem }
	.register-btn { width:100%; padding:.75rem; background:#3498db; color:#fff; border:none; border-radius:6px; font-weight:700 }
</style>

<div class="register-container">
	<div class="card">
		<div class="card-header">
			<h2>Create an account</h2>
			<p style="margin:0; opacity:.9">PeopleAxis — HR Management</p>
		</div>

		<div class="card-body">
			<?php if (session()->has('errors')): ?>
				<div class="alert alert-danger">
					<?php $errs = session('errors'); foreach ($errs as $e): ?>
						<div><?= esc($e) ?></div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if (session()->has('success')): ?>
				<div class="alert alert-success"><?= session()->get('success') ?></div>
			<?php endif; ?>

			<form action="<?= base_url('register') ?>" method="POST">
				<?= csrf_field() ?>

				<div class="form-group">
					<label for="name">Full name</label>
					<input type="text" id="name" name="name" class="form-control" value="<?= old('name') ?>" required>
				</div>

				<div class="form-group">
					<label for="email">Email address</label>
					<input type="email" id="email" name="email" class="form-control" value="<?= old('email') ?>" required>
				</div>

				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" id="password" name="password" class="form-control" required>
				</div>

				<div class="form-group">
					<label for="password_confirmation">Confirm password</label>
					<input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
				</div>

				<button type="submit" class="register-btn">Sign Up</button>
			</form>

			<p style="text-align:center; margin-top:1rem; color:#666">Already have an account? <a href="<?= base_url('login') ?>">Sign in</a></p>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

