<?php
/**
 * ELLCY — Category Model
 * Backs the admin "Categories" page. Public pages still read the
 * static CATEGORY_MAPPINGS in js/data.js for now — this table is
 * the source of truth that the admin panel edits and that
 * ServiceController's category API endpoints read from.
 */
class Category {

    public static function getAll(bool $activeOnly = false): array {
        $where = $activeOnly ? "WHERE status = 'active'" : '';
        return Database::fetchAll(
            "SELECT c.*, p.name AS parent_name,
                    (SELECT COUNT(*) FROM services s WHERE s.category_id = c.id AND s.status = 'active') AS service_count
             FROM service_categories c
             LEFT JOIN service_categories p ON c.parent_id = p.id
             $where
             ORDER BY c.sort_order ASC, c.name ASC"
        );
    }

    public static function getById(int $id): ?array {
        return Database::fetchOne('SELECT * FROM service_categories WHERE id = ?', [$id]);
    }

    public static function getBySlug(string $slug): ?array {
        return Database::fetchOne('SELECT * FROM service_categories WHERE slug = ?', [$slug]);
    }

    public static function create(array $data): int {
        return Database::insert(
            'INSERT INTO service_categories
             (parent_id, name, slug, description, image, sort_order, hidden, status)
             VALUES (?,?,?,?,?,?,?,?)',
            [
                $data['parent_id'] ?: null,
                $data['name'],
                $data['slug'],
                $data['description'] ?? '',
                $data['image'] ?? '',
                $data['sort_order'] ?? 0,
                $data['hidden'] ?? 0,
                $data['status'] ?? 'active',
            ]
        );
    }

    public static function update(int $id, array $data): void {
        $sets    = [];
        $params  = [];
        $allowed = ['parent_id','name','slug','description','image','sort_order','hidden','status'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $sets[]   = "$field = ?";
                $params[] = $data[$field] === '' && $field === 'parent_id' ? null : $data[$field];
            }
        }
        if (!$sets) return;
        $params[] = $id;
        Database::query('UPDATE service_categories SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
    }

    /** Soft-delete: mark inactive rather than removing the row, since
     *  services still reference category_id (matches Service::delete). */
    public static function delete(int $id): void {
        Database::query("UPDATE service_categories SET status = 'inactive' WHERE id = ?", [$id]);
    }

    public static function generateSlug(string $name): string {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $base = $slug;
        $i    = 1;
        while (Database::fetchOne('SELECT id FROM service_categories WHERE slug = ?', [$slug])) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
