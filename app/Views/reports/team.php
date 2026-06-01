<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<?php
    $summaryCards = $summaryCards ?? [];
    $trendCharts = $trendCharts ?? ['daily' => ['labels' => [], 'current' => [], 'previous' => []], 'weekly' => ['labels' => [], 'current' => [], 'previous' => []], 'monthly' => ['labels' => [], 'current' => [], 'previous' => []]];
    $performanceDistribution = $performanceDistribution ?? ['labels' => ['Excellent', 'Good', 'Average', 'Needs Improvement'], 'values' => [0, 0, 0, 0]];
    $attendanceAnalytics = $attendanceAnalytics ?? ['attendance_rate' => 0, 'absences' => 0, 'late_arrivals' => 0, 'overtime_hours' => 0, 'attendance_change' => 0, 'attendance_trend' => 'stable', 'correlation' => []];
    $productivityMetrics = $productivityMetrics ?? ['assigned_tasks' => 0, 'completed_tasks' => 0, 'completion_rate' => 0, 'average_completion_time' => 0, 'productivity_score' => 0, 'productivity_change' => 0, 'bar_chart' => []];
    $departmentInsights = $departmentInsights ?? ['rows' => [], 'highest' => null, 'lowest' => null];
    $aiInsights = $aiInsights ?? [];
    $recommendations = $recommendations ?? ['promotion' => [], 'coaching' => [], 'attendance' => [], 'training' => []];
    $leaderboard = $leaderboard ?? [];
    $performanceRows = $performanceRows ?? [];
    $filterOptions = $filterOptions ?? ['departments' => [], 'teams' => [], 'attendance_statuses' => [], 'performance_categories' => []];
    $filters = $filters ?? ['month' => date('Y-m'), 'start_date' => date('Y-m-01'), 'end_date' => date('Y-m-t'), 'employee_name' => '', 'department_id' => '', 'team' => '', 'attendance_status' => '', 'performance_category' => ''];
    $managedDepartments = $managedDepartments ?? [];
    $periodLabel = $periodLabel ?? date('F Y');
    $dashboardPayload = [
        'summaryCards' => $summaryCards,
        'trendCharts' => $trendCharts,
        'performanceDistribution' => $performanceDistribution,
        'attendanceAnalytics' => $attendanceAnalytics,
        'productivityMetrics' => $productivityMetrics,
        'departmentInsights' => $departmentInsights,
        'aiInsights' => $aiInsights,
        'recommendations' => $recommendations,
        'leaderboard' => $leaderboard,
        'performanceRows' => $performanceRows,
        'filters' => $filters,
    ];
?>

