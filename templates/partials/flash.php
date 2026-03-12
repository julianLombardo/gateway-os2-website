<?php
$flash = $_SESSION['flash'] ?? null;
if ($flash) {
    unset($_SESSION['flash']);
?>
<div class="flash-message <?php echo htmlspecialchars($flash['type']); ?>" role="alert">
    <div class="container">
        <p><?php echo htmlspecialchars($flash['message']); ?></p>
        <button class="flash-close" onclick="this.parentElement.parentElement.remove()" aria-label="Close">&times;</button>
    </div>
</div>
<?php } ?>
