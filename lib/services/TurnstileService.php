<?php
/**
 * GatewayOS2 Website - Cloudflare Turnstile Service
 *
 * Integrates Cloudflare Turnstile CAPTCHA for bot prevention.
 * Only active when TURNSTILE_SITE_KEY is configured (non-empty).
 */

class TurnstileService
{
    /** @var string Turnstile verification endpoint */
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * Check whether Turnstile is configured and active.
     *
     * @return bool True if TURNSTILE_SITE_KEY is defined and non-empty.
     */
    public static function isActive(): bool
    {
        return defined('TURNSTILE_SITE_KEY') && TURNSTILE_SITE_KEY !== '';
    }

    /**
     * Verify a Turnstile response token with Cloudflare's API.
     *
     * @param string      $response The cf-turnstile-response token from the form.
     * @param string|null $remoteIp Optional client IP for additional validation.
     * @return bool True if verification succeeded.
     */
    public static function verify(string $response, ?string $remoteIp = null): bool
    {
        if (!self::isActive()) {
            // If Turnstile is not configured, allow all requests
            return true;
        }

        if (empty($response)) {
            return false;
        }

        $secretKey = defined('TURNSTILE_SECRET_KEY') ? TURNSTILE_SECRET_KEY : '';
        if (empty($secretKey)) {
            return true; // No secret key configured, skip verification
        }

        $postData = [
            'secret'   => $secretKey,
            'response' => $response,
        ];

        if ($remoteIp !== null) {
            $postData['remoteip'] = $remoteIp;
        }

        $ch = curl_init(self::VERIFY_URL);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($postData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($result === false || $status !== 200) {
            return false;
        }

        $data = json_decode($result, true);
        return !empty($data['success']);
    }

    /**
     * Render the Turnstile widget HTML div.
     *
     * Returns an empty string if Turnstile is not configured.
     *
     * @return string HTML div element for the widget.
     */
    public static function renderWidget(): string
    {
        if (!self::isActive()) {
            return '';
        }

        $siteKey = htmlspecialchars(TURNSTILE_SITE_KEY, ENT_QUOTES, 'UTF-8');
        return '<div class="cf-turnstile" data-sitekey="' . $siteKey . '"></div>';
    }

    /**
     * Render the Turnstile JavaScript tag.
     *
     * Returns an empty string if Turnstile is not configured.
     *
     * @return string HTML script element.
     */
    public static function renderScript(): string
    {
        if (!self::isActive()) {
            return '';
        }

        return '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
    }
}
