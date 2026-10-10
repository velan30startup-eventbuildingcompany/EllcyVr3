<?php
/**
 * One-time, environment-gated production setup.
 *
 * No credential is stored in source control. A deployment must explicitly set
 * ELLCY_AUTO_BOOTSTRAP=1 plus ELLCY_ADMIN_PASSWORD_HASH. Once the schema exists,
 * the marker check makes subsequent admin logins a single indexed read.
 */
final class ProductionBootstrap {
    private const MARKER = 'production_schema_version';
    private const VERSION = '2026-10-10.1';

    public static function runIfConfigured(): void {
        if (APP_ENV !== 'production' || getenv('ELLCY_AUTO_BOOTSTRAP') !== '1') {
            return;
        }

        $db = Database::getInstance();
        if (self::isCurrent($db)) {
            self::ensureAdmin($db);
            return;
        }

        // GET_LOCK serializes cold starts so two serverless invocations cannot
        // run the DDL concurrently. If a provider does not support it, the
        // unique tables/columns and marker still keep this process idempotent.
        $hasLock = false;
        try {
            $hasLock = (int)$db->query("SELECT GET_LOCK('ellcy_production_bootstrap', 15)")->fetchColumn() === 1;
        } catch (Throwable $ignored) {
            $hasLock = true;
        }
        if (!$hasLock) {
            throw new RuntimeException('Production setup is already running. Please retry in a few seconds.');
        }

        try {
            if (!self::isCurrent($db)) {
                self::applySqlFiles($db);
                $statement = $db->prepare(
                    'INSERT INTO site_settings (setting_key, setting_val) VALUES (?, ?) '
                    . 'ON DUPLICATE KEY UPDATE setting_val=VALUES(setting_val)'
                );
                $statement->execute([self::MARKER, self::VERSION]);
            }
            self::ensureAdmin($db);
        } finally {
            try { $db->query("SELECT RELEASE_LOCK('ellcy_production_bootstrap')"); } catch (Throwable $ignored) {}
        }
    }

    private static function isCurrent(PDO $db): bool {
        try {
            $stmt = $db->prepare('SELECT setting_val FROM site_settings WHERE setting_key=? LIMIT 1');
            $stmt->execute([self::MARKER]);
            return hash_equals(self::VERSION, (string)$stmt->fetchColumn());
        } catch (Throwable $ignored) {
            return false;
        }
    }

    private static function applySqlFiles(PDO $db): void {
        $files = [
            'ellcy_schema.sql',
            'ellcy_seed_services.sql',
            'enquiries_decoration.sql',
            'media_gallery_migration.sql',
            'new_services_plates_rangoli.sql',
            'production_update_v2_migration.sql',
            'production_update_v3_auth_header_cart.sql',
            'production_update_v4_remove_services.sql',
            'production_update_v5_event_location.sql',
            'production_update_v6_otp_reset.sql',
            'production_update_v7_phone_otp_login.sql',
            'production_update_v8_requested_changes.sql',
            'production_update_v9_vendor_vr2.sql',
            'production_update_v10_catering_admin.sql',
        ];

        foreach ($files as $file) {
            $path = ROOT_PATH . '/sql/' . $file;
            if (!is_file($path)) {
                throw new RuntimeException('Required schema file is missing: ' . $file);
            }
            $sql = (string)file_get_contents($path);
            // The managed database is already selected by TIDB_DATABASE. Never
            // create or switch databases with a hard-coded local name.
            $sql = preg_replace('/^\s*CREATE\s+DATABASE\b.*?;\s*/ims', '', $sql) ?? $sql;
            $sql = preg_replace('/^\s*USE\s+`?[^`;]+`?\s*;\s*/im', '', $sql) ?? $sql;
            // If a cold start stopped halfway through the base schema, a retry
            // must continue rather than fail on the tables/seeds already made.
            if ($file === 'ellcy_schema.sql') {
                $sql = preg_replace('/\bCREATE\s+TABLE\s+(?!IF\s+NOT\s+EXISTS)/i', 'CREATE TABLE IF NOT EXISTS ', $sql) ?? $sql;
                $sql = preg_replace('/\bINSERT\s+INTO\s+/i', 'INSERT IGNORE INTO ', $sql) ?? $sql;
            }
            foreach (self::splitStatements($sql) as $statement) {
                $db->exec($statement);
            }
        }
    }

    /** @return list<string> */
    private static function splitStatements(string $sql): array {
        $statements = [];
        $buffer = '';
        $quote = null;
        $length = strlen($sql);
        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $next = $i + 1 < $length ? $sql[$i + 1] : '';
            if ($quote === null && $char === '-' && $next === '-') {
                while ($i < $length && $sql[$i] !== "\n") $i++;
                $buffer .= "\n";
                continue;
            }
            if ($quote === null && $char === '/' && $next === '*') {
                $i += 2;
                while ($i + 1 < $length && !($sql[$i] === '*' && $sql[$i + 1] === '/')) $i++;
                $i++;
                continue;
            }
            if (($char === "'" || $char === '"' || $char === '`') && ($i === 0 || $sql[$i - 1] !== '\\')) {
                if ($quote === null) $quote = $char;
                elseif ($quote === $char) $quote = null;
            }
            if ($char === ';' && $quote === null) {
                $trimmed = trim($buffer);
                if ($trimmed !== '') $statements[] = $trimmed;
                $buffer = '';
                continue;
            }
            $buffer .= $char;
        }
        $trimmed = trim($buffer);
        if ($trimmed !== '') $statements[] = $trimmed;
        return $statements;
    }

    private static function ensureAdmin(PDO $db): void {
        $name = trim((string)getenv('ELLCY_ADMIN_USER'));
        $hash = trim((string)getenv('ELLCY_ADMIN_PASSWORD_HASH'));
        $email = trim((string)(getenv('ELLCY_ADMIN_EMAIL') ?: 'admin@ellcy.local'));
        if ($name === '' || $hash === '') {
            throw new RuntimeException('Production admin bootstrap variables are incomplete.');
        }
        $hashInfo = password_get_info($hash);
        if (($hashInfo['algoName'] ?? 'unknown') === 'unknown' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Production admin bootstrap variables are invalid.');
        }

        $stmt = $db->prepare("SELECT id FROM users WHERE role IN ('admin','superadmin') AND status='active' LIMIT 1");
        $stmt->execute();
        if ($stmt->fetchColumn()) return;

        $stmt = $db->prepare(
            "INSERT INTO users (name,email,password_hash,role,status) VALUES (?,?,?,'superadmin','active') "
            . "ON DUPLICATE KEY UPDATE name=VALUES(name),password_hash=VALUES(password_hash),role='superadmin',status='active'"
        );
        $stmt->execute([$name, $email, $hash]);
    }
}
