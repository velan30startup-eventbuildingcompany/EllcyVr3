<?php
/**
 * ELLCY — LoginHistory Model
 * Records every login attempt (password or OTP, success or
 * failure) for auditing, and lets the auth flow tell "this
 * phone/email has an account but got the wrong code/password"
 * apart from "no account has ever existed for this phone/email" —
 * the latter should send the person to Create Account instead of
 * a generic error.
 */
class LoginHistory {

    public static function log(?int $userId, string $identifier, string $method, bool $success): void {
        Database::insert(
            'INSERT INTO login_history (user_id, identifier, method, success, ip_address, user_agent) VALUES (?,?,?,?,?,?)',
            [
                $userId,
                $identifier,
                $method,
                $success ? 1 : 0,
                Security::getIp(),
                substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
            ]
        );
    }

    /** Has this identifier (phone or email) ever been associated
     *  with a real account attempt before? Used only for very old
     *  history rows where the account may since have been deleted —
     *  in the normal case we simply check User::findByPhone/Email
     *  directly. Kept for completeness / future admin reporting. */
    public static function hasHistory(string $identifier): bool {
        $row = Database::fetchOne(
            'SELECT id FROM login_history WHERE identifier = ? LIMIT 1',
            [$identifier]
        );
        return $row !== null;
    }
}
