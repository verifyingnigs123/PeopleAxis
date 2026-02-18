<?php

namespace App\Controllers;

class Settings extends BaseController
{
    public function index(): string
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

        $rules = [
            'site_name' => 'required|min_length[3]',
            'site_url' => 'required',
            'timezone' => 'required',
            'date_format' => 'required',
            'maintenance_mode' => 'required',
        ];

        if (!$this->validate($rules)) {
            return back()->with('errors', $this->validator->getErrors());
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

        // Here you would save to database or config file
        session()->setFlashdata('success', 'Settings updated successfully!');

        return redirect()->to('/settings');
    }
}
