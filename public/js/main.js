/* === 00-utils.js === */
/* GatewayOS2 — DOM Utilities */

function $(selector) { return document.querySelector(selector); }
function $$(selector) { return document.querySelectorAll(selector); }
function debounce(fn, ms) { var t; return function() { clearTimeout(t); t = setTimeout(fn.bind(this, ...arguments), ms); }; }
function fetchJSON(url, opts) { return fetch(url, opts).then(function(r) { return r.json(); }); }


/* === 01-navbar.js === */
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


/* === 02-animations.js === */
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


/* === 03-boot-stepper.js === */
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


/* === 04-arch-diagram.js === */
/* GatewayOS2 — Architecture Diagram (guide.php) */

var layerData = {
  apps: {
    title: 'Applications Layer',
    content: '<p>Over 50 applications organized across 12 source files. Each app registers draw and event callbacks with the window manager. Categories include:</p>' +
      '<ul><li><strong>Productivity</strong> — Text editor, calculator, calendar, contacts, notes, mail client</li>' +
      '<li><strong>Games</strong> — Snake, Pong, Tetris, Minesweeper, Chess, 15-Puzzle, Billiards</li>' +
      '<li><strong>Sci-Fi Suite</strong> — Decrypt, Radar, Neural Net, Matrix Rain, Uplink, StarMap</li>' +
      '<li><strong>Security</strong> — GW-Cipher, GW-Fortress, GW-Sentinel, GW-NetScan, GW-Hashlab</li>' +
      '<li><strong>Development</strong> — Java IDE with editor, interpreter, and 8 sample programs</li></ul>'
  },
  gui: {
    title: 'GUI / Window Manager',
    content: '<p>The compositing window manager handles all visual output. Key components:</p>' +
      '<ul><li><strong>Compositor</strong> — Z-ordered window rendering with overlap handling</li>' +
      '<li><strong>Desktop</strong> — Event loop, context menus, wallpaper</li>' +
      '<li><strong>Dock</strong> — Right-edge pixel-art application launcher</li>' +
      '<li><strong>Menu Bar</strong> — Top-of-screen category menus for launching apps</li>' +
      '<li><strong>Font Engine</strong> — Bitmap font rendering for all text display</li></ul>' +
      '<p>All rendering goes through <code>put_pixel()</code> — direct writes to the VESA framebuffer.</p>'
  },
  services: {
    title: 'System Services',
    content: '<p>Mid-level services that bridge applications and hardware:</p>' +
      '<ul><li><strong>Clipboard</strong> — System-wide copy/paste between applications</li>' +
      '<li><strong>PE32 Loader</strong> — Parses DOS/PE headers, maps sections, resolves Win32 imports</li>' +
      '<li><strong>Win32 Shim</strong> — 60+ API functions translating Windows calls to native operations</li>' +
      '<li><strong>Java Interpreter</strong> — Recursive-descent parser with variables, control flow, arrays</li>' +
      '<li><strong>Login System</strong> — Persistent credentials with auto-fill from ATA storage</li></ul>'
  },
  net: {
    title: 'Networking Stack',
    content: '<p>A complete TCP/IP implementation built from the RFCs:</p>' +
      '<ul><li><strong>Ethernet</strong> — Frame construction, MAC addressing, EtherType dispatch</li>' +
      '<li><strong>ARP</strong> — Address resolution with cache table for IP-to-MAC mapping</li>' +
      '<li><strong>IPv4</strong> — Packet routing, header checksum, fragmentation basics</li>' +
      '<li><strong>UDP</strong> — Connectionless datagrams for DNS and DHCP</li>' +
      '<li><strong>TCP</strong> — Full state machine with 3-way handshake, data transfer, teardown</li>' +
      '<li><strong>DHCP</strong> — Automatic IP configuration on boot</li>' +
      '<li><strong>DNS</strong> — Domain name resolution for the mail client</li></ul>'
  },
  drivers: {
    title: 'Hardware Drivers',
    content: '<p>Direct hardware access with no abstraction layers:</p>' +
      '<ul><li><strong>Framebuffer</strong> — VESA VBE mode setting and linear framebuffer access (1024x768x32bpp)</li>' +
      '<li><strong>PS/2 Keyboard</strong> — IRQ1 handler, scan code translation, modifier key tracking</li>' +
      '<li><strong>PS/2 Mouse</strong> — IRQ12 handler, 3-byte packet parsing, cursor position tracking</li>' +
      '<li><strong>E1000 NIC</strong> — Intel Gigabit Ethernet driver with TX/RX descriptor ring buffers</li>' +
      '<li><strong>ATA PIO</strong> — IDE disk access for persistent storage (28-bit LBA, PIO mode)</li>' +
      '<li><strong>PCI</strong> — Bus enumeration to discover and configure hardware devices</li></ul>'
  },
  kernel: {
    title: 'Kernel Core',
    content: '<p>The foundation that everything else is built on:</p>' +
      '<ul><li><strong>GDT</strong> — Global Descriptor Table defining code/data/stack segments for protected mode</li>' +
      '<li><strong>IDT</strong> — Interrupt Descriptor Table mapping IRQs and exceptions to handler functions</li>' +
      '<li><strong>PIC</strong> — Programmable Interrupt Controller remapping to avoid CPU exception conflicts</li>' +
      '<li><strong>PMM</strong> — Physical Memory Manager using bitmap allocation from the Multiboot memory map</li>' +
      '<li><strong>Heap</strong> — Dynamic memory allocator providing malloc/free for kernel and applications</li>' +
      '<li><strong>Entry</strong> — Multiboot-compliant entry point from GRUB, stack setup, C++ handoff</li></ul>' +
      '<p>No paging, no virtual memory — the entire system runs in a flat physical address space.</p>'
  }
};

