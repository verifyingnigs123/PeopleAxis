<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ActiveUserFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return null;
        }

        $userId = (int) $session->get('user_id');

        // A logged-in session without a valid user ID is considered invalid.
        if ($userId <= 0) {
            $session->destroy();
            return $this->buildLogoutResponse($request);
        }

        $userModel = new UserModel();
        $user = $userModel->select('id, is_active, deleted_at')->find($userId);

        if ($user && (int) $user->is_active === 1 && empty($user->deleted_at)) {
            return null;
        }

        $session->destroy();

        return $this->buildLogoutResponse($request);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }

    private function buildLogoutResponse(RequestInterface $request)
    {
        $path = ltrim((string) $request->getUri()->getPath(), '/');
        $accept = strtolower((string) $request->getHeaderLine('Accept'));

        $isApiRequest = strncmp($path, 'api/', 4) === 0;
        $expectsJson = strpos($accept, 'application/json') !== false;

        if ($request->isAJAX() || $isApiRequest || $expectsJson) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'success'      => false,
                    'force_logout' => true,
                    'redirect'     => site_url('/login'),
                    'message'      => 'Your account has been removed or deactivated. Please sign in again.',
                ]);
        }

        return redirect()->to('/login')->with('error', 'Your account has been removed or deactivated. You have been logged out.');
    }
}