<?php
/**
 * ELLCY — Service Model
 */
class Service {

    public static function getAll(array $filters = []): array {
        $where  = ['s.status = "active"'];
        $params = [];

        if (!empty($filters['category_slug'])) {
            $where[]  = 'sc.slug = ?';
            $params[] = $filters['category_slug'];
        }
        if (!empty($filters['search'])) {
            $where[]  = 'MATCH(s.title, s.short_description, s.description, s.tags) AGAINST(? IN BOOLEAN MODE)';
            $params[] = '+' . implode('* +', explode(' ', $filters['search'])) . '*';
        }
        if (isset($filters['featured'])) {
            $where[]  = 's.featured = ?';
            $params[] = (int)$filters['featured'];
        }

        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $limit    = isset($filters['limit']) ? 'LIMIT ' . (int)$filters['limit'] : '';
        $offset   = isset($filters['offset']) ? 'OFFSET ' . (int)$filters['offset'] : '';

        return Database::fetchAll(
            "SELECT s.*, sc.name AS category_name, sc.slug AS category_slug
             FROM services s
             JOIN service_categories sc ON s.category_id = sc.id
             $whereStr
             ORDER BY s.sort_order ASC, s.id ASC
             $limit $offset",
            $params
        );
    }

    public static function getBySlug(string $slug): ?array {
        $service = Database::fetchOne(
            'SELECT s.*, sc.name AS category_name, sc.slug AS category_slug,
                    sc.parent_id AS category_parent_id
             FROM services s
             JOIN service_categories sc ON s.category_id = sc.id
             WHERE s.slug = ? AND s.status = "active"',
            [$slug]
        );
        if (!$service) return null;

        // Load packages
        $service['packages'] = Database::fetchAll(
            'SELECT * FROM service_packages WHERE service_id = ? AND status = "active" ORDER BY sort_order',
            [$service['id']]
        );

        // Load images
        $service['images'] = Database::fetchAll(
            'SELECT * FROM service_images WHERE service_id = ? AND status = "active" ORDER BY sort_order',
            [$service['id']]
        );

        // Load reviews
        $service['reviews'] = Database::fetchAll(
            'SELECT * FROM service_reviews WHERE service_id = ? AND approved = 1 ORDER BY id DESC LIMIT 5',
            [$service['id']]
        );

        return $service;
    }

    public static function getById(int $id): ?array {
        return Database::fetchOne(
            'SELECT s.*, sc.name AS category_name, sc.slug AS category_slug
             FROM services s
             JOIN service_categories sc ON s.category_id = sc.id
             WHERE s.id = ?',
            [$id]
        );
    }

    public static function search(string $query, int $limit = 10): array {
        $q = '%' . $query . '%';
        return Database::fetchAll(
            "SELECT s.id, s.title, s.slug, s.short_description, s.price, s.image,
                    s.category_id, sc.name AS category_name, sc.slug AS category_slug
             FROM services s
             JOIN service_categories sc ON s.category_id = sc.id
             WHERE s.status = 'active'
               AND (s.title LIKE ? OR s.short_description LIKE ? OR s.tags LIKE ?)
             ORDER BY s.featured DESC, s.sort_order ASC
             LIMIT ?",
            [$q, $q, $q, $limit]
        );
    }

    public static function getByCategory(int $categoryId, int $limit = 50): array {
        return Database::fetchAll(
            "SELECT * FROM services WHERE category_id = ? AND status = 'active'
             ORDER BY sort_order ASC, price ASC LIMIT ?",
            [$categoryId, $limit]
        );
    }

    public static function create(array $data): int {
        return Database::insert(
            'INSERT INTO services
             (category_id, title, slug, short_description, description, price, price_unit,
              page_template, image, rating, tags, availability, meta_title, meta_description,
              sort_order, featured, status)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $data['category_id'], $data['title'], $data['slug'],
                $data['short_description'] ?? '', $data['description'] ?? '',
                $data['price'] ?? 0, $data['price_unit'] ?? null,
                $data['page_template'] ?? 'sd', $data['image'] ?? '',
                $data['rating'] ?? 4.5, $data['tags'] ?? '',
                $data['availability'] ?? '', $data['meta_title'] ?? '',
                $data['meta_description'] ?? '', $data['sort_order'] ?? 0,
                (int)($data['featured'] ?? 0), $data['status'] ?? 'active',
            ]
        );
    }

    public static function update(int $id, array $data): void {
        $sets   = [];
        $params = [];
        $allowed = ['title','slug','short_description','description','price','price_unit',
                    'page_template','image','rating','tags','availability','meta_title',
                    'meta_description','sort_order','featured','status','category_id'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $sets[]   = "$field = ?";
                $params[] = $data[$field];
            }
        }
        if (!$sets) return;
        $params[] = $id;
        Database::query('UPDATE services SET ' . implode(',', $sets) . ' WHERE id = ?', $params);
    }

    public static function delete(int $id): void {
        Database::query('UPDATE services SET status = "inactive" WHERE id = ?', [$id]);
    }

    public static function generateSlug(string $title): string {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $base = $slug;
        $i    = 1;
        while (Database::fetchOne('SELECT id FROM services WHERE slug = ?', [$slug])) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
