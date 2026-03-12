<?php
// Reset password — receives $errors (array), $success (bool), $valid_token (bool), $token_raw (string)
$errors = $errors ?? [];
$success = $success ?? false;
$valid_token = $valid_token ?? false;
$token_raw = $token_raw ?? '';
?>
  <section class="hero" style="padding: 4rem 3rem 2rem;">
    <div class="hero-content">
      <p class="overline">Account Recovery</p>
      <h1>Reset your<br><em>password</em></h1>
    </div>
  </section>

  <section class="alt">
    <div class="container">
      <div class="auth-card">
        <?php if ($success): ?>
          <div class="auth-alert success">
            <p>Password has been reset successfully.</p>
          </div>
          <br>
          <a href="/login" class="btn btn-primary" style="width: 100%; justify-content: center;">Sign In</a>

        <?php elseif (!$valid_token): ?>
          <div class="auth-alert error">
            <p>This reset link is invalid or has expired.</p>
          </div>
          <div class="auth-footer" style="border: none; padding-top: 0;">
            <p><a href="/forgot-password">Request a new code</a></p>
          </div>

        <?php else: ?>
          <?php if (!empty($errors)): ?>
            <div class="auth-alert error">
              <?php foreach ($errors as $e): ?><p><?php echo htmlspecialchars($e); ?></p><?php endforeach; ?>
            </div>
          <?php endif; ?>

          <div class="auth-alert success">
            <p>Code verified! Choose a new password.</p>
          </div>

          <form method="POST" action="/reset-password?token=<?php echo urlencode($token_raw); ?>" class="auth-form">
            <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
            <div class="form-group">
              <label for="password">New Password <span class="required">*</span></label>
              <div class="password-wrapper">
                <input type="password" id="password" name="password" required
                       minlength="8" placeholder="At least 8 characters"
                       autocomplete="new-password">
                <button type="button" class="toggle-password" onclick="toggleBothPasswords()" aria-label="Show password">
                  <span class="eye-icon">Show</span>
                </button>
              </div>
            </div>
            <div class="form-group">
              <label for="password_confirm">Confirm Password <span class="required">*</span></label>
              <div class="password-wrapper">
                <input type="password" id="password_confirm" name="password_confirm" required
                       minlength="8" placeholder="Type password again"
                       autocomplete="new-password">
                <button type="button" class="toggle-password" onclick="toggleBothPasswords()" aria-label="Show password">
                  <span class="eye-icon">Show</span>
                </button>
              </div>
            </div>
            <button type="submit" class="btn btn-primary auth-submit">Reset Password</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <script>
  function toggleBothPasswords() {
    var pw = document.getElementById('password');
    var pc = document.getElementById('password_confirm');
    if (!pw) return;
    var isHidden = pw.type === 'password';
    var newType = isHidden ? 'text' : 'password';
    var label = isHidden ? 'Hide' : 'Show';
    pw.type = newType;
    if (pc) pc.type = newType;
    document.querySelectorAll('.toggle-password .eye-icon').forEach(function(el) {
      el.textContent = label;
    });
  }
  </script>
