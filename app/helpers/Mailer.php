<?php
/**
 * ELLCY — Mailer Helper
 * Sends transactional emails through Resend on serverless production and
 * falls back to PHP mail() for traditional PHP hosting/local development.
 */
class Mailer {
    public static function send(string $to, string $subject, string $htmlBody, ?string $replyTo = null): bool {
        $from = (string)(getenv('RESEND_FROM') ?: (defined('MAIL_FROM') ? MAIL_FROM : ('no-reply@' . self::domain())));
        $fromName = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'ELLCY';
        $subject = str_replace(["\r", "\n"], ' ', $subject);
        $replyTo = $replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL) ? $replyTo : null;

        $apiKey = trim((string)(getenv('RESEND_API_KEY') ?: ''));
        if ($apiKey !== '' && function_exists('curl_init')) {
            $payload = [
                'from' => $fromName . ' <' . $from . '>',
                'to' => [$to],
                'subject' => $subject,
                'html' => $htmlBody,
            ];
            if ($replyTo !== null) $payload['reply_to'] = $replyTo;
            $curl = curl_init('https://api.resend.com/emails');
            curl_setopt_array($curl, [
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 12,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ]);
            $response = curl_exec($curl);
            $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            $ok = $response !== false && $status >= 200 && $status < 300;
            if (!$ok) error_log('[Mailer] Resend rejected "' . $subject . '" with HTTP ' . $status);
            return $ok;
        }

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= 'From: ' . $fromName . ' <' . $from . ">\r\n";
        $headers .= 'Reply-To: ' . ($replyTo ?? $from) . "\r\n";

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
