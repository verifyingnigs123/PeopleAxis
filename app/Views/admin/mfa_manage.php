<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<meta name="csrf-name" content="<?= csrf_token() ?>">
<meta name="csrf-hash" content="<?= csrf_hash() ?>">

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-header h1 {
        color: #2f5f45;
        font-weight: 700;
        margin: 0;
    }

    .admin-panel {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .panel-header {
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: white;
        padding: 18px 25px;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table thead th {
        background: #f8f9fa;
        color: #2f5f45;
        font-weight: 600;
        padding: 14px 20px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .admin-table tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f3f5;
        font-size: 0.95rem;
        color: #495057;
    }

    .admin-table tbody tr:hover {
        background: #f8f9ff;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .badge-enabled {
        background: #d4edda;
        color: #155724;
    }

    .badge-disabled {
        background: #f8d7da;
        color: #721c24;
    }

    .btn {
        padding: 6px 14px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover:not(:disabled) {
        background: #218838;
    }

    .btn-warning {
        background: #ffc107;
        color: white;
    }

    .btn-warning:hover:not(:disabled) {
        background: #e0a800;
    }

    .btn-disabled {
        background: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div style="max-width: 1200px; margin: 0 auto;">
    <div class="page-header">
        <h1><i class="fas fa-shield-alt"></i> MFA Management</h1>
    </div>

    <div class="admin-panel">
        <div class="panel-header">
            <i class="fas fa-users-cog"></i> Manage Two-Factor Authentication (<?= count($users) ?>)
        </div>
        <div class="table-responsive">
            <table class="admin-table" id="mfaTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Method</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u):
                        $isSuper = (isset($u->role_name) && strtolower(trim($u->role_name)) === 'super admin');
                    ?>
                    <tr data-user-id="<?= esc($u->id) ?>">
                        <td><?= esc($u->name) ?></td>
                        <td><?= esc($u->email) ?></td>
                        <td style="text-align: center;">
                            <?php if ((int) $u->mfa_enabled === 1): ?>
                                <span class="badge badge-enabled">Enabled</span>
                            <?php else: ?>
                                <span class="badge badge-disabled">Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ((int) $u->mfa_enabled === 1): ?>
                                <?= esc($u->mfa_method) ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($isSuper): ?>
                                <button class="btn btn-disabled" disabled title="Cannot modify Super Admin account">—</button>
                            <?php else: ?>
                                <?php if ((int) $u->mfa_enabled === 1): ?>
                                    <button class="btn btn-warning toggle-mfa" data-action="disable">Disable</button>
                                <?php else: ?>
                                    <button class="btn btn-success toggle-mfa" data-action="enable">Enable</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrfName = document.querySelector('meta[name="csrf-name"]').getAttribute('content');
    var csrfHash = document.querySelector('meta[name="csrf-hash"]').getAttribute('content');

    document.querySelectorAll('.toggle-mfa').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tr = btn.closest('tr');
            var userId = tr.getAttribute('data-user-id');
            var action = btn.getAttribute('data-action');

            if (!confirm('Are you sure you want to ' + action + ' MFA for this user?')) {
                return;
            }

            btn.disabled = true;

            var body = 'action=' + encodeURIComponent(action) + '&' + encodeURIComponent(csrfName) + '=' + encodeURIComponent(csrfHash);

            fetch('<?= site_url('admin/mfa') ?>/' + encodeURIComponent(userId) + '/set', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body
            }).then(function (res) {
                return res.json();
            }).then(function (json) {
                if (json.success) {
                    var statusCell = tr.querySelector('.badge-enabled, .badge-disabled');
                    var methodCell = tr.querySelector('td:nth-child(4)');

                    if (action === 'enable') {
                        if (statusCell) {
                            statusCell.textContent = 'Enabled';
                            statusCell.className = 'badge badge-enabled';
                        }
                        if (methodCell) methodCell.textContent = 'email';
                        btn.textContent = 'Disable';
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-warning');
                        btn.setAttribute('data-action', 'disable');
                    } else {
                        if (statusCell) {
                            statusCell.textContent = 'Disabled';
                            statusCell.className = 'badge badge-disabled';
                        }
                        if (methodCell) methodCell.textContent = '—';
                        btn.textContent = 'Enable';
                        btn.classList.remove('btn-warning');
                        btn.classList.add('btn-success');
                        btn.setAttribute('data-action', 'enable');
                    }

                    alert(json.message || 'MFA updated');
                } else {
                    alert(json.message || 'Failed to update MFA');
                }
            }).catch(function (err) {
                console.error(err);
                alert('An error occurred while updating MFA status.');
            }).finally(function () {
                btn.disabled = false;
            });
        });
    });
});
</script>

<?= $this->endSection() ?>
