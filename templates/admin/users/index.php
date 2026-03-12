<?php
// User management — receives $users (array), $current_user_id (string)
$users = $users ?? [];
$current_user_id = $current_user_id ?? '';
?>
<div class="admin-section">
  <div class="admin-section-header">
    <h2>Users</h2>
  </div>

  <?php if (empty($users)): ?>
    <p class="admin-empty">No users registered.</p>
  <?php else: ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Username</th>
          <th>Display Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Joined</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
            <td><?php echo htmlspecialchars($u['display_name'] ?? $u['username']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td>
              <?php if ($u['id'] === $current_user_id): ?>
                <span class="admin-tag"><?php echo htmlspecialchars($u['role'] ?? 'user'); ?></span>
                <span style="font-size: 0.75rem; color: var(--smoke);">(you)</span>
              <?php else: ?>
                <form method="POST" action="/admin/users/role/<?php echo htmlspecialchars($u['id']); ?>" style="display: inline;">
                  <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
                  <select name="role" class="admin-role-select" onchange="this.form.submit()">
                    <option value="user" <?php echo ($u['role'] ?? 'user') === 'user' ? 'selected' : ''; ?>>user</option>
                    <option value="admin" <?php echo ($u['role'] ?? 'user') === 'admin' ? 'selected' : ''; ?>>admin</option>
                  </select>
                </form>
              <?php endif; ?>
            </td>
            <td><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
            <td class="admin-actions">
              <?php if ($u['id'] !== $current_user_id): ?>
                <form method="POST" action="/admin/users/delete/<?php echo htmlspecialchars($u['id']); ?>" style="display:inline;" onsubmit="return confirm('Delete user <?php echo htmlspecialchars($u['username']); ?>? This cannot be undone.')">
                  <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
                  <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
              <?php else: ?>
                <span style="color: var(--smoke); font-size: 0.8rem;">-</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
