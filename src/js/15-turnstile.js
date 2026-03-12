/* GatewayOS2 — Cloudflare Turnstile Integration */

document.addEventListener('DOMContentLoaded', function() {
  var containers = document.querySelectorAll('.cf-turnstile');
  if (!containers.length) return;

  // Check if Turnstile script is loaded
  if (typeof turnstile === 'undefined') return;

  containers.forEach(function(container) {
    var siteKey = container.getAttribute('data-sitekey');
    if (!siteKey) return;

    turnstile.render(container, {
      sitekey: siteKey,
      theme: 'light',
      callback: function(token) {
        var hiddenInput = container.closest('form').querySelector('input[name="cf-turnstile-response"]');
        if (!hiddenInput) {
          hiddenInput = document.createElement('input');
          hiddenInput.type = 'hidden';
          hiddenInput.name = 'cf-turnstile-response';
          container.closest('form').appendChild(hiddenInput);
        }
        hiddenInput.value = token;
      }
    });
  });
});
