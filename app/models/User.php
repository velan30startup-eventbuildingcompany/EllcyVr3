<?php
/**
 * ELLCY — User Model
 * Public-facing customer accounts (role='user' in the shared users table).
 */
class User {

    public static function findByEmail(string $email): ?array {
        return Database::fetchOne("SELECT * FROM users WHERE email = ?", [$email]) ?: null;
    }

    // ── Look up by phone for OTP login. Matches against the
    //    normalized 10-digit form so "+91 98765 43210",
    //    "919876543210" and "9876543210" all resolve to the same
    //    account, however the number happens to be stored. ──
    public static function findByPhone(string $phone): ?array {
        $normalized = Security::normalizePhone($phone);
        if ($normalized === '') return null;
        return Database::fetchOne(
            "SELECT * FROM users WHERE RIGHT(REGEXP_REPLACE(phone, '[^0-9]', ''), 10) = ?",
            [$normalized]
        ) ?: null;
    }

    public static function findById(int $id): ?array {
        return Database::fetchOne("SELECT * FROM users WHERE id = ?", [$id]) ?: null;
    }

    public static function create(string $name, string $email, string $phone, string $password): int {
        return Database::insert(
            'INSERT INTO users (name, email, phone, password_hash, role, status) VALUES (?,?,?,?,\'user\',\'active\')',
            [$name, $email, $phone, Security::hashPassword($password)]
        );
    }

    public static function authenticate(string $email, string $password): ?array {
        $user = self::findByEmail($email);
        if (!$user || $user['status'] !== 'active') return null;
        if (empty($user['password_hash']) || !Security::verifyPassword($password, $user['password_hash'])) return null;
        return $user;
    }

    public static function emailExists(string $email): bool {
        return self::findByEmail($email) !== null;
    }

    /** Update the editable profile fields shown on Account Settings. */
    public static function updateProfile(int $id, string $name, string $phone, string $address = ''): void {
        Database::query(
            'UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?',
            [$name, $phone, $address, $id]
        );
    }

    public static function updatePassword(int $id, string $newPassword): void {
        Database::query(
            'UPDATE users SET password_hash = ? WHERE id = ?',
            [Security::hashPassword($newPassword), $id]
        );
    }

