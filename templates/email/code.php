<?php
// Verification/reset code email — receives $code, $title, $message, $footer
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f5f0e8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
  <div style="max-width:480px;margin:40px auto;background:#fff;border:1px solid #b8a99a;">
    <div style="background:#1a1a1a;padding:20px 30px;">
      <h1 style="margin:0;color:#f5f0e8;font-size:18px;font-weight:700;">GatewayOS2</h1>
    </div>
    <div style="padding:30px;">
      <h2 style="margin:0 0 10px;color:#1a1a1a;font-size:20px;"><?php echo htmlspecialchars($title); ?></h2>
      <p style="color:#4a4a4a;font-size:14px;line-height:1.6;margin:0 0 20px;"><?php echo htmlspecialchars($message); ?></p>
      <div style="background:#f5f0e8;border:2px solid #1a1a1a;padding:20px;text-align:center;margin:0 0 20px;">
        <span style="font-family:monospace;font-size:36px;font-weight:700;letter-spacing:8px;color:#c4622d;"><?php echo htmlspecialchars($code); ?></span>
      </div>
      <?php if (!empty($footer)): ?>
        <p style="color:#8a8578;font-size:12px;line-height:1.5;margin:0;"><?php echo htmlspecialchars($footer); ?></p>
      <?php endif; ?>
    </div>
    <div style="background:#1a1a1a;padding:15px 30px;text-align:center;">
      <span style="color:#8a8578;font-size:11px;">GatewayOS2 &mdash; 18,000 lines. Zero dependencies.</span>
    </div>
  </div>
</body>
</html>
