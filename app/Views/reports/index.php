<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .page-header h1 {
        color: #2f5f45;
        font-weight: 700;
        margin: 0;
    }

    .reports-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 14px;
        margin-top: 14px;
    }

    .report-card {
        background: white;
        border-radius: 9px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        border-left: 3px solid #6ea988;
    }

    .report-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.1);
    }

    .report-icon {
        font-size: 2rem;
        color: #6ea988;
        margin-bottom: 10px;
    }

    .report-title {
        font-size: 1.03rem;
        font-weight: 700;
        color: #2f5f45;
        margin-bottom: 6px;
    }

    .report-description {
        font-size: 0.84rem;
        line-height: 1.45;
        color: #7f8c8d;
        margin-bottom: 12px;
        flex-grow: 1;
    }

    .report-btn {
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: white;
        padding: 8px 12px;
        border-radius: 7px;
        text-align: center;
        font-size: 0.84rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: none;
        cursor: pointer;
    }

    .report-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.4);
        text-decoration: none;
        color: white;
    }

    .alert {
        border-radius: 6px;
        padding: 15px 20px;
        margin-bottom: 20px;
    }

    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #95a5a6;
    }

    @media (max-width: 768px) {
        .reports-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Reports</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="fas fa-chart-bar"></i> Reports</h1>
        <p>Generate and view various HR analytics and reports</p>
    </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<!-- Reports Grid -->
<div class="reports-grid">
    <!-- Employee Report Card -->
    <div class="report-card">
        <div class="report-icon">
            <i class="fas fa-users"></i>
        </div>
        <h3 class="report-title">Employee Report</h3>
        <p class="report-description">View detailed employee information including contact details, positions, and employment status.</p>
        <a href="<?= base_url('reports/generate/employee') ?>" class="report-btn">
            <i class="fas fa-file-alt"></i> Generate
        </a>
    </div>

    <!-- Attendance Report Card -->
    <div class="report-card">
        <div class="report-icon">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <h3 class="report-title">Attendance Report</h3>
        <p class="report-description">Track attendance records, late arrivals, early departures, and absent days.</p>
        <a href="<?= base_url('reports/generate/attendance') ?>" class="report-btn">
            <i class="fas fa-file-alt"></i> Generate
        </a>
    </div>

    <!-- Leave Report Card -->
    <div class="report-card">
        <div class="report-icon">
            <i class="fas fa-calendar-times"></i>
        </div>
        <h3 class="report-title">Leave Report</h3>
        <p class="report-description">View leave requests, approvals, rejections, and remaining leave balance.</p>
        <a href="<?= base_url('reports/generate/leave') ?>" class="report-btn">
            <i class="fas fa-file-alt"></i> Generate
        </a>
    </div>

    <!-- Salary Report Card -->
    <div class="report-card">
        <div class="report-icon">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <h3 class="report-title">Salary Report</h3>
        <p class="report-description">Generate payroll reports including salaries, allowances, deductions, and net pay.</p>
        <a href="<?= base_url('reports/generate/salary') ?>" class="report-btn">
            <i class="fas fa-file-alt"></i> Generate
        </a>
    </div>

    <!-- Department Report Card -->
    <div class="report-card">
        <div class="report-icon">
            <i class="fas fa-building"></i>
        </div>
        <h3 class="report-title">Department Report</h3>
        <p class="report-description">View department-wise employee distribution and organizational structure.</p>
        <a href="<?= base_url('reports/generate/department') ?>" class="report-btn">
            <i class="fas fa-file-alt"></i> Generate
        </a>
    </div>
</div>

<?= $this->endSection() ?>
