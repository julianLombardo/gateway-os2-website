/* GatewayOS2 — Admin Analytics Charts (Canvas) */

function drawBarChart(canvasId, labels, data, color) {
  var canvas = document.getElementById(canvasId);
  if (!canvas) return;

  var ctx = canvas.getContext('2d');
  var dpr = window.devicePixelRatio || 1;
  var rect = canvas.getBoundingClientRect();
  canvas.width = rect.width * dpr;
  canvas.height = rect.height * dpr;
  ctx.scale(dpr, dpr);

  var w = rect.width;
  var h = rect.height;
  var padding = { top: 20, right: 20, bottom: 40, left: 50 };
  var chartW = w - padding.left - padding.right;
  var chartH = h - padding.top - padding.bottom;

  var maxVal = Math.max.apply(null, data);
  if (maxVal === 0) maxVal = 1;
  var barWidth = chartW / data.length * 0.6;
  var gap = chartW / data.length * 0.4;

  // Background
  ctx.fillStyle = '#faf7f2';
  ctx.fillRect(0, 0, w, h);

  // Grid lines
  ctx.strokeStyle = '#e0ddd6';
  ctx.lineWidth = 1;
  var gridLines = 5;
  for (var g = 0; g <= gridLines; g++) {
    var gy = padding.top + (chartH / gridLines) * g;
    ctx.beginPath();
    ctx.moveTo(padding.left, gy);
    ctx.lineTo(w - padding.right, gy);
    ctx.stroke();

    // Y-axis labels
    var yVal = Math.round(maxVal - (maxVal / gridLines) * g);
    ctx.fillStyle = '#8a8578';
    ctx.font = '10px "Space Grotesk", sans-serif';
    ctx.textAlign = 'right';
    ctx.fillText(yVal, padding.left - 8, gy + 4);
  }

  // Bars
  for (var i = 0; i < data.length; i++) {
    var barH = (data[i] / maxVal) * chartH;
    var x = padding.left + (chartW / data.length) * i + gap / 2;
    var y = padding.top + chartH - barH;

    ctx.fillStyle = color || '#c4622d';
    ctx.fillRect(x, y, barWidth, barH);

    // X-axis labels
    ctx.fillStyle = '#8a8578';
    ctx.font = '10px "Space Grotesk", sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText(labels[i], x + barWidth / 2, h - padding.bottom + 16);
  }
}

function drawLineChart(canvasId, labels, data, color) {
  var canvas = document.getElementById(canvasId);
  if (!canvas) return;

  var ctx = canvas.getContext('2d');
  var dpr = window.devicePixelRatio || 1;
  var rect = canvas.getBoundingClientRect();
  canvas.width = rect.width * dpr;
  canvas.height = rect.height * dpr;
  ctx.scale(dpr, dpr);

  var w = rect.width;
  var h = rect.height;
  var padding = { top: 20, right: 20, bottom: 40, left: 50 };
  var chartW = w - padding.left - padding.right;
  var chartH = h - padding.top - padding.bottom;

  var maxVal = Math.max.apply(null, data);
  if (maxVal === 0) maxVal = 1;

  // Background
  ctx.fillStyle = '#faf7f2';
  ctx.fillRect(0, 0, w, h);

  // Grid lines
  ctx.strokeStyle = '#e0ddd6';
  ctx.lineWidth = 1;
  var gridLines = 5;
  for (var g = 0; g <= gridLines; g++) {
    var gy = padding.top + (chartH / gridLines) * g;
    ctx.beginPath();
    ctx.moveTo(padding.left, gy);
    ctx.lineTo(w - padding.right, gy);
    ctx.stroke();

    var yVal = Math.round(maxVal - (maxVal / gridLines) * g);
    ctx.fillStyle = '#8a8578';
    ctx.font = '10px "Space Grotesk", sans-serif';
    ctx.textAlign = 'right';
    ctx.fillText(yVal, padding.left - 8, gy + 4);
  }

  // Line
  ctx.strokeStyle = color || '#c4622d';
  ctx.lineWidth = 2;
  ctx.beginPath();
  for (var i = 0; i < data.length; i++) {
    var x = padding.left + (chartW / (data.length - 1)) * i;
    var y = padding.top + chartH - (data[i] / maxVal) * chartH;
    if (i === 0) ctx.moveTo(x, y);
    else ctx.lineTo(x, y);
  }
  ctx.stroke();

  // Fill area
  ctx.lineTo(padding.left + chartW, padding.top + chartH);
  ctx.lineTo(padding.left, padding.top + chartH);
  ctx.closePath();
  ctx.fillStyle = (color || '#c4622d') + '15';
  ctx.fill();

  // Data points
  for (var j = 0; j < data.length; j++) {
    var px = padding.left + (chartW / (data.length - 1)) * j;
    var py = padding.top + chartH - (data[j] / maxVal) * chartH;

    ctx.beginPath();
    ctx.arc(px, py, 4, 0, Math.PI * 2);
    ctx.fillStyle = color || '#c4622d';
    ctx.fill();

    // X-axis labels
    ctx.fillStyle = '#8a8578';
    ctx.font = '10px "Space Grotesk", sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText(labels[j], px, h - padding.bottom + 16);
  }
}

// Auto-init charts on page load
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('[data-chart]').forEach(function(canvas) {
    var type = canvas.getAttribute('data-chart');
    var labels = JSON.parse(canvas.getAttribute('data-labels') || '[]');
    var data = JSON.parse(canvas.getAttribute('data-values') || '[]');
    var color = canvas.getAttribute('data-color') || '#c4622d';

    if (type === 'bar') {
      drawBarChart(canvas.id, labels, data, color);
    } else if (type === 'line') {
      drawLineChart(canvas.id, labels, data, color);
    }
  });
});
