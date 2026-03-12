<?php
// Generic card component — receives $title, $body, $variant (optional)
$variant_class = !empty($variant) ? ' card-' . htmlspecialchars($variant) : '';
?>
<div class="card<?php echo $variant_class; ?>">
  <?php if (!empty($title)): ?>
    <h3 class="card-title"><?php echo htmlspecialchars($title); ?></h3>
  <?php endif; ?>
  <div class="card-body">
    <?php echo $body; ?>
  </div>
</div>