function showLayer(name) {
  var data = layerData[name];
  if (!data) return;

  // Highlight the selected layer
  document.querySelectorAll('.arch-layer').forEach(function(el) {
    el.classList.toggle('selected', el.getAttribute('data-layer') === name);
  });

  var panel = document.getElementById('layer-panel');
  var content = document.getElementById('layer-panel-content');
  content.innerHTML = '<h3>' + data.title + '</h3>' + data.content;
  panel.classList.add('open');

  // Scroll to panel
  panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function hideLayer() {
  document.getElementById('layer-panel').classList.remove('open');
  document.querySelectorAll('.arch-layer').forEach(function(el) {
    el.classList.remove('selected');
  });
}


/* === 05-accordion.js === */
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


/* === 06-code-tabs.js === */
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


/* === 07-file-tree.js === */
/* GatewayOS2 — File Tree (guide.php) */

function toggleFolder(el) {
  var children = el.nextElementSibling;
  if (!children || !children.classList.contains('file-tree-children')) return;

  var isOpen = children.style.display !== 'none';
  children.style.display = isOpen ? 'none' : 'block';

  var icon = el.querySelector('.file-icon');
  if (icon) icon.textContent = isOpen ? '\u25B6' : '\u25BC';

  el.classList.toggle('open', !isOpen);
}


/* === 08-forms.js === */
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


/* === 09-modals.js === */
/* GatewayOS2 — Modals */

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay').forEach(function(m) {
      m.style.display = 'none';
    });
  }
});


/* === 10-flash.js === */
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


/* === 11-search.js === */
/* GatewayOS2 — Live Search */

document.addEventListener('DOMContentLoaded', function() {
  var searchInput = document.querySelector('.search-form input[name="q"]');
  if (!searchInput) return;

  var resultsContainer = document.querySelector('.search-results');
  if (!resultsContainer) return;

  var liveSearch = debounce(function() {
    var query = searchInput.value.trim();
    if (query.length < 2) return;

    fetch('/search.php?q=' + encodeURIComponent(query) + '&ajax=1')
      .then(function(r) { return r.text(); })
      .then(function(html) {
        resultsContainer.innerHTML = html;
      })
      .catch(function() {
        // Silently fail on network errors
      });
  }, 300);

  searchInput.addEventListener('input', liveSearch);
});


/* === 12-desktop-sim.js === */
/* GatewayOS2 — Desktop Simulator */

var simWindowId = 0;
var simFocusedWindow = null;
var simDragState = null;
var simSnakeInterval = null;

