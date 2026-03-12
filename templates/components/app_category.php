<?php
// App category — receives $icon, $title, $apps (array of app names)
?>
<div class="app-category">
  <h3><span class="cat-icon"><?php echo htmlspecialchars($icon); ?></span> <?php echo htmlspecialchars($title); ?></h3>
  <ul>
    <?php foreach ($apps as $app): ?>
      <li><?php echo htmlspecialchars($app); ?></li>
    <?php endforeach; ?>
  </ul>
</div>
