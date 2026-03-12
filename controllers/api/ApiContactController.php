<?php
/**
 * GatewayOS2 Website - API Contact Controller
 *
 * Accepts contact form submissions as JSON POST requests,
 * validates the input, and saves the message.
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/MessageRepository.php';
require_once BASE_DIR . '/lib/helpers/Validator.php';
require_once BASE_DIR . '/lib/helpers/Sanitizer.php';

class ApiContactController extends Controller
{
    /**
     * Process a JSON contact form submission.
     *
     * Expects JSON body:
     *   { "name": "...", "email": "...", "subject": "...", "message": "..." }
     */
    public function submit(): void
    {
        $data = $this->request->json();

        if ($data === null) {
            $this->json(['success' => false, 'error' => 'Invalid JSON payload.'], 400);
            return;
        }

        // Validate required fields
        $validation = Validator::validate($data, [
            'name'    => 'required|max:100',
            'email'   => 'required|email|max:255',
            'subject' => 'required|max:200',
            'message' => 'required|min:10|max:5000',
        ]);

        if (!$validation['valid']) {
            $errors = Validator::flatten($validation['errors']);
            $this->json([
                'success' => false,
                'errors'  => $errors,
            ], 422);
            return;
        }

        // Sanitize input
        $messageData = [
            'name'    => Sanitizer::escape(Sanitizer::trim($data['name'] ?? '')),
            'email'   => Sanitizer::trim($data['email'] ?? ''),
            'subject' => Sanitizer::escape(Sanitizer::trim($data['subject'] ?? '')),
            'message' => Sanitizer::escape(Sanitizer::trim($data['message'] ?? '')),
            'ip'      => $this->request->ip(),
            'source'  => 'api',
        ];

        $repo = new MessageRepository();
        $repo->create($messageData);

        $this->json([
            'success' => true,
            'message' => 'Your message has been received. We will get back to you soon.',
        ], 201);
    }
}
