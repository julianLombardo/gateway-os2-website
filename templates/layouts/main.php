<?php // Uses $content, $page_title, $page_desc, $page_image from View::render() ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include BASE_DIR . '/templates/partials/head.php'; ?>
</head>
<body>
  <a href="#main-content" class="skip-nav">Skip to content</a>
  <?php include BASE_DIR . '/templates/partials/navbar.php'; ?>
  <main id="main-content" role="main">
    <?php include BASE_DIR . '/templates/partials/flash.php'; ?>
    <?php echo $content; ?>
  </main>
  <?php include BASE_DIR . '/templates/partials/footer.php'; ?>
  <script src="/js/main.js<?php echo isset($asset_version) ? '?v=' . $asset_version : ''; ?>"></script>
</body>
</html>
