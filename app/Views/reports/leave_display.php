<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .report-header {
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    
    .report-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #2f5f45;
    }
    
    .stat-label {
        color: #666;
        margin-top: 5px;
    }
    
    .leave-table {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .table {
        margin: 0;
    }
    
    .table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #2f5f45;
        border-bottom: 2px solid #dee2e6;
    }
    
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-approved {
        background: #d4edda;
        color: #155724;
    }
    
    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }
    
    .no-data {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }
    
    .action-buttons {
        margin-bottom: 20px;
    }
    
    .btn-export {
        background: #28a745;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .btn-export:hover {
        background: #218838;
        color: white;
        text-decoration: none;
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> / 
    <a href="<?= base_url('reports') ?>"><i class="fas fa-chart-bar"></i> Reports</a> / 
    <span>Leave Report</span>
</div>

<!-- Report Header -->
<div class="report-header">
    <h1><i class="fas fa-calendar-times"></i> Leave Report</h1>
    <p>Comprehensive overview of all leave requests and their status</p>
    <small>Generated on: <?= date('F d, Y h:i A') ?></small>
</div>

<!-- Action Buttons -->
<div class="action-buttons">
    <a href="<?= base_url('reports') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Reports
    </a>
    <button class="btn-export" onclick="exportToExcel()">
        <i class="fas fa-file-excel"></i> Export to Excel
    </button>
</div>

<?php if (!empty($reportData['data'])): ?>
    
    <!-- Statistics Cards -->
    <div class="report-stats">
        <div class="stat-card">
            <div class="stat-number"><?= count($reportData['data']) ?></div>
            <div class="stat-label">Total Leave Requests</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= count(array_filter($reportData['data'], fn($d) => $d['status'] === 'pending')) ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= count(array_filter($reportData['data'], fn($d) => $d['status'] === 'approved')) ?></div>
            <div class="stat-label">Approved</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= count(array_filter($reportData['data'], fn($d) => $d['status'] === 'rejected')) ?></div>
            <div class="stat-label">Rejected</div>
        </div>
    </div>

    <!-- Leave Requests Table -->
    <div class="leave-table">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Employee</th>
                    <th>Employee Code</th>
                    <th>Department</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData['data'] as $leave): ?>
                    <tr>
                        <td><?= $leave['id'] ?></td>
                        <td><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></td>
                        <td><?= htmlspecialchars($leave['emp_code'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($leave['department_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($leave['leave_type']) ?></td>
                        <td><?= date('M d, Y', strtotime($leave['start_date'])) ?></td>
                        <td><?= date('M d, Y', strtotime($leave['end_date'])) ?></td>
                        <td><?= $leave['number_of_days'] ?></td>
                        <td>
                            <span class="status-badge status-<?= $leave['status'] ?>">
                                <?= ucfirst($leave['status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars(substr($leave['reason'], 0, 50)) ?><?= strlen($leave['reason']) > 50 ? '...' : '' ?></td>
                        <td><?= date('M d, Y h:i A', strtotime($leave['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php else: ?>
    
    <!-- No Data Message -->
    <div class="no-data">
        <i class="fas fa-calendar-times" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
        <h3>No Leave Requests Found</h3>
        <p>There are currently no leave requests in the system.</p>
        <a href="<?= base_url('reports') ?>" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
    </div>

<?php endif; ?>

<script>
function exportToExcel() {
    const table = document.querySelector('.table');
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            // Get text content and remove HTML tags
            let text = cols[j].textContent || cols[j].innerText;
            // Clean up the text and escape quotes
            text = text.replace(/"/g, '""').trim();
            // Add quotes if contains comma or quote
            if (text.includes(',') || text.includes('"')) {
                text = `"${text}"`;
            }
            row.push(text);
        }
        csv.push(row.join(','));
    }
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', 'leave_report_' + new Date().toISOString().split('T')[0] + '.csv');
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
</script>

<?= $this->endSection() ?>
