<?php

namespace App\Controllers;

use App\Models\LeaveModel;
use App\Models\AuditModel;

class Leaves extends BaseController
{
    protected $leaveModel;
    protected $auditModel;

    public function __construct()
    {
        $this->leaveModel = new LeaveModel();
        $this->auditModel = new AuditModel();
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

    public function approveByManager($id)
    {
        $session = session();
        $userId = $session->get('user_id');

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
        $userId = $session->get('user_id');

        $this->leaveModel->update($id, [
            'status' => 'rejected',
        ]);

        $this->auditModel->log($userId, 'Leave Rejected', 'Leave id: ' . $id . ' was rejected');
        return redirect()->back()->with('success', 'Leave rejected');
    }
}
