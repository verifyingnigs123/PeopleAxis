<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['NotificationHelper', 'AuditHelper'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
    }

    protected function isManagerUser(): bool
    {
        $role     = strtolower(trim((string) session()->get('role')));
        $roleName = strtolower(trim((string) session()->get('role_name')));

        return $role === 'manager' || $roleName === 'manager';
    }

    /**
     * Returns departments managed by the logged-in manager and the employees
     * assigned to those same departments.
     *
     * @return array<string, array<int, array<string, mixed>>|array<int, int>>
     */
    protected function getManagedTeamContext(): array
    {
        $managerId = (int) session()->get('user_id');

        if ($managerId <= 0) {
            return [
                'departments'   => [],
                'departmentIds' => [],
                'teamMembers'   => [],
                'employeeIds'   => [],
            ];
        }

        $db = \Config\Database::connect();
        $adminRoleNames = ['super admin', 'hr admin'];

        $departments = $db->table('departments')
            ->select('id, name')
            ->where('manager_id', $managerId)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        $departmentIds = array_map('intval', array_column($departments, 'id'));
        // Team members include approved active employees, even when a department
        // manager mapping is missing, so newly approved staff are visible right away.
        $teamMembersById = [];

        if ($departmentIds !== []) {
            $managedEmployees = $db->table('employees')
                ->select('employees.id, employees.employee_id, employees.first_name, employees.last_name, employees.email, employees.department_id, employees.status, employees.account_status, departments.name as department_name')
                ->join('departments', 'departments.id = employees.department_id', 'left')
                ->join('users', 'users.email = employees.email', 'left')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('employees.account_status', 'approved')
                ->groupStart()
                    ->where('employees.status', 'active')
                    ->orWhere('employees.status IS NULL', null, false)
                    ->orWhere('employees.status', '')
                ->groupEnd()
                ->whereIn('employees.department_id', $departmentIds)
                ->groupStart()
                    ->where('employees.user_id IS NULL', null, false)
                    ->orWhere('employees.user_id !=', $managerId)
                ->groupEnd()
                ->groupStart()
                    ->whereNotIn('LOWER(roles.name)', $adminRoleNames)
                    ->orWhere('roles.name IS NULL', null, false)
                ->groupEnd()
                ->orderBy('employees.first_name', 'ASC')
                ->orderBy('employees.last_name', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($managedEmployees as $employee) {
                $teamMembersById[(int) $employee['id']] = $employee;
            }
        }

        $teamMembers = array_values($teamMembersById);
        $employeeIds = array_map('intval', array_column($teamMembers, 'id'));

        return [
            'departments'   => $departments,
            'departmentIds' => $departmentIds,
            'teamMembers'   => $teamMembers,
            'employeeIds'   => $employeeIds,
        ];
    }

    protected function getCurrentEmployeeRecord(): ?object
    {
        $userId = (int) session()->get('user_id');
        $email = (string) session()->get('email');

        if ($userId <= 0 && $email === '') {
            return null;
        }

        $employeeModel = new EmployeeModel();

        if ($userId > 0) {
            $employee = $employeeModel->where('user_id', $userId)->first();
            if ($employee) {
                return $employee;
            }
        }

        if ($email !== '') {
            $employee = $employeeModel->where('email', $email)->first();

            if ($employee && (int) ($employee->user_id ?? 0) !== $userId && $userId > 0) {
                $employeeModel->update($employee->id, ['user_id' => $userId]);
                $employee->user_id = $userId;
            }

            if ($employee) {
                return $employee;
            }
        }

        return null;
    }

    protected function invalidateUserSessions(int $targetUserId): void
    {
        if ($targetUserId <= 0) {
            return;
        }

        $sessionConfig = config('Session');
        $savePath = (string) ($sessionConfig->savePath ?? '');

        if ($savePath === '' || !is_dir($savePath) || !is_readable($savePath)) {
            return;
        }

        $sessionFiles = glob(rtrim($savePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*');
        if (!is_array($sessionFiles) || $sessionFiles === []) {
            return;
        }

        $needleInt = 'user_id|i:' . $targetUserId . ';';
        $needleStringPattern = '/user_id\|s:\d+:"' . preg_quote((string) $targetUserId, '/') . '";/';

        foreach ($sessionFiles as $filePath) {
            if (!is_file($filePath) || !is_readable($filePath)) {
                continue;
            }

            $content = @file_get_contents($filePath);
            if ($content === false) {
                continue;
            }

            $containsUserId = strpos($content, $needleInt) !== false
                || preg_match($needleStringPattern, $content) === 1;

            if ($containsUserId) {
                @unlink($filePath);
            }
        }
    }
}
