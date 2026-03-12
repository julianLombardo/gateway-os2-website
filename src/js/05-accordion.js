/* GatewayOS2 — Accordion (guide.php) */

function toggleAccordion(trigger) {
  var item = trigger.parentElement;
  var wasOpen = item.classList.contains('open');

  // Close all
  document.querySelectorAll('.accordion-item').forEach(function(el) {
    el.classList.remove('open');
  });

  // Toggle clicked
  if (!wasOpen) {
    item.classList.add('open');
  }
}
