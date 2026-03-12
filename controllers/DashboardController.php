<?php
/**
 * GatewayOS2 Website - Dashboard Controller
 *
 * Authenticated user dashboard for viewing and updating profile settings,
 * changing passwords, and deleting accounts.
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/AuthService.php';
require_once BASE_DIR . '/lib/services/UserRepository.php';
require_once BASE_DIR . '/lib/services/SessionManager.php';
require_once BASE_DIR . '/lib/helpers/Validator.php';
require_once BASE_DIR . '/lib/helpers/Sanitizer.php';

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index(): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $user = $this->currentUser();

        $this->view('auth/dashboard', [
            'title' => 'Dashboard - GatewayOS2',
            'user'  => $user,
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Handle POST actions from the dashboard.
     *
     * Supported actions (via POST 'action' field):
     *   - update_profile:  Update display name
     *   - change_password: Change the user's password
     *   - delete_account:  Permanently delete the user's account
     */
    public function update(): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        if (!SessionManager::verifyCsrf()) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/dashboard');
            return;
        }

        $action = $this->request->post('action', '');
        $user   = $this->currentUser();

        switch ($action) {
            case 'update_profile':
                $this->handleUpdateProfile($user);
                break;

            case 'change_password':
                $this->handleChangePassword($user);
                break;

            case 'delete_account':
                $this->handleDeleteAccount($user);
                break;

            default:
                $this->flash('error', 'Unknown action.');
                $this->redirect('/dashboard');
                break;
        }
    }

    /**
     * Update the user's display name.
     */
    private function handleUpdateProfile(array $user): void
    {
        $displayName = Sanitizer::trim($this->request->post('display_name', ''));

        if ($displayName === '') {
            $this->flash('error', 'Display name cannot be empty.');
            $this->redirect('/dashboard');
            return;
        }

        $updated = UserRepository::update($user['id'], [
            'display_name' => Sanitizer::escape($displayName),
        ]);

        if ($updated !== null) {
            // Update session data to reflect the change
            SessionManager::setUser($updated);
            $this->flash('success', 'Profile updated successfully.');
        } else {
            $this->flash('error', 'Failed to update profile.');
        }

        $this->redirect('/dashboard');
    }

    /**
     * Change the user's password.
     */
    private function handleChangePassword(array $user): void
    {
        $validation = Validator::validate($_POST, [
            'current_password' => 'required',
            'new_password'     => 'required|min:8',
            'confirm_password' => 'required|matches:new_password',
        ]);

        if (!$validation['valid']) {
            $errors = Validator::flatten($validation['errors']);
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/dashboard');
            return;
        }

        $currentPassword = $this->request->post('current_password', '');
        $fullUser = UserRepository::find($user['id']);

        if (!$fullUser || !password_verify($currentPassword, $fullUser['password_hash'])) {
            $this->flash('error', 'Current password is incorrect.');
            $this->redirect('/dashboard');
            return;
        }

        $newPassword = $this->request->post('new_password', '');
        $updated = UserRepository::update($user['id'], [
            'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]),
        ]);

        if ($updated !== null) {
            $this->flash('success', 'Password changed successfully.');
        } else {
            $this->flash('error', 'Failed to change password.');
        }

        $this->redirect('/dashboard');
    }

    /**
     * Permanently delete the user's account.
     */
    private function handleDeleteAccount(array $user): void
    {
        $confirmPassword = $this->request->post('confirm_delete_password', '');

        if (empty($confirmPassword)) {
            $this->flash('error', 'Please enter your password to confirm account deletion.');
            $this->redirect('/dashboard');
            return;
        }

        $fullUser = UserRepository::find($user['id']);

        if (!$fullUser || !password_verify($confirmPassword, $fullUser['password_hash'])) {
            $this->flash('error', 'Incorrect password. Account deletion cancelled.');
            $this->redirect('/dashboard');
            return;
        }

        $deleted = UserRepository::delete($user['id']);

        if (!$deleted) {
            $this->flash('error', 'Failed to delete account.');
            $this->redirect('/dashboard');
            return;
        }

        // Clear session
        AuthService::logout();

        $this->flash('success', 'Your account has been permanently deleted.');
        $this->redirect('/');
    }
}