function simCreateWindow(title, width, height, x, y, bodyHTML) {
  var desktop = document.querySelector('.sim-desktop');
  if (!desktop) return null;

  simWindowId++;
  var id = 'sim-win-' + simWindowId;

  var win = document.createElement('div');
  win.className = 'sim-window';
  win.id = id;
  win.style.width = width + 'px';
  win.style.height = height + 'px';
  win.style.left = x + 'px';
  win.style.top = y + 'px';

  win.innerHTML =
    '<div class="sim-window-titlebar">' +
      '<button class="sim-window-btn sim-window-close" onclick="simCloseWindow(\'' + id + '\')">x</button>' +
      '<button class="sim-window-btn sim-window-minimize" onclick="simMinimizeWindow(\'' + id + '\')">-</button>' +
      '<span class="sim-window-title">' + title + '</span>' +
    '</div>' +
    '<div class="sim-window-body">' + bodyHTML + '</div>';

  win.addEventListener('mousedown', function() {
    simFocusWindow(id);
  });

  var titlebar = win.querySelector('.sim-window-titlebar');
  titlebar.addEventListener('mousedown', function(e) {
    if (e.target.classList.contains('sim-window-btn')) return;
    simDragState = {
      el: win,
      startX: e.clientX - win.offsetLeft,
      startY: e.clientY - win.offsetTop
    };
    e.preventDefault();
  });

  desktop.appendChild(win);
  simFocusWindow(id);
  return win;
}

function simFocusWindow(id) {
  document.querySelectorAll('.sim-window').forEach(function(w) {
    w.classList.remove('focused');
  });
  var win = document.getElementById(id);
  if (win) {
    win.classList.add('focused');
    simFocusedWindow = id;
  }
}

function simCloseWindow(id) {
  var win = document.getElementById(id);
  if (win) {
    if (simSnakeInterval && win.querySelector('.snake-canvas')) {
      clearInterval(simSnakeInterval);
      simSnakeInterval = null;
    }
    win.remove();
  }
}

function simMinimizeWindow(id) {
  var win = document.getElementById(id);
  if (win) {
    win.style.display = 'none';
  }
}

// Global drag handling
document.addEventListener('mousemove', function(e) {
  if (!simDragState) return;
  var desktop = document.querySelector('.sim-desktop');
  if (!desktop) return;

  var rect = desktop.getBoundingClientRect();
  var newX = e.clientX - simDragState.startX;
  var newY = e.clientY - simDragState.startY;

  newX = Math.max(0, Math.min(newX, desktop.offsetWidth - 50));
  newY = Math.max(0, Math.min(newY, desktop.offsetHeight - 24));

  simDragState.el.style.left = newX + 'px';
  simDragState.el.style.top = newY + 'px';
});

document.addEventListener('mouseup', function() {
  simDragState = null;
});

// Terminal app
function simOpenTerminal() {
  var body =
    '<div class="sim-terminal-body" id="sim-term-' + (simWindowId + 1) + '">' +
      '<div>GatewayOS2 Terminal v1.0</div>' +
      '<div>Type a command and press Enter.</div>' +
      '<div><br></div>' +
      '<div style="display:flex">' +
        '<span style="color:#c4622d;margin-right:4px">gw2$</span>' +
        '<input type="text" style="background:none;border:none;color:#4ade80;font-family:inherit;font-size:inherit;outline:none;flex:1" ' +
          'onkeydown="simTermKeydown(event, this)">' +
      '</div>' +
    '</div>';
  simCreateWindow('Terminal', 380, 250, 20, 20, body);
}

function simTermKeydown(e, input) {
  if (e.key !== 'Enter') return;
  var cmd = input.value.trim();
  input.value = '';

  var termBody = input.closest('.sim-terminal-body');
  var responses = {
    'help': 'Commands: help, uname, ls, date, whoami, clear, echo, neofetch',
    'uname': 'GatewayOS2 x86 i686 - bare metal',
    'ls': 'kernel.bin  boot.s  desktop.cpp  apps.cpp  net.cpp  drivers.cpp',
    'date': new Date().toString(),
    'whoami': 'root@gateway',
    'echo': '',
    'neofetch': '  _____ ___  ___\n / ___// _ \\/ __|\n| |__ / // /\\__ \\\n \\___/\\___/|___/  GatewayOS2\n\n OS: GatewayOS2 x86\n Kernel: Custom C++\n Resolution: 1024x768\n Shell: gwsh 1.0\n CPU: i686 compatible\n Memory: 128MB'
  };

  var output = '';
  if (cmd === 'clear') {
    while (termBody.childNodes.length > 0) {
      termBody.removeChild(termBody.firstChild);
    }
  } else {
    if (cmd.startsWith('echo ')) {
      output = cmd.substring(5);
    } else if (responses[cmd] !== undefined) {
      output = responses[cmd];
    } else if (cmd !== '') {
      output = 'gwsh: command not found: ' + cmd;
    }

    if (cmd !== 'clear') {
      var cmdLine = document.createElement('div');
      cmdLine.innerHTML = '<span style="color:#c4622d">gw2$</span> ' + cmd;
      termBody.insertBefore(cmdLine, termBody.lastElementChild);

      if (output) {
        var outLine = document.createElement('div');
        outLine.style.whiteSpace = 'pre-wrap';
        outLine.textContent = output;
        termBody.insertBefore(outLine, termBody.lastElementChild);
      }
    }
  }

  // Re-add prompt
  var promptDiv = termBody.lastElementChild;
  if (!promptDiv || !promptDiv.querySelector('input')) {
    var newPrompt = document.createElement('div');
    newPrompt.style.display = 'flex';
    newPrompt.innerHTML =
      '<span style="color:#c4622d;margin-right:4px">gw2$</span>' +
      '<input type="text" style="background:none;border:none;color:#4ade80;font-family:inherit;font-size:inherit;outline:none;flex:1" ' +
        'onkeydown="simTermKeydown(event, this)">';
    termBody.appendChild(newPrompt);
    newPrompt.querySelector('input').focus();
  }

  termBody.scrollTop = termBody.scrollHeight;
}

