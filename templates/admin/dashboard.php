<?php
// Admin dashboard — receives $stats (array), $recent_messages (array), $recent_users (array)
$stats = $stats ?? ['users' => 0, 'posts' => 0, 'unread' => 0, 'views' => 0];
$recent_messages = $recent_messages ?? [];
$recent_users = $recent_users ?? [];
?>
<div class="admin-dashboard">
  <h2>Dashboard</h2>

  <div class="admin-stats-grid">
    <div class="admin-stat-card">
      <div class="admin-stat-number"><?php echo (int)$stats['users']; ?></div>
      <div class="admin-stat-label">Total Users</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-number"><?php echo (int)$stats['posts']; ?></div>
      <div class="admin-stat-label">Blog Posts</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-number"><?php echo (int)$stats['unread']; ?></div>
      <div class="admin-stat-label">Unread Messages</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-number"><?php echo number_format((int)$stats['views']); ?></div>
      <div class="admin-stat-label">Total Page Views</div>
    </div>
  </div>

  <div class="admin-panels">
    <!-- Recent Messages -->
    <div class="admin-panel">
      <div class="admin-panel-header">
        <h3>Recent Messages</h3>
        <a href="/admin/messages" class="admin-panel-link">View all</a>
      </div>
      <?php if (empty($recent_messages)): ?>
        <p class="admin-empty">No messages yet.</p>
      <?php else: ?>
        <table class="admin-table">
          <thead>
            <tr>
              <th>From</th>
              <th>Subject</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_messages as $msg): ?>
              <tr>
                <td><?php echo htmlspecialchars($msg['name']); ?></td>
                <td><a href="/admin/messages/<?php echo htmlspecialchars($msg['id']); ?>"><?php echo htmlspecialchars($msg['subject']); ?></a></td>
                <td><?php echo date('M j', strtotime($msg['created_at'])); ?></td>
                <td>
                  <?php if (empty($msg['read'])): ?>
                    <span class="admin-badge-inline unread">New</span>
                  <?php else: ?>
                    <span class="admin-badge-inline read">Read</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <!-- Recent Users -->
    <div class="admin-panel">
      <div class="admin-panel-header">
        <h3>Recent Signups</h3>
        <a href="/admin/users" class="admin-panel-link">View all</a>
      </div>
      <?php if (empty($recent_users)): ?>
        <p class="admin-empty">No users yet.</p>
      <?php else: ?>
        <table class="admin-table">
          <thead>
            <tr>
              <th>Username</th>
              <th>Email</th>
              <th>Joined</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_users as $u): ?>
              <tr>
                <td><?php echo htmlspecialchars($u['username']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <!-- Quick Links -->
  <div class="admin-quick-links">
    <h3>Quick Links</h3>
    <div class="admin-quick-grid">
      <a href="/admin/blog/new" class="admin-quick-item">New Blog Post</a>
      <a href="/admin/messages" class="admin-quick-item">View Messages</a>
      <a href="/admin/users" class="admin-quick-item">Manage Users</a>
      <a href="/admin/analytics" class="admin-quick-item">View Analytics</a>
      <a href="/settings" class="admin-quick-item">Email Settings</a>
    </div>
  </div>
</div>
