<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Helpers\PrivilegeHelper;

class PrivilegeFilter implements FilterInterface
{
    /**
     * Validate required privileges
     * Usage in routes: /path:(:any) => ['filter' => 'privilege:users_view,users_create']
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (empty($arguments) || !is_array($arguments)) {
            return null;
        }

        $privilegeHelper = new PrivilegeHelper();
        $requiredPrivileges = $arguments;

        // Check if user has ANY of the required privileges
        $hasPrivilege = false;
        foreach ($requiredPrivileges as $privilege) {
            if ($privilegeHelper->hasPrivilege($privilege)) {
                $hasPrivilege = true;
                break;
            }
        }

        if (!$hasPrivilege) {
            // Check if user is super admin (fallback)
            $role = strtolower((string) $session->get('role'));
            $roleName = strtolower((string) $session->get('role_name'));
            
            $isSuperAdmin = in_array($role, ['admin', 'super_admin'], true) || $roleName === 'super admin';
            
            if (!$isSuperAdmin) {
                return redirect()->to('/dashboard')->with('error', 'Insufficient permissions for this action.');
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
