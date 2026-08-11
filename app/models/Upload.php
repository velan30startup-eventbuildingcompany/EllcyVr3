<?php
/** Secure temporary/customer upload records. */
class Upload {
    public static function createJewelleryReference(array $file, string $serviceSlug): array {
        $inspection = Security::inspectImageUpload(
            $file,
            REFERENCE_UPLOAD_MAX_SIZE,
            ['image/jpeg', 'image/png', 'image/webp']
        );
        if ($inspection['errors']) {
            throw new InvalidArgumentException(implode(' ', $inspection['errors']));
        }

        if (!is_dir(REFERENCE_UPLOAD_DIR) && !mkdir(REFERENCE_UPLOAD_DIR, 0755, true) && !is_dir(REFERENCE_UPLOAD_DIR)) {
            throw new RuntimeException('The upload directory is unavailable.');
        }

        $token = bin2hex(random_bytes(32));
        $filename = 'jref_' . bin2hex(random_bytes(16)) . '.' . $inspection['extension'];
        $destination = REFERENCE_UPLOAD_DIR . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('The image could not be saved.');
        }

        $path = '/uploads/references/' . $filename;
        try {
            Database::insert(
                'INSERT INTO uploads
                 (upload_type, service_slug, path, original_name, mime_type, file_size,
                  token_hash, user_id, status, expires_at)
                 VALUES (?,?,?,?,?,?,?,?,"temporary",DATE_ADD(NOW(), INTERVAL 24 HOUR))',
                [
                    'jewellery_reference',
                    $serviceSlug,
                    $path,
                    mb_substr(basename((string)($file['name'] ?? 'reference-image')), 0, 200),
                    $inspection['mime'],
                    (int)$file['size'],
                    hash('sha256', $token),
                    $_SESSION['user_id'] ?? null,
                ]
            );
        } catch (Throwable $e) {
            @unlink($destination);
            throw $e;
        }

        return ['token' => $token, 'path' => $path];
    }

    public static function removeTemporary(string $token): bool {
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) return false;
        $row = Database::fetchOne(
            'SELECT id, path FROM uploads
             WHERE token_hash=? AND status="temporary" AND order_id IS NULL AND request_id IS NULL',
            [hash('sha256', $token)]
        );
        if (!$row) return false;
        self::deletePhysicalPath((string)$row['path']);
        Database::query('DELETE FROM uploads WHERE id=?', [(int)$row['id']]);
        return true;
    }

    public static function attachToOrder(array $tokens, int $orderId, int $userId): void {
        foreach (array_slice(array_values(array_unique($tokens)), 0, 10) as $token) {
            if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) continue;
            Database::query(
                'UPDATE uploads SET order_id=?, user_id=?, status="attached", expires_at=NULL
                 WHERE token_hash=? AND status="temporary" AND expires_at>NOW()',
                [$orderId, $userId, hash('sha256', $token)]
            );
        }
    }

    public static function attachToRequest(string $token, int $requestId): void {
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) return;
        Database::query(
            'UPDATE uploads SET request_id=?, status="attached", expires_at=NULL
             WHERE token_hash=? AND status="temporary" AND expires_at>NOW()',
            [$requestId, hash('sha256', $token)]
        );
    }

    public static function forOrders(array $orderIds): array {
        $ids = array_values(array_filter(array_map('intval', $orderIds)));
        if (!$ids) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $grouped = [];
        foreach (Database::fetchAll(
            "SELECT id, order_id, upload_type, service_slug, path, original_name
             FROM uploads WHERE order_id IN ($placeholders) AND status='attached' ORDER BY id",
            $ids
        ) as $row) {
            $grouped[(int)$row['order_id']][] = $row;
        }
        return $grouped;
    }

    public static function forRequests(array $requestIds): array {
        $ids = array_values(array_filter(array_map('intval', $requestIds)));
        if (!$ids) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $grouped = [];
        foreach (Database::fetchAll(
            "SELECT id, request_id, upload_type, service_slug, path, original_name
             FROM uploads WHERE request_id IN ($placeholders) AND status='attached' ORDER BY id",
            $ids
        ) as $row) {
            $grouped[(int)$row['request_id']][] = $row;
        }
        return $grouped;
    }

    public static function purgeExpired(): void {
        $rows = Database::fetchAll(
            'SELECT id, path FROM uploads WHERE status="temporary" AND expires_at<NOW() LIMIT 100'
        );
        foreach ($rows as $row) {
            self::deletePhysicalPath((string)$row['path']);
            Database::query('DELETE FROM uploads WHERE id=?', [(int)$row['id']]);
        }
    }

    private static function deletePhysicalPath(string $path): void {
        if (!str_starts_with($path, '/uploads/references/')) return;
        $full = ROOT_PATH . str_replace('/', DIRECTORY_SEPARATOR, $path);
        $root = realpath(REFERENCE_UPLOAD_DIR);
        $real = realpath($full);
        if ($root !== false && $real !== false && str_starts_with($real, $root . DIRECTORY_SEPARATOR) && is_file($real)) {
            @unlink($real);
        }
    }
}