// Text editor app
function simOpenEditor() {
  var body =
    '<textarea style="width:100%;height:100%;border:none;background:#fff;font-family:inherit;font-size:inherit;resize:none;outline:none;padding:4px" ' +
      'placeholder="Start typing..."></textarea>';
  simCreateWindow('Text Editor', 320, 220, 60, 40, body);
}

// Calculator app
function simOpenCalc() {
  var calcId = 'calc-' + (simWindowId + 1);
  var body =
    '<div style="background:#222;color:#4ade80;padding:8px;font-size:1.1rem;text-align:right;min-height:32px;font-family:monospace" id="' + calcId + '-display">0</div>' +
    '<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:2px;margin-top:4px">';

  var buttons = ['C', '/', '*', '-', '7', '8', '9', '+', '4', '5', '6', '=', '1', '2', '3', '.', '0'];
  for (var i = 0; i < buttons.length; i++) {
    var span = buttons[i] === '=' ? ' style="grid-row:span 2"' : '';
    if (buttons[i] === '0') span = ' style="grid-column:span 2"';
    body += '<button onclick="simCalcPress(\'' + calcId + '\',\'' + buttons[i] + '\')"' + span +
      ' style="padding:8px;border:1px solid #ccc;background:#e8e0d4;cursor:pointer;font-family:monospace;font-size:0.8rem">' +
      buttons[i] + '</button>';
  }
  body += '</div>';
  simCreateWindow('Calculator', 200, 280, 140, 60, body);
}

var simCalcValue = '';
var simCalcOp = '';
var simCalcPrev = 0;
var simCalcReset = false;

function simCalcPress(calcId, key) {
  var display = document.getElementById(calcId + '-display');
  if (!display) return;

  if (key === 'C') {
    simCalcValue = '';
    simCalcOp = '';
    simCalcPrev = 0;
    simCalcReset = false;
    display.textContent = '0';
    return;
  }

  if (key === '=' && simCalcOp && simCalcValue) {
    var current = parseFloat(simCalcValue);
    var result = 0;
    if (simCalcOp === '+') result = simCalcPrev + current;
    else if (simCalcOp === '-') result = simCalcPrev - current;
    else if (simCalcOp === '*') result = simCalcPrev * current;
    else if (simCalcOp === '/') result = current !== 0 ? simCalcPrev / current : 0;
    display.textContent = result;
    simCalcValue = '' + result;
    simCalcOp = '';
    simCalcReset = true;
    return;
  }

  if (['+', '-', '*', '/'].indexOf(key) !== -1) {
    simCalcPrev = parseFloat(simCalcValue || '0');
    simCalcOp = key;
    simCalcReset = true;
    return;
  }

  if (simCalcReset) {
    simCalcValue = '';
    simCalcReset = false;
  }
  simCalcValue += key;
  display.textContent = simCalcValue;
}

