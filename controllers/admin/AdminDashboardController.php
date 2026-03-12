<?php
/**
 * GatewayOS2 Website - Admin Dashboard Controller
 *
 * Displays the admin overview with aggregate statistics
 * for users, blog posts, messages, and site analytics.
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/UserRepository.php';
require_once BASE_DIR . '/lib/services/BlogRepository.php';
require_once BASE_DIR . '/lib/services/MessageRepository.php';
require_once BASE_DIR . '/lib/services/AnalyticsService.php';

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard with summary statistics.
     */
    public function index(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $userRepo    = new UserRepository();
        $blogRepo    = new BlogRepository();
        $messageRepo = new MessageRepository();
        $analytics   = new AnalyticsService();

        $stats = [
            'user_count'      => count($userRepo->all()),
            'post_count'      => count($blogRepo->all()),
            'unread_messages' => $messageRepo->unreadCount(),
            'analytics'       => $analytics->summary(),
        ];

        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard - GatewayOS2',
            'stats' => $stats,
            'flash' => $this->getFlash(),
        ], 'admin');
    }
}
