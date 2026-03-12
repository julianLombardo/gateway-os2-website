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
