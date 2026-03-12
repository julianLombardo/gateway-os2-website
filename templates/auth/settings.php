<?php
// Settings page — receives $user (array), $config (array), $errors (array), $success (bool),
// $test_result (mixed), $mail_configured (bool)
$errors = $errors ?? [];
$success = $success ?? false;
$test_result = $test_result ?? null;
$mail_configured = $mail_configured ?? false;
$config = $config ?? [];
?>
  <section class="hero" style="padding: 4rem 3rem 2rem;">
    <div class="hero-content">
      <p class="overline">Admin</p>
      <h1>Email <em>Settings</em></h1>
      <p class="subtitle">Set up email delivery so password resets and verification codes reach your users.</p>
    </div>
  </section>

  <section class="alt">
    <div class="container">
      <div class="settings-layout">
        <div class="settings-main">
          <?php if ($success): ?>
            <div class="auth-alert success"><p>Settings saved.</p></div>
          <?php endif; ?>
          <?php if (!empty($errors)): ?>
            <div class="auth-alert error">
              <?php foreach ($errors as $e): ?><p><?php echo htmlspecialchars($e); ?></p><?php endforeach; ?>
            </div>
          <?php endif; ?>
          <?php if ($test_result !== null): ?>
            <?php if ($test_result === true): ?>
              <div class="auth-alert success"><p>Test email sent successfully!</p></div>
            <?php else: ?>
              <div class="auth-alert error"><p>Failed: <?php echo htmlspecialchars($test_result); ?></p></div>
            <?php endif; ?>
          <?php endif; ?>

          <div class="auth-card" style="max-width: 100%;">
            <form method="POST" action="/settings" class="auth-form">
              <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
              <input type="hidden" name="action" value="save_brevo">

              <div class="form-group">
                <label for="brevo_api_key">Brevo API Key <span class="required">*</span></label>
                <div class="password-wrapper">
                  <input type="password" id="brevo_api_key" name="brevo_api_key"
                         value="<?php echo htmlspecialchars($config['brevo_api_key'] ?? ''); ?>"
                         placeholder="xkeysib-..."
                         required autocomplete="off">
                  <button type="button" class="toggle-password" onclick="toggleSingle('brevo_api_key', this)" aria-label="Show key">
                    <span class="eye-icon">Show</span>
                  </button>
                </div>
              </div>

              <div class="form-group">
                <label for="from_email">Sender Email <span class="required">*</span></label>
                <input type="email" id="from_email" name="from_email"
                       value="<?php echo htmlspecialchars($config['from_email'] ?? ''); ?>"
                       placeholder="noreply@yourdomain.com" required>
              </div>

              <div class="form-group">
                <label for="from_name">Sender Name</label>
                <input type="text" id="from_name" name="from_name"
                       value="<?php echo htmlspecialchars($config['from_name'] ?? 'GatewayOS2'); ?>"
                       placeholder="GatewayOS2">
              </div>

              <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Save Settings</button>
            </form>

            <?php if ($mail_configured): ?>
              <form method="POST" action="/settings" style="margin-top: 1rem;">
                <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
                <input type="hidden" name="action" value="clear">
                <button type="submit" class="btn btn-secondary" style="width: 100%; justify-content: center;">Clear Settings</button>
              </form>
            <?php endif; ?>
          </div>
        </div>

        <div class="settings-sidebar">
          <div class="settings-status">
            <h3>Status</h3>
            <div class="status-indicator <?php echo $mail_configured ? 'active' : ''; ?>">
              <span class="status-dot"></span>
              <?php echo $mail_configured ? 'Email active' : 'Not configured'; ?>
            </div>
            <?php if ($mail_configured && !empty($config['from_email'])): ?>
              <p class="status-detail">From: <?php echo htmlspecialchars($config['from_email']); ?></p>
            <?php endif; ?>
          </div>

          <?php if ($mail_configured): ?>
          <div class="settings-test">
            <h3>Send Test</h3>
            <form method="POST" action="/settings" class="auth-form compact">
              <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
              <input type="hidden" name="action" value="test_email">
              <div class="form-group">
                <input type="email" name="test_to" required
                       placeholder="your@email.com"
                       value="<?php echo htmlspecialchars($config['from_email'] ?? ''); ?>">
              </div>
              <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Send Test</button>
            </form>
          </div>
          <?php endif; ?>

          <div class="settings-help">
            <h3>Setup Guide</h3>
            <div class="help-item">
              <h4>Step 1</h4>
              <p>Create a free account at <a href="https://www.brevo.com" target="_blank" rel="noopener">brevo.com</a> (300 emails/day free).</p>
            </div>
            <div class="help-item">
              <h4>Step 2</h4>
              <p>Go to <strong>SMTP & API</strong> in your Brevo dashboard and create an API key.</p>
            </div>
            <div class="help-item">
              <h4>Step 3</h4>
              <p>Paste the API key here and set your sender email (must be verified in Brevo).</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
  function toggleSingle(id, btn) {
    var input = document.getElementById(id);
    var show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.querySelector('.eye-icon').textContent = show ? 'Hide' : 'Show';
  }
  </script>
