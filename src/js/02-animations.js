/* GatewayOS2 — Scroll Animations */

var observer = new IntersectionObserver(function(entries) {
  entries.forEach(function(entry) {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.06, rootMargin: '0px 0px -30px 0px' });

document.querySelectorAll(
  '.feature-card, .app-category, .spec-item, .about-card, .stat, .terminal, .pullquote, .boot-step, .arch-layer, .timeline-item, .accordion-item, .value-item'
).forEach(function(el, i) {
  el.style.opacity = '0';
  el.style.transform = 'translateY(20px)';
  el.style.transition = 'opacity 0.5s ease ' + (i % 8) * 0.06 + 's, transform 0.5s ease ' + (i % 8) * 0.06 + 's';
  observer.observe(el);
});
