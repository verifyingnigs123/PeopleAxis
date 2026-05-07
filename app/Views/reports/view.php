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

    .report-header {
        background: white;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .report-header h2 {
        color: #2f5f45;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .report-metadata {
        display: flex;
        gap: 30px;
        margin-top: 15px;
        flex-wrap: wrap;
        font-size: 0.9rem;
    }

    .metadata-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #7f8c8d;
    }

    .metadata-label {
        font-weight: 600;
        color: #2c3e50;
    }

    .export-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .export-btn {
        padding: 10px 18px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.9rem;
    }

    .export-pdf {
        background: #e74c3c;
        color: white;
    }

    .export-pdf:hover {
        background: #c0392b;
        transform: translateY(-2px);
    }

    .export-csv {
        background: #27ae60;
        color: white;
    }

    .export-csv:hover {
        background: #229954;
        transform: translateY(-2px);
    }

    .export-excel {
        background: #2ecc71;
        color: white;
    }

    .export-excel:hover {
        background: #27ae60;
        transform: translateY(-2px);
    }

    .report-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table thead th {
        background: #f8f9fa;
        color: #2f5f45;
        font-weight: 600;
        padding: 14px 20px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .report-table tbody td {
        padding: 12px 20px;
        border-bottom: 1px solid #f1f3f5;
        font-size: 0.9rem;
        color: #495057;
    }

    .report-table tbody tr:hover {
        background: #f8f9ff;
    }

    .summary-section {
        background: #f8f9ff;
        padding: 20px 25px;
        border-bottom: 1px solid #e1e8ed;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
    }

    .summary-item {
        text-align: center;
    }

    .summary-label {
        font-size: 0.85rem;
        color: #7f8c8d;
        text-transform: uppercase;
        font-weight: 600;
    }

    .summary-value {
        font-size: 1.8rem;
        color: #6ea988;
        font-weight: 700;
        margin-top: 5px;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .badge-success {
        background: #d4edda;
        color: #155724;
    }

    .badge-warning {
        background: #fff3cd;
        color: #856404;
    }

    .badge-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #95a5a6;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #6ea988;
        text-decoration: none;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .back-link:hover {
        text-decoration: underline;
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <a href="<?= base_url('reports') ?>">Reports</a> /
    <span><?= $reportType ?? 'Report' ?></span>
</div>

<!-- Back Link -->
<a href="<?= base_url('reports') ?>" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to Reports
</a>

<!-- Report Header -->
<div class="report-header">
    <h2><i class="fas fa-chart-bar"></i> <?= $reportTitle ?? 'Report' ?></h2>
    <div class="report-metadata">
        <div class="metadata-item">
            <span class="metadata-label">Generated on:</span>
            <span><?= date('M d, Y H:i') ?></span>
        </div>
        <div class="metadata-item">
            <span class="metadata-label">Total Records:</span>
            <span><?= isset($data) ? count($data) : 0 ?></span>
        </div>
    </div>
    
    <!-- Export / Print Helpers -->
    <?= view('reports/_print_helpers') ?>
</div>

<!-- Report Container -->
<div class="report-container">
    <!-- Summary Section -->
    <?php if (isset($summary) && !empty($summary)): ?>
        <div class="summary-section">
            <div class="summary-grid">
                <?php foreach ($summary as $label => $value): ?>
                    <div class="summary-item">
                        <div class="summary-label"><?= $label ?></div>
                        <div class="summary-value"><?= $value ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Report Data Table -->
    <div class="table-responsive" data-print-root>
        <?php if (!empty($data)): ?>
            <table class="report-table">
                <thead>
                    <tr>
                        <?php if (isset($columns) && !empty($columns)): ?>
                            <?php foreach ($columns as $column): ?>
                                <th><?= ucfirst(str_replace('_', ' ', $column)) ?></th>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <?php if (isset($columns) && !empty($columns)): ?>
                                <?php foreach ($columns as $column): ?>
                                    <td><?= isset($row->$column) ? esc($row->$column) : 'N/A' ?></td>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox" style="font-size:3rem;"></i>
                <p>No data available for this report.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Print helpers included via partial -->

<?= $this->endSection() ?>
