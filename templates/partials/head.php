<?php
// <head> content — receives $page_title, $page_desc, $page_image from layout
$og_title = isset($page_title) ? htmlspecialchars($page_title) . ' — GatewayOS2' : 'GatewayOS2 — Bare-Metal x86 Operating System';
$og_desc  = isset($page_desc) ? htmlspecialchars($page_desc) : 'A bare-metal x86 operating system built from scratch with a NeXTSTEP-inspired GUI, 50+ applications, networking stack, and Java IDE.';
$og_url   = SITE_URL . ($_SERVER['REQUEST_URI'] ?? '/');
$og_image = isset($page_image) ? $page_image : SITE_URL . '/favicon.svg';
?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $og_title; ?></title>
  <meta name="description" content="<?php echo $og_desc; ?>">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?php echo $og_title; ?>">
  <meta property="og:description" content="<?php echo $og_desc; ?>">
  <meta property="og:url" content="<?php echo htmlspecialchars($og_url); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($og_image); ?>">
  <meta property="og:site_name" content="GatewayOS2">

  <!-- Twitter / Discord -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo $og_title; ?>">
  <meta name="twitter:description" content="<?php echo $og_desc; ?>">
  <meta name="twitter:image" content="<?php echo htmlspecialchars($og_image); ?>">

  <!-- Theme -->
  <meta name="theme-color" content="#1a1a1a">

  <link rel="stylesheet" href="/css/style.css<?php echo isset($asset_version) ? '?v=' . $asset_version : ''; ?>">

<?php if (defined('UMAMI_ID') && UMAMI_ID): ?>
  <script defer src="https://cloud.umami.is/script.js" data-website-id="<?php echo htmlspecialchars(UMAMI_ID); ?>"></script>
<?php endif; ?>

<?php if (defined('TURNSTILE_SITE_KEY') && TURNSTILE_SITE_KEY): ?>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<?php endif; ?>
