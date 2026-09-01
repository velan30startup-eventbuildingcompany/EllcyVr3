<?php
/**
 * ELLCY — Database Configuration (PDO)
 * 
 * For LOCAL XAMPP DEVELOPMENT:
 * - Local development may use the XAMPP root account if no env vars are set.
 * - Auto-creates ellcy_db on first connection if missing
 * 
 * For PRODUCTION:
 * - Change DB_USER to a dedicated account
 * - Set ELLCY_DB_USER and ELLCY_DB_PASS as environment variables.
 * - Pre-create the database manually
 */
// 127.0.0.1 avoids Windows resolving `localhost` to IPv6 first and waiting
// before falling back to the XAMPP MySQL listener.
define('DB_HOST', (string)(getenv('ELLCY_DB_HOST') ?: '127.0.0.1'));
define('DB_PORT', (string)(getenv('ELLCY_DB_PORT') ?: '3306'));
define('DB_NAME', (string)(getenv('ELLCY_DB_NAME') ?: 'ellcy_db'));
define('DB_USER', (string)(getenv('ELLCY_DB_USER') ?: (APP_ENV === 'production' ? 'ellcy_user' : 'root')));
define('DB_PASS', (string)(getenv('ELLCY_DB_PASS') ?: ''));
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            if (APP_ENV === 'production' && DB_PASS === '') {
                error_log('ELLCY_DB_PASS is required in production.');
                throw new RuntimeException('Production database credentials are not configured.');
            }
            // Fail fast when local MySQL is stopped. PDO's Windows connection
            // timeout otherwise stalls every public request for ~2 seconds
            // before the bundled catalogue fallback can render.
            $probeError = 0;
            $probeMessage = '';
            $probe = @fsockopen(DB_HOST, (int)DB_PORT, $probeError, $probeMessage, 0.25);
            if ($probe === false) {
                throw new RuntimeException('Database temporarily unavailable.');
            }
            fclose($probe);
            $dsn = sprintf(
                'mysql:host=%s;port=%s;charset=%s',
                DB_HOST, DB_PORT, DB_CHARSET
            );
            try {
                // Local setup may create the database. Production credentials
                // should be scoped to the already-created application DB.
                if (APP_ENV !== 'production') {
                    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                        PDO::ATTR_TIMEOUT            => 2,
                    ]);
                    $pdo->exec(sprintf(
                        "CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
                        str_replace('`', '``', DB_NAME)
                    ));
                }
                
                // Now connect WITH the database name
                $dsnWithDb = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
                );
                self::$instance = new PDO($dsnWithDb, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_TIMEOUT            => 2,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                ]);
            } catch (PDOException $e) {
                error_log('DB Connection failed: ' . $e->getMessage());
                // Let controllers with bundled/static fallbacks recover. A
                // hard die here made every public page wait for the database
                // timeout and prevented the existing fallback catalogue from
                // rendering when local MySQL was stopped.
                throw new RuntimeException('Database temporarily unavailable.', 0, $e);
            }
        }
        return self::$instance;
    }

    public static function query(string $sql, array $params = []): PDOStatement {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchOne(string $sql, array $params = []): ?array {
        $row = self::query($sql, $params)->fetch();
        return $row ?: null;
    }

    public static function fetchAll(string $sql, array $params = []): array {
        return self::query($sql, $params)->fetchAll();
    }

    public static function insert(string $sql, array $params = []): int {
        self::query($sql, $params);
        return (int) self::getInstance()->lastInsertId();
    }
}
