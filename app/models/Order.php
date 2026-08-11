<?php
/**
 * ELLCY — Order Model
 */
class Order {

    public static function create(array $data): int {
        $ref = 'EL' . strtoupper(bin2hex(random_bytes(5)));
        return Database::insert(
            'INSERT INTO orders
             (order_ref, user_id, name, email, phone, event_type, event_date,
              event_venue, event_venue_images, event_time, guest_count, items_json, subtotal,
              discount, total, note, status)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $ref,
                $data['user_id']    ?? null,
                $data['name'],
                $data['email']      ?? '',
                $data['phone'],
                $data['event_type'] ?? '',
                $data['event_date'] ?? null,
                $data['event_venue']?? '',
                $data['event_venue_images'] ?? null,
                $data['event_time'] ?? '',
                $data['guest_count']?? null,
                json_encode($data['items'] ?? []),
                $data['subtotal']   ?? 0,
                $data['discount']   ?? 0,
                $data['total']      ?? 0,
                $data['note']       ?? '',
                'pending',
            ]
        );
    }

    public static function getByRef(string $ref): ?array {
        return Database::fetchOne('SELECT * FROM orders WHERE order_ref = ?', [$ref]);
    }

    /** Orders placed by a logged-in user — powers "My Bookings" / "My Orders". */
    public static function getByUserId(int $userId, int $limit = 50): array {
        return Database::fetchAll(
            'SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ?',
            [$userId, $limit]
        );
    }

    public static function getAll(array $filters = [], int $limit = 20, int $offset = 0): array {
        $where  = [];
        $params = [];
        if (!empty($filters['status'])) {
            $where[]  = 'status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[]  = '(name LIKE ? OR phone LIKE ? OR order_ref LIKE ?)';
            $q = '%' . $filters['search'] . '%';
            $params   = array_merge($params, [$q, $q, $q]);
        }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $params[] = $limit;
        $params[] = $offset;
        return Database::fetchAll(
            "SELECT * FROM orders $whereStr ORDER BY created_at DESC LIMIT ? OFFSET ?",
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
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $row = Database::fetchOne("SELECT COUNT(*) AS c FROM orders $whereStr", $params);
        return (int)($row['c'] ?? 0);
    }

    public static function updateStatus(int $id, string $status, string $adminNote = ''): void {
        Database::query(
            'UPDATE orders SET status = ?, admin_note = ? WHERE id = ?',
            [$status, $adminNote, $id]
        );
    }

    public static function getStats(): array {
        $row = Database::fetchOne(
            "SELECT
               COUNT(*) AS total,
               SUM(CASE WHEN status='pending'   THEN 1 ELSE 0 END) AS pending,
               SUM(CASE WHEN status='confirmed' THEN 1 ELSE 0 END) AS confirmed,
               SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) AS completed,
               COALESCE(SUM(total),0) AS revenue
             FROM orders"
        );
        return $row ?: ['total'=>0,'pending'=>0,'confirmed'=>0,'completed'=>0,'revenue'=>0];
    }
}
