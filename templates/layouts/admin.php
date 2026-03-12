<?php // Uses $content, $page_title, $unread_count from View::render() ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include BASE_DIR . '/templates/partials/head.php'; ?>
  <link rel="stylesheet" href="/css/admin.css<?php echo isset($asset_version) ? '?v=' . $asset_version : ''; ?>">
</head>
<body class="admin-body">
  <a href="#main-content" class="skip-nav">Skip to content</a>

  <aside class="admin-sidebar" role="navigation" aria-label="Admin navigation">
    <div class="admin-sidebar-header">
      <a href="/admin" class="admin-logo">
        <div class="icon" aria-hidden="true">G</div>
        <span>GatewayOS2</span>
      </a>
    </div>
    <nav class="admin-nav">
      <?php
      $admin_path = $current_path ?? '';
      $admin_links = [
          ['url' => '/admin', 'label' => 'Dashboard', 'icon' => 'D', 'exact' => true],
          ['url' => '/admin/blog', 'label' => 'Blog', 'icon' => 'B', 'exact' => false],
          ['url' => '/admin/messages', 'label' => 'Messages', 'icon' => 'M', 'exact' => false],
          ['url' => '/admin/users', 'label' => 'Users', 'icon' => 'U', 'exact' => false],
          ['url' => '/admin/analytics', 'label' => 'Analytics', 'icon' => 'A', 'exact' => false],
      ];
      foreach ($admin_links as $link):
          $is_active = $link['exact']
              ? ($admin_path === $link['url'])
              : (strpos($admin_path, $link['url']) === 0);
      ?>
        <a href="<?php echo $link['url']; ?>" class="admin-nav-item<?php echo $is_active ? ' active' : ''; ?>">
          <span class="admin-nav-icon"><?php echo $link['icon']; ?></span>
          <span class="admin-nav-label"><?php echo $link['label']; ?></span>
          <?php if ($link['label'] === 'Messages' && !empty($unread_count) && $unread_count > 0): ?>
            <span class="admin-badge"><?php echo (int)$unread_count; ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </aside>

  <div class="admin-main">
    <header class="admin-header">
      <h1 class="admin-header-title">Admin Panel</h1>
      <a href="/" class="admin-back-link">Back to site</a>
    </header>
    <main id="main-content" role="main" class="admin-content">
      <?php include BASE_DIR . '/templates/partials/flash.php'; ?>
      <?php echo $content; ?>
    </main>
  </div>

  <script src="/js/main.js<?php echo isset($asset_version) ? '?v=' . $asset_version : ''; ?>"></script>
  <script src="/js/admin.js<?php echo isset($asset_version) ? '?v=' . $asset_version : ''; ?>"></script>
</body>
</html>
