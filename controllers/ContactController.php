<?php
/**
 * GatewayOS2 Website - Contact Controller
 *
 * Displays the contact form and processes form submissions
 * with CSRF verification, input validation, honeypot check,
 * and optional Cloudflare Turnstile captcha verification.
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/MessageRepository.php';
require_once BASE_DIR . '/lib/helpers/Validator.php';
require_once BASE_DIR . '/lib/helpers/Sanitizer.php';
require_once BASE_DIR . '/lib/services/SessionManager.php';

class ContactController extends Controller
{
    /**
     * Display the contact form.
     * Pre-fills name and email if the user is logged in.
     */
    public function form(): void
    {
        $prefill = [];

        if ($this->isAuthenticated()) {
            $user = $this->currentUser();
            $prefill['name']  = $user['display_name'] ?? $user['username'] ?? '';
            $prefill['email'] = $user['email'] ?? '';
        }

        $this->view('pages/contact/form', [
            'title'   => 'Contact - GatewayOS2',
            'prefill' => $prefill,
            'flash'   => $this->getFlash(),
        ]);
    }

    /**
     * Process the contact form submission.
     */
    public function submit(): void
    {
        // Verify CSRF token
        if (!SessionManager::verifyCsrf()) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/contact');
            return;
        }

        // Honeypot check - if the hidden field is filled, it's a bot
        $honeypot = $this->request->post('website', '');
        if (!empty($honeypot)) {
            // Silently redirect to avoid revealing the honeypot
            $this->flash('success', 'Your message has been sent.');
            $this->redirect('/contact');
            return;
        }

        // Validate required fields
        $validation = Validator::validate($_POST, [
            'name'    => 'required|max:100',
            'email'   => 'required|email|max:255',
            'subject' => 'required|max:200',
            'message' => 'required|min:10|max:5000',
        ]);

        if (!$validation['valid']) {
            $errors = Validator::flatten($validation['errors']);
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/contact');
            return;
        }

        // Verify Cloudflare Turnstile if configured
        if (defined('TURNSTILE_SECRET_KEY') && TURNSTILE_SECRET_KEY !== '') {
            $turnstileResponse = $this->request->post('cf-turnstile-response', '');
            if (!$this->verifyTurnstile($turnstileResponse)) {
                $this->flash('error', 'Captcha verification failed. Please try again.');
                $this->redirect('/contact');
                return;
            }
        }

        // Sanitize and save the message
        $messageData = [
            'name'    => Sanitizer::escape(Sanitizer::trim($this->request->post('name', ''))),
            'email'   => Sanitizer::trim($this->request->post('email', '')),
            'subject' => Sanitizer::escape(Sanitizer::trim($this->request->post('subject', ''))),
            'message' => Sanitizer::escape(Sanitizer::trim($this->request->post('message', ''))),
            'ip'      => $this->request->ip(),
        ];

        $repo = new MessageRepository();
        $repo->create($messageData);

        $this->flash('success', 'Your message has been sent successfully. We will get back to you soon.');
        $this->redirect('/contact');
    }

    /**
     * Verify a Cloudflare Turnstile response token.
     *
     * @param string $token The cf-turnstile-response token from the form.
     * @return bool True if verification succeeded.
     */
    private function verifyTurnstile(string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $data = [
            'secret'   => TURNSTILE_SECRET_KEY,
            'response' => $token,
            'remoteip' => $this->request->ip(),
        ];

        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query($data),
                'timeout' => 5,
            ],
        ];

        $context  = stream_context_create($options);
        $response = @file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $context);

        if ($response === false) {
            error_log('ContactController: Turnstile verification request failed');
            return false;
        }

        $result = json_decode($response, true);
        return isset($result['success']) && $result['success'] === true;
    }
}
