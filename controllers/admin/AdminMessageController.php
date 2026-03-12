<?php
/**
 * GatewayOS2 Website - Admin Message Controller
 *
 * Manages contact form messages: listing, viewing, marking as read, and deleting.
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/MessageRepository.php';
require_once BASE_DIR . '/lib/services/SessionManager.php';

class AdminMessageController extends Controller
{
    /**
     * List all contact messages.
     */
    public function index(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $repo     = new MessageRepository();
        $messages = $repo->all();

        $this->view('admin/messages/index', [
            'title'    => 'Messages - GatewayOS2',
            'messages' => $messages,
            'flash'    => $this->getFlash(),
        ], 'admin');
    }

    /**
     * View a single message and mark it as read.
     *
     * Route param: :id
     */
    public function show(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $id   = $this->request->param('id');
        $repo = new MessageRepository();
        $message = $repo->find($id);

        if ($message === null) {
            $this->flash('error', 'Message not found.');
            $this->redirect('/admin/messages');
            return;
        }

        // Mark as read when viewed
        if (empty($message['read'])) {
            $repo->markRead($id);
            $message['read'] = true;
        }

        $this->view('admin/messages/show', [
            'title'   => 'View Message - GatewayOS2',
            'message' => $message,
            'flash'   => $this->getFlash(),
        ], 'admin');
    }

    /**
     * Mark a message as read and redirect back to the messages list.
     *
     * Route param: :id
     */
    public function markRead(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $id   = $this->request->param('id');
        $repo = new MessageRepository();

        $repo->markRead($id);

        $this->flash('success', 'Message marked as read.');
        $this->redirect('/admin/messages');
    }

    /**
     * Delete a message.
     *
     * Route param: :id
     */
    public function delete(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $id = $this->request->param('id');

        if (!SessionManager::verifyCsrf()) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/admin/messages');
            return;
        }

        $repo = new MessageRepository();
        $repo->delete($id);

        $this->flash('success', 'Message deleted.');
        $this->redirect('/admin/messages');
    }
}