// About System app
function simOpenAbout() {
  var body =
    '<div style="text-align:center;padding:16px">' +
      '<div style="font-family:serif;font-size:1.5rem;font-weight:900;margin-bottom:8px">GatewayOS2</div>' +
      '<div style="font-size:0.7rem;color:#666;margin-bottom:12px">Version 2.0</div>' +
      '<div style="text-align:left;font-size:0.7rem;line-height:1.6">' +
        '<div><strong>Architecture:</strong> x86 (i686)</div>' +
        '<div><strong>Language:</strong> C++ / x86 ASM</div>' +
        '<div><strong>Lines of Code:</strong> 33,000+</div>' +
        '<div><strong>Applications:</strong> 50+</div>' +
        '<div><strong>Resolution:</strong> 1024x768x32bpp</div>' +
        '<div><strong>Networking:</strong> Full TCP/IP stack</div>' +
        '<div><strong>Boot:</strong> GRUB Multiboot</div>' +
      '</div>' +
    '</div>';
  simCreateWindow('About GatewayOS2', 260, 260, 100, 30, body);
}

// Snake game
function simOpenSnake() {
  var canvasId = 'snake-' + (simWindowId + 1);
  var body =
    '<canvas class="snake-canvas" id="' + canvasId + '" width="280" height="200" style="background:#1a1a1a;display:block;margin:0 auto"></canvas>' +
    '<div style="text-align:center;font-size:0.6rem;color:#888;margin-top:4px">Arrow keys to move</div>';
  var win = simCreateWindow('Snake', 300, 250, 40, 20, body);

  var canvas = document.getElementById(canvasId);
  if (!canvas) return;
  var ctx = canvas.getContext('2d');
  var size = 10;
  var cols = Math.floor(canvas.width / size);
  var rows = Math.floor(canvas.height / size);
  var snake = [{ x: 5, y: 5 }];
  var dir = { x: 1, y: 0 };
  var food = spawnFood();
  var score = 0;

  function spawnFood() {
    return { x: Math.floor(Math.random() * cols), y: Math.floor(Math.random() * rows) };
  }

  function draw() {
    ctx.fillStyle = '#1a1a1a';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    ctx.fillStyle = '#c4622d';
    ctx.fillRect(food.x * size, food.y * size, size - 1, size - 1);

    ctx.fillStyle = '#4ade80';
    for (var i = 0; i < snake.length; i++) {
      ctx.fillRect(snake[i].x * size, snake[i].y * size, size - 1, size - 1);
    }

    ctx.fillStyle = '#666';
    ctx.font = '9px monospace';
    ctx.fillText('Score: ' + score, 4, 12);
  }

  function update() {
    var head = { x: snake[0].x + dir.x, y: snake[0].y + dir.y };

    if (head.x < 0) head.x = cols - 1;
    if (head.x >= cols) head.x = 0;
    if (head.y < 0) head.y = rows - 1;
    if (head.y >= rows) head.y = 0;

    for (var i = 0; i < snake.length; i++) {
      if (snake[i].x === head.x && snake[i].y === head.y) {
        snake = [{ x: 5, y: 5 }];
        dir = { x: 1, y: 0 };
        score = 0;
        food = spawnFood();
        return;
      }
    }

    snake.unshift(head);

    if (head.x === food.x && head.y === food.y) {
      score++;
      food = spawnFood();
    } else {
      snake.pop();
    }
  }

  function tick() {
    update();
    draw();
  }

  if (simSnakeInterval) clearInterval(simSnakeInterval);
  simSnakeInterval = setInterval(tick, 120);
  draw();

  document.addEventListener('keydown', function snakeKeys(e) {
    if (!document.getElementById(canvasId)) {
      document.removeEventListener('keydown', snakeKeys);
      return;
    }
    if (e.key === 'ArrowUp' && dir.y !== 1) { dir = { x: 0, y: -1 }; }
    else if (e.key === 'ArrowDown' && dir.y !== -1) { dir = { x: 0, y: 1 }; }
    else if (e.key === 'ArrowLeft' && dir.x !== 1) { dir = { x: -1, y: 0 }; }
    else if (e.key === 'ArrowRight' && dir.x !== -1) { dir = { x: 1, y: 0 }; }
  });
}

