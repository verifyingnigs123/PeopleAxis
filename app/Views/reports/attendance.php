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

    .page-header p {
        color: #7f8c8d;
        margin: 0;
    }

    .report-container {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    @media (max-width: 1024px) {
        .report-container {
            grid-template-columns: 1fr;
        }
    }

    .report-panel {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 25px;
    }

    .report-panel h3 {
        color: #1e3c72;
        font-weight: 700;
        margin: 0 0 20px 0;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f2f5;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .form-input {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #e1e8ed;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .form-input:focus {
        outline: none;
        border-color: #2a5298;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
    }

    .form-input select {
        cursor: pointer;
    }

    .btn-generate {
        width: 100%;
        padding: 12px 20px;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-generate:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.4);
    }

    .btn-secondary {
        background: #95a5a6;
        margin-top: 10px;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
    }

    .preview-panel {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 0;
        overflow: hidden;
    }

    .preview-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .preview-header h3 {
        margin: 0;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .preview-content {
        padding: 25px;
    }

    .report-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .summary-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        border-left: 4px solid #667eea;
    }

    .summary-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e3c72;
        margin: 5px 0;
    }

    .summary-label {
        font-size: 0.85rem;
        color: #7f8c8d;
        text-transform: uppercase;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .report-table thead th {
        background: #f8f9fa;
        color: #1e3c72;
        font-weight: 600;
        padding: 12px 15px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .report-table tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid #f1f3f5;
        font-size: 0.9rem;
        color: #495057;
    }

    .report-table tbody tr:hover {
        background: #f8f9ff;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-present {
        background: #d4edda;
        color: #155724;
    }

    .badge-absent {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-late {
        background: #fff3cd;
        color: #856404;
    }

    .badge-leave {
        background: #d1ecf1;
        color: #0c5460;
    }

    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
        border-left: 4px solid #0c5460;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .alert-info i {
        margin-top: 2px;
    }

    .export-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .btn-export {
        padding: 8px 16px;
        border: 2px solid #667eea;
        background: white;
        color: #667eea;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
    }

    .btn-export:hover {
        background: #667eea;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <a href="<?= base_url('reports') ?>"><i class="fas fa-chart-bar"></i> Reports</a> /
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
<div class="alert-info">
    <i class="fas fa-lightbulb"></i>
    <div>
        <strong>Report Generation Tips:</strong>
        <p style="margin: 5px 0 0 0;">Select date range and parameters to generate customized attendance reports. You can export data in CSV or PDF format for further analysis.</p>
    </div>
</div>

<!-- Report Generator -->
<div class="report-container">
    <!-- Configuration Panel -->
    <div class="report-panel">
        <h3><i class="fas fa-sliders-h"></i> Report Configuration</h3>

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
            <div id="reportTitle" style="font-size: 0.9rem; opacity: 0.9;"></div>
        </div>
        <div class="preview-content" id="reportPreview">
            <div class="empty-state">
                <i class="fas fa-chart-bar"></i>
                <p><strong>Report preview will appear here</strong></p>
                <p style="font-size: 0.9rem;">Fill in the configuration form and click "Generate Report" to see the preview.</p>
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
