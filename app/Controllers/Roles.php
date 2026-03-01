<?php

namespace App\Controllers;

use App\Models\RoleModel;

class Roles extends BaseController
{
    protected $roleModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
    }

    /**
     * Display all roles (including deleted)
     */
    public function index()
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        // Get all roles (active and deleted)
        $db = \Config\Database::connect();
        $roles = $db->table('roles')
            ->orderBy('deleted_at', 'ASC') // Active roles first, then deleted
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResult();

        $data['roles'] = $roles;
        return view('auth/roles', $data);
    }

    /**
     * Show create role form
     */
    public function create()
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        return view('auth/create_role');
    }

    /**
     * Store new role
     */
    public function store()
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[50]|is_unique[roles.name]',
            'description' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->roleModel->insert([
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/roles')->with('success', 'Role created successfully!');
    }

    /**
     * Show edit role form
     */
    public function edit($id)
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        $role = $this->roleModel->find($id);
        if (!$role) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Role not found");
        }

        $data['role'] = $role;
        return view('auth/edit_role', $data);
    }

    /**
     * Update role (AJAX)
     */
    public function update($id)
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin only.'
            ]);
        }

        $role = $this->roleModel->find($id);
        if (!$role) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Role not found'
            ]);
        }

        // Prevent editing of default roles
        if (in_array($role->name, ['Super Admin', 'admin', 'employee', 'user'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Cannot edit default system roles'
            ]);
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[50]',
            'description' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $this->roleModel->update($id, [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Role updated successfully',
            'csrf_hash' => csrf_hash()
        ]);
    }

    /**
     * Delete role (soft delete)
     */
    public function delete($id)
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin only.'
            ]);
        }

        $role = $this->roleModel->find($id);
        if (!$role) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Role not found'
            ]);
        }

        // Prevent deletion of default roles
        if (in_array($role->name, ['Super Admin', 'admin', 'employee', 'user'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Cannot delete default system roles'
            ]);
        }

        // Soft delete - update deleted_at timestamp
        try {
            $this->roleModel->update($id, [
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Role deleted successfully',
                'status' => 'DELETED',
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Delete role error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to delete role: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Restore a soft-deleted role
     */
    public function restore($id)
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin only.'
            ]);
        }

        $role = $this->roleModel->find($id);
        if (!$role) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Role not found'
            ]);
        }

        try {
            $this->roleModel->update($id, [
                'deleted_at' => null,
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Role restored successfully',
                'status' => 'RESTORED',
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Restore role error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to restore role: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get a single role for AJAX
     */
    public function getRole($id)
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        $role = $this->roleModel->find($id);
        if (!$role) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Role not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $role
        ]);
    }
}
