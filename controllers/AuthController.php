<?php
/**
 * GatewayOS2 Website - Authentication Controller
 *
 * Handles all authentication flows: login, registration, logout,
 * password reset (forgot/reset), and email verification.
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/AuthService.php';
require_once BASE_DIR . '/lib/services/SessionManager.php';
require_once BASE_DIR . '/lib/helpers/Validator.php';
require_once BASE_DIR . '/lib/helpers/Sanitizer.php';

class AuthController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    // Login
    // ──────────────────────────────────────────────────────────────

    /**
     * Display the login form.
     * Redirects to dashboard if already authenticated.
     */
    public function loginForm(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
            return;
        }

        $this->view('auth/login', [
            'title' => 'Login - GatewayOS2',
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Process the login form submission.
     */
    public function login(): void
    {
        if (!SessionManager::verifyCsrf()) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/login');
            return;
        }

        $username = Sanitizer::trim($this->request->post('username', ''));
        $password = $this->request->post('password', '');

        if ($username === '' || $password === '') {
            $this->flash('error', 'Username and password are required.');
            $this->redirect('/login');
            return;
        }

        $remember = (bool) $this->request->post('remember');
        $result   = AuthService::login($username, $password, $remember);

        if (!$result['success']) {
            $errors = $result['errors'] ?? ['Invalid username or password.'];
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/login');
            return;
        }

        // Redirect to intended URL or dashboard
        $redirect = $this->request->query('redirect', '/dashboard');
        $this->redirect($redirect);
    }

    // ──────────────────────────────────────────────────────────────
    // Registration
    // ──────────────────────────────────────────────────────────────

    /**
     * Display the registration form.
     * Redirects to dashboard if already authenticated.
     */
    public function registerForm(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
            return;
        }

        $this->view('auth/register', [
            'title' => 'Register - GatewayOS2',
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Process the registration form submission.
     */
    public function register(): void
    {
        if (!SessionManager::verifyCsrf()) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/register');
            return;
        }

        $validation = Validator::validate($_POST, [
            'username'         => 'required|min:3|max:30|alpha_num',
            'email'            => 'required|email|max:255',
            'password'         => 'required|min:8',
            'password_confirm' => 'required|matches:password',
        ]);

        if (!$validation['valid']) {
            $errors = Validator::flatten($validation['errors']);
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/register');
            return;
        }

        $result = AuthService::register(
            Sanitizer::trim($this->request->post('username', '')),
            Sanitizer::trim($this->request->post('email', '')),
            $this->request->post('password', ''),
            Sanitizer::trim($this->request->post('display_name', ''))
        );

        if (!$result['success']) {
            $errors = $result['errors'] ?? ['Registration failed.'];
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/register');
            return;
        }

        // Auto-login after registration
        SessionManager::setUser($result['user']);

        $this->flash('success', 'Account created successfully. Please check your email to verify your account.');
        $this->redirect('/dashboard');
    }

    // ──────────────────────────────────────────────────────────────
    // Logout
    // ──────────────────────────────────────────────────────────────

    /**
     * Log the user out and redirect to the login page.
     */
    public function logout(): void
    {
        AuthService::logout();

        $this->flash('success', 'You have been logged out.');
        $this->redirect('/login');
    }

    // ──────────────────────────────────────────────────────────────
    // Forgot Password
    // ──────────────────────────────────────────────────────────────

    /**
     * Display the forgot password form.
     * Supports two steps: email entry and code verification.
     */
    public function forgotForm(): void
    {
        $step = $this->request->query('step', 'email');

        $this->view('auth/forgot_password', [
            'title' => 'Forgot Password - GatewayOS2',
            'step'  => $step,
            'email' => $this->request->query('email', ''),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Process the forgot password form.
     * Handles both steps: sending the reset code and verifying it.
     */
    public function forgot(): void
    {
        if (!SessionManager::verifyCsrf()) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/forgot-password');
            return;
        }

        $step = $this->request->post('step', 'email');

        if ($step === 'email') {
            // Step 1: Send reset code
            $email = Sanitizer::trim($this->request->post('email', ''));

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->flash('error', 'Please enter a valid email address.');
                $this->redirect('/forgot-password');
                return;
            }

            $result = AuthService::requestPasswordReset($email);

            // Always show success to prevent email enumeration
            $this->flash('success', 'If an account exists with that email, a reset code has been sent.');
            $this->redirect('/forgot-password?step=code&email=' . urlencode($email));

        } elseif ($step === 'code') {
            // Step 2: Verify reset code
            $email = Sanitizer::trim($this->request->post('email', ''));
            $code  = Sanitizer::trim($this->request->post('code', ''));

            if (empty($email) || empty($code)) {
                $this->flash('error', 'Email and reset code are required.');
                $this->redirect('/forgot-password?step=code&email=' . urlencode($email));
                return;
            }

            $result = AuthService::verifyResetCode($email, $code);

            if (!$result['success']) {
                $this->flash('error', $result['error'] ?? 'Invalid or expired reset code.');
                $this->redirect('/forgot-password?step=code&email=' . urlencode($email));
                return;
            }

            // Code verified - redirect to reset password form with token
            $this->redirect('/reset-password?token=' . urlencode($result['token']));
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Reset Password
    // ──────────────────────────────────────────────────────────────

    /**
     * Display the password reset form.
     * Validates the reset token before showing the form.
     */
    public function resetForm(): void
    {
        $token        = $this->request->query('token', '');
        $sessionToken = SessionManager::get('reset_token');

        if (empty($token) || !$sessionToken || !hash_equals($sessionToken, $token)) {
            $this->flash('error', 'Invalid or expired reset link. Please request a new one.');
            $this->redirect('/forgot-password');
            return;
        }

        $this->view('auth/reset_password', [
            'title' => 'Reset Password - GatewayOS2',
            'token' => $token,
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Process the password reset form.
     */
    public function reset(): void
    {
        if (!SessionManager::verifyCsrf()) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/forgot-password');
            return;
        }

        $token = $this->request->post('token', '');

        $validation = Validator::validate($_POST, [
            'password'         => 'required|min:8',
            'password_confirm' => 'required|matches:password',
        ]);

        if (!$validation['valid']) {
            $errors = Validator::flatten($validation['errors']);
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/reset-password?token=' . urlencode($token));
            return;
        }

        $result = AuthService::resetPassword($token, $this->request->post('password', ''));

        if (!$result['success']) {
            $this->flash('error', $result['error'] ?? 'Password reset failed.');
            $this->redirect('/forgot-password');
            return;
        }

        $this->flash('success', 'Password has been reset successfully. Please log in with your new password.');
        $this->redirect('/login');
    }

    // ──────────────────────────────────────────────────────────────
    // Email Verification
    // ──────────────────────────────────────────────────────────────

    /**
     * Verify a user's email address using a token from the verification link.
     *
     * Query params:
     *   ?token=xxx
     */
    public function verify(): void
    {
        $token = $this->request->query('token', '');

        if (empty($token)) {
            $this->flash('error', 'No verification token provided.');
            $this->redirect('/login');
            return;
        }

        $success = AuthService::verifyEmail($token);

        if ($success) {
            $this->flash('success', 'Email verified successfully. You can now log in.');
        } else {
            $this->flash('error', 'Invalid or expired verification token.');
        }

        $this->redirect('/login');
    }
}
