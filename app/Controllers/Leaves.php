<?php

namespace App\Controllers;

use App\Models\LeaveModel;
use App\Models\AuditModel;
use App\Models\EmployeeModel;
use App\Controllers\Audit;

class Leaves extends BaseController
{
    protected $leaveModel;
    protected $auditModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->leaveModel = new LeaveModel();
        $this->auditModel = new AuditModel();
        $this->employeeModel = new EmployeeModel();
    }

    /**
     * Display leave form
     */
    public function create()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data['leaveTypes'] = ['Annual', 'Sick', 'Maternity', 'Paternity', 'Unpaid', 'Other'];
        $data['errors'] = []; // Initialize errors array
        return view('leaves/create', $data);
    }

    /**
     * Display leaves status
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if ($this->isManagerUser()) {
            return redirect()->to('/leaves/team');
        }

        $role = session()->get('role');
        $roleName = session()->get('role_name');
        $data = [];
        
        if ($role === 'admin' || $roleName === 'Super Admin') {
            // Super Admin - show all leaves
            $leaves = $this->leaveModel
                ->select("leave_requests.*, CONCAT(employees.first_name, ' ', employees.last_name) as name, employees.employee_id")
                ->join('employees', 'employees.id = leave_requests.employee_id', 'left')
                ->orderBy('leave_requests.start_date', 'DESC')
                ->paginate(20);
            $data['canApprove'] = true;
            $data['isHRAdmin'] = false;
        } else if (in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin'])) {
            // HR Admin - show pending & manager approved leaves for approval
            $leaves = $this->leaveModel
                ->select("leave_requests.*, CONCAT(employees.first_name, ' ', employees.last_name) as name, employees.employee_id")
                ->join('employees', 'employees.id = leave_requests.employee_id', 'left')
                ->whereIn('leave_requests.status', ['pending', 'manager_approved'])
                ->orderBy('leave_requests.start_date', 'DESC')
                ->paginate(20);
            $data['canApprove'] = true;
            $data['isHRAdmin'] = true;
        } else {
            // Employees and others - show own leaves
            $employee = $this->getCurrentEmployeeRecord();

            $statusFilter = strtolower(trim((string) $this->request->getGet('status')));
            $allowedStatuses = ['pending', 'manager_approved', 'approved', 'rejected'];

            if (! in_array($statusFilter, $allowedStatuses, true)) {
                $statusFilter = '';
            }
            $today = date('Y-m-d');
            $summary = [
                'pending'          => 0,
                'manager_approved' => 0,
                'approved'         => 0,
                'rejected'         => 0,
            ];
            $activeLeave = null;
            $nextLeave = null;

            if ($employee) {
                $employeeId = (int) $employee->id;
                $db = \Config\Database::connect();

                $summaryRows = $db->table('leave_requests')
                    ->select('status, COUNT(*) AS total')
                    ->where('employee_id', $employeeId)
                    ->groupBy('status')
                    ->get()
                    ->getResultArray();

                foreach ($summaryRows as $row) {
                    $status = strtolower((string) ($row['status'] ?? ''));
                    if (array_key_exists($status, $summary)) {
                        $summary[$status] = (int) ($row['total'] ?? 0);
                    }
                }

                $leaveQuery = $this->leaveModel
                    ->where('employee_id', $employeeId)
                    ->orderBy('start_date', 'DESC')
                    ->orderBy('created_at', 'DESC');

                if ($statusFilter !== '') {
                    $leaveQuery->where('status', $statusFilter);
                }

                $activeLeave = $db->table('leave_requests')
                    ->where('employee_id', $employeeId)
                    ->whereIn('status', ['approved', 'manager_approved'])
                    ->where('start_date <=', $today)
                    ->where('end_date >=', $today)
                    ->orderBy('start_date', 'ASC')
                    ->get(1)
                    ->getRow();

                $nextLeave = $db->table('leave_requests')
                    ->where('employee_id', $employeeId)
                    ->whereIn('status', ['approved', 'manager_approved'])
                    ->where('start_date >', $today)
                    ->orderBy('start_date', 'ASC')
                    ->get(1)
                    ->getRow();

                $data['leaves'] = $leaveQuery->paginate(12);
                $data['pager'] = $this->leaveModel->pager;
            } else {
                // No linked employee profile yet; keep the page visible with empty data.
                $data['leaves'] = [];
                $data['pager'] = null;
            }

            $data['employee'] = $employee;
            $data['statusFilter'] = $statusFilter;
            $data['leaveSummary'] = $summary;
            $data['activeLeave'] = $activeLeave;
            $data['nextLeave'] = $nextLeave;

            return view('leaves/status', $data);
        }

        $data['leaves'] = $leaves;
        $data['pager'] = $this->leaveModel->pager;

        return view('leaves/index', $data);
    }

    /**
     * Display the manager leave approval dashboard for the manager's own team.
     */
    public function team()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (! $this->isManagerUser()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Manager only.');
        }

        $statusFilter = strtolower(trim((string) $this->request->getGet('status')));
        $allowedStatuses = ['pending', 'manager_approved', 'approved', 'rejected'];

        if (! in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = '';
        }

        try {
            $teamContext = $this->getManagedTeamContext();
            $data = [
                'managedDepartments' => $teamContext['departments'],
                'statusFilter'       => $statusFilter,
                'leaveSummary'       => [
                    'pending'          => 0,
                    'manager_approved' => 0,
                    'approved'         => 0,
                    'rejected'         => 0,
                ],
                'teamLeaves'         => [],
                'pager'              => null,
            ];

            if ($teamContext['employeeIds'] === []) {
                return view('leaves/team', $data);
            }

            $db = \Config\Database::connect();
            $summaryRows = $db->table('leave_requests')
                ->select('status, COUNT(*) AS total')
                ->whereIn('employee_id', $teamContext['employeeIds'])
                ->groupBy('status')
                ->get()
                ->getResultArray();

            foreach ($summaryRows as $row) {
                $status = strtolower((string) ($row['status'] ?? ''));
                if (array_key_exists($status, $data['leaveSummary'])) {
                    $data['leaveSummary'][$status] = (int) ($row['total'] ?? 0);
                }
            }

            $leaveQuery = $this->leaveModel
                ->select("leave_requests.*, CONCAT(employees.first_name, ' ', employees.last_name) as employee_name, employees.employee_id as staff_code, departments.name as department_name")
                ->join('employees', 'employees.id = leave_requests.employee_id', 'left')
                ->join('departments', 'departments.id = employees.department_id', 'left')
                ->whereIn('leave_requests.employee_id', $teamContext['employeeIds'])
                ->orderBy('leave_requests.created_at', 'DESC');

            if ($statusFilter !== '') {
                $leaveQuery->where('leave_requests.status', $statusFilter);
            }

            $data['teamLeaves'] = $leaveQuery->paginate(20);
            $data['pager'] = $this->leaveModel->pager;

            return view('leaves/team', $data);
        } catch (\Exception $e) {
            log_message('error', 'Manager leave dashboard error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load team leave requests.');
        }
    }

    public function submit()
    {
        $session = session();
        $userId = $session->get('user_id');

        $data = [
            'employee_id' => $this->request->getPost('employee_id'),
            'leave_type' => $this->request->getPost('leave_type'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'number_of_days' => $this->request->getPost('number_of_days'),
            'reason' => $this->request->getPost('reason'),
            'status' => 'pending',
        ];

        if ($this->leaveModel->save($data)) {
            $this->auditModel->log($userId, 'Leave Submitted', 'Leave request submitted');
            return redirect()->back()->with('success', 'Leave submitted');
        }

        return redirect()->back()->with('error', 'Unable to submit leave');
    }

    /**
     * Store a new leave request
     */
    public function store()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $session = session();
        $userId = $session->get('user_id');

        // Get the employee profile for this user
        $employee = $this->getCurrentEmployeeRecord();
        
        if (!$employee) {
            return redirect()->to('/dashboard')->with('error', 'Employee profile not found. Please contact HR.');
        }

        // Validate input
        $fromDate = $this->request->getPost('from_date');
        $toDate = $this->request->getPost('to_date');
        
        if (empty($fromDate) || empty($toDate)) {
            $data['errors'] = ['from_date' => 'From date and To date are required'];
            $data['leaveTypes'] = ['Annual', 'Sick', 'Maternity', 'Paternity', 'Unpaid', 'Other'];
            return view('leaves/create', $data);
        }

        if ($toDate < $fromDate) {
            $data['errors'] = ['to_date' => 'End date must be after start date'];
            $data['leaveTypes'] = ['Annual', 'Sick', 'Maternity', 'Paternity', 'Unpaid', 'Other'];
            return view('leaves/create', $data);
        }

        // Calculate number of days
        $from = new \DateTime($fromDate);
        $to = new \DateTime($toDate);
        $days = $to->diff($from)->days + 1;

        $data = [
            'employee_id' => $employee->id,
            'leave_type' => $this->request->getPost('leave_type'),
            'start_date' => $fromDate,
            'end_date' => $toDate,
            'number_of_days' => $days,
            'reason' => $this->request->getPost('reason'),
            'status' => 'pending',
        ];

        if ($this->leaveModel->save($data)) {
            $this->auditModel->log($userId, 'Leave Submitted', 'Leave request submitted for ' . $days . ' days');
            return redirect()->to('/leaves')->with('success', 'Leave request submitted successfully!');
        }

        return redirect()->back()->with('error', 'Unable to submit leave request. Please try again.')->withInput();
    }

    public function approveByManager($id)
    {
        $session = session();
        $userId = (int) $session->get('user_id');

        if (! $this->isManagerUser()) {
            return redirect()->back()->with('error', 'Access denied. Manager only.');
        }

        $leave = $this->getManagerScopedLeave((int) $id);

        if (! $leave) {
            return redirect()->back()->with('error', 'Leave request not found for your team.');
        }

        if (strtolower((string) ($leave->status ?? '')) !== 'pending') {
            return redirect()->back()->with('error', 'Only pending leave requests can be approved.');
        }

        $this->leaveModel->update($id, [
            'approved_by_manager' => $userId,
            'status' => 'manager_approved',
        ]);

        $this->auditModel->log($userId, 'Manager Approved Leave', 'Manager approved leave id: ' . $id);
        return redirect()->back()->with('success', 'Leave approved by manager');
    }

    public function approveByHR($id)
    {
        $session = session();
        $userId = $session->get('user_id');
        $role = $session->get('role');
        $roleName = $session->get('role_name');

        // Check if user is HR Admin or Super Admin
        if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
            return redirect()->back()->with('error', 'Access denied. HR Admin only.');
        }

        $this->leaveModel->update($id, [
            'approved_by_hr' => $userId,
            'status' => 'approved',
        ]);

        $this->auditModel->log($userId, 'HR Approved Leave', 'HR approved leave id: ' . $id);
        return redirect()->back()->with('success', 'Leave approved by HR');
    }

    public function reject($id)
    {
        $session = session();
        $userId = (int) $session->get('user_id');
        $role = $session->get('role');
        $roleName = $session->get('role_name');
        $isManager = $this->isManagerUser();
        $isHrOrAdmin = $role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']);

        if (! $isManager && ! $isHrOrAdmin) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $leave = $this->leaveModel->find($id);

        if (! $leave) {
            return redirect()->back()->with('error', 'Leave request not found.');
        }

        if ($isManager) {
            $leave = $this->getManagerScopedLeave((int) $id);

            if (! $leave) {
                return redirect()->back()->with('error', 'Leave request not found for your team.');
            }

            if (strtolower((string) ($leave->status ?? '')) !== 'pending') {
                return redirect()->back()->with('error', 'Only pending leave requests can be rejected by a manager.');
            }
        }

        $this->leaveModel->update($id, [
            'status' => 'rejected',
        ]);

        $auditAction = $isManager ? 'Manager Rejected Leave' : 'Leave Rejected';
        $this->auditModel->log($userId, $auditAction, 'Leave id: ' . $id . ' was rejected');
        return redirect()->back()->with('success', 'Leave rejected');
    }

    private function getManagerScopedLeave(int $leaveId): ?object
    {
        $leave = $this->leaveModel->find($leaveId);

        if (! $leave) {
            return null;
        }

        $teamContext = $this->getManagedTeamContext();
        $employeeId = (int) ($leave->employee_id ?? 0);

        if (! in_array($employeeId, $teamContext['employeeIds'], true)) {
            return null;
        }

        return $leave;
    }
}
