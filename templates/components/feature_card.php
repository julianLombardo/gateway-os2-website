<?php
// Numbered feature card — receives $number, $title, $description
// Optional: $card_style, $number_style, $title_style, $desc_style for dark sections
?>
<div class="feature-card"<?php echo !empty($card_style) ? ' style="' . htmlspecialchars($card_style) . '"' : ''; ?>>
  <div class="card-number"<?php echo !empty($number_style) ? ' style="' . htmlspecialchars($number_style) . '"' : ''; ?>><?php echo htmlspecialchars(str_pad($number, 2, '0', STR_PAD_LEFT)); ?></div>
  <h3<?php echo !empty($title_style) ? ' style="' . htmlspecialchars($title_style) . '"' : ''; ?>><?php echo htmlspecialchars($title); ?></h3>
  <p<?php echo !empty($desc_style) ? ' style="' . htmlspecialchars($desc_style) . '"' : ''; ?>><?php echo $description; ?></p>
</div>
