<?php
/**
 * ELLCY — Mailer Helper
 * Sends transactional emails (password reset, etc.) using PHP's
 * built-in mail(). Most shared PHP hosts (cPanel, etc.) have this
 * wired to a local MTA out of the box. If your host requires SMTP
 * (many do, for deliverability), swap the body of send() for an
 * SMTP client such as PHPMailer — the calling code in
 * AuthController does not need to change either way.
 */
class Mailer {
    public static function send(string $to, string $subject, string $htmlBody): bool {
        $from = defined('MAIL_FROM') ? MAIL_FROM : ('no-reply@' . self::domain());
        $fromName = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'ELLCY';

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= 'From: ' . $fromName . ' <' . $from . ">\r\n";
        $headers .= 'Reply-To: ' . $from . "\r\n";

        try {
            $ok = @mail($to, $subject, $htmlBody, $headers);
        } catch (\Throwable $e) {
            $ok = false;
        }

        if (!$ok) {
            // Never throw — a failed email should not break the request
            // flow (the UI response must stay generic either way to
            // avoid leaking whether an address has an account). Log it
            // for the site owner to notice and configure SMTP if needed.
            error_log('[Mailer] Failed to send "' . $subject . '" to ' . $to);
        }
        return $ok;
    }

    private static function domain(): string {
        return preg_replace('/[^a-zA-Z0-9.\-]/', '', $_SERVER['HTTP_HOST'] ?? 'ellcy.in');
    }
}
