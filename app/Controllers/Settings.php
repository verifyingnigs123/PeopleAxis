<?php

namespace App\Controllers;

class Settings extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = model('UserModel');
    }

    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'user' => session()->get(),
            'role' => session()->get('role'),
        ];

        return view('auth/settings', $data);
    }

    public function update()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $currentUserId = (int) session()->get('user_id');
        $currentUser = $currentUserId > 0 ? $this->userModel->find($currentUserId) : null;

        if (!$currentUser) {
            return redirect()->to('/login')->with('error', 'Your session is no longer valid. Please log in again.');
        }

        $rules = [
            'site_name' => 'required|min_length[3]',
            'site_url' => 'required',
            'timezone' => 'required',
            'date_format' => 'required',
            'maintenance_mode' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword = (string) $this->request->getPost('new_password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');

        $passwordChangeRequested = $currentPassword !== '' || $newPassword !== '' || $confirmPassword !== '';

        if ($passwordChangeRequested) {
            $passwordErrors = [];

            if ($currentPassword === '') {
                $passwordErrors['current_password'] = 'Current password is required to change your password.';
            } elseif (!$this->userModel->verifyPassword($currentPassword, (string) $currentUser->password)) {
                $passwordErrors['current_password'] = 'Current password is incorrect.';
            }

            if ($newPassword === '') {
                $passwordErrors['new_password'] = 'New password is required.';
            } elseif (strlen(trim($newPassword)) < 6) {
                $passwordErrors['new_password'] = 'New password must be at least 6 characters.';
            }

            if ($confirmPassword === '') {
                $passwordErrors['confirm_password'] = 'Please confirm your new password.';
            } elseif ($newPassword !== $confirmPassword) {
                $passwordErrors['confirm_password'] = 'New password confirmation does not match.';
            }

            if (!empty($passwordErrors)) {
                return redirect()->back()->withInput()->with('errors', $passwordErrors);
            }
        }

        $settings = [
            'site_name' => $this->request->getPost('site_name'),
            'site_url' => $this->request->getPost('site_url'),
            'timezone' => $this->request->getPost('timezone'),
            'date_format' => $this->request->getPost('date_format'),
            'maintenance_mode' => $this->request->getPost('maintenance_mode'),
            'items_per_page' => $this->request->getPost('items_per_page'),
            'enable_notifications' => $this->request->getPost('enable_notifications') ?? 0,
            'enable_email_notifications' => $this->request->getPost('enable_email_notifications') ?? 0,
        ];

        if ($passwordChangeRequested) {
            $this->userModel->update($currentUserId, [
                'password' => $newPassword,
            ]);
            session()->setFlashdata('success', 'Settings updated successfully and your password was changed.');
            return redirect()->to('/settings');
        }

        // Here you would save to database or config file
        session()->setFlashdata('success', 'Settings updated successfully!');

        return redirect()->to('/settings');
    }
}
