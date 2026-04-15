<?php

namespace App\Controllers;

use App\Models\LeaveModel;
use App\Models\AuditModel;
use App\Models\EmployeeModel;
use App\Models\NotificationModel;
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
            $data['dashboardStats'] = $this->getLeaveDashboardStats();
        } else if (in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin'])) {
            // HR Admin - one page with queue + responded history scopes
            $scope = strtolower(trim((string) $this->request->getGet('scope')));
            if (! in_array($scope, ['queue', 'responded'], true)) {
                $scope = 'queue';
            }

            $statusSet = $scope === 'responded'
                ? ['approved', 'rejected', 'ended']
                : ['pending', 'manager_approved'];

            $leaves = $this->leaveModel
                ->select("leave_requests.*, CONCAT(employees.first_name, ' ', employees.last_name) as name, employees.employee_id")
                ->join('employees', 'employees.id = leave_requests.employee_id', 'left')
                ->whereIn('leave_requests.status', $statusSet)
                ->orderBy('leave_requests.updated_at', 'DESC')
                ->paginate(20);
            $data['canApprove'] = $scope === 'queue';
            $data['isHRAdmin'] = true;
            $data['hrViewScope'] = $scope;
            $data['dashboardStats'] = $this->getLeaveDashboardStats();
        } else {
            // Employees and others - show own leaves
            $employee = $this->getCurrentEmployeeRecord();

            $statusFilter = strtolower(trim((string) $this->request->getGet('status')));
            $allowedStatuses = ['pending', 'manager_approved', 'approved', 'rejected', 'ended'];

            if (! in_array($statusFilter, $allowedStatuses, true)) {
                $statusFilter = '';
            }
            $today = date('Y-m-d');
            $summary = [
                'pending'          => 0,
                'manager_approved' => 0,
                'approved'         => 0,
                'rejected'         => 0,
                'ended'            => 0,
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
                    ->where('status', 'approved')
                    ->where('early_returned_at', null)
                    ->where('start_date <=', $today)
                    ->where('end_date >=', $today)
                    ->orderBy('start_date', 'ASC')
                    ->get(1)
                    ->getRow();

                $nextLeave = $db->table('leave_requests')
                    ->where('employee_id', $employeeId)
                    ->where('status', 'approved')
                    ->where('early_returned_at', null)
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

    public function hrSummary()
    {
        if (! session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $role = session()->get('role');
        $roleName = session()->get('role_name');
        $isAllowed = $role === 'admin'
            || $roleName === 'Super Admin'
            || in_array($roleName, ['HR Admin', 'hr'], true)
            || in_array($role, ['hr', 'hr_admin'], true);

        if (! $isAllowed) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        return $this->response->setJSON($this->getLeaveDashboardStats());
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
        $allowedStatuses = ['pending', 'manager_approved', 'approved', 'rejected', 'ended'];

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
                    'ended'            => 0,
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
            $leaveId = (int) ($this->leaveModel->getInsertID() ?: 0);
            $employee = $this->getCurrentEmployeeRecord();
            if ($employee) {
                $this->notifyManagerForLeaveRequest($employee, $leaveId);
            }
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
            $leaveId = (int) ($this->leaveModel->getInsertID() ?: 0);
            $this->notifyManagerForLeaveRequest($employee, $leaveId);
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

        $this->notifyEmployeeForLeaveDecision($leave, 'manager_approved');
        $this->notifyHrForManagerApprovedLeave($leave);

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

        $leave = $this->leaveModel->find((int) $id);
        if (! $leave) {
            return redirect()->back()->with('error', 'Leave request not found.');
        }

        if (strtolower((string) ($leave->status ?? '')) !== 'manager_approved') {
            return redirect()->back()->with('error', 'Only manager-approved leave requests can be approved by HR.');
        }

        $this->leaveModel->update($id, [
            'approved_by_hr' => $userId,
            'status' => 'approved',
        ]);

        $this->notifyEmployeeForLeaveDecision($leave, 'approved');
        $this->notifyManagerForHrApprovedLeave($leave);

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

        $this->notifyEmployeeForLeaveRejection($leave, $isManager);

        $auditAction = $isManager ? 'Manager Rejected Leave' : 'Leave Rejected';
        $this->auditModel->log($userId, $auditAction, 'Leave id: ' . $id . ' was rejected');
        return redirect()->back()->with('success', 'Leave rejected');
    }

    public function emergencyBack($id)
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = (int) session()->get('user_id');
        $employee = $this->getCurrentEmployeeRecord();

        if (! $employee) {
            return redirect()->to('/leaves')->with('error', 'Employee profile not found.');
        }

        $leave = $this->leaveModel
            ->where('id', (int) $id)
            ->where('employee_id', (int) $employee->id)
            ->first();

        if (! $leave) {
            return redirect()->to('/leaves')->with('error', 'Leave request not found.');
        }

        $today = date('Y-m-d');
        $status = strtolower((string) ($leave->status ?? ''));
        $alreadyReturned = ! empty($leave->early_returned_at);
        $isActiveWindow = ! empty($leave->start_date)
            && ! empty($leave->end_date)
            && $leave->start_date <= $today
            && $leave->end_date >= $today;

        if ($status !== 'approved' || $alreadyReturned || ! $isActiveWindow) {
            return redirect()->to('/leaves')->with('error', 'Emergency back is only available for your current approved leave.');
        }

        $updated = $this->leaveModel->update((int) $id, [
            'status' => 'ended',
            'early_returned_at' => date('Y-m-d H:i:s'),
        ]);

        if (! $updated) {
            return redirect()->to('/leaves')->with('error', 'Unable to process emergency back. Please try again.');
        }

        $this->notifyManagerForEmergencyBack($employee, $leave);

        $this->auditModel->log($userId, 'Emergency Back from Leave', 'Employee returned early from leave id: ' . (int) $id);

        return redirect()->to('/leaves')->with('success', 'Emergency back applied. You are now marked as back to work.');
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

    private function getLeaveDashboardStats(): array
    {
        $summary = [
            'pending'          => 0,
            'manager_approved' => 0,
            'approved'         => 0,
            'rejected'         => 0,
            'ended'            => 0,
        ];

        $rows = $this->leaveModel
            ->select('status, COUNT(*) AS total')
            ->groupBy('status')
            ->findAll();

        foreach ($rows as $row) {
            $status = strtolower((string) ($row->status ?? ''));
            if (array_key_exists($status, $summary)) {
                $summary[$status] = (int) ($row->total ?? 0);
            }
        }

        $awaitingReview = $summary['pending'] + $summary['manager_approved'];
        $responded = $summary['approved'] + $summary['rejected'] + $summary['ended'];

        return [
            'total_requests'    => $awaitingReview + $responded,
            'pending'           => $summary['pending'],
            'manager_approved'  => $summary['manager_approved'],
            'awaiting_review'   => $awaitingReview,
            'approved'          => $summary['approved'],
            'rejected'          => $summary['rejected'],
            'ended'             => $summary['ended'],
            'responded'         => $responded,
            'updated_at'        => date('Y-m-d H:i:s'),
        ];
    }

    private function notifyManagerForEmergencyBack(object $employee, object $leave): void
    {
        $departmentId = (int) ($employee->department_id ?? 0);
        if ($departmentId <= 0) {
            return;
        }

        $managerUserIds = $this->getDepartmentManagerUserIds($departmentId);
        if ($managerUserIds === []) {
            return;
        }

        $employeeName = trim((string) (($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')));
        if ($employeeName === '') {
            $employeeName = (string) ($employee->employee_id ?? 'An employee');
        }

        $staffCode = (string) ($employee->employee_id ?? 'N/A');
        $leaveType = (string) ($leave->leave_type ?? 'Leave');
        $title = 'Leave Ended Early';
        $message = $employeeName . ' (' . $staffCode . ') ended ' . $leaveType . ' early and is now back to work.';
        $link = base_url('leaves/team?status=ended');

        $notificationModel = new NotificationModel();

        foreach ($managerUserIds as $managerUserId) {
            $notificationModel->createNotification(
                $managerUserId,
                $title,
                $message,
                'info',
                $link,
                'fas fa-user-check'
            );
        }
    }

    private function notifyManagerForLeaveRequest(object $employee, int $leaveId = 0): void
    {
        $departmentId = (int) ($employee->department_id ?? 0);
        if ($departmentId <= 0) {
            return;
        }

        $managerUserIds = $this->getDepartmentManagerUserIds($departmentId);
        if ($managerUserIds === []) {
            return;
        }

        $employeeName = trim((string) (($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')));
        if ($employeeName === '') {
            $employeeName = (string) ($employee->employee_id ?? 'An employee');
        }

        $staffCode = (string) ($employee->employee_id ?? 'N/A');
        $title = 'New Leave Request';
        $message = $employeeName . ' (' . $staffCode . ') submitted a leave request for your review.';
        $link = base_url('leaves/team?status=pending');

        $notificationModel = new NotificationModel();

        foreach ($managerUserIds as $managerUserId) {
            $notificationModel->createNotification(
                $managerUserId,
                $title,
                $message,
                'info',
                $link,
                'fas fa-calendar-check'
            );
        }
    }

    /**
     * Resolve active manager user IDs for a department.
     * Uses the department manager_id first, then falls back to active manager-role users
     * linked to employees in the same department.
     */
    private function getDepartmentManagerUserIds(int $departmentId): array
    {
        if ($departmentId <= 0) {
            return [];
        }

        $db = \Config\Database::connect();
        $userIds = [];

        $department = $db->table('departments')
            ->select('manager_id')
            ->where('id', $departmentId)
            ->get()
            ->getRow();

        $directManagerUserId = (int) ($department->manager_id ?? 0);
        if ($directManagerUserId > 0) {
            $directManager = $db->table('users')
                ->select('id')
                ->where('id', $directManagerUserId)
                ->where('is_active', 1)
                ->get()
                ->getRow();

            if ($directManager) {
                $userIds[$directManagerUserId] = true;
            }
        }

        $usersHasRoleColumn = $db->fieldExists('role', 'users');

        $managerQuery = $db->table('employees')
            ->select('users.id as user_id')
            ->join('users', 'users.id = employees.user_id', 'inner')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('employees.department_id', $departmentId)
            ->where('users.is_active', 1)
            ->groupStart()
                ->where('LOWER(roles.name)', 'manager');

        if ($usersHasRoleColumn) {
            $managerQuery->orWhereIn('users.role', ['manager']);
        }

        $managerRows = $managerQuery
            ->groupEnd()
            ->get()
            ->getResultArray();

        foreach ($managerRows as $row) {
            $managerUserId = (int) ($row['user_id'] ?? 0);
            if ($managerUserId > 0) {
                $userIds[$managerUserId] = true;
            }
        }

        return array_map('intval', array_keys($userIds));
    }

    private function notifyEmployeeForLeaveDecision(object $leave, string $newStatus): void
    {
        $employeeId = (int) ($leave->employee_id ?? 0);
        if ($employeeId <= 0) {
            return;
        }

        $employee = $this->employeeModel->find($employeeId);
        $employeeUserId = (int) ($employee->user_id ?? 0);
        if ($employeeUserId <= 0) {
            return;
        }

        $leaveType = (string) ($leave->leave_type ?? 'Leave');
        $link = base_url('leaves?status=' . $newStatus);

        if ($newStatus === 'manager_approved') {
            $title = 'Leave Approved By Manager';
            $message = 'Your ' . $leaveType . ' request was approved by your manager and is now awaiting HR approval.';
            $icon = 'fas fa-user-check';
        } else {
            $title = 'Leave Approved By HR';
            $message = 'Your ' . $leaveType . ' request was approved by HR.';
            $icon = 'fas fa-check-circle';
        }

        (new NotificationModel())->createNotification(
            $employeeUserId,
            $title,
            $message,
            'info',
            $link,
            $icon
        );
    }

    private function notifyManagerForHrApprovedLeave(object $leave): void
    {
        $employeeId = (int) ($leave->employee_id ?? 0);
        if ($employeeId <= 0) {
            return;
        }

        $employee = $this->employeeModel->find($employeeId);
        if (! $employee) {
            return;
        }

        $departmentId = (int) ($employee->department_id ?? 0);
        if ($departmentId <= 0) {
            return;
        }

        $managerUserIds = $this->getDepartmentManagerUserIds($departmentId);
        if ($managerUserIds === []) {
            return;
        }

        $employeeName = trim((string) (($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')));
        if ($employeeName === '') {
            $employeeName = (string) ($employee->employee_id ?? 'An employee');
        }

        $staffCode = (string) ($employee->employee_id ?? 'N/A');
        $leaveType = (string) ($leave->leave_type ?? 'Leave');
        $title = 'Leave Approved By HR';
        $message = $employeeName . ' (' . $staffCode . ') has a ' . $leaveType . ' request approved by HR.';
        $link = base_url('leaves/team?status=approved');

        $notificationModel = new NotificationModel();
        foreach ($managerUserIds as $managerUserId) {
            $notificationModel->createNotification(
                $managerUserId,
                $title,
                $message,
                'success',
                $link,
                'fas fa-check-circle'
            );
        }
    }

    private function notifyEmployeeForLeaveRejection(object $leave, bool $isManagerRejection): void
    {
        $employeeId = (int) ($leave->employee_id ?? 0);
        if ($employeeId <= 0) {
            return;
        }

        $employee = $this->employeeModel->find($employeeId);
        $employeeUserId = (int) ($employee->user_id ?? 0);
        if ($employeeUserId <= 0) {
            return;
        }

        $leaveType = (string) ($leave->leave_type ?? 'Leave');
        $title = $isManagerRejection ? 'Leave Rejected By Manager' : 'Leave Rejected';
        $message = $isManagerRejection
            ? 'Your ' . $leaveType . ' request was rejected by your manager.'
            : 'Your ' . $leaveType . ' request was rejected by HR.';

        (new NotificationModel())->createNotification(
            $employeeUserId,
            $title,
            $message,
            'warning',
            base_url('leaves?status=rejected'),
            'fas fa-times-circle'
        );
    }

    private function notifyHrForManagerApprovedLeave(object $leave): void
    {
        $db = \Config\Database::connect();

        $hrRows = $db->table('users')
            ->select('users.id')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.is_active', 1)
            ->groupStart()
                ->whereIn('users.role', ['hr', 'hr_admin'])
                ->orWhere('roles.name', 'HR Admin')
                ->orWhere('roles.name', 'hr')
            ->groupEnd()
            ->get()
            ->getResultArray();

        if ($hrRows === []) {
            return;
        }

        $employeeId = (int) ($leave->employee_id ?? 0);
        $employee = $employeeId > 0 ? $this->employeeModel->find($employeeId) : null;
        $employeeName = trim((string) (($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')));
        if ($employeeName === '') {
            $employeeName = 'An employee';
        }

        $staffCode = (string) ($employee->employee_id ?? 'N/A');
        $leaveType = (string) ($leave->leave_type ?? 'Leave');
        $title = 'Leave Awaiting HR Approval';
        $message = $employeeName . ' (' . $staffCode . ') has a ' . $leaveType . ' request awaiting HR approval.';
        $link = base_url('leaves');

        $notificationModel = new NotificationModel();
        $hrUserIds = [];
        foreach ($hrRows as $row) {
            $hrUserId = (int) ($row['id'] ?? 0);
            if ($hrUserId > 0) {
                $hrUserIds[$hrUserId] = true;
            }
        }

        foreach (array_keys($hrUserIds) as $hrUserId) {
            $notificationModel->createNotification(
                $hrUserId,
                $title,
                $message,
                'info',
                $link,
                'fas fa-user-clock'
            );
        }
    }
}
