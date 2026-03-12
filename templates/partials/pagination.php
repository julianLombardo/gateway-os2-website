<?php
// Pagination links — receives $pagination array with: current, pages, prev, next, base_url
if (!isset($pagination) || $pagination['pages'] <= 1) return;

$base_url = $pagination['base_url'] ?? '?';
$separator = strpos($base_url, '?') !== false ? '&' : '?';
?>
<nav class="pagination" aria-label="Pagination">
  <?php if ($pagination['prev']): ?>
    <a href="<?php echo htmlspecialchars($base_url . $separator . 'page=' . $pagination['prev']); ?>" class="pagination-link pagination-prev" aria-label="Previous page">&larr; Prev</a>
  <?php else: ?>
    <span class="pagination-link pagination-prev disabled" aria-disabled="true">&larr; Prev</span>
  <?php endif; ?>

  <div class="pagination-pages">
    <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
      <?php if ($i === $pagination['current']): ?>
        <span class="pagination-link active" aria-current="page"><?php echo $i; ?></span>
      <?php elseif ($i === 1 || $i === $pagination['pages'] || abs($i - $pagination['current']) <= 2): ?>
        <a href="<?php echo htmlspecialchars($base_url . $separator . 'page=' . $i); ?>" class="pagination-link"><?php echo $i; ?></a>
      <?php elseif (abs($i - $pagination['current']) === 3): ?>
        <span class="pagination-ellipsis">&hellip;</span>
      <?php endif; ?>
    <?php endfor; ?>
  </div>

  <?php if ($pagination['next']): ?>
    <a href="<?php echo htmlspecialchars($base_url . $separator . 'page=' . $pagination['next']); ?>" class="pagination-link pagination-next" aria-label="Next page">Next &rarr;</a>
  <?php else: ?>
    <span class="pagination-link pagination-next disabled" aria-disabled="true">Next &rarr;</span>
  <?php endif; ?>
</nav>
