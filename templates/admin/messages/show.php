<?php
// Single message view — receives $message (array)
?>
<div class="admin-section">
  <div class="admin-section-header">
    <h2>Message</h2>
    <a href="/admin/messages" class="btn btn-secondary">Back to Inbox</a>
  </div>

  <div class="admin-message-view">
    <div class="admin-message-header">
      <div class="admin-message-meta">
        <div class="admin-meta-row">
          <span class="admin-meta-label">From</span>
          <span class="admin-meta-value"><?php echo htmlspecialchars($message['name']); ?> &lt;<?php echo htmlspecialchars($message['email']); ?>&gt;</span>
        </div>
        <div class="admin-meta-row">
          <span class="admin-meta-label">Subject</span>
          <span class="admin-meta-value"><strong><?php echo htmlspecialchars($message['subject']); ?></strong></span>
        </div>
        <div class="admin-meta-row">
          <span class="admin-meta-label">Date</span>
          <span class="admin-meta-value"><?php echo date('F j, Y \a\t g:i A', strtotime($message['created_at'])); ?></span>
        </div>
        <div class="admin-meta-row">
          <span class="admin-meta-label">IP</span>
          <span class="admin-meta-value"><?php echo htmlspecialchars($message['ip'] ?? 'unknown'); ?></span>
        </div>
        <?php if (!empty($message['user_agent'])): ?>
          <div class="admin-meta-row">
            <span class="admin-meta-label">User Agent</span>
            <span class="admin-meta-value" style="font-size: 0.8rem; word-break: break-all;"><?php echo htmlspecialchars($message['user_agent']); ?></span>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="admin-message-body">
      <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
    </div>

    <div class="admin-message-actions">
      <?php if (empty($message['read'])): ?>
        <form method="POST" action="/admin/messages/<?php echo htmlspecialchars($message['id']); ?>/read" style="display: inline;">
          <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
          <button type="submit" class="btn btn-primary">Mark as Read</button>
        </form>
      <?php else: ?>
        <span class="admin-badge-inline read" style="padding: 0.5rem 1rem;">Already read</span>
      <?php endif; ?>

      <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>?subject=Re: <?php echo rawurlencode($message['subject']); ?>" class="btn btn-secondary">Reply via Email</a>

      <form method="POST" action="/admin/messages/delete/<?php echo htmlspecialchars($message['id']); ?>" style="display: inline;" onsubmit="return confirm('Permanently delete this message?')">
        <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
        <button type="submit" class="btn btn-danger">Delete</button>
      </form>
    </div>
  </div>
</div>
