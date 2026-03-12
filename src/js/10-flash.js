/* GatewayOS2 — Flash Messages */

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.flash-message').forEach(function(el) {
    setTimeout(function() {
      el.style.opacity = '0';
      el.style.transform = 'translateY(-20px)';
      setTimeout(function() {
        el.remove();
      }, 300);
    }, 5000);
  });
});
