/* GatewayOS2 — Forms (Password Toggle, Strength Meter, Validation) */

function togglePassword(id, btn) {
  var input = document.getElementById(id);
  var isPassword = input.type === 'password';
  input.type = isPassword ? 'text' : 'password';
  btn.querySelector('.eye-icon').textContent = isPassword ? 'Hide' : 'Show';
}

// Password strength meter
document.addEventListener('DOMContentLoaded', function() {
  var pwInput = document.getElementById('password');
  if (pwInput && document.getElementById('strength-fill')) {
    pwInput.addEventListener('input', function() {
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
  }
});
