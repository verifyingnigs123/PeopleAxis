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
        color: #1e3c72;
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
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 20px 25px;
    }

    .panel-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.3rem;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .salary-table {
        width: 100%;
        border-collapse: collapse;
    }

    .salary-table thead th {
        background: #f8f9fa;
        color: #1e3c72;
        font-weight: 600;
        padding: 12px 15px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.8rem;
        text-transform: uppercase;
    }

    .salary-table tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid #f1f3f5;
        font-size: 0.85rem;
        color: #495057;
    }

    .salary-table tbody tr:hover {
        background: #f8f9ff;
    }

    .salary-input {
        padding: 6px 8px;
        border: 1px solid #e1e8ed;
        border-radius: 4px;
        font-size: 0.85rem;
        width: 100%;
        transition: all 0.3s ease;
    }

    .salary-input:focus {
        outline: none;
        border-color: #2a5298;
        box-shadow: 0 0 0 2px rgba(42, 82, 152, 0.1);
    }

    .currency {
        color: #27ae60;
        font-weight: 700;
    }

    .action-buttons {
        display: flex;
        gap: 6px;
    }

    .btn-save {
        padding: 6px 12px;
        background: #27ae60;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        background: #229954;
    }

    .btn-cancel {
        padding: 6px 12px;
        background: #e1e8ed;
        color: #2c3e50;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: #d5dce3;
    }

    .btn-edit {
        padding: 6px 12px;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-edit:hover {
        background: #2980b9;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #95a5a6;
    }

    .alert {
        border-radius: 6px;
        padding: 15px 20px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        padding: 20px;
        background: #f8f9fa;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border-radius: 4px;
        background: white;
        color: #2a5298;
        text-decoration: none;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .pagination a:hover {
        background: #2a5298;
        color: white;
        border-color: #2a5298;
    }

    .pagination .active {
        background: #2a5298;
        color: white;
        border-color: #2a5298;
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Salary Management</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="fas fa-money-bill-wave"></i> Salary Management</h1>
        <p>Manage employee salaries and compensation</p>
    </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<!-- Salary Table -->
<div class="admin-panel">
    <div class="panel-header">
        <h2><i class="fas fa-list"></i> Employee Salaries (<?= count($salaries ?? []) ?>)</h2>
    </div>

    <div class="table-responsive">
        <?php if (!empty($salaries)): ?>
            <table class="salary-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee Name</th>
                        <th>Position</th>
                        <th>Base Salary</th>
                        <th>Allowances</th>
                        <th>Deductions</th>
                        <th>Gross Salary</th>
                        <th>Net Salary</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salaries as $i => $salary): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <strong><?= esc($salary->employee_name ?? 'N/A') ?></strong>
                                <br>
                                <small style="color: #7f8c8d;"><?= esc($salary->employee_id ?? 'N/A') ?></small>
                            </td>
                            <td><?= esc($salary->position_name ?? 'N/A') ?></td>
                            <td class="currency"><?= number_format($salary->base_salary ?? 0, 2) ?></td>
                            <td class="currency"><?= number_format($salary->allowances ?? 0, 2) ?></td>
                            <td class="currency"><?= number_format($salary->deductions ?? 0, 2) ?></td>
                            <td class="currency">
                                <?php
                                    $grossSalary = ($salary->base_salary ?? 0) + ($salary->allowances ?? 0);
                                    echo number_format($grossSalary, 2);
                                ?>
                            </td>
                            <td class="currency" style="color:#2a5298;font-weight:700;">
                                <?php
                                    $netSalary = $grossSalary - ($salary->deductions ?? 0);
                                    echo number_format($netSalary, 2);
                                ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-edit" onclick="editSalary(<?= $salary->id ?? 0 ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No salary records found.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($pager) && $pager): ?>
        <div class="pagination">
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    <?php endif; ?>
</div>

<!-- Edit Salary Modal -->
<div class="modal fade" id="editSalaryModal" tabindex="-1" aria-labelledby="editSalaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);color:white;border:none;">
                <h5 class="modal-title" id="editSalaryModalLabel">
                    <i class="fas fa-edit me-2"></i> Update Salary
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSalaryForm" method="POST" action="">
                <?= csrf_field() ?>
                <div class="modal-body" style="padding:30px;">
                    <div class="mb-3">
                        <label for="modal_base_salary" class="form-label fw-600">Base Salary</label>
                        <input type="number" class="form-control" id="modal_base_salary" name="base_salary" placeholder="Enter base salary" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="modal_allowances" class="form-label fw-600">Allowances</label>
                        <input type="number" class="form-control" id="modal_allowances" name="allowances" placeholder="Enter allowances" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="modal_deductions" class="form-label fw-600">Deductions</label>
                        <input type="number" class="form-control" id="modal_deductions" name="deductions" placeholder="Enter deductions" step="0.01" required>
                    </div>

                    <div class="mb-3" style="background:#f8f9ff;padding:12px;border-radius:6px;">
                        <p style="margin:0;">
                            <strong>Net Salary: </strong>
                            <span id="net_salary_display" style="color:#2a5298;font-weight:700;">0.00</span>
                        </p>
                    </div>
                </div>
                <div class="modal-footer" style="border:none;padding:15px 30px 25px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);border:none;padding:8px 24px;">
                        <i class="fas fa-save me-1"></i> Update Salary
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
<script>
function editSalary(salaryId) {
    // Fetch salary data via AJAX
    fetch('<?= base_url('employees/getSalary') ?>/' + salaryId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modal_base_salary').value = data.data.base_salary || 0;
                document.getElementById('modal_allowances').value = data.data.allowances || 0;
                document.getElementById('modal_deductions').value = data.data.deductions || 0;
                document.getElementById('editSalaryForm').action = '<?= base_url('employees/updateSalary') ?>/' + salaryId;
                calculateNetSalary();
                new bootstrap.Modal(document.getElementById('editSalaryModal')).show();
            }
        })
        .catch(error => alert('Error loading salary data: ' + error.message));
}

function calculateNetSalary() {
    const baseSalary = parseFloat(document.getElementById('modal_base_salary').value || 0);
    const allowances = parseFloat(document.getElementById('modal_allowances').value || 0);
    const deductions = parseFloat(document.getElementById('modal_deductions').value || 0);
    const netSalary = (baseSalary + allowances) - deductions;
    document.getElementById('net_salary_display').textContent = netSalary.toFixed(2);
}

document.getElementById('modal_base_salary').addEventListener('change', calculateNetSalary);
document.getElementById('modal_allowances').addEventListener('change', calculateNetSalary);
document.getElementById('modal_deductions').addEventListener('change', calculateNetSalary);
</script>

<?= $this->endSection() ?>
