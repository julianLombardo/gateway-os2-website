/* GatewayOS2 — Code Explorer Tabs (code.php) */

function showCodeTab(name) {
  document.querySelectorAll('.code-tab').forEach(function(tab) {
    tab.classList.toggle('active', tab.textContent.toLowerCase().replace(/\s/g, '') === name.replace(/\s/g, ''));
  });

  document.querySelectorAll('.code-panel').forEach(function(panel) {
    panel.classList.remove('active');
  });

  var target = document.getElementById('panel-' + name);
  if (target) target.classList.add('active');
}

// Match tab text to panel id
document.querySelectorAll('.code-tab').forEach(function(tab) {
  tab.addEventListener('click', function() {
    var names = {
      'Bootloader': 'bootloader',
      'Kernel': 'kernel',
      'Graphics': 'graphics',
      'Networking': 'network',
      'PE32 Loader': 'pe32',
      'Crypto': 'crypto'
    };
    var name = names[this.textContent.trim()];
    if (name) showCodeTab(name);
  });
});
