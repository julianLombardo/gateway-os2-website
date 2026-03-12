<?php
/**
 * GatewayOS2 Website - Mail Service
 *
 * Sends email via Brevo (free API — 300/day) or raw SMTP fallback.
 * Extracted from public/includes/mailer.php — all existing code logic preserved.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

require_once BASE_DIR . '/lib/helpers/JsonStore.php';

class MailService
{
    /**
     * Load the mail configuration from the JSON config file.
     *
     * @return array Configuration array (brevo_api_key, smtp_user, smtp_pass, from_email, from_name, etc.)
     */
    public static function loadConfig(): array
    {
        $file = defined('MAIL_CONFIG_FILE') ? MAIL_CONFIG_FILE : BASE_DIR . '/data/mail_config.json';
        if (!file_exists($file)) {
            return [];
        }
        return json_decode(file_get_contents($file), true) ?: [];
    }

    /**
     * Check whether email sending is configured.
     *
     * @return bool True if either Brevo API key or SMTP credentials are set.
     */
    public static function isConfigured(): bool
    {
        $c = self::loadConfig();
        return !empty($c['brevo_api_key']) || (!empty($c['smtp_user']) && !empty($c['smtp_pass']));
    }

    /**
     * Send an email — tries Brevo API first, then SMTP fallback.
     *
     * @param string $to       Recipient email address.
     * @param string $subject  Email subject.
     * @param string $bodyHtml HTML body content.
     * @return string|bool True on success, error message string on failure.
     */
    public static function send(string $to, string $subject, string $bodyHtml): string|bool
    {
        $config = self::loadConfig();

        // Try Brevo API first
        if (!empty($config['brevo_api_key'])) {
            return self::sendBrevo($to, $subject, $bodyHtml, $config);
        }

        // SMTP fallback
        if (!empty($config['smtp_user']) && !empty($config['smtp_pass'])) {
            return self::sendSmtp($to, $subject, $bodyHtml, $config);
        }

        return 'Email not configured';
    }

    /**
     * Send a verification or reset code email.
     *
     * @param string $to   Recipient email address.
     * @param string $code The verification code.
     * @param string $type 'reset', 'verify', or other.
     * @return string|bool True on success, error message on failure.
     */
    public static function sendCode(string $to, string $code, string $type = 'reset'): string|bool
    {
        if ($type === 'reset') {
            $subject = "Your password reset code: $code";
            $body = self::buildCodeEmailHtml(
                $code,
                'Password Reset',
                'You requested a password reset for your GatewayOS2 account. Enter this code on the reset page:',
                'This code expires in 15 minutes. If you didn\'t request this, you can ignore this email.'
            );
        } elseif ($type === 'verify') {
            $subject = "Verify your email: $code";
            $body = self::buildCodeEmailHtml(
                $code,
                'Email Verification',
                'Welcome to GatewayOS2! Enter this code to verify your email address:',
                'This code expires in 24 hours.'
            );
        } else {
            $subject = "Your GatewayOS2 code: $code";
            $body = self::buildCodeEmailHtml(
                $code,
                'Verification Code',
                'Here is your verification code:',
                ''
            );
        }

        return self::send($to, $subject, $body);
    }

    /**
     * Build a branded HTML email template for a verification code.
     *
     * @param string $code    The code to display prominently.
     * @param string $title   Email heading.
     * @param string $message Explanatory text above the code.
     * @param string $footer  Small print below the code.
     * @return string Complete HTML document.
     */
    public static function buildCodeEmailHtml(string $code, string $title, string $message, string $footer): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f5f0e8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
  <div style="max-width:480px;margin:40px auto;background:#fff;border:1px solid #b8a99a;">
    <div style="background:#1a1a1a;padding:20px 30px;">
      <h1 style="margin:0;color:#f5f0e8;font-size:18px;font-weight:700;">GatewayOS2</h1>
    </div>
    <div style="padding:30px;">
      <h2 style="margin:0 0 10px;color:#1a1a1a;font-size:20px;">{$title}</h2>
      <p style="color:#4a4a4a;font-size:14px;line-height:1.6;margin:0 0 20px;">{$message}</p>
      <div style="background:#f5f0e8;border:2px solid #1a1a1a;padding:20px;text-align:center;margin:0 0 20px;">
        <span style="font-family:monospace;font-size:36px;font-weight:700;letter-spacing:8px;color:#c4622d;">{$code}</span>
      </div>
      <p style="color:#8a8578;font-size:12px;line-height:1.5;margin:0;">{$footer}</p>
    </div>
    <div style="background:#1a1a1a;padding:15px 30px;text-align:center;">
      <span style="color:#8a8578;font-size:11px;">GatewayOS2 &mdash; 18,000 lines. Zero dependencies.</span>
    </div>
  </div>
