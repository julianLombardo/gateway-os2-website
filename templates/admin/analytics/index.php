<?php
// Analytics dashboard — receives $total_views (int), $daily_views (array), $top_pages (array)
$total_views = $total_views ?? 0;
$daily_views = $daily_views ?? [];
$top_pages = $top_pages ?? [];
?>
<div class="admin-section">
  <div class="admin-section-header">
    <h2>Analytics</h2>
  </div>

  <div class="admin-stats-grid" style="max-width: 400px;">
    <div class="admin-stat-card">
      <div class="admin-stat-number"><?php echo number_format($total_views); ?></div>
      <div class="admin-stat-label">Total Page Views</div>
    </div>
  </div>

  <!-- Daily Views Chart -->
  <div class="admin-panel" style="margin-top: 2rem;">
    <div class="admin-panel-header">
      <h3>Daily Views (Last 30 Days)</h3>
    </div>
    <div class="admin-chart-container">
      <canvas id="daily-chart" width="800" height="300"></canvas>
    </div>
  </div>

  <!-- Top Pages -->
  <div class="admin-panel" style="margin-top: 2rem;">
    <div class="admin-panel-header">
      <h3>Top Pages</h3>
    </div>
    <?php if (empty($top_pages)): ?>
      <p class="admin-empty">No page view data yet.</p>
    <?php else: ?>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Page</th>
            <th>Views</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($top_pages as $page): ?>
            <tr>
              <td><code><?php echo htmlspecialchars($page['path']); ?></code></td>
              <td><?php echo number_format($page['views']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <!-- Views by Day Table -->
  <div class="admin-panel" style="margin-top: 2rem;">
    <div class="admin-panel-header">
      <h3>Views by Day</h3>
    </div>
    <?php if (empty($daily_views)): ?>
      <p class="admin-empty">No daily data available.</p>
    <?php else: ?>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Views</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($daily_views as $day): ?>
            <tr>
              <td><?php echo htmlspecialchars($day['date']); ?></td>
              <td><?php echo number_format($day['views']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<script>
// Simple bar chart using canvas
(function() {
  var canvas = document.getElementById('daily-chart');
  if (!canvas) return;
  var ctx = canvas.getContext('2d');
  var data = <?php echo json_encode(array_map(function($d) {
    return ['date' => $d['date'], 'views' => (int)$d['views']];
  }, $daily_views)); ?>;

  if (!data.length) {
    ctx.fillStyle = '#8a8578';
    ctx.font = '14px sans-serif';
    ctx.fillText('No data to display', 20, 150);
    return;
  }

  var maxViews = Math.max.apply(null, data.map(function(d) { return d.views; })) || 1;
  var w = canvas.width;
  var h = canvas.height;
  var padding = { top: 20, right: 20, bottom: 50, left: 60 };
  var chartW = w - padding.left - padding.right;
  var chartH = h - padding.top - padding.bottom;
  var barW = Math.max(2, (chartW / data.length) - 2);

  // Axes
  ctx.strokeStyle = '#b8a99a';
  ctx.beginPath();
  ctx.moveTo(padding.left, padding.top);
  ctx.lineTo(padding.left, h - padding.bottom);
  ctx.lineTo(w - padding.right, h - padding.bottom);
  ctx.stroke();

  // Y-axis labels
  ctx.fillStyle = '#8a8578';
  ctx.font = '11px sans-serif';
  ctx.textAlign = 'right';
  for (var i = 0; i <= 4; i++) {
    var val = Math.round(maxViews * i / 4);
    var y = h - padding.bottom - (chartH * i / 4);
    ctx.fillText(val, padding.left - 8, y + 4);
  }

  // Bars
  ctx.fillStyle = '#c4622d';
  data.forEach(function(d, idx) {
    var barH = (d.views / maxViews) * chartH;
    var x = padding.left + (idx * (chartW / data.length)) + 1;
    var y = h - padding.bottom - barH;
    ctx.fillRect(x, y, barW, barH);
  });

  // X-axis labels (show every Nth)
  ctx.fillStyle = '#8a8578';
  ctx.font = '10px sans-serif';
  ctx.textAlign = 'center';
  var step = Math.max(1, Math.floor(data.length / 8));
  data.forEach(function(d, idx) {
    if (idx % step === 0) {
      var x = padding.left + (idx * (chartW / data.length)) + barW / 2;
      ctx.save();
      ctx.translate(x, h - padding.bottom + 12);
      ctx.rotate(-0.5);
      ctx.fillText(d.date, 0, 0);
      ctx.restore();
    }
  });
})();
</script>
