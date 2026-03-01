<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // If user is already logged in, redirect to dashboard
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
        
        return view('page/home');
    }
}
