<?php
class RequestCall {

    public static function create(array $data): int {
        return Database::insert(
            'INSERT INTO request_for_call (phone, service, best_time, note, ip_address)
             VALUES (?,?,?,?,?)',
            [
                $data['phone'],
                $data['service']   ?? '',
                $data['best_time'] ?? '',
                $data['note']      ?? '',
                $data['ip']        ?? '',
            ]
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
            $q = '%' . $filters['search'] . '%';
            $where[]  = '(phone LIKE ? OR service LIKE ?)';
            $params   = array_merge($params, [$q, $q]);
        }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $params[] = $limit;
        $params[] = $offset;
        return Database::fetchAll(
            "SELECT * FROM request_for_call $whereStr ORDER BY created_at DESC LIMIT ? OFFSET ?",
            $params
        );
    }

    public static function countAll(string $status = ''): int {
        $where  = $status ? 'WHERE status = ?' : '';
        $params = $status ? [$status] : [];
        $row = Database::fetchOne("SELECT COUNT(*) AS c FROM request_for_call $where", $params);
        return (int)($row['c'] ?? 0);
    }

    public static function updateStatus(int $id, string $status, string $note = ''): void {
        Database::query(
            'UPDATE request_for_call SET status = ?, admin_note = ? WHERE id = ?',
            [$status, $note, $id]
        );
    }
}
