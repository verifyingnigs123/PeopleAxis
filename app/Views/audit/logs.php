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

    .filters-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .filter-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #2c3e50;
        text-transform: uppercase;
    }

    .filter-input {
        padding: 10px 12px;
        border: 2px solid #e1e8ed;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .filter-input:focus {
        outline: none;
        border-color: #6ea988;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
    }

    .filter-btn {
        padding: 10px 20px;
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.4);
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

    .audit-table {
        width: 100%;
        border-collapse: collapse;
    }

    .audit-table thead th {
        background: #f8f9fa;
        color: #2f5f45;
        font-weight: 600;
        padding: 14px 20px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .audit-table tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f3f5;
        font-size: 0.9rem;
        color: #495057;
    }

    .audit-table tbody tr:hover {
        background: #f8f9ff;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .badge-info {
        background: #d1ecf1;
        color: #0c5460;
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

    .badge-primary {
        background: #d1d9f8;
        color: #004085;
    }

    .badge-secondary {
        background: #e2e3e5;
        color: #383d41;
    }

    .timestamp {
        font-size: 0.85rem;
        color: #7f8c8d;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #95a5a6;
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
        color: #6ea988;
        text-decoration: none;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .pagination a:hover {
        background: #6ea988;
        color: white;
        border-color: #6ea988;
    }

    .pagination .active {
        background: #6ea988;
        color: white;
        border-color: #6ea988;
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Audit Logs</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="fas fa-history"></i> Activity Logs</h1>
        <p>Track all system activities and user actions</p>
    </div>
</div>

<!-- Filters -->
<div class="filters-section">
    <form action="<?= base_url('audit') ?>" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; width: 100%;">
        <div class="filter-group">
            <label class="filter-label">User</label>
            <input type="text" name="user" class="filter-input" placeholder="Username or email" value="<?= isset($_GET['user']) ? esc($_GET['user']) : '' ?>">
        </div>

        <div class="filter-group">
            <label class="filter-label">Action</label>
            <select name="action" class="filter-input">
                <option value="">All Actions</option>
                <option value="create" <?= isset($_GET['action']) && $_GET['action'] === 'create' ? 'selected' : '' ?>>Create</option>
                <option value="update" <?= isset($_GET['action']) && $_GET['action'] === 'update' ? 'selected' : '' ?>>Update</option>
                <option value="delete" <?= isset($_GET['action']) && $_GET['action'] === 'delete' ? 'selected' : '' ?>>Delete</option>
                <option value="restore" <?= isset($_GET['action']) && $_GET['action'] === 'restore' ? 'selected' : '' ?>>Restore</option>
                <option value="login" <?= isset($_GET['action']) && $_GET['action'] === 'login' ? 'selected' : '' ?>>Login</option>
                <option value="logout" <?= isset($_GET['action']) && $_GET['action'] === 'logout' ? 'selected' : '' ?>>Logout</option>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">Date From</label>
            <input type="date" name="date_from" class="filter-input" value="<?= isset($_GET['date_from']) ? esc($_GET['date_from']) : '' ?>">
        </div>

        <div class="filter-group">
            <label class="filter-label">Date To</label>
            <input type="date" name="date_to" class="filter-input" value="<?= isset($_GET['date_to']) ? esc($_GET['date_to']) : '' ?>">
        </div>

        <button type="submit" class="filter-btn">
            <i class="fas fa-search"></i> Filter
        </button>
        <a href="<?= base_url('audit') ?>" class="filter-btn" style="background: #95a5a6;">
            <i class="fas fa-times"></i> Reset
        </a>
    </form>
</div>

<!-- Audit Logs Table -->
<div class="admin-panel">
    <div class="panel-header">
        <h2><i class="fas fa-list"></i> System Activity Log (<?= isset($total) ? $total : 0 ?> records)</h2>
    </div>

    <div class="table-responsive">
        <?php if (!empty($logs)): ?>
            <table class="audit-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Timestamp</th>
                        <th>Admin User</th>
                        <th>Action</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $i => $log): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <div class="timestamp"><?= date('M d, Y H:i:s', strtotime($log->timestamp ?? now())) ?></div>
                            </td>
                            <td>
                                <strong><?= esc($log->admin_name ?? 'System') ?></strong><br>
                                <small style="opacity:0.7;">(ID: <?= esc($log->user_id ?? 'N/A') ?>)</small>
                            </td>
                            <td>
                                <?php 
                                    $action = strtoupper($log->action ?? '');
                                    $actionBadge = match(strtolower($action)) {
                                        'create' => 'badge-success',
                                        'update' => 'badge-info',
                                        'delete' => 'badge-danger',
                                        'restore' => 'badge-warning',
                                        'sync' => 'badge-primary',
                                        'login' => 'badge-info',
                                        'logout' => 'badge-secondary',
                                        default => 'badge-secondary'
                                    };
                                ?>
                                <span class="badge <?= $actionBadge ?>"><?= esc($action) ?></span>
                            </td>
                            <td>
                                <small><?= esc($log->description ?? 'N/A') ?></small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No audit logs found.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($pager) && $pager): ?>
        <div class="pagination">
            <?= $pager->links('default', 'default_full') ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
