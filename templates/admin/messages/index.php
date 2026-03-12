<?php
// Admin messages inbox — receives $messages (array)
$messages = $messages ?? [];
?>
<div class="admin-section">
  <div class="admin-section-header">
    <h2>Messages</h2>
  </div>

  <?php if (empty($messages)): ?>
    <p class="admin-empty">No messages received yet.</p>
  <?php else: ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Status</th>
          <th>Name</th>
          <th>Email</th>
          <th>Subject</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($messages as $msg): ?>
          <tr class="<?php echo empty($msg['read']) ? 'admin-row-unread' : ''; ?>">
            <td>
              <?php if (empty($msg['read'])): ?>
                <span class="admin-badge-inline unread">New</span>
              <?php else: ?>
                <span class="admin-badge-inline read">Read</span>
              <?php endif; ?>
            </td>
            <td><strong><?php echo htmlspecialchars($msg['name']); ?></strong></td>
            <td><?php echo htmlspecialchars($msg['email']); ?></td>
            <td><a href="/admin/messages/<?php echo htmlspecialchars($msg['id']); ?>"><?php echo htmlspecialchars($msg['subject']); ?></a></td>
            <td><?php echo date('M j, Y g:ia', strtotime($msg['created_at'])); ?></td>
            <td class="admin-actions">
              <a href="/admin/messages/<?php echo htmlspecialchars($msg['id']); ?>" class="btn btn-secondary btn-sm">View</a>
              <form method="POST" action="/admin/messages/delete/<?php echo htmlspecialchars($msg['id']); ?>" style="display:inline;" onsubmit="return confirm('Delete this message?')">
                <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