// Dock initialization
document.addEventListener('DOMContentLoaded', function() {
  var dock = document.querySelector('.sim-dock');
  if (!dock) return;

  var apps = [
    { label: '>_', title: 'Terminal', fn: simOpenTerminal },
    { label: 'Ed', title: 'Text Editor', fn: simOpenEditor },
    { label: '#', title: 'Calculator', fn: simOpenCalc },
    { label: 'i', title: 'About', fn: simOpenAbout },
    { label: 'Sn', title: 'Snake', fn: simOpenSnake }
  ];

  for (var i = 0; i < apps.length; i++) {
    var item = document.createElement('div');
    item.className = 'sim-dock-item';
    item.title = apps[i].title;
    item.textContent = apps[i].label;
    item.onclick = (function(fn) { return function() { fn(); }; })(apps[i].fn);
    dock.appendChild(item);
  }

  // Menubar clock
  var clockEl = document.querySelector('.sim-menu-right');
  if (clockEl) {
    setInterval(function() {
      var now = new Date();
      var h = now.getHours();
      var m = now.getMinutes();
      var s = now.getSeconds();
      clockEl.textContent =
        (h < 10 ? '0' : '') + h + ':' +
        (m < 10 ? '0' : '') + m + ':' +
        (s < 10 ? '0' : '') + s;
    }, 1000);
  }
});


/* === 13-network-viz.js === */
/* GatewayOS2 — TCP Handshake Network Visualizer */

var vizAnimating = false;
var vizTimeouts = [];

function startNetworkViz() {
  if (vizAnimating) return;
  vizAnimating = true;

  var path = document.querySelector('.viz-path');
  var log = document.querySelector('.viz-log');
  if (!path || !log) { vizAnimating = false; return; }

  log.innerHTML = '';
  var pathWidth = path.offsetWidth;
  var startTime = Date.now();

  function addLog(msg) {
    var elapsed = ((Date.now() - startTime) / 1000).toFixed(2);
    var entry = document.createElement('div');
    entry.className = 'viz-log-entry';
    entry.innerHTML = '<span class="viz-time">[' + elapsed + 's]</span> ' + msg;
    log.appendChild(entry);
    log.scrollTop = log.scrollHeight;
  }

  function animatePacket(cls, label, fromLeft, duration) {
    return new Promise(function(resolve) {
      var packet = document.createElement('div');
      packet.className = 'viz-packet ' + cls;
      packet.textContent = label;
      path.appendChild(packet);

      if (fromLeft) {
        packet.style.left = '0px';
        packet.style.transition = 'left ' + duration + 'ms ease-in-out';
        requestAnimationFrame(function() {
          requestAnimationFrame(function() {
            packet.style.left = (pathWidth - 20) + 'px';
          });
        });
      } else {
        packet.style.left = (pathWidth - 20) + 'px';
        packet.style.transition = 'left ' + duration + 'ms ease-in-out';
        requestAnimationFrame(function() {
          requestAnimationFrame(function() {
            packet.style.left = '0px';
          });
        });
      }

      var t = setTimeout(function() {
        packet.remove();
        resolve();
      }, duration);
      vizTimeouts.push(t);
    });
  }

  addLog('Client initiating TCP connection...');

  var t1 = setTimeout(function() {
    addLog('Client -> Server: SYN (seq=100)');
    animatePacket('syn', 'S', true, 1200).then(function() {
      addLog('Server received SYN');

      var t2 = setTimeout(function() {
        addLog('Server -> Client: SYN-ACK (seq=300, ack=101)');
        animatePacket('synack', 'SA', false, 1200).then(function() {
          addLog('Client received SYN-ACK');

          var t3 = setTimeout(function() {
            addLog('Client -> Server: ACK (ack=301)');
            animatePacket('ack', 'A', true, 1200).then(function() {
              addLog('Server received ACK');
              addLog('TCP connection ESTABLISHED');
              addLog('Three-way handshake complete.');
              vizAnimating = false;
            });
          }, 400);
          vizTimeouts.push(t3);
        });
      }, 400);
      vizTimeouts.push(t2);
    });
  }, 500);
  vizTimeouts.push(t1);
}

function resetNetworkViz() {
  vizAnimating = false;
  for (var i = 0; i < vizTimeouts.length; i++) {
    clearTimeout(vizTimeouts[i]);
  }
  vizTimeouts = [];

  var path = document.querySelector('.viz-path');
  if (path) {
    var packets = path.querySelectorAll('.viz-packet');
    for (var j = 0; j < packets.length; j++) {
      packets[j].remove();
    }
  }

  var log = document.querySelector('.viz-log');
  if (log) {
    log.innerHTML = '<div class="viz-log-entry"><span class="viz-time">[0.00s]</span> Ready. Click "Start Handshake" to begin.</div>';
  }
}


/* === 14-crypto-demo.js === */
/* GatewayOS2 — Crypto Playground */

