/* GatewayOS2 — Navbar */

// Mobile nav: close on link click
document.querySelectorAll('.navbar nav a').forEach(function(link) {
  link.addEventListener('click', function() {
    document.querySelector('.navbar nav').classList.remove('open');
  });
});

// Smooth navbar shadow on scroll
var navbar = document.querySelector('.navbar');
if (navbar) {
  window.addEventListener('scroll', function() {
    if (window.scrollY > 10) {
      navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.06)';
    } else {
      navbar.style.boxShadow = 'none';
    }
  });
}
