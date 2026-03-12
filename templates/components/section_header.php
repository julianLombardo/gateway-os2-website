<?php
// Section header — receives $overline, $heading, $description (optional)
// Optional: $overline_style, $desc_style for dark sections
?>
<div class="section-header">
  <?php if (!empty($overline)): ?>
    <span class="overline"<?php echo !empty($overline_style) ? ' style="' . htmlspecialchars($overline_style) . '"' : ''; ?>><?php echo htmlspecialchars($overline); ?></span>
  <?php endif; ?>
  <h2><?php echo $heading; ?></h2>
  <?php if (!empty($description)): ?>
    <p<?php echo !empty($desc_style) ? ' style="' . htmlspecialchars($desc_style) . '"' : ''; ?>><?php echo $description; ?></p>
  <?php endif; ?>
</div>