function caesarCipher(text, shift, decrypt) {
  var s = decrypt ? (26 - (shift % 26)) : (shift % 26);
  var result = '';
  for (var i = 0; i < text.length; i++) {
    var c = text.charCodeAt(i);
    if (c >= 65 && c <= 90) {
      result += String.fromCharCode(((c - 65 + s) % 26) + 65);
    } else if (c >= 97 && c <= 122) {
      result += String.fromCharCode(((c - 97 + s) % 26) + 97);
    } else {
      result += text[i];
    }
  }
  return result;
}

function vigenereCipher(text, key, decrypt) {
  if (!key) return text;
  var k = key.toUpperCase();
  var result = '';
  var ki = 0;
  for (var i = 0; i < text.length; i++) {
    var c = text.charCodeAt(i);
    var shift = k.charCodeAt(ki % k.length) - 65;
    if (decrypt) shift = 26 - shift;
    if (c >= 65 && c <= 90) {
      result += String.fromCharCode(((c - 65 + shift) % 26) + 65);
      ki++;
    } else if (c >= 97 && c <= 122) {
      result += String.fromCharCode(((c - 97 + shift) % 26) + 97);
      ki++;
    } else {
      result += text[i];
    }
  }
  return result;
}

function xorCipher(text, key) {
  if (!key) return text;
  var result = '';
  for (var i = 0; i < text.length; i++) {
    var xored = text.charCodeAt(i) ^ key.charCodeAt(i % key.length);
    result += String.fromCharCode(xored);
  }
  return result;
}

function base64Encode(text) {
  try { return btoa(text); }
  catch (e) { return '[Error: invalid input]'; }
}

function base64Decode(text) {
  try { return atob(text); }
  catch (e) { return '[Error: invalid base64]'; }
}

function updateCrypto() {
  var algoSelect = document.getElementById('crypto-algo');
  var keyInput = document.getElementById('crypto-key');
  var inputArea = document.getElementById('crypto-input');
  var outputArea = document.getElementById('crypto-output');

  if (!algoSelect || !inputArea || !outputArea) return;

  var algo = algoSelect.value;
  var key = keyInput ? keyInput.value : '';
  var input = inputArea.value;
  var output = '';

  if (algo === 'caesar') {
    var shift = parseInt(key) || 3;
    output = caesarCipher(input, shift, false);
  } else if (algo === 'vigenere') {
    output = vigenereCipher(input, key || 'KEY', false);
  } else if (algo === 'xor') {
    var xored = xorCipher(input, key || 'K');
    output = '';
    for (var i = 0; i < xored.length; i++) {
      var hex = xored.charCodeAt(i).toString(16);
      output += (hex.length < 2 ? '0' : '') + hex + ' ';
    }
    output = output.trim();
  } else if (algo === 'base64') {
    output = base64Encode(input);
  }

  outputArea.value = output;
}

function decryptCrypto() {
  var algoSelect = document.getElementById('crypto-algo');
  var keyInput = document.getElementById('crypto-key');
  var inputArea = document.getElementById('crypto-input');
  var outputArea = document.getElementById('crypto-output');

  if (!algoSelect || !inputArea || !outputArea) return;

  var algo = algoSelect.value;
  var key = keyInput ? keyInput.value : '';
  var input = outputArea.value;
  var output = '';

  if (algo === 'caesar') {
    var shift = parseInt(key) || 3;
    output = caesarCipher(input, shift, true);
  } else if (algo === 'vigenere') {
    output = vigenereCipher(input, key || 'KEY', true);
  } else if (algo === 'xor') {
    var bytes = input.trim().split(/\s+/);
    var text = '';
    for (var i = 0; i < bytes.length; i++) {
      text += String.fromCharCode(parseInt(bytes[i], 16));
    }
    output = xorCipher(text, key || 'K');
  } else if (algo === 'base64') {
    output = base64Decode(input);
  }

  inputArea.value = output;
}

document.addEventListener('DOMContentLoaded', function() {
  var inputArea = document.getElementById('crypto-input');
  var algoSelect = document.getElementById('crypto-algo');
  var keyInput = document.getElementById('crypto-key');

  if (inputArea) {
    inputArea.addEventListener('input', updateCrypto);
  }
  if (algoSelect) {
    algoSelect.addEventListener('change', updateCrypto);
  }
  if (keyInput) {
    keyInput.addEventListener('input', updateCrypto);
  }
});


/* === 15-turnstile.js === */
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
