/* GatewayOS2 — Boot Sequence Stepper (guide.php) */

var currentBootStep = 0;

function initBootSequence() {
  var steps = document.querySelectorAll('.boot-step');
  if (!steps.length) return;

  var indicators = document.querySelector('.boot-indicators');
  if (indicators) {
    for (var i = 0; i < steps.length; i++) {
      var dot = document.createElement('div');
      dot.className = 'boot-indicator' + (i === 0 ? ' active' : '');
      dot.setAttribute('data-i', i);
      dot.onclick = function() { goToBootStep(parseInt(this.getAttribute('data-i'))); };
      indicators.appendChild(dot);
    }
  }
}

function goToBootStep(idx) {
  var steps = document.querySelectorAll('.boot-step');
  var dots = document.querySelectorAll('.boot-indicator');
  if (idx < 0 || idx >= steps.length) return;

  currentBootStep = idx;
  steps.forEach(function(s, i) {
    s.classList.toggle('active', i === idx);
  });
  dots.forEach(function(d, i) {
    d.classList.toggle('active', i === idx);
  });
}

function bootStep(dir) {
  var steps = document.querySelectorAll('.boot-step');
  var next = currentBootStep + dir;
  if (next >= 0 && next < steps.length) {
    goToBootStep(next);
  }
}

document.addEventListener('DOMContentLoaded', function() {
  initBootSequence();
});
