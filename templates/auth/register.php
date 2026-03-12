<?php
// Register form — receives $errors (array), $old (array with username, email, display_name)
$errors = $errors ?? [];
$old = $old ?? ['username' => '', 'email' => '', 'display_name' => ''];
?>
  <section class="hero" style="padding: 4rem 3rem 2rem;">
    <div class="hero-content">
      <p class="overline">Join</p>
      <h1>Create your<br><em>account</em></h1>
    </div>
  </section>

  <section class="alt">
    <div class="container">
      <div class="auth-card">
        <?php if (!empty($errors)): ?>
          <div class="auth-alert error">
            <?php foreach ($errors as $error): ?>
              <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="/register" class="auth-form" autocomplete="on">
          <?php if (function_exists('csrf_field')) echo csrf_field(); ?>

          <div class="form-group">
            <label for="display_name">Display Name</label>
            <input type="text" id="display_name" name="display_name"
                   value="<?php echo htmlspecialchars($old['display_name']); ?>"
                   placeholder="How you'd like to be called"
                   autocomplete="name">
          </div>

          <div class="form-group">
            <label for="username">Username <span class="required">*</span></label>
            <input type="text" id="username" name="username" required
                   value="<?php echo htmlspecialchars($old['username']); ?>"
                   placeholder="Letters, numbers, underscores"
                   autocomplete="username"
                   pattern="[a-zA-Z0-9_]{3,30}"
                   minlength="3" maxlength="30">
          </div>

          <div class="form-group">
            <label for="email">Email <span class="required">*</span></label>
            <input type="email" id="email" name="email" required
                   value="<?php echo htmlspecialchars($old['email']); ?>"
                   placeholder="you@example.com"
                   autocomplete="email">
          </div>

          <div class="form-group">
            <label for="password">Password <span class="required">*</span></label>
            <div class="password-wrapper">
              <input type="password" id="password" name="password" required
                     placeholder="At least 8 characters"
                     autocomplete="new-password"
                     minlength="8">
              <button type="button" class="toggle-password" onclick="togglePassword('password', this)" aria-label="Show password">
                <span class="eye-icon">Show</span>
              </button>
            </div>
            <div class="password-strength" id="strength-bar">
              <div class="strength-fill" id="strength-fill"></div>
            </div>
            <span class="strength-label" id="strength-label"></span>
          </div>

          <div class="form-group">
            <label for="password_confirm">Confirm Password <span class="required">*</span></label>
            <div class="password-wrapper">
              <input type="password" id="password_confirm" name="password_confirm" required
                     placeholder="Type password again"
                     autocomplete="new-password"
                     minlength="8">
              <button type="button" class="toggle-password" onclick="togglePassword('password_confirm', this)" aria-label="Show password">
                <span class="eye-icon">Show</span>
              </button>
            </div>
          </div>

          <button type="submit" class="btn btn-primary auth-submit">Create Account</button>
        </form>

        <div class="auth-footer">
          <p>Already have an account? <a href="/login">Sign in</a></p>
        </div>
      </div>
    </div>
  </section>

  <script>
  // Password strength meter
  document.getElementById('password').addEventListener('input', function() {
    var pw = this.value;
    var score = 0;
    if (pw.length >= 8) score++;
    if (pw.length >= 12) score++;
    if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^a-zA-Z0-9]/.test(pw)) score++;

    var fill = document.getElementById('strength-fill');
    var label = document.getElementById('strength-label');
    var pct = (score / 5) * 100;
    fill.style.width = pct + '%';

    var colors = ['#e55', '#e55', '#db4', '#db4', '#5b5', '#2a6f6f'];
    var labels = ['', 'Weak', 'Weak', 'Fair', 'Strong', 'Very strong'];
    fill.style.background = colors[score];
    label.textContent = labels[score];
    label.style.color = colors[score];
  });

  function togglePassword(id, btn) {
    var ids = ['password', 'password_confirm'];
    var buttons = document.querySelectorAll('.toggle-password');
    var isPassword = document.getElementById(id).type === 'password';
    var newType = isPassword ? 'text' : 'password';
    var label = isPassword ? 'Hide' : 'Show';

    ids.forEach(function(fieldId) {
      var input = document.getElementById(fieldId);
      if (input) input.type = newType;
    });
    buttons.forEach(function(b) {
      var icon = b.querySelector('.eye-icon');
      if (icon) icon.textContent = label;
    });
  }
  </script>
