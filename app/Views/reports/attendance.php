<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .attendance-shell {
        max-width: 1240px;
        margin: 0 auto;
        padding: 8px 0 18px;
    }

    .breadcrumbs {
        margin-bottom: 14px;
        font-size: 0.9rem;
    }

    .breadcrumbs a {
        color: #6ea988;
        text-decoration: none;
    }

    .breadcrumbs a:hover {
        text-decoration: underline;
    }

    .breadcrumbs span {
        color: #6f8192;
        margin: 0 6px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 14px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 18px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
    }

    .page-header h1 {
        color: #2f5f45;
        font-weight: 700;
        margin: 0;
        font-size: 2rem;
        line-height: 1;
    }

    .page-header p {
        color: #6f8192;
        margin: 6px 0 0;
        font-size: 0.92rem;
    }

    .tips-alert {
        background: #eaf3fb;
        color: #2a587f;
        border: 1px solid #d4e3f2;
        border-left: 4px solid #2a587f;
        padding: 12px 14px;
        border-radius: 8px;
        margin-bottom: 14px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .tips-alert i {
        margin-top: 2px;
    }

    .tips-alert p {
        margin: 4px 0 0;
    }

    .report-container {
        display: grid;
        grid-template-columns: 340px minmax(0, 1fr);
        gap: 14px;
        margin-bottom: 14px;
    }

    .report-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        padding: 14px;
    }

    .report-panel .panel-heading {
        color: #1f3550;
        font-weight: 700;
        margin: 0 0 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e7edf4;
        font-size: 1.08rem;
        line-height: 1.25;
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #2f4358;
        margin-bottom: 6px;
        font-size: 0.9rem;
    }

    .form-input {
        width: 100%;
        padding: 9px 11px;
        border: 1px solid #d9e2ec;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        box-sizing: border-box;
        background: #ffffff;
    }

    .form-input:focus {
        outline: none;
        border-color: #6ea988;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
    }

    .btn-generate {
        width: 100%;
        padding: 9px 12px;
        background: #6ea988;
        color: #ffffff;
        border: 1px solid #6ea988;
        border-radius: 8px;
        font-size: 0.86rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease, border-color 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        line-height: 1;
    }

    .btn-generate:hover {
        background: #21437c;
        border-color: #21437c;
    }

    .btn-secondary {
        margin-top: 8px;
        background: #f1f5fb;
        color: #6ea988;
        border-color: #c9d8ef;
    }

    .btn-secondary:hover {
        background: #e7effa;
        border-color: #bdd2ee;
    }

    .preview-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .preview-header {
        background: #6ea988;
        color: #ffffff;
        padding: 12px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .preview-header h3 {
        margin: 0;
        font-weight: 700;
        font-size: 1.05rem;
    }

    .preview-period {
        font-size: 0.84rem;
        opacity: 0.92;
        font-weight: 600;
    }

    .preview-content {
        padding: 14px;
    }

    .report-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(135px, 1fr));
        gap: 8px;
        margin-bottom: 12px;
    }

    .summary-item {
        background: #f8fbff;
        border: 1px solid #e2ebf4;
        padding: 10px;
        border-radius: 8px;
    }

    .summary-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2f5f45;
        margin: 4px 0;
        line-height: 1.1;
    }

    .summary-label {
        font-size: 0.72rem;
        color: #6f8192;
        text-transform: uppercase;
        letter-spacing: 0.35px;
        font-weight: 700;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .report-table thead th {
        background: #f7f9fc;
        color: #445b72;
        font-weight: 700;
        padding: 10px 12px;
        text-align: left;
        border-bottom: 1px solid #e1e9f2;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.35px;
        white-space: nowrap;
    }

    .report-table tbody td {
        padding: 10px 12px;
        border-bottom: 1px solid #edf2f7;
        font-size: 0.86rem;
        color: #42586e;
    }

    .report-table tbody tr:hover {
        background: #fbfdff;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .badge-present {
        background: #e9f7ec;
        color: #1d7a3f;
        border-color: #cfead8;
    }

    .badge-absent {
        background: #fdecec;
        color: #b43a3a;
        border-color: #f4d3d3;
    }

    .badge-late {
        background: #fff4e4;
        color: #9f6310;
        border-color: #f1debb;
    }

    .badge-leave {
        background: #e8f4fd;
        color: #1e6fa5;
        border-color: #c7dff1;
    }

    .export-buttons {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        flex-wrap: wrap;
    }

    .btn-export {
        padding: 7px 11px;
        border: 1px solid #c9d8ef;
        background: #f1f5fb;
        color: #6ea988;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 700;
        transition: background 0.2s ease, border-color 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
    }

    .btn-export:hover {
        background: #e7effa;
        border-color: #bdd2ee;
    }

    .empty-state {
        text-align: center;
        padding: 34px 14px;
        color: #7f90a0;
    }

    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 10px;
        opacity: 0.45;
        color: #8fa3b8;
    }

    .empty-note {
        font-size: 0.88rem;
        margin-top: 6px;
    }

    @media (max-width: 1100px) {
        .report-container {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .page-header {
            padding: 14px;
        }

        .page-header h1 {
            font-size: 1.6rem;
        }

        .preview-content,
        .report-panel {
            padding: 12px;
        }
    }
</style>

<div class="attendance-shell">

<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
    <span>/</span>
    <a href="<?= base_url('reports') ?>"><i class="fas fa-chart-bar"></i> Reports</a>
    <span>/</span>
    <span>Attendance Report</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="fas fa-chart-line"></i> Generate Attendance Report</h1>
        <p>Create detailed attendance reports for your organization</p>
    </div>
</div>

<!-- Info Alert -->
<div class="tips-alert">
    <i class="fas fa-lightbulb"></i>
    <div>
        <strong>Report Generation Tips:</strong>
        <p>Select date range and parameters to generate customized attendance reports. You can export data in CSV or PDF format for further analysis.</p>
    </div>
</div>

<!-- Report Generator -->
<div class="report-container">
    <!-- Configuration Panel -->
    <div class="report-panel">
        <h3 class="panel-heading"><i class="fas fa-sliders-h"></i> Report Configuration</h3>

        <form id="reportForm" method="POST" action="<?= base_url('reports/generate/attendance') ?>">
            <?= csrf_field() ?>

            <!-- Date Range -->
            <div class="form-group">
                <label class="form-label"><i class="fas fa-calendar"></i> Start Date</label>
                <input type="date" class="form-input" name="start_date" id="startDate" required 
                    value="<?= date('Y-m-01') ?>">
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-calendar"></i> End Date</label>
                <input type="date" class="form-input" name="end_date" id="endDate" required 
                    value="<?= date('Y-m-d') ?>">
            </div>

            <!-- Department Filter -->
            <div class="form-group">
                <label class="form-label"><i class="fas fa-sitemap"></i> Filter by Department</label>
                <select class="form-input" name="department_id" id="department">
                    <option value="">All Departments</option>
                    <option value="1">HR</option>
                    <option value="2">Finance</option>
                    <option value="3">Operations</option>
                    <option value="4">Development</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="form-group">
                <label class="form-label"><i class="fas fa-filter"></i> Filter by Status</label>
                <select class="form-input" name="status_filter" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                    <option value="late">Late</option>
                    <option value="leave">On Leave</option>
                </select>
            </div>

            <!-- Report Type -->
            <div class="form-group">
                <label class="form-label"><i class="fas fa-file-alt"></i> Report Type</label>
                <select class="form-input" name="report_type" id="reportType">
                    <option value="summary">Summary Report</option>
                    <option value="detailed">Detailed Report</option>
                    <option value="individual">Individual Report</option>
                </select>
            </div>

            <!-- Buttons -->
            <button type="button" class="btn-generate" onclick="generateReport()">
                <i class="fas fa-play-circle"></i> Generate Report
            </button>
            <button type="reset" class="btn-generate btn-secondary">
                <i class="fas fa-redo"></i> Reset Form
            </button>
        </form>
    </div>

    <!-- Preview Panel -->
    <div class="preview-panel">
        <div class="preview-header">
            <h3><i class="fas fa-eye"></i> Report Preview</h3>
            <div id="reportTitle" class="preview-period"></div>
        </div>
        <div class="preview-content" id="reportPreview">
            <div class="empty-state">
                <i class="fas fa-chart-bar"></i>
                <p><strong>Report preview will appear here</strong></p>
                <p class="empty-note">Fill in the configuration form and click "Generate Report" to see the preview.</p>
            </div>
        </div>
    </div>
</div>

</div>

<script>
    function generateReport() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const department = document.getElementById('department').value;
        const status = document.getElementById('statusFilter').value;
        const reportType = document.getElementById('reportType').value;

        // Validate dates
        if (!startDate || !endDate) {
            alert('Please select both start and end dates');
            return;
        }

        if (new Date(startDate) > new Date(endDate)) {
            alert('Start date cannot be after end date');
            return;
        }

        // Show loading state
        const preview = document.getElementById('reportPreview');
        preview.innerHTML = '<div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Generating report...</p></div>';

        // Simulate report generation
        setTimeout(() => {
            generateSampleReport(startDate, endDate, reportType);
        }, 800);
    }

    function generateSampleReport(startDate, endDate, reportType) {
        const preview = document.getElementById('reportPreview');
        const startDateObj = new Date(startDate);
        const endDateObj = new Date(endDate);
        const daysInRange = Math.ceil((endDateObj - startDateObj) / (1000 * 60 * 60 * 24)) + 1;

        let html = `
            <div class="report-summary">
                <div class="summary-item">
                    <div class="summary-label">Period</div>
                    <div class="summary-value">${daysInRange} days</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Employees</div>
                    <div class="summary-value">45</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Avg Attendance</div>
                    <div class="summary-value">94.2%</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Absences</div>
                    <div class="summary-value">78</div>
                </div>
            </div>
        `;

        if (reportType === 'summary') {
            html += `
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Metric</th>
                            <th>Count</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="status-badge badge-present">Present</span></td>
                            <td>1847</td>
                            <td>94.2%</td>
                        </tr>
                        <tr>
                            <td><span class="status-badge badge-absent">Absent</span></td>
                            <td>78</td>
                            <td>3.9%</td>
                        </tr>
                        <tr>
                            <td><span class="status-badge badge-late">Late</span></td>
                            <td>45</td>
                            <td>2.3%</td>
                        </tr>
                        <tr>
                            <td><span class="status-badge badge-leave">On Leave</span></td>
                            <td>32</td>
                            <td>1.6%</td>
                        </tr>
                    </tbody>
                </table>
            `;
        } else if (reportType === 'detailed') {
            html += `
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Late</th>
                            <th>Leave</th>
                            <th>Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>John Doe</strong></td>
                            <td>28</td>
                            <td>1</td>
                            <td>2</td>
                            <td>1</td>
                            <td><span style="color: #27ae60; font-weight: 600;">96.5%</span></td>
                        </tr>
                        <tr>
                            <td><strong>Jane Smith</strong></td>
                            <td>29</td>
                            <td>0</td>
                            <td>1</td>
                            <td>2</td>
                            <td><span style="color: #27ae60; font-weight: 600;">96.7%</span></td>
                        </tr>
                        <tr>
                            <td><strong>Mike Johnson</strong></td>
                            <td>26</td>
                            <td>3</td>
                            <td>2</td>
                            <td>1</td>
                            <td><span style="color: #f39c12; font-weight: 600;">86.7%</span></td>
                        </tr>
                        <tr>
                            <td><strong>Sarah Williams</strong></td>
                            <td>30</td>
                            <td>0</td>
                            <td>0</td>
                            <td>2</td>
                            <td><span style="color: #27ae60; font-weight: 600;">100%</span></td>
                        </tr>
                    </tbody>
                </table>
            `;
        } else {
            html += `
                <div class="form-group">
                    <label class="form-label">Select Employee</label>
                    <select class="form-input">
                        <option>John Doe - 96.5% Attendance</option>
                        <option>Jane Smith - 96.7% Attendance</option>
                        <option>Mike Johnson - 86.7% Attendance</option>
                    </select>
                </div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>${startDate}</td>
                            <td>08:15</td>
                            <td>17:45</td>
                            <td><span class="status-badge badge-present">Present</span></td>
                            <td>9h 30m</td>
                        </tr>
                        <tr>
                            <td>${new Date(new Date(startDate).getTime() + 86400000).toISOString().split('T')[0]}</td>
                            <td>08:00</td>
                            <td>17:30</td>
                            <td><span class="status-badge badge-present">Present</span></td>
                            <td>9h 30m</td>
                        </tr>
                    </tbody>
                </table>
            `;
        }

        html += `
            <div class="export-buttons">
                <button class="btn-export" onclick="exportReport('pdf')">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <button class="btn-export" onclick="exportReport('csv')">
                    <i class="fas fa-file-csv"></i> Export CSV
                </button>
                <button class="btn-export" onclick="printReport()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        `;

        preview.innerHTML = html;

        // Update title
        const startStr = new Date(startDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        const endStr = new Date(endDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        document.getElementById('reportTitle').textContent = `${startStr} - ${endStr}`;
    }

    function exportReport(format) {
        alert(`Report will be exported as ${format.toUpperCase()}`);
        // Implement actual export functionality
    }

    function printReport() {
        alert('Print dialog will open');
        window.print();
    }
</script>

<?= $this->endSection() ?>