</body>
</html>
HTML;
    }

    // ── Private Transport Methods ─────────────────────────────────

    /**
     * Send via Brevo HTTP API (free tier: 300 emails/day).
     */
    private static function sendBrevo(string $to, string $subject, string $bodyHtml, array $config): string|bool
    {
        $apiKey    = $config['brevo_api_key'];
        $fromName  = $config['from_name'] ?? 'GatewayOS2';
        $fromEmail = $config['from_email'] ?? '';

        if (empty($fromEmail)) {
            return 'Sender email not configured';
        }

        $payload = json_encode([
            'sender'      => ['name' => $fromName, 'email' => $fromEmail],
            'to'          => [['email' => $to]],
            'subject'     => $subject,
            'htmlContent' => $bodyHtml,
        ]);

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => [
                'accept: application/json',
                'api-key: ' . $apiKey,
                'content-type: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return 'Failed to connect to email service: ' . $error;
        }

        if ($status >= 200 && $status < 300) {
            return true;
        }

        $result = json_decode($response, true);
        return $result['message'] ?? "Email API error (HTTP $status)";
    }

    /**
     * Send via raw SMTP with STARTTLS.
     */
    private static function sendSmtp(string $to, string $subject, string $bodyHtml, array $config): string|bool
    {
        $host      = $config['smtp_host'] ?? 'smtp.gmail.com';
        $port      = $config['smtp_port'] ?? 587;
        $user      = $config['smtp_user'];
        $pass      = $config['smtp_pass'];
        $fromEmail = $config['from_email'] ?? $user;
        $fromName  = $config['from_name'] ?? 'GatewayOS2';

        $socket = @fsockopen($host, $port, $errno, $errstr, 10);
        if (!$socket) return "Connection failed: $errstr";

        $response = self::smtpRead($socket);
        if (substr($response, 0, 3) !== '220') return "Bad greeting: $response";

        self::smtpWrite($socket, "EHLO localhost\r\n");
        $response = self::smtpRead($socket);

        self::smtpWrite($socket, "STARTTLS\r\n");
        $response = self::smtpRead($socket);
        if (substr($response, 0, 3) !== '220') return "STARTTLS failed: $response";

        $crypto = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);
        if (!$crypto) return 'TLS upgrade failed';

        self::smtpWrite($socket, "EHLO localhost\r\n");
        $response = self::smtpRead($socket);

        self::smtpWrite($socket, "AUTH LOGIN\r\n");
        $response = self::smtpRead($socket);
        if (substr($response, 0, 3) !== '334') return "Auth not supported: $response";

        self::smtpWrite($socket, base64_encode($user) . "\r\n");
        $response = self::smtpRead($socket);

        self::smtpWrite($socket, base64_encode($pass) . "\r\n");
        $response = self::smtpRead($socket);
        if (substr($response, 0, 3) !== '235') return "Auth failed: $response";

        self::smtpWrite($socket, "MAIL FROM:<$fromEmail>\r\n");
        $response = self::smtpRead($socket);

        self::smtpWrite($socket, "RCPT TO:<$to>\r\n");
        $response = self::smtpRead($socket);
        if (substr($response, 0, 3) !== '250') return "Recipient rejected: $response";

        self::smtpWrite($socket, "DATA\r\n");
        $response = self::smtpRead($socket);

        $date  = date('r');
        $msgId = '<' . bin2hex(random_bytes(8)) . '@gatewayos2.com>';

        $message  = "From: $fromName <$fromEmail>\r\n";
        $message .= "To: $to\r\n";
        $message .= "Subject: $subject\r\n";
        $message .= "Date: $date\r\n";
        $message .= "Message-ID: $msgId\r\n";
        $message .= "MIME-Version: 1.0\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 8bit\r\n";
        $message .= "\r\n";
        $message .= $bodyHtml . "\r\n";
        $message .= ".\r\n";

        self::smtpWrite($socket, $message);
        $response = self::smtpRead($socket);
        if (substr($response, 0, 3) !== '250') return "Send failed: $response";

        self::smtpWrite($socket, "QUIT\r\n");
        fclose($socket);

        return true;
    }

    /**
     * Write data to an SMTP socket.
     *
     * @param resource $socket The open socket.
     * @param string   $data   Raw SMTP data to send.
     */
    private static function smtpWrite($socket, string $data): void
    {
        fwrite($socket, $data);
    }

    /**
     * Read a response from an SMTP socket.
     *
     * @param resource $socket The open socket.
     * @return string Trimmed response string.
     */
    private static function smtpRead($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 512)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return trim($response);
    }
}
