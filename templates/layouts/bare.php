<?php // Minimal layout for embeds. Uses $content, $page_title ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title ?? 'GatewayOS2'); ?></title>
  <link rel="stylesheet" href="/css/style.css<?php echo isset($asset_version) ? '?v=' . $asset_version : ''; ?>">
</head>
<body>
  <?php echo $content; ?>
  <script src="/js/main.js<?php echo isset($asset_version) ? '?v=' . $asset_version : ''; ?>"></script>
</body>
</html>
