<?php
// Forgot password — receives $errors (array), $step ('email'|'code'), $old_email (string)
$errors = $errors ?? [];
$step = $step ?? 'email';
$old_email = $old_email ?? '';
?>
  <section class="hero" style="padding: 4rem 3rem 2rem;">
    <div class="hero-content">
      <p class="overline">Account Recovery</p>
      <?php if ($step === 'code'): ?>
        <h1>Check your <em>email</em></h1>
      <?php else: ?>
        <h1>Forgot your<br><em>password?</em></h1>
      <?php endif; ?>
    </div>
  </section>

  <section class="alt">
    <div class="container">
      <div class="auth-card">

        <?php if (!empty($errors)): ?>
          <div class="auth-alert error">
            <?php foreach ($errors as $e): ?><p><?php echo htmlspecialchars($e); ?></p><?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if ($step === 'code'): ?>

          <?php if (!empty($mail_error)): ?>
            <div class="auth-alert error">
              <p>We couldn't send the email. Please try again later or contact support.</p>
            </div>
          <?php else: ?>
            <div class="auth-alert success">
              <p>We sent a 6-digit code to <strong><?php echo htmlspecialchars($old_email); ?></strong>. Check your inbox and spam folder.</p>
            </div>
          <?php endif; ?>

          <form method="POST" action="/forgot-password" class="auth-form">
            <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
            <input type="hidden" name="action" value="verify_code">
            <div class="form-group">
              <label for="code">Verification Code</label>
              <input type="text" id="code" name="code" required
                     placeholder="000000"
                     maxlength="6" minlength="6"
                     pattern="[0-9]{6}"
                     inputmode="numeric"
                     autocomplete="one-time-code"
                     class="code-input"
                     autofocus>
            </div>
            <button type="submit" class="btn btn-primary auth-submit">Verify Code</button>
          </form>

          <div class="auth-footer">
            <p>Didn't get it? <a href="/forgot-password?restart=1">Resend code</a></p>
          </div>

        <?php else: ?>

          <p style="font-size: 0.9rem; color: var(--smoke); margin-bottom: 1.5rem; line-height: 1.6;">
            Enter your email address and we'll send you a code to reset your password.
          </p>
          <form method="POST" action="/forgot-password" class="auth-form">
            <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
            <input type="hidden" name="action" value="send_code">
            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email" required
                     value="<?php echo htmlspecialchars($old_email); ?>"
                     placeholder="you@example.com"
                     autocomplete="email"
                     autofocus>
            </div>
            <button type="submit" class="btn btn-primary auth-submit">Send Code</button>
          </form>

          <div class="auth-footer">
            <p>Remember your password? <a href="/login">Sign in</a></p>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </section>
