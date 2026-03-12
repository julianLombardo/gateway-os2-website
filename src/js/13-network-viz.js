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
