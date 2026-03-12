<?php
// Navigation bar — receives $current_path from layout/controller
$current_path = $current_path ?? ($_SERVER['REQUEST_URI'] ?? '/');
$current_path = strtok($current_path, '?'); // strip query string

$logged_in = isset($_SESSION['user_id']);
?>
  <header class="navbar" role="banner">
    <div class="container">
      <a href="/" class="logo" aria-label="GatewayOS2 Home">
        <div class="icon" aria-hidden="true">G</div>
        <span>GatewayOS2</span>
      </a>
      <button class="mobile-toggle" onclick="document.querySelector('.navbar nav').classList.toggle('open')" aria-label="Toggle navigation" aria-expanded="false">&#9776;</button>
      <nav role="navigation" aria-label="Main navigation">
        <a href="/" class="<?php echo $current_path === '/' ? 'active' : ''; ?>">Home</a>
        <a href="/features" class="<?php echo $current_path === '/features' ? 'active' : ''; ?>">Features</a>
        <a href="/apps" class="<?php echo $current_path === '/apps' ? 'active' : ''; ?>">Apps</a>
        <a href="/guide" class="<?php echo $current_path === '/guide' ? 'active' : ''; ?>">Guide</a>
        <a href="/code" class="<?php echo $current_path === '/code' ? 'active' : ''; ?>">Code</a>
        <a href="/about" class="<?php echo $current_path === '/about' ? 'active' : ''; ?>">About</a>
        <a href="/blog" class="<?php echo strpos($current_path, '/blog') === 0 ? 'active' : ''; ?>">Blog</a>
        <a href="/demo" class="<?php echo $current_path === '/demo' ? 'active' : ''; ?>">Demo</a>
        <a href="/download" class="<?php echo $current_path === '/download' ? 'active' : ''; ?>">Download</a>
        <a href="/search" class="nav-search-link<?php echo $current_path === '/search' ? ' active' : ''; ?>" aria-label="Search" title="Search">Search</a>
        <?php if ($logged_in): ?>
          <div class="nav-auth">
            <div class="nav-avatar" aria-hidden="true"><?php echo strtoupper(substr($_SESSION['display_name'] ?? 'U', 0, 1)); ?></div>
            <a href="/dashboard" class="nav-user-link"><?php echo htmlspecialchars($_SESSION['display_name'] ?? 'Account'); ?></a>
            <a href="/logout" class="btn btn-logout">Logout</a>
          </div>
        <?php else: ?>
          <div class="nav-auth">
            <a href="/login" class="nav-user-link">Sign In</a>
          </div>
        <?php endif; ?>
        <!-- Mobile-only auth links -->
        <div class="mobile-auth">
          <?php if ($logged_in): ?>
            <a href="/dashboard">Dashboard</a>
            <a href="/logout">Logout</a>
          <?php else: ?>
            <a href="/login">Sign In</a>
            <a href="/register">Create Account</a>
          <?php endif; ?>
        </div>
      </nav>
    </div>
  </header>
