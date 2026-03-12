<?php
// Login form — receives $errors (array), $old_username (string), $flash (array|null)
$errors = $errors ?? [];
$old_username = $old_username ?? '';
$flash = $flash ?? null;
?>
  <section class="hero" style="padding: 4rem 3rem 2rem;">
    <div class="hero-content">
      <p class="overline">Welcome Back</p>
      <h1>Sign <em>in</em></h1>
    </div>
  </section>

  <section class="alt">
    <div class="container">
      <div class="auth-card">
        <?php if ($flash): ?>
          <div class="auth-alert <?php echo htmlspecialchars($flash['type']); ?>">
            <p><?php echo htmlspecialchars($flash['message']); ?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
          <div class="auth-alert error">
            <?php foreach ($errors as $error): ?>
              <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="/login<?php echo isset($redirect) ? '?redirect=' . urlencode($redirect) : ''; ?>" class="auth-form" autocomplete="on">
          <?php if (function_exists('csrf_field')) echo csrf_field(); ?>

          <div class="form-group">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" required
                   value="<?php echo htmlspecialchars($old_username); ?>"
                   placeholder="Enter your username or email"
                   autocomplete="username">
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <div class="password-wrapper">
              <input type="password" id="password" name="password" required
                     placeholder="Enter your password"
                     autocomplete="current-password">
              <button type="button" class="toggle-password" onclick="togglePassword('password', this)" aria-label="Show password">
                <span class="eye-icon">Show</span>
              </button>
            </div>
          </div>

          <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
            <label class="checkbox-label">
              <input type="checkbox" name="remember" checked>
              <span class="checkmark"></span>
              Remember me for <?php echo defined('REMEMBER_DAYS') ? REMEMBER_DAYS : 30; ?> days
            </label>
            <a href="/forgot-password" style="font-size: 0.8rem;">Forgot password?</a>
          </div>

          <button type="submit" class="btn btn-primary auth-submit">Sign In</button>
        </form>

        <div class="auth-footer">
          <p>Don't have an account? <a href="/register">Create one</a></p>
        </div>
      </div>
    </div>
  </section>

  <script>
  function togglePassword(id, btn) {
    var input = document.getElementById(id);
    var isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    btn.querySelector('.eye-icon').textContent = isPassword ? 'Hide' : 'Show';
  }
  </script>
