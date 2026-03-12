<?php
// Dashboard — receives $user (array), $flash (array|null)
$flash = $flash ?? null;
?>
  <section class="hero" style="padding: 4rem 3rem 2rem;">
    <div class="hero-content">
      <p class="overline">Dashboard</p>
      <h1>Welcome,<br><em><?php echo htmlspecialchars($user['display_name']); ?></em></h1>
    </div>
  </section>

  <section class="alt">
    <div class="container">
      <?php if ($flash): ?>
        <div class="auth-alert <?php echo htmlspecialchars($flash['type']); ?>" style="max-width: 600px; margin: 0 auto 2rem;">
          <p><?php echo htmlspecialchars($flash['message']); ?></p>
        </div>
      <?php endif; ?>

      <?php if (isset($user['email_verified']) && !$user['email_verified']): ?>
        <div class="auth-alert" style="max-width: 600px; margin: 0 auto 2rem; background: rgba(219,180,46,0.08); border-left: 3px solid #db4;">
          <p style="color: var(--smoke); font-size: 0.85rem;">Your email is not yet verified. Check your inbox for the verification link.</p>
        </div>
      <?php endif; ?>

      <div class="dashboard-grid">
        <!-- Profile Card -->
        <div class="dash-card">
          <div class="dash-card-header">
            <div class="avatar"><?php echo strtoupper(substr($user['display_name'], 0, 1)); ?></div>
            <div>
              <h3><?php echo htmlspecialchars($user['display_name']); ?></h3>
              <p class="dash-meta">@<?php echo htmlspecialchars($user['username']); ?></p>
            </div>
          </div>
          <div class="dash-card-body">
            <div class="dash-info-row">
              <span class="dash-label">Email</span>
              <span class="dash-value"><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            <div class="dash-info-row">
              <span class="dash-label">Member since</span>
              <span class="dash-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
            </div>
          </div>
        </div>

        <!-- Edit Profile -->
        <div class="dash-card">
          <h3 class="dash-card-title">Edit Profile</h3>
          <form method="POST" action="/dashboard" class="auth-form compact">
            <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
            <input type="hidden" name="action" value="update_profile">
            <div class="form-group">
              <label for="display_name">Display Name</label>
              <input type="text" id="display_name" name="display_name"
                     value="<?php echo htmlspecialchars($user['display_name']); ?>"
                     maxlength="50" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Save Changes</button>
          </form>
        </div>

        <!-- Change Password -->
        <div class="dash-card">
          <h3 class="dash-card-title">Change Password</h3>
          <form method="POST" action="/dashboard" class="auth-form compact">
            <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
            <input type="hidden" name="action" value="change_password">
            <div class="form-group">
              <label for="current_password">Current Password</label>
              <input type="password" id="current_password" name="current_password" required
                     autocomplete="current-password">
            </div>
            <div class="form-group">
              <label for="new_password">New Password</label>
              <input type="password" id="new_password" name="new_password" required
                     minlength="8" autocomplete="new-password">
            </div>
            <div class="form-group">
              <label for="confirm_password">Confirm New Password</label>
              <input type="password" id="confirm_password" name="confirm_password" required
                     minlength="8" autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Update Password</button>
          </form>
        </div>

        <!-- Danger Zone -->
        <div class="dash-card danger">
          <h3 class="dash-card-title">Danger Zone</h3>
          <p style="font-size: 0.85rem; color: var(--smoke); margin-bottom: 1rem;">Permanently delete your account. This action cannot be undone.</p>
          <button class="btn btn-danger" onclick="document.getElementById('delete-modal').style.display='flex'">Delete Account</button>
        </div>
      </div>
    </div>
  </section>

  <!-- Delete Confirmation Modal -->
  <div class="modal-overlay" id="delete-modal" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal-content">
      <h3>Delete Account</h3>
      <p>Enter your password to permanently delete your account. All data will be lost.</p>
      <form method="POST" action="/dashboard" class="auth-form compact">
        <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
        <input type="hidden" name="action" value="delete_account">
        <div class="form-group">
          <label for="confirm_delete_password">Password</label>
          <input type="password" id="confirm_delete_password" name="confirm_delete_password" required
                 autocomplete="current-password">
        </div>
        <div style="display: flex; gap: 1rem;">
          <button type="button" class="btn btn-secondary" style="flex:1;" onclick="document.getElementById('delete-modal').style.display='none'">Cancel</button>
          <button type="submit" class="btn btn-danger" style="flex:1;">Delete Forever</button>
        </div>
      </form>
    </div>
  </div>
