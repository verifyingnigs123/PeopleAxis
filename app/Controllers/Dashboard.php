<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Models\LeaveModel;
use App\Models\ProfilePhotoModel;
use Config\Database;

class Dashboard extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $employeeModel = new EmployeeModel();
        $attendanceModel = new AttendanceModel();
        $leaveModel = new LeaveModel();

        $session = session();
        $role = $session->get('role_name') ?? 'Employee';

        $data = ['user' => $session->get()];

        switch ($role) {
            case 'Super Admin':
                $data['totalUsers'] = $userModel->countAllResults();
                $data['totalEmployees'] = $employeeModel->countAllResults();
                $data['auditCount'] = model('App\\Models\\AuditModel')->countAllResults();
                $data['attendanceSummary'] = $attendanceModel->getSummary();
                break;

            case 'HR Admin':
                $data['totalEmployees'] = $employeeModel->countAllResults();
                $data['pendingLeaves'] = $leaveModel->where('status', 'pending')->countAllResults();
                $data['attendanceSummary'] = $attendanceModel->getSummary();
                $statusCounts = [
                    'pending'  => 0,
                    'approved' => 0,
                    'rejected' => 0,
                ];

                $statusRows = Database::connect()
                    ->table('employees')
                    ->select('LOWER(account_status) as account_status, COUNT(*) as total', false)
                    ->whereIn('account_status', ['pending', 'approved', 'rejected'])
                    ->groupBy('account_status')
                    ->get()
                    ->getResultArray();

                foreach ($statusRows as $row) {
                    $status = strtolower((string) ($row['account_status'] ?? ''));
                    if (array_key_exists($status, $statusCounts)) {
                        $statusCounts[$status] = (int) ($row['total'] ?? 0);
                    }
                }

                $data['employeeAccountStatusCounts'] = $statusCounts;
                break;

            case 'Manager':
                try {
                    $teamContext = $this->getManagedTeamContext();
                    $selectedDailyAttendanceDate = (string) $this->request->getGet('team_attendance_date');
                    if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDailyAttendanceDate)) {
                        $selectedDailyAttendanceDate = date('Y-m-d');
                    }

                    $sortBy = strtolower((string) $this->request->getGet('team_attendance_sort_by'));
                    if (! in_array($sortBy, ['employee_name', 'date'], true)) {
                        $sortBy = 'date';
                    }

                    $sortDir = strtolower((string) $this->request->getGet('team_attendance_sort_dir'));
                    if (! in_array($sortDir, ['asc', 'desc'], true)) {
                        $sortDir = 'desc';
                    }

                    $data['managedDepartments'] = $teamContext['departments'];
                    $data['managedDepartmentCount'] = count($teamContext['departments']);
                    $data['teamCount'] = count($teamContext['teamMembers']);
                    $data['pendingTeamLeaves'] = 0;
                    $data['teamAttendance'] = [
                        'present' => 0,
                        'late'    => 0,
                        'absent'  => 0,
                        'leave'   => 0,
                    ];
                    $data['teamDailyAttendanceDate'] = $selectedDailyAttendanceDate;
                    $data['teamDailyAttendanceSortBy'] = $sortBy;
                    $data['teamDailyAttendanceSortDir'] = $sortDir;
                    $data['teamDailyAttendanceRecords'] = [];

                    if ($teamContext['employeeIds'] !== []) {
                        $db = \Config\Database::connect();
                        $today = date('Y-m-d');

                        $todayAttendance = $db->table('attendance_logs')
                            ->select('employee_id, status')
                            ->whereIn('employee_id', $teamContext['employeeIds'])
                            ->where('date', $today)
                            ->get()
                            ->getResultArray();

                        $recordedEmployees = [];
                        $presentCount = 0;
                        $lateCount = 0;
                        $explicitAbsentCount = 0;

                        foreach ($todayAttendance as $row) {
                            $employeeId = (int) ($row['employee_id'] ?? 0);
                            $status = strtolower((string) ($row['status'] ?? ''));

                            $recordedEmployees[$employeeId] = true;

                            if (in_array($status, ['late', 'half-day', 'half day'], true)) {
                                $lateCount++;
                            } elseif ($status === 'absent') {
                                $explicitAbsentCount++;
                            } else {
                                $presentCount++;
                            }
                        }

                        $leaveCount = (int) ($db->table('leave_requests')
                            ->select('COUNT(DISTINCT employee_id) AS total', false)
                            ->whereIn('employee_id', $teamContext['employeeIds'])
                            ->where('status', 'approved')
                            ->where('early_returned_at', null)
                            ->where('start_date <=', $today)
                            ->where('end_date >=', $today)
                            ->get()
                            ->getRow('total') ?? 0);

                        $missingCount = max($data['teamCount'] - count($recordedEmployees) - $leaveCount, 0);

                        $data['teamAttendance'] = [
                            'present' => $presentCount,
                            'late'    => $lateCount,
                            'absent'  => $explicitAbsentCount + $missingCount,
                            'leave'   => $leaveCount,
                        ];

                        $data['pendingTeamLeaves'] = $db->table('leave_requests')
                            ->whereIn('employee_id', $teamContext['employeeIds'])
                            ->where('status', 'pending')
                            ->countAllResults();
                    }

                    if ($teamContext['employeeIds'] !== []) {
                        $db = \Config\Database::connect();

                        $dailyRows = $db->table('employees')
                            ->select("employees.id AS employee_pk_id, employees.employee_id AS employee_code, employees.first_name, employees.last_name, departments.name AS department_name, MIN(attendance_logs.time_in) AS time_in, MAX(attendance_logs.time_out) AS time_out, GROUP_CONCAT(DISTINCT LOWER(attendance_logs.status)) AS statuses", false)
                            ->join('departments', 'departments.id = employees.department_id', 'left')
                            ->join('attendance_logs', "attendance_logs.employee_id = employees.id AND attendance_logs.date = " . $db->escape($selectedDailyAttendanceDate), 'left')
                            ->whereIn('employees.id', $teamContext['employeeIds'])
                            ->where('employees.account_status', 'approved')
                            ->groupStart()
                                ->where('employees.status', 'active')
                                ->orWhere('employees.status IS NULL', null, false)
                                ->orWhere('employees.status', '')
                            ->groupEnd()
                            ->groupBy('employees.id')
                            ->groupBy('employees.employee_id')
                            ->groupBy('employees.first_name')
                            ->groupBy('employees.last_name')
                            ->groupBy('departments.name')
                            ->get()
                            ->getResultArray();

                        $onLeaveRows = $db->table('leave_requests')
                            ->select('employee_id')
                            ->distinct()
                            ->whereIn('employee_id', array_map('intval', array_column($dailyRows, 'employee_pk_id')))
                            ->where('status', 'approved')
                            ->where('early_returned_at', null)
                            ->where('start_date <=', $selectedDailyAttendanceDate)
                            ->where('end_date >=', $selectedDailyAttendanceDate)
                            ->get()
                            ->getResultArray();

                        $onLeaveLookup = [];
                        foreach ($onLeaveRows as $row) {
                            $employeeId = (int) ($row['employee_id'] ?? 0);
                            if ($employeeId > 0) {
                                $onLeaveLookup[$employeeId] = true;
                            }
                        }

                        $normalizedRows = [];
                        foreach ($dailyRows as $row) {
                            $employeePkId = (int) ($row['employee_pk_id'] ?? 0);
                            $statuses = array_filter(array_map('trim', explode(',', (string) ($row['statuses'] ?? ''))));
                            $statusLookup = array_fill_keys($statuses, true);
                            $isOnLeave = isset($onLeaveLookup[$employeePkId]);

                            if ($isOnLeave) {
                                $normalizedStatus = 'Leave';
                            } elseif (isset($statusLookup['absent'])) {
                                $normalizedStatus = 'Absent';
                            } elseif (isset($statusLookup['late']) || isset($statusLookup['half-day']) || isset($statusLookup['half day'])) {
                                $normalizedStatus = 'Late';
                            } elseif ($statuses !== []) {
                                $normalizedStatus = 'Present';
                            } else {
                                $normalizedStatus = 'Absent';
                            }

                            $timeIn = $row['time_in'] ?? null;
                            $timeOut = $row['time_out'] ?? null;
                            $hasAnyLog = ! empty($timeIn) || ! empty($timeOut);

                            $normalizedRows[] = [
                                'employee_name'   => trim(((string) ($row['first_name'] ?? '')) . ' ' . ((string) ($row['last_name'] ?? ''))),
                                'department_name' => (string) ($row['department_name'] ?? 'Unassigned'),
                                'date'            => $selectedDailyAttendanceDate,
                                'time_in'         => $timeIn,
                                'time_out'        => $timeOut,
                                'status'          => $normalizedStatus,
                                'has_log'         => $hasAnyLog,
                                'employee_code'   => (string) ($row['employee_code'] ?? ''),
                            ];
                        }

                        usort($normalizedRows, static function (array $a, array $b) use ($sortBy, $sortDir): int {
                            if ($sortBy === 'employee_name') {
                                $compare = strcmp(strtolower((string) $a['employee_name']), strtolower((string) $b['employee_name']));
                                if ($compare === 0) {
                                    $compare = strcmp(strtolower((string) $a['department_name']), strtolower((string) $b['department_name']));
                                }
                            } else {
                                $compare = strcmp((string) $a['date'], (string) $b['date']);
                                if ($compare === 0) {
                                    $compare = strcmp(strtolower((string) $a['employee_name']), strtolower((string) $b['employee_name']));
                                }
                            }

                            return $sortDir === 'asc' ? $compare : -$compare;
                        });

                        $data['teamDailyAttendanceRecords'] = $normalizedRows;
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Manager Dashboard Error: ' . $e->getMessage());
                    $data['managedDepartments'] = [];
                    $data['managedDepartmentCount'] = 0;
                    $data['teamCount'] = 0;
                    $data['pendingTeamLeaves'] = 0;
                    $data['teamAttendance'] = [
                        'present' => 0,
                        'late'    => 0,
                        'absent'  => 0,
                        'leave'   => 0,
                    ];
                    $data['teamDailyAttendanceDate'] = date('Y-m-d');
                    $data['teamDailyAttendanceSortBy'] = 'date';
                    $data['teamDailyAttendanceSortDir'] = 'desc';
                    $data['teamDailyAttendanceRecords'] = [];
                }
                break;

            default:
                // Employee — get employee record linked to user
                try {
                    $employee = $employeeModel->where('user_id', $session->get('user_id'))->first();
                    if ($employee) {
                        $data['employee'] = $employee;
                        $db = Database::connect();
                        $data['attendanceCount'] = (int) $db->table('attendance_logs')
                            ->where('employee_id', $employee->id)
                            ->countAllResults();
                        $data['attendance'] = $attendanceModel
                            ->where('employee_id', $employee->id)
                            ->orderBy('date', 'DESC')
                            ->orderBy('time_in', 'DESC')
                            ->findAll(10);
                        $data['leaves'] = $leaveModel->where('employee_id', $employee->id)->findAll();
                    } else {
                        // No employee record exists yet - create placeholder data
                        $data['employee'] = null;
                        $data['attendanceCount'] = 0;
                        $data['attendance'] = [];
                        $data['leaves'] = [];
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Dashboard Employee Error: ' . $e->getMessage());
                    $data['employee'] = null;
                    $data['attendanceCount'] = 0;
                    $data['attendance'] = [];
                    $data['leaves'] = [];
                    $data['error'] = 'Unable to load employee data.';
                }
                break;
        }

        return view('auth/dashboard', $data);
    }

    /**
     * Display user profile
     */
    public function profile()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $db = \Config\Database::connect();
        $userId = session()->get('user_id');

        $data['user'] = $userModel->find($userId);

        return view('profile/view', $data);
    }

    /**
     * Update user profile
     */
    public function updateProfile()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $db = \Config\Database::connect();
        $userId = session()->get('user_id');

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'errors' => $errors,
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Update password if provided
        $password = $this->request->getPost('password');
        if (!empty($password) && strlen($password) >= 6) {
            $data['password'] = password_hash(trim($password), PASSWORD_BCRYPT);
        }

        $uploadError = null;
        $photoUploadData = null;

        // Handle profile photo upload
        $photo = $this->request->getFile('profile_photo');
        if ($photo && $photo->getName() !== '') {
            if (!$photo->isValid()) {
                $uploadError = $photo->getErrorString() ?: 'Profile photo upload failed.';
            } else {
                // Validate MIME type and size (max 2MB)
                $allowed = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
                if (!in_array($photo->getClientMimeType(), $allowed, true)) {
                    $uploadError = 'Profile photo must be a PNG, JPG, or WEBP image.';
                } elseif ($photo->getSize() > 2 * 1024 * 1024) {
                    $uploadError = 'Profile photo must be 2MB or smaller.';
                } else {
                    try {
                        $newName = $photo->getRandomName();
                        $uploadPath = FCPATH . 'uploads/profile_photos/';
                        if (!is_dir($uploadPath)) {
                            mkdir($uploadPath, 0755, true);
                        }
                        $photo->move($uploadPath, $newName);
                        $photoPath = 'uploads/profile_photos/' . $newName;
                        $data['profile_photo'] = $photoPath;
                        
                        // Store photo metadata for profile_photos table
                        $photoUploadData = [
                            'file_path' => $photoPath,
                            'original_filename' => $photo->getClientName(),
                            'file_size' => $photo->getSize(),
                            'mime_type' => $photo->getClientMimeType(),
                        ];
                    } catch (\Throwable $e) {
                        $uploadError = 'Unable to save the uploaded photo.';
                        log_message('error', 'Profile photo upload failed: ' . $e->getMessage());
                    }
                }
            }
        }

        if ($uploadError !== null) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'errors' => ['profile_photo' => $uploadError],
                ]);
            }
            return redirect()->back()->withInput()->with('errors', ['profile_photo' => $uploadError]);
        }

        // Update user profile
        try {
            $updateResult = $db->table('users')->where('id', $userId)->update($data);
            if ($updateResult === false) {
                $dbError = $db->error();
                $errorMsg = 'Database error: ' . ($dbError['message'] ?? 'Unknown error');
                log_message('error', 'Profile update failed: ' . $errorMsg);
                
                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(500)->setJSON([
                        'success' => false,
                        'errors' => ['profile' => $errorMsg],
                    ]);
                }
                return redirect()->back()->withInput()->with('errors', ['profile' => $errorMsg]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Profile update exception: ' . $e->getMessage());
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'errors' => ['profile' => 'Unable to update profile.'],
                ]);
            }
            return redirect()->back()->withInput()->with('errors', ['profile' => 'Unable to update profile.']);
        }

        // Save to profile_photos table if photo was uploaded
        if ($photoUploadData !== null) {
            try {
                $profilePhotoModel = new ProfilePhotoModel();
                $profilePhotoModel->saveProfilePhoto(
                    $userId,
                    $photoUploadData['file_path'],
                    $photoUploadData['original_filename'],
                    $photoUploadData['file_size'],
                    $photoUploadData['mime_type']
                );
            } catch (\Exception $e) {
                log_message('error', 'Failed to save profile photo metadata: ' . $e->getMessage());
                // Continue anyway - the photo is still saved in users table
            }
        }

        // Fetch updated user
        $updatedUser = null;
        try {
            $updatedUser = $userModel->find($userId);
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch updated user: ' . $e->getMessage());
        }

        if ($updatedUser) {
            session()->set([
                'name' => $updatedUser->name,
                'email' => $updatedUser->email,
                'profile_photo' => $updatedUser->profile_photo ?? null,
            ]);
        }

        $payload = [
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $updatedUser->id ?? $userId,
                'name' => $updatedUser->name ?? $data['name'],
                'email' => $updatedUser->email ?? $data['email'],
                'profile_photo' => (!empty($updatedUser->profile_photo) ? base_url($updatedUser->profile_photo) : null),
            ],
        ];

        if ($this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setJSON($payload);
        }

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    /**
     * Remove/delete profile photo
     */
    public function removeProfilePhoto()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $profilePhotoModel = new ProfilePhotoModel();
        $db = \Config\Database::connect();
        $userId = session()->get('user_id');

        try {
            $user = $userModel->find($userId);
            if (!$user) {
                if ($this->request->isAJAX()) {
                    return $this->response
                        ->setStatusCode(404)
                        ->setContentType('application/json')
                        ->setJSON([
                            'success' => false,
                            'message' => 'User not found',
                        ]);
                }
                return redirect()->back()->with('error', 'User not found');
            }

            // Delete the physical file if it exists
            if (!empty($user->profile_photo)) {
                $filePath = FCPATH . $user->profile_photo;
                if (is_file($filePath)) {
                    @unlink($filePath);
                }

                // Soft delete matching record in profile_photos table
                try {
                    $profilePhoto = $profilePhotoModel->where('user_id', $userId)
                        ->where('file_path', $user->profile_photo)
                        ->where('deleted_at', null)
                        ->first();
                    
                    if ($profilePhoto) {
                        $profilePhotoModel->softDelete($profilePhoto->id);
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Failed to soft delete profile photo record: ' . $e->getMessage());
                    // Continue anyway - the file is still deleted
                }
            }

            // Clear the profile_photo field in users table
            $updated = $db->table('users')->where('id', $userId)->update([
                'profile_photo' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if ($updated === false) {
                if ($this->request->isAJAX()) {
                    return $this->response
                        ->setStatusCode(500)
                        ->setContentType('application/json')
                        ->setJSON([
                            'success' => false,
                            'message' => 'Failed to remove profile photo',
                        ]);
                }
                return redirect()->back()->with('error', 'Failed to remove profile photo');
            }

            // Update session
            session()->set([
                'profile_photo' => null,
            ]);

            $payload = [
                'success' => true,
                'message' => 'Profile photo removed successfully',
                'profile_photo' => null,
            ];

            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setJSON($payload);
            }

            return redirect()->back()->with('success', 'Profile photo removed successfully');
        } catch (\Exception $e) {
            log_message('error', 'Remove profile photo failed: ' . $e->getMessage());
            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(500)
                    ->setContentType('application/json')
                    ->setJSON([
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage(),
                    ]);
            }
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Debug/Check database profile photo status
     */
    public function checkProfilePhotoStatus()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'error' => 'Not logged in'
            ]);
        }

        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $profilePhotoModel = new ProfilePhotoModel();
        $db = \Config\Database::connect();

        // Check users table
        $userProfilePhoto = $db->table('users')
            ->select('id, profile_photo')
            ->where('id', $userId)
            ->first();

        // Check profile_photos table
        $profilePhotos = $profilePhotoModel->where('user_id', $userId)
            ->where('deleted_at', null)
            ->orderBy('uploaded_at', 'DESC')
            ->findAll();

        // Check if file exists
        $fileStatus = [];
        if ($userProfilePhoto && !empty($userProfilePhoto->profile_photo)) {
            $filePath = FCPATH . $userProfilePhoto->profile_photo;
            $fileStatus = [
                'path' => $userProfilePhoto->profile_photo,
                'exists' => is_file($filePath),
                'file_size' => is_file($filePath) ? filesize($filePath) : 0,
                'last_modified' => is_file($filePath) ? date('Y-m-d H:i:s', filemtime($filePath)) : null,
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'user_id' => $userId,
            'users_table' => [
                'profile_photo_path' => $userProfilePhoto?->profile_photo,
                'has_photo' => !empty($userProfilePhoto?->profile_photo),
            ],
            'profile_photos_table' => [
                'count' => count($profilePhotos),
                'photos' => array_map(function($p) {
                    return [
                        'id' => $p->id,
                        'file_path' => $p->file_path,
                        'file_size' => $p->file_size,
                        'mime_type' => $p->mime_type,
                        'uploaded_at' => $p->uploaded_at,
                    ];
                }, $profilePhotos),
            ],
            'file_system' => $fileStatus,
        ]);
    }
}
