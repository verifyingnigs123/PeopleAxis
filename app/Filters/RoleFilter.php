<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (empty($arguments) || !is_array($arguments)) {
            return null;
        }

        $roleName = $session->get('role_name') ?? '';
        if (!in_array($roleName, $arguments)) {
            // unauthorized
            return redirect()->to('/dashboard')->with('error', 'Unauthorized');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
