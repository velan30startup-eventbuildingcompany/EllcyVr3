<?php
/**
 * ELLCY — OtpLogin Model
 * Handles one-time-passcodes for phone-number login.
 *
 * Only sha256(code) is ever stored — the raw 6-digit code exists
 * solely inside the SMS sent to the user and in the current
 * request, never logged or persisted anywhere.
 */
class OtpLogin {

    private const TTL_SECONDS      = 300; // OTP valid for 5 minutes
    private const RESEND_COOLDOWN  = 30;  // seconds between sends
    public  const MAX_ATTEMPTS     = 3;   // wrong guesses before a resend is required

    /** Generate + store a fresh OTP for this phone. Returns the raw
     *  6-digit code (caller is responsible for sending it via SMS —
     *  this model never has access to the raw code again after
     *  this call returns). Returns null if still in the resend
     *  cooldown window (caller should tell the user to wait). */
    public static function create(string $normalizedPhone, string $ip): ?string {
        $last = Database::fetchOne(
            'SELECT created_at FROM otp_logins WHERE phone = ? ORDER BY created_at DESC LIMIT 1',
            [$normalizedPhone]
        );
        if ($last && strtotime($last['created_at']) > time() - self::RESEND_COOLDOWN) {
            return null; // still cooling down
        }

        $code     = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $codeHash = hash('sha256', $code);
        $expires  = date('Y-m-d H:i:s', time() + self::TTL_SECONDS);

        // Invalidate any earlier unconsumed codes for this phone first.
        Database::query('DELETE FROM otp_logins WHERE phone = ? AND consumed_at IS NULL', [$normalizedPhone]);
        Database::insert(
            'INSERT INTO otp_logins (phone, otp_hash, expires_at, ip_address) VALUES (?,?,?,?)',
            [$normalizedPhone, $codeHash, $expires, $ip]
        );

        return $code;
    }

    /** How many seconds until this phone can request another OTP
     *  (0 if it can request one right now). Used to drive the
     *  "Resend OTP" button's cooldown state on the frontend. */
    public static function secondsUntilResend(string $normalizedPhone): int {
        $last = Database::fetchOne(
            'SELECT created_at FROM otp_logins WHERE phone = ? ORDER BY created_at DESC LIMIT 1',
            [$normalizedPhone]
        );
        if (!$last) return 0;
        $remaining = self::RESEND_COOLDOWN - (time() - strtotime($last['created_at']));
        return max(0, $remaining);
    }

    /** Result shape: ['ok' => bool, 'reason' => 'invalid'|'expired'|'locked'|'', 'attemptsLeft' => int] */
    public static function verify(string $normalizedPhone, string $code): array {
        $row = Database::fetchOne(
            'SELECT * FROM otp_logins WHERE phone = ? AND consumed_at IS NULL ORDER BY created_at DESC LIMIT 1',
            [$normalizedPhone]
        );

        if (!$row) {
            return ['ok' => false, 'reason' => 'expired', 'attemptsLeft' => 0];
        }
        if (strtotime($row['expires_at']) < time()) {
            return ['ok' => false, 'reason' => 'expired', 'attemptsLeft' => 0];
        }
        if ((int)$row['attempts'] >= self::MAX_ATTEMPTS) {
            return ['ok' => false, 'reason' => 'locked', 'attemptsLeft' => 0];
        }

        if (!hash_equals($row['otp_hash'], hash('sha256', $code))) {
            Database::query('UPDATE otp_logins SET attempts = attempts + 1 WHERE id = ?', [$row['id']]);
            $left = self::MAX_ATTEMPTS - ((int)$row['attempts'] + 1);
            return ['ok' => false, 'reason' => $left <= 0 ? 'locked' : 'invalid', 'attemptsLeft' => max(0, $left)];
        }

        Database::query('UPDATE otp_logins SET consumed_at = NOW() WHERE id = ?', [$row['id']]);
        return ['ok' => true, 'reason' => '', 'attemptsLeft' => self::MAX_ATTEMPTS];
    }
}
