<?php

namespace App\Controllers;

class Reports extends BaseController
{
    /**
     * Generate attendance report (HR Admin only)
     */
    public function attendance()
    {
        // Check if user is HR Admin or Super Admin
        $roleName = session()->get('role_name');
        $role = session()->get('role');
        
        if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. HR Admin only.');
        }

        return view('reports/attendance');
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

        $reports = [
            [
                'id' => 'employee-summary',
                'title' => 'Employee Summary Report',
                'description' => 'Overview of all employees in the system',
                'icon' => 'fa-users'
            ],
            [
                'id' => 'attendance-report',
                'title' => 'Attendance Report',
                'description' => 'Monthly and yearly attendance statistics',
                'icon' => 'fa-calendar-check'
            ],
            [
                'id' => 'leave-report',
                'title' => 'Leave Report',
                'description' => 'Leave requests and approvals summary',
                'icon' => 'fa-calendar-times'
            ],
            [
                'id' => 'salary-report',
                'title' => 'Salary Report',
                'description' => 'Employee salary and payroll information',
                'icon' => 'fa-dollar-sign'
            ],
            [
                'id' => 'user-activity',
                'title' => 'User Activity Report',
                'description' => 'System user activities and access logs',
                'icon' => 'fa-history'
            ],
            [
                'id' => 'department-report',
                'title' => 'Department Report',
                'description' => 'Department-wise employee distribution',
                'icon' => 'fa-sitemap'
            ]
        ];

        $data['reports'] = $reports;
        return view('reports/index', $data);
    }

    /**
     * Generate specific report
     */
    public function generate()
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin only.'
            ]);
        }

        $reportType = $this->request->getPost('report_type');
        $format = $this->request->getPost('format') ?? 'pdf'; // pdf, csv, excel

        if (!$reportType) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Report type is required'
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

        // For now, return JSON. In production, convert to PDF/CSV/Excel
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Report generated successfully',
            'data' => $reportData
        ]);
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
     * Generate report data (placeholder)
     */
    private function generateReportData($reportType)
    {
        // This is a placeholder. Implement actual report generation logic
        $reports = [
            'employee-summary' => [
                'title' => 'Employee Summary Report',
                'generated_at' => date('Y-m-d H:i:s'),
                'total_employees' => 0,
                'by_department' => []
            ],
            'attendance-report' => [
                'title' => 'Attendance Report',
                'generated_at' => date('Y-m-d H:i:s'),
                'period' => date('Y-m'),
                'data' => []
            ],
            'leave-report' => [
                'title' => 'Leave Report',
                'generated_at' => date('Y-m-d H:i:s'),
                'period' => date('Y'),
                'data' => []
            ],
            'salary-report' => [
                'title' => 'Salary Report',
                'generated_at' => date('Y-m-d H:i:s'),
                'period' => date('Y-m'),
                'data' => []
            ],
            'user-activity' => [
                'title' => 'User Activity Report',
                'generated_at' => date('Y-m-d H:i:s'),
                'data' => []
            ],
            'department-report' => [
                'title' => 'Department Report',
                'generated_at' => date('Y-m-d H:i:s'),
                'data' => []
            ]
        ];

        return $reports[$reportType] ?? null;
    }
}