    // ── Forgot Password ──────────────────────────────────────────
    // Only a SHA-256 hash of the token is ever stored — the raw
    // token exists solely inside the emailed link and is never
    // logged or persisted, so a DB leak alone cannot be used to
    // reset anyone's password.
    public static function createPasswordReset(int $userId): string {
        $rawToken  = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);
        // Invalidate any previous unused tokens for this user first.
        Database::query('DELETE FROM password_resets WHERE user_id = ? AND used_at IS NULL', [$userId]);
        Database::query(
            'INSERT INTO password_resets (user_id, token_hash, expires_at, ip_address) VALUES (?,?,?,?)',
            [$userId, $tokenHash, date('Y-m-d H:i:s', time() + 3600), Security::getIp()]
        );
        return $rawToken;
    }

    /** Returns the user row for a valid, unexpired, unused token — or null. */
    public static function findByValidResetToken(string $rawToken): ?array {
        $tokenHash = hash('sha256', $rawToken);
        $row = Database::fetchOne(
            'SELECT pr.id AS reset_id, u.* FROM password_resets pr
             JOIN users u ON u.id = pr.user_id
             WHERE pr.token_hash = ? AND pr.used_at IS NULL AND pr.expires_at > NOW()',
            [$tokenHash]
        );
        return $row ?: null;
    }

    // ── 6-digit OTP password reset (replaces the emailed-link flow) ──
    // Only a SHA-256 hash of the code is ever stored — same principle
    // as the old token approach — so a leaked database never reveals
    // a usable code. attempts is capped to blunt brute-forcing a
    // 6-digit space; Security::checkRateLimit (IP-based) is the other
    // layer of defense, checked by the controller before this runs.
    public static function createPasswordResetCode(int $userId): string {
        $code     = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $codeHash = hash('sha256', $code);
        Database::query('DELETE FROM password_resets WHERE user_id = ? AND used_at IS NULL', [$userId]);
        Database::query(
            'INSERT INTO password_resets (user_id, token_hash, expires_at, ip_address, attempts) VALUES (?,?,?,?,0)',
            [$userId, $codeHash, date('Y-m-d H:i:s', time() + 600), Security::getIp()]
        );
        return $code;
    }

    /** Returns the user row for a valid, unexpired, unused code — or null.
     *  Wrong guesses increment `attempts`; after 5 the code is locked out
     *  and the user must request a new one. */
    public static function findByValidResetCode(string $email, string $code): ?array {
        $user = self::findByEmail($email);
        if (!$user) return null;

        $row = Database::fetchOne(
            'SELECT id AS reset_id, attempts FROM password_resets
             WHERE user_id = ? AND used_at IS NULL AND expires_at > NOW()
             ORDER BY id DESC LIMIT 1',
            [$user['id']]
        );
        if (!$row || (int)$row['attempts'] >= 5) return null;

        $codeHash = hash('sha256', $code);
        $match = Database::fetchOne(
            'SELECT pr.id AS reset_id, u.* FROM password_resets pr JOIN users u ON u.id = pr.user_id
             WHERE pr.id = ? AND pr.token_hash = ?',
            [$row['reset_id'], $codeHash]
        );
        if (!$match) {
            Database::query('UPDATE password_resets SET attempts = attempts + 1 WHERE id = ?', [$row['reset_id']]);
            return null;
        }
        return $match;
    }

    public static function consumePasswordReset(int $resetId): void {
        Database::query('UPDATE password_resets SET used_at = NOW() WHERE id = ?', [$resetId]);
    }

    // ── Admin: Users list ──────────────────────────────────────────
    public static function getAll(array $filters = [], int $limit = 20, int $offset = 0): array {
        $where  = [];
        $params = [];
        if (!empty($filters['status'])) {
            $where[]  = 'status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[]  = '(name LIKE ? OR email LIKE ? OR phone LIKE ?)';
            $q = '%' . $filters['search'] . '%';
            $params   = array_merge($params, [$q, $q, $q]);
        }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $params[] = $limit;
        $params[] = $offset;
        return Database::fetchAll(
            "SELECT id, name, email, phone, role, status, last_login, created_at
             FROM users $whereStr ORDER BY created_at DESC LIMIT ? OFFSET ?",
            $params
        );
    }

    public static function countAll(array $filters = []): int {
        $where  = [];
        $params = [];
        if (!empty($filters['status'])) {
            $where[]  = 'status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[]  = '(name LIKE ? OR email LIKE ? OR phone LIKE ?)';
            $q = '%' . $filters['search'] . '%';
            $params   = array_merge($params, [$q, $q, $q]);
        }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $row = Database::fetchOne("SELECT COUNT(*) AS c FROM users $whereStr", $params);
        return (int)($row['c'] ?? 0);
    }

    public static function getStats(): array {
        $row = Database::fetchOne(
            "SELECT COUNT(*) AS total,
                    SUM(CASE WHEN status='active'   THEN 1 ELSE 0 END) AS active,
                    SUM(CASE WHEN status='inactive' THEN 1 ELSE 0 END) AS inactive,
                    SUM(CASE WHEN status='banned'   THEN 1 ELSE 0 END) AS banned
             FROM users"
        );
        return $row ?: ['total'=>0,'active'=>0,'inactive'=>0,'banned'=>0];
    }

    public static function setStatus(int $id, string $status): void {
        if (!in_array($status, ['active','inactive','banned'], true)) return;
        Database::query('UPDATE users SET status = ? WHERE id = ?', [$status, $id]);
    }
}
