<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .biometric-header h1 { color: #1e3c72; font-weight: 700; margin: 0 0 10px 0; }
    .biometric-header p { color: #7f8c8d; margin: 0; }
    .biometric-table { width: 100%; border-collapse: collapse; }
    .biometric-table th { background: #f8f9fa; color: #1e3c72; font-weight: 600; padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; }
    .biometric-table td { padding: 12px; border-bottom: 1px solid #e9ecef; }
    .biometric-table tbody tr:hover { background: #f8f9ff; }
    .status-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-present { background: #d4edda; color: #155724; }
    .badge-absent { background: #f8d7da; color: #721c24; }
    .badge-late { background: #fff3cd; color: #856404; }
    .empty-msg { text-align: center; padding: 40px; color: #7f8c8d; }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px; font-size: 0.9rem;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> / 
    <span><i class="fas fa-fingerprint"></i> Biometric Logs</span>
</div>

<!-- Header -->
<div class="biometric-header" style="margin-bottom: 25px;">
    <h1><i class="fas fa-fingerprint"></i> Biometric Attendance Logs</h1>
    <p>Employee attendance records from the biometric device</p>
</div>

<!-- Table Panel -->
<div style="background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <div style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 18px 20px;">
        <h2 style="margin: 0; font-size: 1.1rem; font-weight: 700;"><i class="fas fa-table"></i> Records (<?= count($logs ?? []) ?>)</h2>
    </div>
    <div style="padding: 20px; overflow-x: auto;">
        <?php if (!empty($logs)): ?>
            <table class="biometric-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee Name</th>
                        <th>Employee ID</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $index => $log): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><strong><?= esc($log->name ?? 'N/A') ?></strong></td>
                            <td><?= esc($log->employee_id ?? 'N/A') ?></td>
                            <td><?= !empty($log->date) ? date('M d, Y', strtotime($log->date)) : 'N/A' ?></td>
                            <td class="<?= !empty($log->time_in) ? 'text-success' : 'text-muted' ?>">
                                <?= !empty($log->time_in) ? '<i class="fas fa-arrow-right"></i> ' . date('H:i', strtotime($log->time_in)) : '—' ?>
                            </td>
                            <td class="<?= !empty($log->time_out) ? 'text-danger' : 'text-muted' ?>">
                                <?= !empty($log->time_out) ? '<i class="fas fa-arrow-left"></i> ' . date('H:i', strtotime($log->time_out)) : '—' ?>
                            </td>
                            <td>
                                <?php 
                                    $status = $log->status ?? 'Absent';
                                    $badgeClass = match(strtolower($status)) {
                                        'present' => 'badge-present',
                                        'late' => 'badge-late',
                                        default => 'badge-absent'
                                    };
                                ?>
                                <span class="status-badge <?= $badgeClass ?>"><?= esc(ucfirst($status)) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-msg">
                <i class="fas fa-inbox" style="font-size: 2.5rem; opacity: 0.4; margin-bottom: 10px;"></i>
                <p><strong>No attendance records found</strong></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pagination -->
<?php if (!empty($logs) && isset($pager)): ?>
    <div style="margin-top: 20px; text-align: center;">
        <?= $pager->links('default_full', 'default_full') ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
