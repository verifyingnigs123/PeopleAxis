<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .report-title {
        color: #2f5f45;
        font-weight: 700;
        font-size: 1.5rem;
        margin: 0;
    }

    .report-meta {
        color: #7f8c8d;
        font-size: 0.9rem;
        margin: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        border-left: 4px solid #6ea988;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #2f5f45;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #7f8c8d;
        font-size: 0.9rem;
    }

    .department-stats {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .department-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .department-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #6ea988;
    }

    .department-name {
        font-weight: 600;
        color: #2f5f45;
    }

    .department-count {
        background: #6ea988;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .employee-table {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .table th {
        background: #2f5f45;
        color: white;
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table td {
        padding: 12px 15px;
        border-bottom: 1px solid #e9ecef;
        font-size: 0.9rem;
    }

    .table tr:hover {
        background: #f8f9fa;
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    .status-active {
        background: #d4edda;
        color: #155724;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-inactive {
        background: #f8d7da;
        color: #721c24;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge {
        background: #6ea988;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: white;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    @media (max-width: 768px) {
        .report-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .department-grid {
            grid-template-columns: 1fr;
        }

        .table {
            font-size: 0.8rem;
        }

        .table th, .table td {
            padding: 8px;
        }
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <a href="<?= base_url('reports') ?>"><i class="fas fa-chart-bar"></i> Reports</a> /
    <span>Employee Report</span>
</div>

<!-- Report Header -->
<div class="report-header">
    <div>
        <h1 class="report-title"><i class="fas fa-users"></i> <?= esc($title) ?></h1>
        <p class="report-meta">Generated on: <?= date('F j, Y \a\t g:i A', strtotime($generated_at)) ?></p>
    </div>
</div>

<!-- Statistics Overview -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?= number_format($total_employees) ?></div>
        <div class="stat-label">Total Employees</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= count($by_department) ?></div>
        <div class="stat-label">Departments</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= count(array_filter($employees, function($emp) { return $emp['account_status'] === 'approved'; })) ?></div>
        <div class="stat-label">Active Employees</div>
    </div>
</div>

<!-- Department Statistics -->
<div class="department-stats">
    <h3 style="color: #2f5f45; margin-bottom: 10px;"><i class="fas fa-building"></i> Department Distribution</h3>
    <div class="department-grid">
        <?php foreach ($by_department as $dept): ?>
            <div class="department-item">
                <span class="department-name"><?= esc($dept['name'] ?? 'Unassigned') ?></span>
                <span class="department-count"><?= number_format($dept['count']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Employee Table -->
<div class="employee-table" data-print-root>
    <table class="table">
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Position</th>
                <th>Status</th>
                <th>Join Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($employees)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #7f8c8d;">
                        <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 10px; display: block;"></i>
                        <p>No employees found</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($employees as $employee): ?>
                    <tr>
                        <td>
                            <span class="badge"><?= esc($employee['employee_id'] ?? 'N/A') ?></span>
                        </td>
                        <td>
                            <strong><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></strong>
                        </td>
                        <td><?= esc($employee['user_email'] ?? $employee['email'] ?? 'N/A') ?></td>
                        <td><?= esc($employee['department_name'] ?? 'Unassigned') ?></td>
                        <td><?= esc($employee['position_name'] ?? 'N/A') ?></td>
                        <td>
                            <?php if ($employee['account_status'] === 'approved'): ?>
                                <span class="status-active">Active</span>
                            <?php else: ?>
                                <span class="status-inactive">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('M j, Y', strtotime($employee['date_of_joining'])) ?></td>
                        <td>
                            <a href="<?= base_url('employee/show/' . $employee['id']) ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.8rem;">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Action Buttons -->
<div class="action-buttons">
    <a href="<?= base_url('reports') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Reports
    </a>
    <?= view('reports/_print_helpers') ?>
</div>

<?= $this->endSection() ?>
