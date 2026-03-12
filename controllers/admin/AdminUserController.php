<?php
/**
 * GatewayOS2 Website - Admin User Controller
 *
 * Manages user accounts: listing, role updates, and deletion.
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/UserRepository.php';
require_once BASE_DIR . '/lib/services/SessionManager.php';

class AdminUserController extends Controller
{
    /**
     * List all registered users.
     */
    public function index(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $repo  = new UserRepository();
        $users = $repo->all();

        $this->view('admin/users/index', [
            'title' => 'Manage Users - GatewayOS2',
            'users' => $users,
            'flash' => $this->getFlash(),
        ], 'admin');
    }

    /**
     * Update a user's role.
     *
     * Route param: :id
     * POST field: role (e.g. 'admin', 'user')
     */
    public function updateRole(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        if (!SessionManager::verifyCsrf()) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/admin/users');
            return;
        }

        $id   = $this->request->param('id');
        $role = $this->request->post('role', '');

        // Validate role
        $allowedRoles = ['user', 'admin'];
        if (!in_array($role, $allowedRoles, true)) {
            $this->flash('error', 'Invalid role specified.');
            $this->redirect('/admin/users');
            return;
        }

        $repo = new UserRepository();
        $user = $repo->find($id);

        if ($user === null) {
            $this->flash('error', 'User not found.');
            $this->redirect('/admin/users');
            return;
        }

        $repo->update($id, ['role' => $role]);

        $this->flash('success', 'User role updated to "' . htmlspecialchars($role) . '".');
        $this->redirect('/admin/users');
    }

    /**
     * Delete a user account.
     * Prevents admins from deleting their own account through this interface.
     *
     * Route param: :id
     */
    public function delete(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        if (!SessionManager::verifyCsrf()) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/admin/users');
            return;
        }

        $id          = $this->request->param('id');
        $currentUser = $this->currentUser();

        // Prevent self-deletion through admin panel
        if ($id === ($currentUser['id'] ?? null)) {
            $this->flash('error', 'You cannot delete your own account from the admin panel. Use the dashboard instead.');
            $this->redirect('/admin/users');
            return;
        }

        $repo = new UserRepository();
        $user = $repo->find($id);

        if ($user === null) {
            $this->flash('error', 'User not found.');
            $this->redirect('/admin/users');
            return;
        }

        $repo->delete($id);

        $this->flash('success', 'User account deleted.');
        $this->redirect('/admin/users');
    }
}
