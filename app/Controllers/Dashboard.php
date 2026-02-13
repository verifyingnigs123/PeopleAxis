<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index(): string
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'user' => session()->get(),
        ];

        return view('auth/dashboard', $data);
    }
}
