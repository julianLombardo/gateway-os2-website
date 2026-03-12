<?php
// Alert/notification — receives $type ('success'|'error'|'warning'), $message
?>
<div class="auth-alert <?php echo htmlspecialchars($type); ?>" role="alert">
  <p><?php echo htmlspecialchars($message); ?></p>
</div>
