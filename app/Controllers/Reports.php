<?php

namespace App\Controllers;

class Reports extends BaseController
{
    /**
     * Generate attendance report (HR Admin only)
     */
    public function attendance()
    {
        try {
            // Enable error reporting in development
            if (ENVIRONMENT === 'development') {
                ini_set('display_errors', 1);
                error_reporting(E_ALL);
            }
            
            // Check if user is HR Admin or Super Admin
            $roleName = session()->get('role_name');
            $role = session()->get('role');
            
            if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
                return redirect()->to('/dashboard')->with('error', 'Access denied. HR Admin only.');
            }

            return view('reports/attendance');
        } catch (\Throwable $e) {
            log_message('error', 'Attendance method error: ' . $e->getMessage());
            return redirect()->to('/dashboard')->with('error', 'Unable to load attendance report.');
        }
    }

    /**
     * Generate attendance report (for direct URL access)
     * This method handles the route: /reports/generate/attendance
     */
    public function generateAttendance()
    {
        try {
            // Enable error reporting in development
            if (ENVIRONMENT === 'development') {
                ini_set('display_errors', 1);
                error_reporting(E_ALL);
            }
            
            // Check if user is HR Admin or Super Admin
            $roleName = session()->get('role_name');
            $role = session()->get('role');
            
            if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
                return redirect()->to('/dashboard')->with('error', 'Access denied. HR Admin only.');
            }

            // Generate attendance report data
            $reportData = $this->generateReportData('attendance');

            if (!$reportData) {
                log_message('error', 'Failed to generate attendance report data');
                return redirect()->to('/reports')->with('error', 'Unable to generate attendance report.');
            }

            // Prepare data for view
            $data = [
                'reportData' => $reportData
            ];

            return view('reports/attendance_display', $data);
            
        } catch (\Throwable $e) {
            log_message('error', 'GenerateAttendance method error: ' . $e->getMessage());
            return redirect()->to('/reports')->with('error', 'Unable to generate attendance report.');
        }
    }

    /**
     * Export attendance report as Excel-compatible spreadsheet.
     */
    public function exportAttendanceExcel()
    {
        try {
            $roleName = session()->get('role_name');
            $role = session()->get('role');

            if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
                return redirect()->to('/dashboard')->with('error', 'Access denied. HR Admin only.');
            }

            $reportData = $this->generateReportData('attendance');
            if (!$reportData) {
                return redirect()->to('/reports')->with('error', 'Unable to export attendance report.');
            }

            $rows = $reportData['data'] ?? [];
            $period = $reportData['period'] ?? date('Y-m');

            $html = '<!DOCTYPE html><html><head><meta charset="UTF-8">';
            $html .= '<style>
                body { font-family: Arial, sans-serif; color: #1f2937; }
                .report-title { font-size: 20px; font-weight: 700; margin-bottom: 6px; color: #2f5f45; }
                .report-meta { margin-bottom: 14px; color: #6b7280; font-size: 12px; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #dbe3ea; padding: 8px 10px; font-size: 12px; vertical-align: top; }
                th { background: #2f5f45; color: #fff; text-align: left; }
                tbody tr:nth-child(even) { background: #f8fbf9; }
                .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 11px; }
                .present { background: #e9f7ec; color: #1d7a3f; }
                .late { background: #fff4e4; color: #9f6310; }
                .absent { background: #fdecec; color: #b43a3a; }
                .leave { background: #e8f4fd; color: #1e6fa5; }
                .text { white-space: nowrap; }
            </style></head><body>';

            $html .= '<div class="report-title">Attendance Report</div>';
            $html .= '<div class="report-meta">Period: ' . htmlspecialchars(date('F Y', strtotime($period . '-01')), ENT_QUOTES, 'UTF-8') . ' | Generated on ' . date('M d, Y h:i A') . '</div>';
            $html .= '<table><thead><tr>';

            $headers = ['#', 'Employee', 'Employee ID', 'RFID Number', 'Department', 'Date', 'Time In', 'Break Out', 'Break In', 'Time Out', 'Status'];
            foreach ($headers as $header) {
                $html .= '<th>' . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . '</th>';
            }
            $html .= '</tr></thead><tbody>';

            foreach ($rows as $index => $record) {
                $employeeName = trim((string) (($record['first_name'] ?? '') . ' ' . ($record['last_name'] ?? '')));
                $status = strtolower((string) ($record['status'] ?? ''));
                $dateValue = !empty($record['date']) ? date('M d, Y', strtotime((string) $record['date'])) : '-';
                $timeIn = !empty($record['time_in']) ? date('h:i A', strtotime((string) $record['time_in'])) : '-';
                $breakOut = !empty($record['break_out']) ? date('h:i A', strtotime((string) $record['break_out'])) : '-';
                $breakIn = !empty($record['break_in']) ? date('h:i A', strtotime((string) $record['break_in'])) : '-';
                $timeOut = !empty($record['time_out']) ? date('h:i A', strtotime((string) $record['time_out'])) : '-';

                $html .= '<tr>';
                $html .= '<td class="text">' . ($index + 1) . '</td>';
                $html .= '<td class="text"><strong>' . htmlspecialchars($employeeName, ENT_QUOTES, 'UTF-8') . '</strong></td>';
                $html .= '<td class="text">' . htmlspecialchars((string) ($record['emp_code'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') . '</td>';
                $html .= '<td class="text">' . htmlspecialchars((string) ($record['rfid_number'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') . '</td>';
                $html .= '<td class="text">' . htmlspecialchars((string) ($record['department_name'] ?? 'Unassigned'), ENT_QUOTES, 'UTF-8') . '</td>';
                $html .= '<td class="text">' . $dateValue . '</td>';
                $html .= '<td class="text">' . $timeIn . '</td>';
                $html .= '<td class="text">' . $breakOut . '</td>';
                $html .= '<td class="text">' . $breakIn . '</td>';
                $html .= '<td class="text">' . $timeOut . '</td>';
                $html .= '<td><span class="badge ' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars(ucfirst($status ?: 'Pending'), ENT_QUOTES, 'UTF-8') . '</span></td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table></body></html>';

            $filename = 'Attendance_Report_' . date('Y-m-d_H-i-s') . '.xls';

            return $this->response
                ->setHeader('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($html);
        } catch (\Throwable $e) {
            log_message('error', 'Export Attendance Excel error: ' . $e->getMessage());
            return redirect()->to('/reports')->with('error', 'Unable to export attendance report.');
        }
    }

    /**
     * Generate leave report (for direct URL access)
     * This method handles the route: /reports/generate/leave
     */
    public function generateLeave()
    {
        try {
            // Enable error reporting in development
            if (ENVIRONMENT === 'development') {
                ini_set('display_errors', 1);
                error_reporting(E_ALL);
            }
            
            // Check if user is HR Admin or Super Admin
            $roleName = session()->get('role_name');
            $role = session()->get('role');
            
            if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
                return redirect()->to('/dashboard')->with('error', 'Access denied. HR Admin only.');
            }

            // Generate leave report data
            $reportData = $this->generateReportData('leave');

            if (!$reportData) {
                log_message('error', 'Failed to generate leave report data');
                return redirect()->to('/reports')->with('error', 'Unable to generate leave report.');
            }

            // Prepare data for view
            $data = [
                'reportData' => $reportData
            ];

            return view('reports/leave_display', $data);
            
        } catch (\Throwable $e) {
            log_message('error', 'GenerateLeave method error: ' . $e->getMessage());
            return redirect()->to('/reports')->with('error', 'Unable to generate leave report.');
        }
    }

    /**
     * Generate salary report (for direct URL access)
     * This method handles the route: /reports/generate/salary
     */
    public function generateSalary()
    {
        try {
            // Enable error reporting in development
            if (ENVIRONMENT === 'development') {
                ini_set('display_errors', 1);
                error_reporting(E_ALL);
            }
            
            // Check if user is HR Admin or Super Admin
            $roleName = session()->get('role_name');
            $role = session()->get('role');
            
            if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
                return redirect()->to('/dashboard')->with('error', 'Access denied. HR Admin only.');
            }

            // Generate salary report data
            $reportData = $this->generateReportData('salary');

            if (!$reportData) {
                log_message('error', 'Failed to generate salary report data');
                return redirect()->to('/reports')->with('error', 'Unable to generate salary report.');
            }

            // Prepare data for view
            $data = [
                'reportData' => $reportData
            ];

            return view('reports/salary_display', $data);
            
        } catch (\Throwable $e) {
            log_message('error', 'GenerateSalary method error: ' . $e->getMessage());
            return redirect()->to('/reports')->with('error', 'Unable to generate salary report.');
        }
    }

    /**
     * Export salary report as Excel (CSV format)
     */
    public function exportSalaryExcel()
    {
        try {
            // Check if user is HR Admin or Super Admin
            $roleName = session()->get('role_name');
            $role = session()->get('role');
            
            if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
                return redirect()->to('/dashboard')->with('error', 'Access denied. HR Admin only.');
            }

            // Get salary data
            $db = \Config\Database::connect();
            $employees = $db->table('employees')
                ->select("employees.id AS employee_pk, employees.employee_id AS emp_code,
                          CONCAT(employees.first_name, ' ', employees.last_name) AS employee_name,
                          employees.status,
                          employees.rate,
                          employees.rate_type,
                          roles.name AS role_name,
                          departments.name AS department_name,
                          salaries.id AS salary_id,
                          salaries.base_salary,
                          salaries.allowances,
                          salaries.deductions,
                          salaries.sss_contribution,
                          salaries.philhealth_contribution,
                          salaries.pagibig_contribution,
                          salaries.withholding_tax,
                          salaries.net_salary,
                          salaries.effective_from")
                ->join('users',        'users.email = employees.email AND users.deleted_at IS NULL AND users.is_active = 1', 'left')
                ->join('roles',        'roles.id = users.role_id AND roles.deleted_at IS NULL', 'left')
                ->join('departments',  'departments.id = employees.department_id', 'left')
                ->join('salaries',     'salaries.employee_id = employees.id', 'left')
                ->where('employees.status', 'active')
                ->orderBy('employees.first_name', 'ASC')
                ->get()
                ->getResultObject();

            $html = '<!DOCTYPE html><html><head><meta charset="UTF-8">';
            $html .= '<style>
                body { font-family: Arial, sans-serif; color: #1f2937; }
                .report-title { font-size: 20px; font-weight: 700; margin-bottom: 6px; color: #2f5f45; }
                .report-meta { margin-bottom: 14px; color: #6b7280; font-size: 12px; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #dbe3ea; padding: 8px 10px; font-size: 12px; vertical-align: top; }
                th { background: #2f5f45; color: #fff; text-align: left; }
                tbody tr:nth-child(even) { background: #f8fbf9; }
                .num { text-align: right; white-space: nowrap; }
                .text { white-space: nowrap; }
                .net { font-weight: 700; color: #166534; }
                .currency { mso-number-format:"\0022Php\0022\#\,\#\#0\.00"; }
                .date { white-space: nowrap; }
            </style></head><body>';

            $html .= '<div class="report-title">Salary Report</div>';
            $html .= '<div class="report-meta">Generated on ' . date('M d, Y h:i A') . '</div>';
            $html .= '<table><thead><tr>';
            $headers = [
                'Employee ID', 'Employee Name', 'Department', 'Position', 'Rate', 'Rate Type',
                'Base Salary', 'Allowances', 'SSS', 'PhilHealth', 'Pag-IBIG', 'Withholding Tax',
                'Extra Deductions', 'Gross', 'Net Pay', 'Effective From'
            ];

            foreach ($headers as $header) {
                $html .= '<th>' . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . '</th>';
            }

            $html .= '</tr></thead><tbody>';

            foreach ($employees as $emp) {
                $empRate = (float)($emp->rate ?? 0);
                $hasSalary = !empty($emp->salary_id);

                $base = $hasSalary ? (float)($emp->base_salary ?? 0) : 0;
                $allowances = $hasSalary ? (float)($emp->allowances ?? 0) : 0;
                $sss = $hasSalary ? (float)($emp->sss_contribution ?? 0) : 0;
                $philhealth = $hasSalary ? (float)($emp->philhealth_contribution ?? 0) : 0;
                $pagibig = $hasSalary ? (float)($emp->pagibig_contribution ?? 0) : 0;
                $wTax = $hasSalary ? (float)($emp->withholding_tax ?? 0) : 0;
                $extraDed = $hasSalary ? (float)($emp->deductions ?? 0) : 0;
                $gross = $base + $allowances;
                $net = (float)($emp->net_salary ?? ($gross - $sss - $philhealth - $pagibig - $wTax - $extraDed));
                $effectiveFrom = !empty($emp->effective_from) ? date('M d, Y', strtotime($emp->effective_from)) : '';

                $html .= '<tr>';
                $html .= '<td class="text">' . htmlspecialchars((string) ($emp->emp_code ?? ''), ENT_QUOTES, 'UTF-8') . '</td>';
                $html .= '<td class="text">' . htmlspecialchars((string) ($emp->employee_name ?? ''), ENT_QUOTES, 'UTF-8') . '</td>';
                $html .= '<td class="text">' . htmlspecialchars((string) ($emp->department_name ?? 'Unassigned'), ENT_QUOTES, 'UTF-8') . '</td>';
                $html .= '<td class="text">' . htmlspecialchars((string) ($emp->role_name ?? 'N/A'), ENT_QUOTES, 'UTF-8') . '</td>';
                $html .= '<td class="num currency">' . number_format($empRate, 2) . '</td>';
                $html .= '<td class="text">' . htmlspecialchars((string) ($emp->rate_type ?? 'monthly'), ENT_QUOTES, 'UTF-8') . '</td>';
                $html .= '<td class="num currency">' . number_format($base, 2) . '</td>';
                $html .= '<td class="num currency">' . number_format($allowances, 2) . '</td>';
                $html .= '<td class="num currency">' . number_format($sss, 2) . '</td>';
                $html .= '<td class="num currency">' . number_format($philhealth, 2) . '</td>';
                $html .= '<td class="num currency">' . number_format($pagibig, 2) . '</td>';
                $html .= '<td class="num currency">' . number_format($wTax, 2) . '</td>';
                $html .= '<td class="num currency">' . number_format($extraDed, 2) . '</td>';
                $html .= '<td class="num currency">' . number_format($gross, 2) . '</td>';
                $html .= '<td class="num currency net">' . number_format($net, 2) . '</td>';
                $html .= '<td class="text date">' . htmlspecialchars($effectiveFrom, ENT_QUOTES, 'UTF-8') . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table></body></html>';

            $filename = 'Salary_Report_' . date('Y-m-d_H-i-s') . '.xls';

            return $this->response
                ->setHeader('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($html);
            
        } catch (\Throwable $e) {
            log_message('error', 'Export Salary Excel error: ' . $e->getMessage());
            return redirect()->to('/reports')->with('error', 'Unable to export salary report.');
        }
    }

    /**
     * Generate department report (for direct URL access)
     * This method handles the route: /reports/generate/department
     */
    public function generateDepartment()
    {
        try {
            // Enable error reporting in development
            if (ENVIRONMENT === 'development') {
                ini_set('display_errors', 1);
                error_reporting(E_ALL);
            }

            // Check if user is HR Admin or Super Admin
            $roleName = session()->get('role_name');
            $role = session()->get('role');
            
            if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
                return redirect()->to('/dashboard')->with('error', 'Access denied. HR Admin only.');
            }

            // Generate department report data
            $reportData = $this->generateReportData('department');

            if (!$reportData) {
                log_message('error', 'Failed to generate department report data');
                return redirect()->to('/reports')->with('error', 'Unable to generate department report.');
            }

            // Prepare data for view
            $data = [
                'reportData' => $reportData
            ];

            return view('reports/department_display', $data);
            
        } catch (\Throwable $e) {
            log_message('error', 'GenerateDepartment method error: ' . $e->getMessage());
            return redirect()->to('/reports')->with('error', 'Unable to generate department report.');
        }
    }

    /**
     * Export department report as Excel-compatible spreadsheet.
     */
    public function exportDepartmentExcel()
    {
        try {
            $roleName = session()->get('role_name');
            $role = session()->get('role');

            if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
                return redirect()->to('/dashboard')->with('error', 'Access denied. HR Admin only.');
            }

            $reportData = $this->generateReportData('department');
            if (!$reportData) {
                return redirect()->to('/reports')->with('error', 'Unable to export department report.');
            }

            $rows = $reportData['data'] ?? [];

            $html = '<!DOCTYPE html><html><head><meta charset="UTF-8">';
            $html .= '<style>
                body { font-family: Arial, sans-serif; color: #1f2937; }
                .report-title { font-size: 20px; font-weight: 700; margin-bottom: 6px; color: #2f5f45; }
                .report-meta { margin-bottom: 14px; color: #6b7280; font-size: 12px; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #dbe3ea; padding: 8px 10px; font-size: 12px; vertical-align: top; }
                th { background: #2f5f45; color: #fff; text-align: left; }
                tbody tr:nth-child(even) { background: #f8fbf9; }
                .num { text-align: right; white-space: nowrap; }
                .status-active { color: #1d7a3f; font-weight: 700; }
                .status-inactive { color: #b43a3a; font-weight: 700; }
            </style></head><body>';

            $html .= '<div class="report-title">Department Report</div>';
            $html .= '<div class="report-meta">Generated on ' . date('M d, Y h:i A') . '</div>';
            $html .= '<table><thead><tr>';

            foreach (['Department ID', 'Name', 'Description', 'Employee Count', 'Status'] as $header) {
                $html .= '<th>' . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . '</th>';
            }

            $html .= '</tr></thead><tbody>';

            foreach ($rows as $row) {
                $isActive = (int) ($row['is_active'] ?? 0) === 1;
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars((string) ($row['id'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') . '</td>';
                $html .= '<td>' . htmlspecialchars((string) ($row['name'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') . '</td>';
                $html .= '<td>' . htmlspecialchars((string) ($row['description'] ?? 'No description'), ENT_QUOTES, 'UTF-8') . '</td>';
                $html .= '<td class="num">' . (int) ($row['employee_count'] ?? 0) . '</td>';
                $html .= '<td class="' . ($isActive ? 'status-active' : 'status-inactive') . '">' . ($isActive ? 'Active' : 'Inactive') . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table></body></html>';

            $filename = 'Department_Report_' . date('Y-m-d_H-i-s') . '.xls';

            return $this->response
                ->setHeader('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($html);
        } catch (\Throwable $e) {
            log_message('error', 'Export Department Excel error: ' . $e->getMessage());
            return redirect()->to('/reports')->with('error', 'Unable to export department report.');
        }
    }

    /**
     * Display available reports
     */
    public function index()
    {
        // Check if user is Super Admin or HR Admin
        $roleName = session()->get('role_name');
        $role = session()->get('role');
        
        if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }
        return view('reports/index');
    }

    /**
     * Escape CSV values properly
     */
    private function escapeCsvValue($value)
    {
        if ($value === null || $value === '') {
            return '""';
        }

        $value = (string) $value;

        if (strpos($value, ',') !== false || strpos($value, '"') !== false || strpos($value, "\n") !== false || strpos($value, "\r") !== false) {
            return '"' . str_replace('"', '""', $value) . '"';
        }

        return $value;
    }

    /**
     * Generate specific report (for AJAX/API calls)
     */
    public function generate($reportType = null)
    {
        try {
            // Enable error reporting in development
            if (ENVIRONMENT === 'development') {
                ini_set('display_errors', 1);
                error_reporting(E_ALL);
            }
            
            // Get report type from URL parameter first, then from POST data
            if (!$reportType) {
                $reportType = $this->request->getPost('report_type');
            }
            
            $format = $this->request->getPost('format') ?? 'json'; // json, pdf, csv, excel

            if (!$reportType) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Report type is required'
                ]);
            }

            // Check if user is authenticated (allow both admin and hr_admin for attendance reports)
            $userRole = session()->get('role');
            $roleName = session()->get('role_name');
            
            // For attendance reports, allow HR Admin access as well
            $isAuthorized = ($userRole === 'admin' || 
                           $roleName === 'Super Admin' || 
                           in_array($roleName, ['HR Admin', 'hr']) || 
                           in_array($userRole, ['hr', 'hr_admin']));
            
            if (!$isAuthorized) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Access denied. Admin or HR Admin access required.'
                ]);
            }

            // Generate report based on type
            $reportData = $this->generateReportData($reportType);

            if (!$reportData) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Report type not found'
                ]);
            }

            // Ensure data is not null
            if (empty($reportData)) {
                $reportData = ['data' => []];
            }

            // Check if this is an AJAX request or direct URL access
            $isAjax = $this->request->isAJAX() || $this->request->getPost('format') !== null;
            
            if ($isAjax) {
                // Return JSON for AJAX calls
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Report generated successfully',
                    'data' => $reportData ?? []
                ]);
            } else {
                // Return view for direct URL access
                if ($reportType === 'attendance' || $reportType === 'attendance-report') {
                    $data['reportData'] = $reportData;
                    return view('reports/attendance_display', $data);
                } else {
                    // For other report types, return a generic view or redirect
                    return redirect()->to('/reports')->with('error', 'Report type not supported for direct viewing');
                }
            }
            
        } catch (\Throwable $e) {
            // Log the error
            log_message('error', 'Exception in generate method: ' . $e->getMessage());
            
            // Return standardized error response
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Internal server error occurred',
                'data' => [],
                'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * Generate Employee Report (for direct URL access)
     */
    public function generateEmployee()
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        // Generate employee report data
        $reportData = $this->generateReportData('employee');

        if (!$reportData) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Employee report not found");
        }

        // Prepare data for view
        $data = [
            'title' => $reportData['title'],
            'generated_at' => $reportData['generated_at'],
            'employees' => $reportData['employees'],
            'total_employees' => $reportData['total_employees'],
            'by_department' => $reportData['by_department']
        ];

        return view('superadmin/reports/employee_report', $data);
    }

    /**
     * View a specific report
     */
    public function view($reportId)
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        $reportData = $this->generateReportData($reportId);

        if (!$reportData) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Report not found");
        }

        $data['reportId'] = $reportId;
        $data['reportData'] = $reportData;

        return view('reports/view', $data);
    }

    /**
     * Generate report data
     */
    private function generateReportData($reportType)
    {
        try {
            $db = \Config\Database::connect();
            
            // Check database connection
            if (!$db) {
                log_message('error', 'Database connection failed');
                return null;
            }
            
            switch ($reportType) {
                case 'employee':
                case 'employee-summary':
                    // Get actual employee data
                    $employees = $db->table('employees')
                        ->select('employees.*, departments.name as department_name, users.email as user_email')
                        ->join('departments', 'departments.id = employees.department_id', 'left')
                        ->join('users', 'users.id = employees.user_id', 'left')
                        ->where('employees.account_status', 'approved')
                        ->orderBy('employees.first_name', 'ASC')
                        ->get()
                        ->getResultArray();
                    
                    // Get department statistics
                    $departmentStats = $db->table('employees')
                        ->select('departments.name, COUNT(*) as count')
                        ->join('departments', 'departments.id = employees.department_id', 'left')
                        ->where('employees.account_status', 'approved')
                        ->groupBy('departments.id, departments.name', false)
                        ->orderBy('count', 'DESC')
                        ->get()
                        ->getResultArray();
                    
                    return [
                        'title' => 'Employee Summary Report',
                        'generated_at' => date('Y-m-d H:i:s'),
                        'total_employees' => count($employees),
                        'by_department' => $departmentStats,
                        'employees' => $employees ?? []
                    ];
                    
                case 'attendance':
                case 'attendance-report':
                    $currentMonth = date('Y-m');
                    
                    // Enhanced query with better error handling
                    $attendanceData = $db->table('attendance_logs')
                        ->select('attendance_logs.*, employees.first_name, employees.last_name, employees.employee_id as emp_code, employees.rfid_number, departments.name as department_name')
                        ->join('employees', 'employees.id = attendance_logs.employee_id', 'inner')
                        ->join('departments', 'departments.id = employees.department_id', 'left')
                        ->where('attendance_logs.date >=', $currentMonth . '-01')
                        ->where('attendance_logs.date <=', $currentMonth . '-31')
                        ->orderBy('attendance_logs.date', 'DESC')
                        ->get()
                        ->getResultArray();
                    
                    return [
                        'title' => 'Attendance Report',
                        'generated_at' => date('Y-m-d H:i:s'),
                        'period' => $currentMonth,
                        'data' => $attendanceData ?? []
                    ];
                
                case 'leave':
                case 'leave-report':
                    $leaveData = $db->table('leave_requests')
                        ->select('leave_requests.*, employees.first_name, employees.last_name, employees.employee_id as emp_code, departments.name as department_name')
                        ->join('employees', 'employees.id = leave_requests.employee_id', 'inner')
                        ->join('departments', 'departments.id = employees.department_id', 'left')
                        ->orderBy('leave_requests.created_at', 'DESC')
                        ->get()
                        ->getResultArray();
                    
                    return [
                        'title' => 'Leave Report',
                        'generated_at' => date('Y-m-d H:i:s'),
                        'period' => date('Y'),
                        'data' => $leaveData ?? []
                    ];
                    
                case 'salary':
                case 'salary-report':
                    // Fetch complete salary information matching the salary management page
                    $salaryData = $db->table('employees')
                        ->select("employees.id AS employee_pk, employees.employee_id AS emp_code,
                                  CONCAT(employees.first_name, ' ', employees.last_name) AS employee_name,
                                  employees.status,
                                  employees.rate,
                                  employees.rate_type,
                                  roles.name AS role_name,
                                  departments.name AS department_name,
                                  salaries.id AS salary_id,
                                  salaries.base_salary,
                                  salaries.allowances,
                                  salaries.deductions,
                                  salaries.sss_contribution,
                                  salaries.philhealth_contribution,
                                  salaries.pagibig_contribution,
                                  salaries.withholding_tax,
                                  salaries.net_salary,
                                  salaries.effective_from")
                        ->join('users',        'users.email = employees.email AND users.deleted_at IS NULL AND users.is_active = 1', 'left')
                        ->join('roles',        'roles.id = users.role_id AND roles.deleted_at IS NULL', 'left')
                        ->join('departments',  'departments.id = employees.department_id', 'left')
                        ->join('salaries',     'salaries.employee_id = employees.id', 'left')
                        ->where('employees.status', 'active')
                        ->orderBy('employees.first_name', 'ASC')
                        ->get()
                        ->getResultObject();
                    
                    return [
                        'title' => 'Salary Report',
                        'generated_at' => date('Y-m-d H:i:s'),
                        'period' => date('Y-m'),
                        'data' => $salaryData ?? []
                    ];
                    
                case 'department':
                case 'department-report':
                    $departmentData = $db->table('departments')
                        ->select('MIN(departments.id) as id, departments.name, departments.description, departments.is_active, COUNT(CASE WHEN employees.account_status = "approved" THEN employees.id END) as employee_count')
                        ->join('employees', 'employees.department_id = departments.id', 'left')
                        ->groupBy(['departments.name', 'departments.description', 'departments.is_active'])
                        ->orderBy('employee_count', 'DESC')
                        ->get()
                        ->getResultArray();
                    
                    return [
                        'title' => 'Department Report',
                        'generated_at' => date('Y-m-d H:i:s'),
                        'data' => $departmentData ?? []
                    ];
                    
                default:
                    log_message('error', 'Unknown report type: ' . $reportType);
                    return null;
                    
            }
            
        } catch (\Throwable $e) {
            log_message('error', 'Exception in generateReportData: ' . $e->getMessage());
            return null;
        }
    }
}
