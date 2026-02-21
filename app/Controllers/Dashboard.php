<?php

namespace App\Controllers;

use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();

        $data = [
            'user' => session()->get(),
            'totalUsers' => $userModel->countAll(),
            'adminCount' => $userModel->where('role', 'admin')->countAllResults(),
            'activeUsers' => $userModel->where('is_active', 1)->countAllResults(),
            'inactiveUsers' => $userModel->where('is_active', 0)->countAllResults(),
        ];

        return view('auth/dashboard', $data);
    }
}
