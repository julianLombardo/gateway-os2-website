/* === 20-admin-charts.js === */
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


/* === 21-admin-editor.js === */
/* GatewayOS2 — Admin Markdown Editor with Live Preview */

document.addEventListener('DOMContentLoaded', function() {
  var editorTextarea = document.getElementById('editor-markdown');
  var previewPane = document.getElementById('editor-preview');
  if (!editorTextarea || !previewPane) return;

  function parseMarkdown(md) {
    var html = md;

    // Headers
    html = html.replace(/^### (.+)$/gm, '<h3>$1</h3>');
    html = html.replace(/^## (.+)$/gm, '<h2>$1</h2>');
    html = html.replace(/^# (.+)$/gm, '<h1>$1</h1>');

    // Bold and italic
    html = html.replace(/\*\*\*(.+?)\*\*\*/g, '<strong><em>$1</em></strong>');
    html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');

    // Inline code
    html = html.replace(/`([^`]+)`/g, '<code>$1</code>');

    // Code blocks
    html = html.replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>');

    // Links
    html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>');

    // Unordered lists
    html = html.replace(/^- (.+)$/gm, '<li>$1</li>');
    html = html.replace(/(<li>.*<\/li>\n?)+/g, function(match) {
      return '<ul>' + match + '</ul>';
    });

    // Ordered lists
    html = html.replace(/^\d+\. (.+)$/gm, '<li>$1</li>');

    // Blockquotes
    html = html.replace(/^> (.+)$/gm, '<blockquote>$1</blockquote>');

    // Horizontal rules
    html = html.replace(/^---$/gm, '<hr>');

    // Paragraphs: wrap lines that are not already wrapped in block tags
    var lines = html.split('\n');
    var result = [];
    var inBlock = false;
    for (var i = 0; i < lines.length; i++) {
      var line = lines[i].trim();
      if (line === '') {
        result.push('');
        continue;
      }
      if (line.match(/^<(h[1-6]|ul|ol|li|pre|blockquote|hr|div|table)/)) {
        inBlock = true;
        result.push(line);
      } else if (line.match(/^<\/(ul|ol|pre|blockquote|div|table)/)) {
        inBlock = false;
        result.push(line);
      } else if (!inBlock && !line.match(/^<(h[1-6]|li|hr)/)) {
        result.push('<p>' + line + '</p>');
      } else {
        result.push(line);
      }
    }

    return result.join('\n');
  }

  function updatePreview() {
    previewPane.innerHTML = parseMarkdown(editorTextarea.value);
  }

  editorTextarea.addEventListener('input', updatePreview);

  // Initial render
  updatePreview();

  // Tab key support in textarea
  editorTextarea.addEventListener('keydown', function(e) {
    if (e.key === 'Tab') {
      e.preventDefault();
      var start = this.selectionStart;
      var end = this.selectionEnd;
      this.value = this.value.substring(0, start) + '  ' + this.value.substring(end);
      this.selectionStart = this.selectionEnd = start + 2;
      updatePreview();
    }
  });
});


/* === 22-admin-tables.js === */
/* GatewayOS2 — Admin Table Sorting & Filtering */

document.addEventListener('DOMContentLoaded', function() {
  // Sortable tables
  document.querySelectorAll('.admin-table th[data-sort]').forEach(function(th) {
    th.style.cursor = 'pointer';
    th.addEventListener('click', function() {
      var table = this.closest('table');
      var tbody = table.querySelector('tbody');
      if (!tbody) return;

      var colIndex = Array.prototype.indexOf.call(this.parentElement.children, this);
      var sortKey = this.getAttribute('data-sort');
      var ascending = this.getAttribute('data-dir') !== 'asc';
      this.setAttribute('data-dir', ascending ? 'asc' : 'desc');

      // Reset other column sort indicators
      table.querySelectorAll('th[data-sort]').forEach(function(otherTh) {
        if (otherTh !== th) otherTh.removeAttribute('data-dir');
      });

      var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));

      rows.sort(function(a, b) {
        var aVal = a.children[colIndex] ? a.children[colIndex].textContent.trim() : '';
        var bVal = b.children[colIndex] ? b.children[colIndex].textContent.trim() : '';

        if (sortKey === 'number') {
          aVal = parseFloat(aVal) || 0;
          bVal = parseFloat(bVal) || 0;
          return ascending ? aVal - bVal : bVal - aVal;
        }

        if (sortKey === 'date') {
          aVal = new Date(aVal).getTime() || 0;
          bVal = new Date(bVal).getTime() || 0;
          return ascending ? aVal - bVal : bVal - aVal;
        }

        // Default: string sort
        aVal = aVal.toLowerCase();
        bVal = bVal.toLowerCase();
        if (aVal < bVal) return ascending ? -1 : 1;
        if (aVal > bVal) return ascending ? 1 : -1;
        return 0;
      });

      for (var i = 0; i < rows.length; i++) {
        tbody.appendChild(rows[i]);
      }
    });
  });

  // Table filter
  var filterInput = document.getElementById('admin-table-filter');
  if (filterInput) {
    filterInput.addEventListener('input', function() {
      var query = this.value.toLowerCase();
      var table = document.querySelector(this.getAttribute('data-table') || '.admin-table');
      if (!table) return;

      var tbody = table.querySelector('tbody');
      if (!tbody) return;

      tbody.querySelectorAll('tr').forEach(function(row) {
        var text = row.textContent.toLowerCase();
        row.style.display = text.indexOf(query) !== -1 ? '' : 'none';
      });
    });
  }
});
