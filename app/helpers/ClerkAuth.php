<?php
/**
 * Minimal Clerk authentication adapter for the PHP 8.0 application.
 *
 * Clerk's current PHP SDK requires PHP 8.2. ELLCY still runs on XAMPP PHP 8.0,
 * so this adapter follows Clerk's documented manual JWT verification flow:
 * RS256 signature, time claims, issuer, authorized party and session status.
 */
final class ClerkAuth
{
    private const CLOCK_SKEW = 5;

    public static function isEnabled(): bool
    {
        return strtolower(trim((string)getenv('ELLCY_AUTH_PROVIDER'))) === 'clerk'
            || self::publishableKey() !== ''
            || self::jwtKey() !== ''
            || trim((string)getenv('ELLCY_CLERK_ADMIN_USER_IDS')) !== '';
    }

    public static function isConfigured(): bool
    {
        return self::publishableKey() !== ''
            && self::jwtKey() !== ''
            && self::adminUserIds() !== [];
    }

    public static function publishableKey(): string
    {
        return trim((string)getenv('CLERK_PUBLISHABLE_KEY'));
    }

    public static function scriptUrl(): string
    {
        return 'https://cdn.jsdelivr.net/npm/@clerk/clerk-js@5/dist/clerk.browser.js';
    }

    public static function adminUserIds(): array
    {
        $raw = (string)(getenv('ELLCY_CLERK_ADMIN_USER_IDS') ?: '');
        return array_values(array_filter(array_map('trim', explode(',', $raw)), static fn(string $id): bool =>
            preg_match('/^user_[A-Za-z0-9]+$/', $id) === 1
        ));
    }

    public static function verifyRequest(): ?array
    {
        if (!self::isConfigured()) return null;

        $token = self::requestToken();
        if ($token === '') return null;

        $segments = explode('.', $token);
        if (count($segments) !== 3) return null;

        [$encodedHeader, $encodedPayload, $encodedSignature] = $segments;
        $header = self::decodeJsonSegment($encodedHeader);
        $claims = self::decodeJsonSegment($encodedPayload);
        $signature = self::base64UrlDecode($encodedSignature);
        if (!$header || !$claims || $signature === false) return null;

        if (($header['alg'] ?? '') !== 'RS256' || ($header['typ'] ?? 'JWT') !== 'JWT') return null;
        $verified = openssl_verify(
            $encodedHeader . '.' . $encodedPayload,
            $signature,
            self::jwtKey(),
            OPENSSL_ALGO_SHA256
        );
        if ($verified !== 1) return null;

        $now = time();
        $exp = filter_var($claims['exp'] ?? null, FILTER_VALIDATE_INT);
        $nbf = filter_var($claims['nbf'] ?? null, FILTER_VALIDATE_INT);
        $iat = filter_var($claims['iat'] ?? null, FILTER_VALIDATE_INT);
        if ($exp === false || $exp < ($now - self::CLOCK_SKEW)) return null;
        if ($nbf !== false && $nbf > ($now + self::CLOCK_SKEW)) return null;
        if ($iat !== false && $iat > ($now + self::CLOCK_SKEW)) return null;

        $subject = (string)($claims['sub'] ?? '');
        $session = (string)($claims['sid'] ?? '');
        if (!preg_match('/^user_[A-Za-z0-9]+$/', $subject) || !preg_match('/^sess_[A-Za-z0-9]+$/', $session)) {
            return null;
        }

        $issuer = rtrim((string)($claims['iss'] ?? ''), '/');
        $expectedIssuer = rtrim(self::issuer(), '/');
        if ($expectedIssuer === '' || !hash_equals($expectedIssuer, $issuer)) return null;

        $authorizedParty = rtrim((string)($claims['azp'] ?? ''), '/');
        if ($authorizedParty !== '' && !in_array($authorizedParty, self::authorizedParties(), true)) return null;

        if (($claims['sts'] ?? '') === 'pending') return null;
        return $claims;
    }

    public static function isAdmin(array $claims): bool
    {
        return in_array((string)($claims['sub'] ?? ''), self::adminUserIds(), true);
    }

    public static function establishAdminSession(array $claims): void
    {
        $subject = (string)$claims['sub'];
        $_SESSION['admin_id'] = null;
        $_SESSION['admin_clerk_user_id'] = $subject;
        $_SESSION['admin_name'] = trim((string)(getenv('ELLCY_CLERK_ADMIN_NAME') ?: 'ELLCY Admin'));
        $_SESSION['admin_role'] = 'superadmin';
        $_SESSION['admin_auth'] = 'clerk';
    }

    private static function requestToken(): string
    {
        $authorization = trim((string)($_SERVER['HTTP_AUTHORIZATION'] ?? ''));
        if (preg_match('/^Bearer\s+([^\s]+)$/i', $authorization, $match)) return $match[1];
        return trim((string)($_COOKIE['__session'] ?? ''));
    }

    private static function jwtKey(): string
    {
        $key = trim((string)(getenv('CLERK_JWT_KEY') ?: getenv('CLERK_PEM_PUBLIC_KEY') ?: ''));
        return str_replace('\\n', "\n", $key);
    }

    private static function issuer(): string
    {
        $configured = trim((string)(getenv('CLERK_ISSUER') ?: ''));
        if ($configured !== '') return $configured;

        $key = self::publishableKey();
        if (!preg_match('/^pk_(?:test|live)_(.+)$/', $key, $match)) return '';
        $decoded = self::base64UrlDecode($match[1]);
        if ($decoded === false) return '';
        $frontendApi = rtrim($decoded, "$\0 \t\n\r\x0B");
        return preg_match('/^[a-z0-9.-]+$/i', $frontendApi) ? 'https://' . $frontendApi : '';
    }

    private static function authorizedParties(): array
    {
        $configured = array_filter(array_map('trim', explode(',', (string)(getenv('ELLCY_CLERK_AUTHORIZED_PARTIES') ?: ''))));
        $origins = array_merge([APP_ORIGIN], $configured);
        return array_values(array_unique(array_map(static fn(string $origin): string => rtrim($origin, '/'), $origins)));
    }

    private static function decodeJsonSegment(string $segment): ?array
    {
        $decoded = self::base64UrlDecode($segment);
        if ($decoded === false) return null;
        $value = json_decode($decoded, true, 32, JSON_BIGINT_AS_STRING);
        return is_array($value) ? $value : null;
    }

    private static function base64UrlDecode(string $value): string|false
    {
        if ($value === '' || preg_match('/[^A-Za-z0-9_-]/', $value)) return false;
        $padding = (4 - strlen($value) % 4) % 4;
        return base64_decode(strtr($value, '-_', '+/') . str_repeat('=', $padding), true);
    }
}
