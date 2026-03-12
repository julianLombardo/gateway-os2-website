<?php
// Contact form — receives $success (bool), $errors (array), $old (array with name, email, subject, message)
$success = $success ?? false;
$errors = $errors ?? [];
$old = $old ?? ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
?>
  <section class="hero" style="padding: 5rem 3rem 3rem;">
    <div class="hero-content">
      <p class="overline">Reach Out</p>
      <h1>Get in <em>touch</em></h1>
      <p class="subtitle">Questions, feedback, collaboration ideas — we'd love to hear from you.</p>
    </div>
  </section>

  <section class="alt">
    <div class="container">
      <div class="contact-layout">
        <div class="contact-form-wrap">
          <?php if ($success): ?>
            <div class="auth-alert success">
              <p>Message sent! We'll get back to you soon.</p>
            </div>
          <?php endif; ?>

          <?php if (!empty($errors)): ?>
            <div class="auth-alert error">
              <?php foreach ($errors as $e): ?>
                <p><?php echo htmlspecialchars($e); ?></p>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php if (!$success): ?>
          <form method="POST" action="/contact" class="auth-form">
            <?php if (function_exists('csrf_field')) echo csrf_field(); ?>

            <!-- Honeypot -->
            <div style="position: absolute; left: -9999px;" aria-hidden="true">
              <input type="text" name="website_url" tabindex="-1" autocomplete="off">
            </div>

            <?php if (defined('TURNSTILE_SITE_KEY') && TURNSTILE_SITE_KEY): ?>
              <div class="cf-turnstile" data-sitekey="<?php echo htmlspecialchars(TURNSTILE_SITE_KEY); ?>"></div>
            <?php endif; ?>

            <div class="form-row">
              <div class="form-group">
                <label for="name">Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" required
                       value="<?php echo htmlspecialchars($old['name']); ?>"
                       placeholder="Your name">
              </div>
              <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" required
                       value="<?php echo htmlspecialchars($old['email']); ?>"
                       placeholder="you@example.com">
              </div>
            </div>

            <div class="form-group">
              <label for="subject">Subject <span class="required">*</span></label>
              <input type="text" id="subject" name="subject" required
                     value="<?php echo htmlspecialchars($old['subject']); ?>"
                     placeholder="What's this about?">
            </div>

            <div class="form-group">
              <label for="message">Message <span class="required">*</span></label>
              <textarea id="message" name="message" required rows="6"
                        placeholder="Your message (at least 10 characters)"
                        minlength="10"><?php echo htmlspecialchars($old['message']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary auth-submit">Send Message</button>
          </form>
          <?php endif; ?>
        </div>

        <div class="contact-info">
          <div class="contact-info-card">
            <h3>Other ways to connect</h3>
            <div class="contact-method">
              <h4>GitHub</h4>
              <p><a href="https://github.com/julianLombardo/GatewayOS2" target="_blank" rel="noopener">julianLombardo/GatewayOS2</a></p>
            </div>
            <div class="contact-method">
              <h4>Open an Issue</h4>
              <p><a href="https://github.com/julianLombardo/GatewayOS2/issues" target="_blank" rel="noopener">Report bugs or request features</a></p>
            </div>
            <div class="contact-method">
              <h4>Organization</h4>
              <p>GateWay Software</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