<style>
    :root {
        --pea-bg: #f3f7f4;
        --pea-surface: #ffffff;
        --pea-text: #12301f;
        --pea-muted: #6b7b72;
        --pea-primary: #256d4a;
        --pea-primary-dark: #184c33;
        --pea-accent: #7cc6a4;
        --pea-warning: #d4a017;
        --pea-danger: #d1495b;
        --pea-success: #1f8a55;
        --pea-info: #2b6cb0;
        --pea-border: rgba(20, 40, 30, 0.08);
        --pea-shadow: 0 18px 40px rgba(20, 40, 30, 0.08);
    }

    .team-analytics-page {
        color: var(--pea-text);
    }

    .team-hero {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        padding: 28px;
        background: radial-gradient(circle at top left, rgba(124, 198, 164, 0.35), transparent 34%), linear-gradient(135deg, #f7fbf8 0%, #eef5ef 100%);
        border: 1px solid var(--pea-border);
        box-shadow: var(--pea-shadow);
        margin-bottom: 22px;
    }

    .team-hero::after {
        content: '';
        position: absolute;
        right: -40px;
        top: -40px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(37, 109, 74, 0.08);
        filter: blur(6px);
    }

    .hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(320px, 0.9fr);
        gap: 20px;
        align-items: stretch;
        position: relative;
        z-index: 1;
    }

    .hero-title {
        font-size: clamp(2rem, 3vw, 3.25rem);
        line-height: 1.03;
        margin: 0 0 12px;
        font-weight: 800;
        letter-spacing: -0.04em;
        color: var(--pea-primary-dark);
    }

    .hero-copy {
        margin: 0;
        color: var(--pea-muted);
        max-width: 68ch;
        font-size: 0.98rem;
    }

    .hero-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .hero-pill,
    .badge-soft {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 8px 12px;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .hero-pill {
        background: rgba(37, 109, 74, 0.08);
        color: var(--pea-primary-dark);
    }

    .hero-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .team-utility-card {
        background: rgba(255, 255, 255, 0.76);
        backdrop-filter: blur(14px);
        border: 1px solid var(--pea-border);
        border-radius: 22px;
        padding: 18px;
        box-shadow: var(--pea-shadow);
    }

    .utility-stat {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 14px;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid rgba(20, 40, 30, 0.08);
    }

    .utility-stat:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .utility-stat:first-child {
        padding-top: 0;
    }

    .utility-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        background: linear-gradient(135deg, var(--pea-primary), var(--pea-accent));
        box-shadow: 0 12px 20px rgba(37, 109, 74, 0.18);
    }

    .utility-stat h6,
    .utility-stat p {
        margin: 0;
    }

    .utility-stat h6 {
        font-weight: 800;
        color: var(--pea-primary-dark);
    }

    .utility-stat p {
        color: var(--pea-muted);
        font-size: 0.9rem;
    }

    .filter-card,
    .dashboard-card {
        background: var(--pea-surface);
        border: 1px solid var(--pea-border);
        border-radius: 22px;
        box-shadow: var(--pea-shadow);
    }

    .filter-card {
        padding: 18px;
        margin-bottom: 22px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 14px;
    }

    .filter-grid .field-wide {
        grid-column: span 2;
    }

    .filter-grid .field-full {
        grid-column: 1 / -1;
    }

    .filter-grid label {
        display: block;
        margin-bottom: 6px;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--pea-muted);
    }

    .filter-grid .form-control,
    .filter-grid .form-select {
        border-radius: 14px;
        border-color: rgba(20, 40, 30, 0.12);
        min-height: 46px;
    }

    .section-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    .kpi-card {
        padding: 18px;
        position: relative;
        overflow: hidden;
        min-height: 152px;
    }

    .kpi-card::after {
        content: '';
        position: absolute;
        inset: auto -16px -16px auto;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(37, 109, 74, 0.08), transparent 62%);
    }

    .kpi-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 14px;
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        background: linear-gradient(135deg, var(--pea-primary), var(--pea-accent));
    }

    .kpi-label {
        margin: 0;
        color: var(--pea-muted);
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 800;
    }

    .kpi-value {
        font-size: clamp(1.65rem, 2.6vw, 2.5rem);
        font-weight: 800;
        letter-spacing: -0.03em;
        margin: 6px 0 0;
    }

    .kpi-delta {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        font-size: 0.84rem;
        font-weight: 700;
        border-radius: 999px;
        padding: 6px 10px;
    }

    .delta-up { background: rgba(31, 138, 85, 0.1); color: var(--pea-success); }
    .delta-down { background: rgba(209, 73, 91, 0.1); color: var(--pea-danger); }
    .delta-flat { background: rgba(43, 108, 176, 0.1); color: var(--pea-info); }

    .grid-span-3 { grid-column: span 3; }
    .grid-span-4 { grid-column: span 4; }
    .grid-span-5 { grid-column: span 5; }
    .grid-span-6 { grid-column: span 6; }
    .grid-span-7 { grid-column: span 7; }
    .grid-span-8 { grid-column: span 8; }
    .grid-span-12 { grid-column: span 12; }

    .section-title {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
        margin-bottom: 14px;
    }

    .section-title h2,
    .section-title h3 {
        margin: 0;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: var(--pea-primary-dark);
    }

    .section-title p {
        margin: 4px 0 0;
        color: var(--pea-muted);
    }

    .chart-shell {
        padding: 18px;
        min-height: 340px;
    }

    .chart-wrap {
        position: relative;
        min-height: 280px;
    }

    .leaderboard-list,
    .insight-list,
    .recommendation-list {
        display: grid;
        gap: 10px;
    }

    .leaderboard-item,
    .insight-item,
    .recommendation-item {
        border: 1px solid rgba(20, 40, 30, 0.08);
        border-radius: 18px;
        padding: 14px;
        background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,251,248,.98));
    }

    .leaderboard-item {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 12px;
        align-items: center;
    }

    .rank-badge {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--pea-primary), var(--pea-accent));
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
    }

    .leaderboard-item h5,
    .leaderboard-item p,
    .recommendation-item p,
    .insight-item p {
        margin: 0;
    }

    .leaderboard-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 6px;
        color: var(--pea-muted);
        font-size: 0.88rem;
    }

    .status-badge,
    .badge-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 5px 10px;
        font-weight: 700;
        font-size: 0.78rem;
    }

    .status-excellent { background: rgba(31, 138, 85, 0.12); color: var(--pea-success); }
    .status-good { background: rgba(43, 108, 176, 0.12); color: var(--pea-info); }
    .status-average { background: rgba(212, 160, 23, 0.14); color: #8a6500; }
    .status-risk { background: rgba(209, 73, 91, 0.12); color: var(--pea-danger); }
    .tone-green { background: rgba(31, 138, 85, 0.12); color: var(--pea-success); }
    .tone-yellow { background: rgba(212, 160, 23, 0.14); color: #8a6500; }
    .tone-red { background: rgba(209, 73, 91, 0.12); color: var(--pea-danger); }

    .progress-soft {
        height: 10px;
        border-radius: 999px;
        background: rgba(20, 40, 30, 0.06);
        overflow: hidden;
    }

    .progress-soft .progress-bar {
        border-radius: 999px;
    }

    .table-shell {
        padding: 18px;
    }

    .analytics-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .analytics-table thead th {
        position: sticky;
        top: 0;
        background: #f8fbf9;
        color: var(--pea-primary-dark);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.76rem;
        padding: 14px 14px;
        border-bottom: 1px solid rgba(20, 40, 30, 0.12);
        white-space: nowrap;
    }

    .analytics-table tbody td {
        padding: 14px;
        border-bottom: 1px solid rgba(20, 40, 30, 0.08);
        vertical-align: top;
    }

    .analytics-table tbody tr:hover {
        background: rgba(124, 198, 164, 0.06);
    }

    .table-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .icon-btn {
        border: 1px solid rgba(20, 40, 30, 0.12);
        background: #fff;
        color: var(--pea-primary-dark);
        border-radius: 12px;
        padding: 8px 12px;
        font-weight: 700;
        font-size: 0.84rem;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .icon-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 18px rgba(20, 40, 30, 0.08);
    }

    .empty-state {
        text-align: center;
        padding: 42px 24px;
        color: var(--pea-muted);
    }

    .empty-state i {
        font-size: 2.8rem;
        display: block;
        margin-bottom: 10px;
        color: var(--pea-accent);
    }

    .table-toolbar {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: end;
        margin-bottom: 16px;
    }

    .table-toolbar .form-control,
    .table-toolbar .form-select {
        min-height: 44px;
        border-radius: 14px;
    }

    .toolbar-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .badge-inline {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 5px 10px;
        font-weight: 700;
        font-size: 0.78rem;
    }

    .recommendation-item h5 {
        margin: 0 0 6px;
        font-weight: 800;
        color: var(--pea-primary-dark);
    }

    .department-row {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 12px;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid rgba(20, 40, 30, 0.08);
    }

    .department-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .department-row:first-child {
        padding-top: 0;
    }

    .rating-chip {
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 0.8rem;
        font-weight: 800;
    }

    .rating-green { background: rgba(31, 138, 85, 0.12); color: var(--pea-success); }
    .rating-yellow { background: rgba(212, 160, 23, 0.14); color: #8a6500; }
    .rating-red { background: rgba(209, 73, 91, 0.12); color: var(--pea-danger); }

    .mini-stat {
        border-radius: 18px;
        padding: 14px;
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,251,248,0.98));
        border: 1px solid rgba(20, 40, 30, 0.08);
    }

    .mini-stat h4 {
        margin: 0;
        font-weight: 800;
    }

    .mini-stat p {
        margin: 6px 0 0;
        color: var(--pea-muted);
        font-size: 0.9rem;
    }

    .modal-content {
        border-radius: 22px;
        border: 1px solid rgba(20, 40, 30, 0.08);
        box-shadow: var(--pea-shadow);
    }

    @media (max-width: 1200px) {
        .hero-grid { grid-template-columns: 1fr; }
        .filter-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-span-3, .grid-span-4, .grid-span-5, .grid-span-6, .grid-span-7, .grid-span-8 { grid-column: span 12; }
    }

    @media (max-width: 768px) {
        .team-hero { padding: 20px; border-radius: 20px; }
        .filter-grid { grid-template-columns: 1fr; }
        .filter-grid .field-wide { grid-column: span 1; }
        .hero-actions, .table-actions { width: 100%; }
        .section-grid { gap: 14px; }
        .leaderboard-item { grid-template-columns: auto 1fr; }
        .leaderboard-item .leaderboard-score { grid-column: 1 / -1; }
    }

    @media print {
        .no-print { display: none !important; }
        .team-hero, .filter-card, .dashboard-card, .table-shell, .chart-shell, .mini-stat { box-shadow: none !important; }
        body { background: #fff !important; }
    }
</style>

<div class="team-analytics-page">
    <div class="team-hero no-print">
        <div class="hero-grid">
            <div>
                <div class="hero-meta">
                    <span class="hero-pill"><i class="fas fa-chart-line"></i> Team Performance Analytics</span>
                    <span class="hero-pill"><i class="fas fa-bolt"></i> Real-time manager insights</span>
                    <span class="hero-pill"><i class="fas fa-layer-group"></i> Period: <?= esc($periodLabel) ?></span>
                </div>
                <h1 class="hero-title">Professional team performance dashboard for managers</h1>
                <p class="hero-copy">Track attendance and department health from a single executive view. The dashboard uses current attendance and leave signals to generate performance trends, actionable recommendations, and export-ready reporting.</p>
                <div class="hero-actions">
                    <button type="button" class="btn btn-success btn-lg" onclick="window.peopleAxisTeamReport.printSummary()"><i class="fas fa-print me-2"></i>Print Summary</button>
                    <button type="button" class="btn btn-outline-success btn-lg" onclick="window.peopleAxisTeamReport.downloadExcel()"><i class="fas fa-file-excel me-2"></i>Excel Report</button>
                    
                </div>
            </div>

            <div class="team-utility-card">
                <div class="utility-stat">
                    <div class="utility-icon"><i class="fas fa-users"></i></div>
                    <div>
                        <h6><?= count($managedDepartments) ?> managed departments</h6>
                        <p><?= (int) $teamCount ?> team members in scope</p>
                    </div>
                </div>
                <div class="utility-stat">
                    <div class="utility-icon"><i class="fas fa-wave-square"></i></div>
                    <div>
                        <h6><?= number_format((float) ($attendanceAnalytics['attendance_rate'] ?? 0), 1) ?>% attendance rate</h6>
                        <p>Attendance summary for the selected period</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="filter-card dashboard-card no-print">
        <form action="<?= base_url('reports/team') ?>" method="get">
            <div class="filter-grid">
                <div>
                    <label for="team-month">Month</label>
                    <input id="team-month" class="form-control" type="month" name="month" value="<?= esc($filters['month'] ?? date('Y-m')) ?>">
                </div>
                <div>
                    <label for="team-start-date">Start Date</label>
                    <input id="team-start-date" class="form-control" type="date" name="start_date" value="<?= esc($filters['start_date'] ?? date('Y-m-01')) ?>">
                </div>
                <div>
                    <label for="team-end-date">End Date</label>
                    <input id="team-end-date" class="form-control" type="date" name="end_date" value="<?= esc($filters['end_date'] ?? date('Y-m-t')) ?>">
                </div>
                <div class="field-wide">
                    <label for="team-employee">Employee Name</label>
                    <input id="team-employee" class="form-control" type="search" name="employee_name" placeholder="Search employee" value="<?= esc($filters['employee_name'] ?? '') ?>">
                </div>
                <div>
                    <label for="team-department">Department</label>
                    <select id="team-department" class="form-select" name="department_id">
                        <option value="">All departments</option>
                        <?php foreach ($filterOptions['departments'] as $department): ?>
                            <option value="<?= esc($department['value']) ?>" <?= (($filters['department_id'] ?? '') === $department['value']) ? 'selected' : '' ?>><?= esc($department['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="team-group">Team</label>
                    <select id="team-group" class="form-select" name="team">
                        <option value="">All teams</option>
                        <?php foreach ($filterOptions['teams'] as $team): ?>
                            <option value="<?= esc($team['value']) ?>" <?= (($filters['team'] ?? '') === $team['value']) ? 'selected' : '' ?>><?= esc($team['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="team-attendance-status">Attendance Status</label>
                    <select id="team-attendance-status" class="form-select" name="attendance_status">
                        <?php foreach ($filterOptions['attendance_statuses'] as $status): ?>
                            <option value="<?= esc($status['value']) ?>" <?= (($filters['attendance_status'] ?? '') === $status['value']) ? 'selected' : '' ?>><?= esc($status['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="team-performance-category">Performance Category</label>
                    <select id="team-performance-category" class="form-select" name="performance_category">
                        <?php foreach ($filterOptions['performance_categories'] as $category): ?>
                            <option value="<?= esc($category['value']) ?>" <?= (($filters['performance_category'] ?? '') === $category['value']) ? 'selected' : '' ?>><?= esc($category['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field-full d-flex flex-wrap gap-2 justify-content-end">
                    <button type="submit" class="btn btn-success"><i class="fas fa-filter me-2"></i>Apply Filters</button>
                    <a href="<?= base_url('reports/team') ?>" class="btn btn-outline-secondary"><i class="fas fa-undo me-2"></i>Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="section-grid">
        <?php foreach ($summaryCards as $card): ?>
            <?php
                $delta = (float) ($card['delta'] ?? 0);
                $deltaClass = 'delta-flat';
                $deltaIcon = 'fa-arrows-left-right';
                if ($delta > 0.1) {
                    $deltaClass = 'delta-up';
                    $deltaIcon = 'fa-arrow-up';
                } elseif ($delta < -0.1) {
                    $deltaClass = 'delta-down';
                    $deltaIcon = 'fa-arrow-down';
                }
            ?>
            <div class="grid-span-3">
                <div class="dashboard-card kpi-card">
                    <div class="kpi-header">
                        <div>
                            <p class="kpi-label"><?= esc($card['label'] ?? '') ?></p>
                            <div class="kpi-value"><?= esc($card['value'] ?? '0') ?></div>
                        </div>
                        <div class="kpi-icon"><i class="fas <?= esc($card['icon'] ?? 'fa-chart-line') ?>"></i></div>
                    </div>
                    <span class="kpi-delta <?= $deltaClass ?>"><i class="fas <?= $deltaIcon ?>"></i><?= esc($card['delta_label'] ?? 'Stable') ?> <?= $delta !== 0.0 ? '(' . ($delta > 0 ? '+' : '') . number_format($delta, 1) . ')' : '' ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="section-grid">
        <div class="grid-span-12 dashboard-card chart-shell">
            <div class="section-title">
                <div>
                    <h2>Team Performance Trend</h2>
                    <p>Compare daily, weekly, and monthly performance against previous periods.</p>
                </div>
                <span class="badge-soft tone-green"><i class="fas fa-sync-alt"></i>Current vs previous</span>
            </div>
            <div class="row g-3">
                <div class="col-12">
                    <div class="mini-stat mb-3">
                        <h4><?= number_format((float) ($attendanceAnalytics['attendance_rate'] ?? 0), 1) ?>%</h4>
                        <p>Attendance rate for the selected period</p>
                    </div>
                    <div class="chart-wrap"><canvas id="dailyTrendChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-grid">
        <div class="grid-span-7 dashboard-card chart-shell">
            <div class="section-title">
                <div>
                    <h3>Top Performers</h3>
                    <p>Ranked by performance score and attendance.</p>
                </div>
            </div>
            <div class="leaderboard-list">
                <?php if (!empty($leaderboard)): ?>
                    <?php foreach ($leaderboard as $row): ?>
                        <div class="leaderboard-item">
                            <div class="rank-badge">#<?= (int) ($row['rank'] ?? 0) ?></div>
                            <div>
                                <h5><?= esc($row['employee_name'] ?? '') ?></h5>
                                <p class="text-muted"><?= esc(($row['department_name'] ?? 'Unassigned') . ' • ' . ($row['position_name'] ?? 'Unassigned')) ?></p>
                                <div class="leaderboard-stats">
                                    <span class="status-badge <?= esc($row['status_class'] ?? 'status-good') ?>"><?= esc($row['performance_category'] ?? 'Good') ?></span>
                                    <span><i class="fas fa-star text-warning"></i> <?= number_format((float) ($row['performance_score'] ?? 0), 1) ?>%</span>
                                    <span><i class="fas fa-calendar-check"></i> <?= number_format((float) ($row['attendance_rate'] ?? 0), 1) ?>%</span>
                                </div>
                                <div class="mt-2 d-flex flex-wrap gap-2">
                                    <?php foreach (($row['badge_list'] ?? []) as $badge): ?>
                                        <span class="badge-chip tone-green"><i class="fas fa-medal"></i> <?= esc($badge) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="text-end leaderboard-score">
                                <div class="fs-3 fw-bold text-success"><?= number_format((float) ($row['performance_score'] ?? 0), 1) ?></div>
                                <small class="text-muted">Performance score</small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-trophy"></i>
                        <p>No leaderboard data is available for the current filters.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid-span-5 dashboard-card chart-shell">
            <div class="section-title">
                <div>
                    <h3>Performance Distribution</h3>
                    <p>How the team is distributed across performance bands.</p>
                </div>
            </div>
            <div class="chart-wrap" style="min-height: 280px;">
                <canvas id="performanceDistributionChart"></canvas>
            </div>
            <div class="mt-3 d-flex flex-wrap gap-2">
                <span class="badge-chip tone-green"><i class="fas fa-circle"></i> Excellent</span>
                <span class="badge-chip tone-yellow"><i class="fas fa-circle"></i> Good / Average</span>
                <span class="badge-chip tone-red"><i class="fas fa-circle"></i> Needs Attention</span>
            </div>
        </div>
    </div>

    <div class="section-grid">
        <div class="grid-span-6 dashboard-card chart-shell">
            <div class="section-title">
                <div>
                    <h3>Attendance Analytics</h3>
                    <p>Attendance rate, absences, late arrivals, overtime, and correlation to performance.</p>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-6 col-md-3"><div class="mini-stat"><h4><?= number_format((float) ($attendanceAnalytics['attendance_rate'] ?? 0), 1) ?>%</h4><p>Attendance rate</p></div></div>
                <div class="col-6 col-md-3"><div class="mini-stat"><h4><?= number_format((float) ($attendanceAnalytics['absences'] ?? 0), 0) ?></h4><p>Absences</p></div></div>
                <div class="col-6 col-md-3"><div class="mini-stat"><h4><?= number_format((float) ($attendanceAnalytics['late_arrivals'] ?? 0), 0) ?></h4><p>Late arrivals</p></div></div>
                <div class="col-6 col-md-3"><div class="mini-stat"><h4><?= number_format((float) ($attendanceAnalytics['overtime_hours'] ?? 0), 1) ?></h4><p>Overtime hours</p></div></div>
            </div>
            <div class="chart-wrap mb-4"><canvas id="attendanceCorrelationChart"></canvas></div>
            <div class="progress-soft mb-2"><div class="progress-bar bg-success" style="width: <?= (float) ($attendanceAnalytics['attendance_rate'] ?? 0) ?>%"></div></div>
            <small class="text-muted">Attendance trend: <?= esc(ucfirst((string) ($attendanceAnalytics['attendance_trend'] ?? 'stable'))) ?> | Change: <?= ((float) ($attendanceAnalytics['attendance_change'] ?? 0)) > 0 ? '+' : '' ?><?= number_format((float) ($attendanceAnalytics['attendance_change'] ?? 0), 1) ?>%</small>
        </div>

        
    </div>

    <div class="section-grid">
        <div class="grid-span-6 dashboard-card chart-shell">
            <div class="section-title">
                <div>
                    <h3>Department Insights</h3>
                    <p>Highlighting the highest and lowest performing departments.</p>
                </div>
            </div>
            <?php if (!empty($departmentInsights['rows'])): ?>
                <div class="department-row">
                    <div>
                        <div class="fw-bold text-success">Highest performing department</div>
                        <div class="text-muted"><?= esc((string) ($departmentInsights['highest']['name'] ?? 'N/A')) ?></div>
                    </div>
                    <span class="rating-chip rating-green"><?= number_format((float) ($departmentInsights['highest']['average_score'] ?? 0), 1) ?>%</span>
                </div>
                <div class="department-row">
                    <div>
                        <div class="fw-bold text-danger">Lowest performing department</div>
                        <div class="text-muted"><?= esc((string) ($departmentInsights['lowest']['name'] ?? 'N/A')) ?></div>
                    </div>
                    <span class="rating-chip rating-red"><?= number_format((float) ($departmentInsights['lowest']['average_score'] ?? 0), 1) ?>%</span>
                </div>
                <?php foreach ($departmentInsights['rows'] as $department): ?>
                    <div class="department-row">
                        <div>
                            <div class="fw-bold"><?= esc($department['name']) ?></div>
                            <div class="text-muted"><?= (int) $department['members'] ?> employees • Attendance <?= number_format((float) ($department['average_attendance'] ?? 0), 1) ?>%</div>
                        </div>
                        <?php $chipClass = $department['status'] === 'green' ? 'rating-green' : ($department['status'] === 'yellow' ? 'rating-yellow' : 'rating-red'); ?>
                        <span class="rating-chip <?= $chipClass ?>"><?= number_format((float) $department['average_score'], 1) ?>%</span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-building"></i>
                    <p>No department insights are available for the current selection.</p>
                </div>
            <?php endif; ?>
        </div>

        
    </div>

    <div class="section-grid">
        <div class="grid-span-3 dashboard-card chart-shell">
            <div class="section-title"><div><h3>Promotion Ready</h3><p>Employees eligible for promotion.</p></div></div>
            <div class="recommendation-list">
                <?php if (!empty($recommendations['promotion'])): ?>
                    <?php foreach ($recommendations['promotion'] as $item): ?>
                        <div class="recommendation-item">
                            <h5><?= esc($item['name']) ?></h5>
                            <p class="text-muted"><?= esc($item['reason']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state py-3"><p>No promotion-ready employees at the moment.</p></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="grid-span-3 dashboard-card chart-shell">
            <div class="section-title"><div><h3>Needs Coaching</h3><p>Employees requiring coaching.</p></div></div>
            <div class="recommendation-list">
                <?php if (!empty($recommendations['coaching'])): ?>
                    <?php foreach ($recommendations['coaching'] as $item): ?>
                        <div class="recommendation-item">
                            <h5><?= esc($item['name']) ?></h5>
                            <p class="text-muted"><?= esc($item['reason']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state py-3"><p>No urgent coaching flags.</p></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="grid-span-3 dashboard-card chart-shell">
            <div class="section-title"><div><h3>Attendance Issues</h3><p>Attendance problems to review.</p></div></div>
            <div class="recommendation-list">
                <?php if (!empty($recommendations['attendance'])): ?>
                    <?php foreach ($recommendations['attendance'] as $item): ?>
                        <div class="recommendation-item">
                            <h5><?= esc($item['name']) ?></h5>
                            <p class="text-muted"><?= esc($item['reason']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state py-3"><p>No attendance flags detected.</p></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="grid-span-3 dashboard-card chart-shell">
            <div class="section-title"><div><h3>Training Recommendations</h3><p>Employees to include in training.</p></div></div>
            <div class="recommendation-list">
                <?php if (!empty($recommendations['training'])): ?>
                    <?php foreach ($recommendations['training'] as $item): ?>
                        <div class="recommendation-item">
                            <h5><?= esc($item['name']) ?></h5>
                            <p class="text-muted"><?= esc($item['reason']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state py-3"><p>No training flags detected.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="dashboard-card table-shell mb-4">
        <div class="section-title no-print">
            <div>
                <h3>Employee Performance Table</h3>
                <p>Search, filter, and export detailed performance records.</p>
            </div>
                <div class="toolbar-actions">
                    <button type="button" class="icon-btn" onclick="window.peopleAxisTeamReport.downloadExcel()"><i class="fas fa-file-excel"></i>Export Excel</button>
                </div>
        </div>

        <div class="table-toolbar no-print">
            <div class="flex-grow-1">
                <label for="quick-search" class="form-label small text-uppercase fw-bold text-muted">Quick Search</label>
                <input id="quick-search" type="search" class="form-control" placeholder="Search by employee, position, or department">
            </div>
            <div style="min-width: 180px;">
                <label for="quick-category" class="form-label small text-uppercase fw-bold text-muted">Category</label>
                <select id="quick-category" class="form-select">
                    <option value="">All categories</option>
                    <option value="Excellent">Excellent</option>
                    <option value="Good">Good</option>
                    <option value="Average">Average</option>
                    <option value="Needs Improvement">Needs Improvement</option>
                </select>
            </div>
            <div class="toolbar-actions">
                <button type="button" class="btn btn-outline-success" onclick="window.peopleAxisTeamReport.printSummary()"><i class="fas fa-print me-2"></i>Printable Summary</button>
            </div>
        </div>

        <div class="table-responsive">
            <?php if (!empty($performanceRows)): ?>
                <table class="analytics-table" id="performanceTable">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Position</th>
                            <th>Attendance Rate</th>
                            <th>Performance Score</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($performanceRows as $index => $row): ?>
                            <?php
                                $statusClass = match ($row['performance_category'] ?? 'Needs Improvement') {
                                    'Excellent' => 'status-excellent',
                                    'Good' => 'status-good',
                                    'Average' => 'status-average',
                                    default => 'status-risk',
                                };
                            ?>
                            <tr data-row-index="<?= (int) $index ?>" data-search="<?= esc(strtolower((string) ($row['employee_name'] ?? '') . ' ' . ($row['department_name'] ?? '') . ' ' . ($row['position_name'] ?? ''))) ?>" data-category="<?= esc((string) ($row['performance_category'] ?? '')) ?>">
                                <td>
                                    <div class="fw-bold"><?= esc((string) ($row['employee_name'] ?? '')) ?></div>
                                    <div class="text-muted small"><?= esc((string) ($row['employee_code'] ?? '')) ?> • <?= esc((string) ($row['department_name'] ?? 'Unassigned')) ?></div>
                                </td>
                                <td><?= esc((string) ($row['position_name'] ?? 'Unassigned')) ?></td>
                                <td>
                                    <div class="fw-bold"><?= number_format((float) ($row['attendance_rate'] ?? 0), 1) ?>%</div>
                                    <div class="progress-soft mt-2"><div class="progress-bar bg-success" style="width: <?= (float) ($row['attendance_rate'] ?? 0) ?>%"></div></div>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= number_format((float) ($row['performance_score'] ?? 0), 1) ?>%</div>
                                    <small class="text-muted">Delta <?= ((float) ($row['performance_delta'] ?? 0)) > 0 ? '+' : '' ?><?= number_format((float) ($row['performance_delta'] ?? 0), 1) ?></small>
                                </td>
                                <td>
                                    <span class="badge-inline <?= $statusClass ?>"><?= esc((string) ($row['performance_category'] ?? 'Needs Improvement')) ?></span>
                                    <div class="mt-2 d-flex flex-wrap gap-1">
                                        <?php foreach (($row['badge_list'] ?? []) as $badge): ?>
                                            <span class="badge-chip tone-yellow"><i class="fas fa-award"></i> <?= esc($badge) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <button type="button" class="icon-btn" onclick="window.peopleAxisTeamReport.openEmployeeModal(<?= (int) $index ?>, 'details')"><i class="fas fa-eye"></i>View</button>
                                        <button type="button" class="icon-btn" onclick="window.peopleAxisTeamReport.openEmployeeModal(<?= (int) $index ?>, 'history')"><i class="fas fa-clock-rotate-left"></i>History</button>
                                        <button type="button" class="icon-btn" onclick="window.peopleAxisTeamReport.downloadEmployeeReport(<?= (int) $index ?>)"><i class="fas fa-file-export"></i>Export</button>
                                        <button type="button" class="icon-btn" onclick="window.peopleAxisTeamReport.openEmployeeModal(<?= (int) $index ?>, 'evaluation')"><i class="fas fa-pen-to-square"></i>Evaluate</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-table"></i>
                    <p>No employee performance records matched the current filters.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="employeePerformanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="employeePerformanceModalTitle">Employee Details</h5>
                    <small class="text-muted" id="employeePerformanceModalSubtitle">Performance snapshot</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="employeePerformanceModalBody"></div>
            <div class="modal-footer no-print">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="employeePerformanceModalExport">Export employee report</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    window.peopleAxisTeamReport = {
        data: <?= json_encode($dashboardPayload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
        employees: <?= json_encode($performanceRows, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
        init() {
            this.bindSearch();
            this.renderCharts();
            this.bindModal();
        },
        colorPalette(index) {
            const palette = ['#256d4a', '#2b6cb0', '#d4a017', '#d1495b'];
            return palette[index % palette.length];
        },
        chartDataset(labels, current, previous, currentLabel, previousLabel) {
            return {
                labels,
                datasets: [
                    {
                        label: currentLabel,
                        data: current,
                        borderColor: '#256d4a',
                        backgroundColor: 'rgba(37, 109, 74, 0.12)',
                        tension: 0.38,
                        pointRadius: 2,
                    },
                    {
                        label: previousLabel,
                        data: previous,
                        borderColor: '#d4a017',
                        backgroundColor: 'rgba(212, 160, 23, 0.10)',
                        borderDash: [7, 5],
                        tension: 0.38,
                        pointRadius: 2,
                    }
                ]
            };
        },
        renderCharts() {
            if (typeof Chart === 'undefined') {
                return;
            }

            const charts = this.data.trendCharts || {};
            const dailyEl = document.getElementById('dailyTrendChart');
            const distributionEl = document.getElementById('performanceDistributionChart');
            const attendanceEl = document.getElementById('attendanceCorrelationChart');

            if (dailyEl) {
                new Chart(dailyEl, {
                    type: 'line',
                    data: this.chartDataset((charts.daily || {}).labels || [], (charts.daily || {}).current || [], (charts.daily || {}).previous || [], 'Current period', 'Previous period'),
                    options: this.lineOptions('%')
                });
            }


            if (distributionEl) {
                new Chart(distributionEl, {
                    type: 'doughnut',
                    data: {
                        labels: (this.data.performanceDistribution || {}).labels || [],
                        datasets: [{
                            data: (this.data.performanceDistribution || {}).values || [],
                            backgroundColor: ['#1f8a55', '#2b6cb0', '#d4a017', '#d1495b'],
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: { legend: { position: 'bottom' } },
                    },
                });
            }

            if (attendanceEl) {
                new Chart(attendanceEl, {
                    type: 'scatter',
                    data: {
                        datasets: [{
                            label: 'Attendance vs Performance',
                            data: (this.data.attendanceAnalytics || {}).correlation || [],
                            backgroundColor: 'rgba(37, 109, 74, 0.65)',
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { title: { display: true, text: 'Attendance Rate (%)' }, min: 0, max: 100 },
                            y: { title: { display: true, text: 'Performance Score (%)' }, min: 0, max: 100 },
                        },
                    },
                });
            }

            // Productivity charts removed per manager view configuration
        },
        lineOptions(unit) {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    y: { beginAtZero: true, max: 100, ticks: { callback: value => value + unit } },
                },
            };
        },
        bindSearch() {
            const searchInput = document.getElementById('quick-search');
            const categorySelect = document.getElementById('quick-category');
            const rows = Array.from(document.querySelectorAll('#performanceTable tbody tr'));

            const applyFilter = () => {
                const term = (searchInput?.value || '').toLowerCase().trim();
                const category = (categorySelect?.value || '').trim();

                rows.forEach(row => {
                    const haystack = (row.dataset.search || '').toLowerCase();
                    const rowCategory = row.dataset.category || '';
                    const matchesTerm = term === '' || haystack.includes(term);
                    const matchesCategory = category === '' || rowCategory === category;
                    row.style.display = matchesTerm && matchesCategory ? '' : 'none';
                });
            };

            searchInput?.addEventListener('input', applyFilter);
            categorySelect?.addEventListener('change', applyFilter);
        },
        bindModal() {
            const exportButton = document.getElementById('employeePerformanceModalExport');
            exportButton?.addEventListener('click', () => {
                const index = Number(exportButton.dataset.employeeIndex || '0');
                this.downloadEmployeeReport(index);
            });
        },
        openEmployeeModal(index, mode) {
            const employee = this.employees[index];
            if (!employee) {
                return;
            }

            const modalEl = document.getElementById('employeePerformanceModal');
            const titleEl = document.getElementById('employeePerformanceModalTitle');
            const subtitleEl = document.getElementById('employeePerformanceModalSubtitle');
            const bodyEl = document.getElementById('employeePerformanceModalBody');
            const exportButton = document.getElementById('employeePerformanceModalExport');

            if (!modalEl || !titleEl || !subtitleEl || !bodyEl || !exportButton) {
                return;
            }

            exportButton.dataset.employeeIndex = String(index);

            titleEl.textContent = mode === 'evaluation' ? 'Generate Evaluation' : mode === 'history' ? 'Performance History' : 'Employee Details';
            subtitleEl.textContent = employee.employee_name + ' • ' + (employee.department_name || 'Unassigned');

            const badges = (employee.badge_list || []).map(item => `<span class="badge-chip tone-green me-1 mb-1"><i class="fas fa-award"></i> ${this.escapeHtml(item)}</span>`).join('');
            const evaluationText = this.generateEvaluationText(employee);
            const content = mode === 'history'
                ? `<div class="row g-3">
                        <div class="col-md-6"><div class="mini-stat"><h4>${this.number(employee.attendance_rate)}%</h4><p>Attendance rate</p></div></div>
                        <div class="col-md-6"><div class="mini-stat"><h4>${this.number(employee.performance_score)}%</h4><p>Performance score</p></div></div>
                    </div>
                    <hr>
                    <p class="mb-2"><strong>Recent trend:</strong> ${employee.performance_delta > 0 ? '+' : ''}${this.number(employee.performance_delta)} points versus the previous period.</p>
                    <p class="mb-0 text-muted">This dashboard uses the current and previous period to derive a trend view.</p>`
                : `<div class="row g-3">
                        <div class="col-md-6"><div class="mini-stat"><h4>${this.number(employee.attendance_rate)}%</h4><p>Attendance</p></div></div>
                        <div class="col-md-6"><div class="mini-stat"><h4>${this.number(employee.performance_score)}%</h4><p>Performance</p></div></div>
                    </div>
                    <hr>
                    <p><strong>Position:</strong> ${this.escapeHtml(employee.position_name || 'Unassigned')}</p>
                    <div class="mt-2">${badges || '<span class="text-muted">No badges assigned</span>'}</div>`;

            if (mode === 'evaluation') {
                bodyEl.innerHTML = `<div class="mini-stat mb-3"><h4>${this.escapeHtml(employee.performance_category || 'Needs Improvement')}</h4><p>Current evaluation summary</p></div><p class="mb-3">${this.escapeHtml(evaluationText)}</p><div class="alert alert-success mb-0"><i class="fas fa-circle-info me-2"></i>${this.escapeHtml(employee.reason || 'Evaluation generated from attendance and performance metrics.')}</div>`;
            } else {
                bodyEl.innerHTML = content;
            }

            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        },
        generateEvaluationText(employee) {
            return `${employee.employee_name} has a ${this.number(employee.performance_score)}% performance score and ${this.number(employee.attendance_rate)}% attendance. ${employee.performance_category} is the current status.`;
        },
        downloadEmployeeReport(index) {
            const employee = this.employees[index];
            if (!employee) {
                return;
            }

            const rows = [
                ['Employee Name', employee.employee_name],
                ['Employee Code', employee.employee_code],
                ['Department', employee.department_name],
                ['Position', employee.position_name],
                ['Attendance Rate', employee.attendance_rate],
                ['Performance Score', employee.performance_score],
                ['Status', employee.performance_category],
            ];

            this.downloadBlob(rows.map(row => row.join('\t')).join('\n'), `${employee.employee_name.replace(/\s+/g, '_')}_Performance_Report.xls`, 'application/vnd.ms-excel');
        },
        downloadExcel() {
            const rows = [['Rank', 'Employee Name', 'Department', 'Position', 'Attendance Rate', 'Performance Score', 'Status']];
            this.employees.forEach(employee => {
                rows.push([
                    employee.rank || '',
                    employee.employee_name,
                    employee.department_name,
                    employee.position_name,
                    employee.attendance_rate,
                    employee.performance_score,
                    employee.performance_category,
                ]);
            });

            this.downloadBlob(rows.map(row => row.join('\t')).join('\n'), 'Team_Performance_Analytics.xls', 'application/vnd.ms-excel');
        },
        downloadAnalytics() {
            const payload = JSON.stringify(this.data, null, 2);
            this.downloadBlob(payload, 'Team_Performance_Analytics.json', 'application/json');
        },
        downloadPdf() {
            window.print();
        },
        printSummary() {
            window.print();
        },
        downloadBlob(content, fileName, mimeType) {
            const blob = new Blob([content], { type: mimeType + ';charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = fileName;
            document.body.appendChild(link);
            link.click();
            link.remove();
            URL.revokeObjectURL(url);
        },
        number(value) {
            const parsed = Number(value || 0);
            return Number.isFinite(parsed) ? parsed.toFixed(1) : '0.0';
        },
        escapeHtml(value) {
            return String(value || '').replace(/[&<>'"]/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[char]));
        },
    };

    document.addEventListener('DOMContentLoaded', () => {
        window.peopleAxisTeamReport.init();
    });
</script>

<?= $this->endSection() ?>