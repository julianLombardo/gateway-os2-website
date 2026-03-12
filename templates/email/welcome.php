<?php
// Welcome email — receives $display_name
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
      <h2 style="margin:0 0 10px;color:#1a1a1a;font-size:20px;">Welcome to GatewayOS2</h2>
      <p style="color:#4a4a4a;font-size:14px;line-height:1.6;margin:0 0 20px;">
        Hi <?php echo htmlspecialchars($display_name); ?>,
      </p>
      <p style="color:#4a4a4a;font-size:14px;line-height:1.6;margin:0 0 20px;">
        Your account has been created. You now have access to the full GatewayOS2 community &mdash;
        blog comments, downloads, and more.
      </p>
      <p style="color:#4a4a4a;font-size:14px;line-height:1.6;margin:0 0 20px;">
        GatewayOS2 is a bare-metal x86 operating system built entirely from scratch &mdash;
        18,000 lines of C++, assembly, and zero external dependencies.
      </p>
      <div style="text-align:center;margin:20px 0;">
        <a href="https://gatewayos2.com/dashboard" style="display:inline-block;background:#c4622d;color:#fff;padding:12px 28px;text-decoration:none;font-weight:600;font-size:14px;">Go to Dashboard</a>
      </div>
      <p style="color:#8a8578;font-size:12px;line-height:1.5;margin:0;">
        If you didn't create this account, you can ignore this email.
      </p>
    </div>
    <div style="background:#1a1a1a;padding:15px 30px;text-align:center;">
      <span style="color:#8a8578;font-size:11px;">GatewayOS2 &mdash; 18,000 lines. Zero dependencies.</span>
    </div>
  </div>
</body>
</html>
