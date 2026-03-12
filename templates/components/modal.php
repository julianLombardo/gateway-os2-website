<?php
// Modal dialog — receives $id, $title, $content (HTML string)
?>
<div class="modal-overlay" id="<?php echo htmlspecialchars($id); ?>" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
  <div class="modal-content">
    <div class="modal-header">
      <h3><?php echo htmlspecialchars($title); ?></h3>
      <button class="modal-close" onclick="document.getElementById('<?php echo htmlspecialchars($id); ?>').style.display='none'" aria-label="Close modal">&times;</button>
    </div>
    <div class="modal-body">
      <?php echo $content; ?>
    </div>
  </div>
</div>
