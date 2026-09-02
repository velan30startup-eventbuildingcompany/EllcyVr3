<?php
/**
 * ELLCY — ServiceController
 */
class ServiceController {

    private static function publicService(array $row): array {
        return array_intersect_key($row, array_flip(['id','category_id','title','slug','short_description','description','price','price_unit','page_template','image','rating','tags','availability','featured','category_name','category_slug','packages','images','reviews']));
    }
    private static function publicPackage(array $row): array {
        return array_intersect_key($row, array_flip(['id','service_id','pkg_key','label','slug','price','description','image','sort_order']));
    }
    private static function publicImage(array $row): array {
        return array_intersect_key($row, array_flip(['id','service_id','path','thumbnail','alt','media_type','video_provider','is_primary','sort_order']));
    }
    private static function publicReview(array $row): array {
        return array_intersect_key($row, array_flip(['reviewer','rating','review_text']));
    }
    private static function publicApiHeaders(): void {
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: public, max-age=60, stale-while-revalidate=300');
    }

    // Every image path stored in the DB (admin uploads, seed data) is
    // root-relative, e.g. "/uploads/services/svc_xxx.png". That is only
    // correct if the app is installed at the domain root. If it's
    // installed in a subfolder (APP_BASE = "/ellcy" etc.), a bare
    // root-relative path resolves to the wrong place in the browser and
    // the image silently fails to load — this is why admin-uploaded
    // images can appear to "not show up on the live site" even though
    // the upload and DB update both succeeded. Prefixing with the
    // same APP_BASE the PHP app already computes for itself
    // (config/app.php) makes this correct in either deployment.
    private static function withBase($path) {
        if (!$path) return $path;
        if (preg_match('#^https?://#i', $path)) return $path; // already absolute
        // Prefer the much smaller generated WebP for database records that
        // still contain their original PNG/JPEG path. The original remains
        // available to the admin editor and as a compatibility fallback.
        if (preg_match('/\.(?:png|jpe?g)$/i', $path)) {
            $relative = ltrim((string)preg_replace('/\.(?:png|jpe?g)$/i', '.webp', $path), '/');
            if (is_file(PUBLIC_PATH . '/' . $relative)) {
                $path = '/' . $relative;
            }
        }
        if (APP_BASE !== '' && strpos($path, APP_BASE . '/') === 0) return $path; // already prefixed
        if ($path[0] === '/') return APP_BASE . $path;
        return $path;
    }

    private static function applyBaseToService(array $s): array {
        if (isset($s['image']))     $s['image']     = self::withBase($s['image']);
        if (!empty($s['packages']) && is_array($s['packages'])) {
            foreach ($s['packages'] as &$package) {
                if (isset($package['image'])) $package['image'] = self::withBase($package['image']);
            }
            unset($package);
        }
        if (!empty($s['images']) && is_array($s['images'])) {
            foreach ($s['images'] as &$img) {
                if (isset($img['path'])) $img['path'] = self::withBase($img['path']);
                if (isset($img['thumbnail'])) $img['thumbnail'] = self::withBase($img['thumbnail']);
            }
            unset($img);
        }
        return $s;
    }

    // GET /services — listing page (JS-driven, PHP renders the shell)
    public function listing(): void {
        $settings = $this->getSettings();
        require VIEWS_PATH . '/pages/services.php';
    }

    // GET /search — AJAX JSON endpoint
    public function search(): void {
        self::publicApiHeaders();
        $q     = Security::sanitizeString($_GET['q'] ?? '', 100);
        $limit = Security::sanitizeInt($_GET['limit'] ?? 10, 1, 30);
        if (strlen($q) < 2) {
            echo json_encode(['results' => []]);
            return;
        }
        $results = Service::search($q, $limit);
        echo json_encode(['results' => $results]);
    }

    // GET /services/by-category/:slug — JSON for JS renderer
    public function byCategory(string $slug): void {
        self::publicApiHeaders();
        $cat = Database::fetchOne(
            'SELECT id, parent_id, name, slug, description, image FROM service_categories WHERE slug = ? AND status = "active"',
            [Security::sanitizeString($slug)]
        );
        if (!$cat) { echo json_encode(['services'=>[]]); return; }
        $services = Service::getByCategory((int)$cat['id']);
        echo json_encode(['category' => $cat, 'services' => array_map([self::class, 'publicService'], $services)]);
    }

    // ── Public JSON API ──────────────────────────────────────────────
    // These power the live, database-driven frontend (js/data.js loads
    // from here instead of using hardcoded static data).

