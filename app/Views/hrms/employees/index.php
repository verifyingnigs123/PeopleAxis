<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Employees</h1>
</div>

<?php if (empty($employees)): ?>
    <p>No employees found.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Position</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $i => $e): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc(trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? ''))) ?></td>
                        <td><?= esc($e->email ?? '') ?></td>
                        <td><?= esc($e->position ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
