<?php
// Terminal code block — receives $lines (array of ['type' => 'comment|prompt|cmd', 'text' => '...'])
// Optional: $style for inline styles
?>
<div class="terminal"<?php echo !empty($style) ? ' style="' . htmlspecialchars($style) . '"' : ''; ?>>
  <div class="terminal-header">
    <span class="terminal-dot red"></span>
    <span class="terminal-dot yellow"></span>
    <span class="terminal-dot green"></span>
  </div>
  <div class="terminal-body">
    <?php foreach ($lines as $i => $line): ?>
      <?php if ($line['type'] === 'comment'): ?>
        <span class="comment"><?php echo htmlspecialchars($line['text']); ?></span><br>
      <?php elseif ($line['type'] === 'prompt'): ?>
        <span class="prompt">$</span> <span class="cmd"><?php echo htmlspecialchars($line['text']); ?></span><br>
      <?php elseif ($line['type'] === 'cmd'): ?>
        <span class="cmd"><?php echo htmlspecialchars($line['text']); ?></span><br>
      <?php elseif ($line['type'] === 'blank'): ?>
        <br>
      <?php else: ?>
        <?php echo htmlspecialchars($line['text']); ?><br>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>