    // GET /api/categories — full category tree (parents + children)
    public function apiCategories(): void {
        self::publicApiHeaders();
        $rows = Database::fetchAll(
            "SELECT id, parent_id, name, slug, description, image, sort_order, hidden
             FROM service_categories WHERE status='active' ORDER BY sort_order, name"
        );
        $byId = [];
        foreach ($rows as $r) { $r['children'] = []; $byId[$r['id']] = $r; }
        $tree = [];
        foreach ($byId as $id => $row) {
            if ($row['parent_id'] && isset($byId[$row['parent_id']])) {
                $byId[$row['parent_id']]['children'][] = $row;
            } else {
                $tree[] = $row;
            }
        }
        // Re-attach children (PHP arrays are copied by value above)
        foreach ($tree as &$t) {
            $t['children'] = array_values(array_filter($byId, fn($r) => $r['parent_id'] == $t['id']));
        }
        unset($t);
        array_walk($tree, function (&$t) {
            if (isset($t['image'])) $t['image'] = self::withBase($t['image']);
            foreach ($t['children'] as &$c) {
                if (isset($c['image'])) $c['image'] = self::withBase($c['image']);
            }
            unset($c);
        });
        echo json_encode(['categories' => $tree]);
    }

    // GET /api/services — all active services, optionally filtered by
    // ?category=<slug>, with their packages and gallery images attached.
    public function apiServices(): void {
        self::publicApiHeaders();
        $categorySlug = Security::sanitizeString($_GET['category'] ?? '', 120);
        $filters = [];
        if ($categorySlug) $filters['category_slug'] = $categorySlug;

        $services = Service::getAll($filters);
        $ids = array_column($services, 'id');
        $packagesByService = [];
        $imagesByService   = [];
        if ($ids) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            foreach (Database::fetchAll(
                "SELECT id,service_id,pkg_key,label,slug,price,description,image,sort_order FROM service_packages WHERE service_id IN ($placeholders) AND status='active' ORDER BY sort_order",
                $ids
            ) as $pkg) {
                $packagesByService[$pkg['service_id']][] = $pkg;
            }
            foreach (Database::fetchAll(
                "SELECT id,service_id,path,thumbnail,alt,media_type,video_provider,is_primary,sort_order FROM service_images WHERE service_id IN ($placeholders) AND status='active' ORDER BY sort_order",
                $ids
            ) as $img) {
                $imagesByService[$img['service_id']][] = $img;
            }
        }
        foreach ($services as &$s) {
            $s['packages'] = array_map([self::class, 'publicPackage'], $packagesByService[$s['id']] ?? []);
            $s['images']   = array_map([self::class, 'publicImage'], $imagesByService[$s['id']] ?? []);
            $s = self::applyBaseToService(self::publicService($s));
        }
        unset($s);
        echo json_encode(['services' => $services]);
    }

    // GET /api/catering-staff?style=banana_leaf|buffet&guest_count=N&dish_band=0-10
    // Excel-sourced lookup — see app/models/CateringStaffCalculator.php
    public function apiCateringStaff(): void {
        self::publicApiHeaders();
        header('Cache-Control: no-store');
        $style = Security::sanitizeString($_GET['style'] ?? '', 20);
        $guest = Security::sanitizeInt($_GET['guest_count'] ?? 0, 0, 100000);
        $band  = Security::sanitizeString($_GET['dish_band'] ?? '', 10);

        $result = CateringStaffCalculator::calculate($style, $guest, $band);
        if (!$result['ok']) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $result['message']]);
            return;
        }
        echo json_encode(['success' => true, 'workers' => $result['workers']]);
    }

    // GET /api/services/:slug — single service with packages, images, reviews
    public function apiServiceDetail(string $slug): void {
        self::publicApiHeaders();
        $service = Service::getBySlug(Security::sanitizeString($slug, 220));
        if (!$service) {
            http_response_code(404);
            echo json_encode(['error' => 'Service not found']);
            return;
        }
        $service['packages'] = array_map([self::class, 'publicPackage'], $service['packages'] ?? []);
        $service['images'] = array_map([self::class, 'publicImage'], $service['images'] ?? []);
        $service['reviews'] = array_map([self::class, 'publicReview'], $service['reviews'] ?? []);
        echo json_encode(['service' => self::applyBaseToService(self::publicService($service))]);
    }

    private function getSettings(): array {
        try {
            $rows = Database::fetchAll('SELECT setting_key, setting_val FROM site_settings');
            return array_column($rows, 'setting_val', 'setting_key');
        } catch (Exception $e) { return []; }
    }
}
