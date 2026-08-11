<?php
/**
 * ELLCY — AdminController
 * Handles all admin panel routes.
 */
class AdminController {

    // ── Authentication ───────────────────────────────────────────────
    public function loginPage(): void {
        if (!empty($_SESSION['admin_id'])) {
            Router::redirect('admin');
        }
        $needsSetup = !Database::fetchOne(
            "SELECT id FROM users WHERE role IN ('admin','superadmin') AND status='active' LIMIT 1"
        );
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::requireCsrf();
            if (!Security::checkRateLimit('admin_login', Security::getIp())) {
                $error = 'Too many login attempts. Please wait 60 seconds.';
            } else {
                $email    = Security::sanitizeString($_POST['email'] ?? '', 150);
                $password = $_POST['password'] ?? '';
                $user     = Database::fetchOne(
                    "SELECT * FROM users WHERE email = ? AND role IN ('admin','superadmin') AND status = 'active'",
                    [$email]
                );
                if ($user && Security::verifyPassword($password, $user['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_id']   = $user['id'];
                    $_SESSION['admin_name'] = $user['name'];
                    $_SESSION['admin_role'] = $user['role'];
                    Database::query('UPDATE users SET last_login=NOW() WHERE id=?', [$user['id']]);
                    $this->log('admin_login', 'users', 'Login from ' . Security::getIp());
                    Router::redirect('admin');
                } else {
                    $error = 'Invalid email or password.';
                    sleep(1); // Slow down brute force
                }
            }
        }
        require VIEWS_PATH . '/admin/login.php';
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        Router::redirect('admin/login');
    }

    // ── Middleware ───────────────────────────────────────────────────
    private function requireAuth(): void {
        if (empty($_SESSION['admin_id'])) {
            Router::redirect('admin/login');
        }
    }

    // ── Dashboard ────────────────────────────────────────────────────
    public function dashboard(): void {
        $this->requireAuth();
        $orderStats     = Order::getStats();
        $pending_orders = $orderStats['pending'];
        $new_requests   = RequestCall::countAll('new');

        $stats = [
            'services' => Database::fetchOne("SELECT COUNT(*) AS c FROM services WHERE status='active'")['c'] ?? 0,
            'orders'   => $orderStats['total'],
            'requests' => RequestCall::countAll(),
            'revenue'  => $orderStats['revenue'],
        ];
        $recent_orders   = Order::getAll([], 5, 0);
        $recent_requests = RequestCall::getAll([], 5, 0);
        require VIEWS_PATH . '/admin/dashboard.php';
    }

    // ── Services ─────────────────────────────────────────────────────
    public function servicesList(): void {
        $this->requireAuth();
        $q      = Security::sanitizeString($_GET['q'] ?? '', 100);
        $status = Security::sanitizeString($_GET['status'] ?? '', 20);
        $page   = max(1, Security::sanitizeInt($_GET['page'] ?? 1));
        $limit  = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $where  = ['1=1'];
        $params = [];
        if ($q) { $where[] = '(s.title LIKE ? OR s.slug LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }
        if ($status) { $where[] = 's.status = ?'; $params[] = $status; }

        $whereStr = implode(' AND ', $where);
        $services = Database::fetchAll(
            "SELECT s.*, sc.name AS category_name
             FROM services s LEFT JOIN service_categories sc ON s.category_id=sc.id
             WHERE $whereStr ORDER BY s.sort_order, s.id LIMIT ? OFFSET ?",
            array_merge($params, [$limit, $offset])
        );
        $total_count  = (int)(Database::fetchOne("SELECT COUNT(*) AS c FROM services s WHERE $whereStr", $params)['c'] ?? 0);
        $total_pages  = (int)ceil($total_count / $limit);
        $current_page = $page;

        $pending_orders = Order::getStats()['pending'];
        $new_requests   = RequestCall::countAll('new');
        require VIEWS_PATH . '/admin/services_list.php';
    }

    public function serviceCreate(): void {
        $this->requireAuth();
        $service    = null;
        $categories = Database::fetchAll("SELECT id, name FROM service_categories WHERE status='active' ORDER BY name");
        $pending_orders = Order::getStats()['pending'];
        $new_requests   = RequestCall::countAll('new');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::requireCsrf();
            $data  = $this->extractServiceData();
            $image = $this->handleImageUpload();
            if ($image) $data['image'] = $image;
            if (empty($data['slug'])) $data['slug'] = Service::generateSlug($data['title']);
            $id = Service::create($data);
            $this->log('service_create', 'services', "ID $id: {$data['title']}");
            $_SESSION['flash'] = ['msg' => 'Service created successfully.', 'type' => 'success'];
            Router::redirect("admin/services/edit/$id");
        }
        require VIEWS_PATH . '/admin/service_form.php';
    }

    public function serviceEdit(string $id): void {
        $this->requireAuth();
        $service    = Service::getById((int)$id);
        if (!$service) { http_response_code(404); echo '404 Not Found'; return; }
        $categories = Database::fetchAll("SELECT id, name FROM service_categories WHERE status='active' ORDER BY name");
        $gallery    = Database::fetchAll('SELECT * FROM service_images WHERE service_id = ? ORDER BY sort_order, id', [(int)$id]);
        $packages   = Database::fetchAll('SELECT * FROM service_packages WHERE service_id=? ORDER BY sort_order,id', [(int)$id]);
        $pending_orders = Order::getStats()['pending'];
        $new_requests   = RequestCall::countAll('new');

        $flash_message = $_SESSION['flash']['msg'] ?? null;
        $flash_type    = $_SESSION['flash']['type'] ?? 'success';
        unset($_SESSION['flash']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::requireCsrf();
            $data  = $this->extractServiceData();
            $image = $this->handleImageUpload();
            if ($image) $data['image'] = $image;
            Service::update((int)$id, $data);
            if (isset($_POST['packages']) && is_array($_POST['packages'])) {
                $this->saveServicePackages((int)$id, $_POST['packages']);
            }
            $this->log('service_update', 'services', "ID $id");
            $flash_message = 'Service updated successfully.';
            $flash_type    = 'success';
            $service = Service::getById((int)$id);
            $packages = Database::fetchAll('SELECT * FROM service_packages WHERE service_id=? ORDER BY sort_order,id', [(int)$id]);
        }
        require VIEWS_PATH . '/admin/service_form.php';
    }

    public function serviceDelete(string $id): void {
        $this->requireAuth();
        header('Content-Type: application/json');
        if (!Security::verifyCsrf()) { echo json_encode(['success'=>false,'message'=>'Invalid token']); return; }
        Service::delete((int)$id);
        $this->log('service_delete', 'services', "ID $id");
        echo json_encode(['success' => true]);
    }

    // ── Categories ───────────────────────────────────────────────────
    public function categoriesList(): void {
        $this->requireAuth();
        $categories     = Category::getAll();
        $pending_orders = Order::getStats()['pending'];
        $new_requests   = RequestCall::countAll('new');
        require VIEWS_PATH . '/admin/categories_list.php';
    }

    public function categoryCreate(): void {
        $this->requireAuth();
        $category       = null;
        $parents        = Category::getAll();
        $pending_orders = Order::getStats()['pending'];
        $new_requests   = RequestCall::countAll('new');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::requireCsrf();
            $data = $this->extractCategoryData();
            $image = $this->handleCategoryImageUpload();
            if ($image) $data['image'] = $image;
            if (empty($data['slug'])) $data['slug'] = Category::generateSlug($data['name']);
            if (empty($data['name'])) {
                $_SESSION['flash'] = ['msg' => 'Category name is required.', 'type' => 'error'];
            } else {
                $id = Category::create($data);
                $this->log('category_create', 'service_categories', "ID $id: {$data['name']}");
                $_SESSION['flash'] = ['msg' => 'Category created successfully.', 'type' => 'success'];
                Router::redirect("admin/categories/edit/$id");
            }
        }
        require VIEWS_PATH . '/admin/category_form.php';
    }

    public function categoryEdit(string $id): void {
        $this->requireAuth();
        $category = Category::getById((int)$id);
        if (!$category) { http_response_code(404); echo '404 Not Found'; return; }
        // A category can't be its own parent, and to keep the parent
        // dropdown simple we don't support grandchildren — only this
        // category's siblings/top-level categories are offered.
        $parents        = array_filter(Category::getAll(), fn($c) => (int)$c['id'] !== (int)$id);
        $pending_orders = Order::getStats()['pending'];
        $new_requests   = RequestCall::countAll('new');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::requireCsrf();
            $data  = $this->extractCategoryData();
            $image = $this->handleCategoryImageUpload();
            if ($image) $data['image'] = $image;
            Category::update((int)$id, $data);
            $this->log('category_update', 'service_categories', "ID $id: {$data['name']}");
            $_SESSION['flash'] = ['msg' => 'Category updated successfully.', 'type' => 'success'];
            Router::redirect("admin/categories/edit/$id");
        }
        require VIEWS_PATH . '/admin/category_form.php';
    }

    public function categoryDelete(string $id): void {
        $this->requireAuth();
        header('Content-Type: application/json');
        if (!Security::verifyCsrf()) { echo json_encode(['success'=>false,'message'=>'Invalid token']); return; }
        Category::delete((int)$id);
        $this->log('category_delete', 'service_categories', "ID $id");
        echo json_encode(['success' => true]);
    }

    private function extractCategoryData(): array {
        return [
            'parent_id'   => Security::sanitizeInt($_POST['parent_id'] ?? 0),
            'name'        => Security::sanitizeString($_POST['name'] ?? '', 100),
            'slug'        => Security::sanitizeString($_POST['slug'] ?? '', 120),
            'description' => Security::sanitizeString($_POST['description'] ?? '', 1000),
            'sort_order'  => Security::sanitizeInt($_POST['sort_order'] ?? 0),
            'hidden'      => !empty($_POST['hidden']) ? 1 : 0,
            'status'      => in_array($_POST['status'] ?? '', ['active','inactive']) ? $_POST['status'] : 'active',
        ];
    }

    private function handleCategoryImageUpload(): ?string {
        if (empty($_FILES['image']['tmp_name'])) return null;
        $inspection = Security::inspectImageUpload($_FILES['image']);
        if ($inspection['errors']) return null;
        $ext      = $inspection['extension'];
        $filename = 'cat_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dest     = ROOT_PATH . '/uploads/category/' . $filename;
        if (!is_dir(dirname($dest))) { mkdir(dirname($dest), 0755, true); }
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            return '/uploads/category/' . $filename;
        }
        return null;
    }

    // ── Users ────────────────────────────────────────────────────────
    public function usersList(): void {
        $this->requireAuth();
        $q      = Security::sanitizeString($_GET['q'] ?? '', 100);
        $status = Security::sanitizeString($_GET['status'] ?? '', 30);
        $page   = max(1, Security::sanitizeInt($_GET['page'] ?? 1));
        $offset = ($page - 1) * ITEMS_PER_PAGE;

        $filters = [];
        if ($q)      $filters['search'] = $q;
        if ($status) $filters['status'] = $status;

        $users        = User::getAll($filters, ITEMS_PER_PAGE, $offset);
        $total_count  = User::countAll($filters);
        $total_pages  = (int)ceil($total_count / ITEMS_PER_PAGE);
        $current_page = $page;
        $user_stats   = User::getStats();

        $pending_orders = Order::getStats()['pending'];
        $new_requests   = RequestCall::countAll('new');
        require VIEWS_PATH . '/admin/users_list.php';
    }

    public function userSetStatus(string $id): void {
        $this->requireAuth();
        header('Content-Type: application/json');
        if (!Security::verifyCsrf()) { echo json_encode(['success'=>false,'message'=>'Invalid token']); return; }
        $status = Security::sanitizeString($_POST['status'] ?? '', 20);
        if (!in_array($status, ['active','inactive','banned'], true)) {
            echo json_encode(['success'=>false,'message'=>'Invalid status']); return;
        }
        User::setStatus((int)$id, $status);
        $this->log('user_status', 'users', "ID $id -> $status");
        echo json_encode(['success' => true]);
    }

    // ── Service Gallery (photos + videos shown on the public description pages) ──
    public function serviceGalleryAdd(string $id): void {
        $this->requireAuth();
        header('Content-Type: application/json');
        if (!Security::verifyCsrf()) { echo json_encode(['success'=>false,'message'=>'Invalid token']); return; }

        try {
            $serviceId = (int)$id;
            $service = Service::getById($serviceId);
            if (!$service) { echo json_encode(['success'=>false,'message'=>'Service not found']); return; }

            $mode = $_POST['media_mode'] ?? 'image'; // 'image' | 'video_url' | 'video_upload'
            $replaceId = Security::sanitizeInt($_POST['replace_id'] ?? 0);
            $replaceRow = $replaceId ? Database::fetchOne(
                'SELECT * FROM service_images WHERE id=? AND service_id=?', [$replaceId, $serviceId]
            ) : null;
            $isDecoration = in_array($service['category_slug'] ?? '', ['stage-decoration','light-decoration'], true);
            if ($mode === 'image' && $isDecoration) {
                $imageCount = (int)(Database::fetchOne(
                    "SELECT COUNT(*) AS c FROM service_images WHERE service_id=? AND media_type='image' AND status='active'",
                    [$serviceId]
                )['c'] ?? 0);
                if ($imageCount >= 5 && !$replaceRow) {
                    echo json_encode(['success'=>false,'message'=>'Decoration services can contain a maximum of five images. Replace or remove an image first.']); return;
                }
            }
            $nextSort = $replaceRow ? (int)$replaceRow['sort_order'] : (int)(Database::fetchOne(
                'SELECT COALESCE(MAX(sort_order),0)+1 AS n FROM service_images WHERE service_id = ?', [$serviceId]
            )['n'] ?? 1);
            // First item added for a service becomes primary automatically —
            // so a brand-new service shows something without an extra step.
            $isFirstItem = $replaceRow ? !empty($replaceRow['is_primary']) : $nextSort === 1;

            if ($mode === 'video_url') {
                $url = trim($_POST['video_url'] ?? '');
                $provider = str_contains($url, 'youtu') ? 'youtube' : (str_contains($url, 'vimeo') ? 'vimeo' : null);
                if (!$provider || !filter_var($url, FILTER_VALIDATE_URL)) {
                    echo json_encode(['success'=>false,'message'=>'Please paste a valid YouTube or Vimeo URL.']); return;
                }
                [$thumbnail, $thumbnailError] = $this->handleOptionalMediaThumbnail();
                if ($thumbnailError) { echo json_encode(['success'=>false,'message'=>$thumbnailError]); return; }
                Database::query(
                    'INSERT INTO service_images (service_id, path, media_type, video_provider, thumbnail, alt, sort_order, is_primary) VALUES (?,?,?,?,?,?,?,?)',
                    [$serviceId, $url, 'video', $provider, $thumbnail, Security::sanitizeString($_POST['alt'] ?? '', 200), $nextSort, $isFirstItem ? 1 : 0]
                );
                if ($replaceRow) $this->removeServiceMediaRow($replaceRow);
                $this->log('gallery_add_video', 'service_images', "service $serviceId: $url");
                echo json_encode(['success' => true]);
                return;
            }

            // image or uploaded video file
            if (empty($_FILES['media']['name'])) {
                echo json_encode(['success'=>false,'message'=>'Please choose a file to upload.']); return;
            }
            $file = $_FILES['media'];
            if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) { echo json_encode(['success'=>false,'message'=>'Upload failed.']); return; }

            $isVideo = $mode === 'video_upload';
            $maxSize = $isVideo ? 40 * 1024 * 1024 : UPLOAD_MAX_SIZE;
            if ($file['size'] > $maxSize) { echo json_encode(['success'=>false,'message'=>'File is too large.']); return; }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $imageExt = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
            $videoExt = ['video/mp4'=>'mp4','video/webm'=>'webm','video/quicktime'=>'mov'];
            if (!$isVideo) {
                $inspection = Security::inspectImageUpload($file, UPLOAD_MAX_SIZE);
                if ($inspection['errors']) { echo json_encode(['success'=>false,'message'=>implode(' ', $inspection['errors'])]); return; }
                $ext = $inspection['extension'];
            } else {
                $ext = $videoExt[$mime] ?? null;
                $clientExt = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
                $allowedClientExt = ['video/mp4'=>['mp4'],'video/webm'=>['webm'],'video/quicktime'=>['mov']];
                if ($ext && !in_array($clientExt, $allowedClientExt[$mime] ?? [], true)) $ext = null;
            }
            if (!$ext) {
                echo json_encode(['success'=>false,'message'=> $isVideo ? 'Only MP4, WebM or MOV videos are allowed.' : 'Only JPG, PNG, WebP or GIF images are allowed.']); return;
            }

            if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
            $filename = ($isVideo ? 'video_' : 'gallery_') . bin2hex(random_bytes(10)) . '.' . $ext;
            $dest = UPLOAD_DIR . $filename;
            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                echo json_encode(['success'=>false,'message'=>'Could not save the file.']); return;
            }

            [$thumbnail, $thumbnailError] = $isVideo ? $this->handleOptionalMediaThumbnail() : [null, null];
            if ($thumbnailError) { @unlink($dest); echo json_encode(['success'=>false,'message'=>$thumbnailError]); return; }
            Database::query(
                'INSERT INTO service_images (service_id, path, media_type, thumbnail, alt, sort_order, is_primary) VALUES (?,?,?,?,?,?,?)',
                [$serviceId, '/uploads/services/' . $filename, $isVideo ? 'video' : 'image', $thumbnail, Security::sanitizeString($_POST['alt'] ?? '', 200), $nextSort, $isFirstItem ? 1 : 0]
            );
            if ($replaceRow) $this->removeServiceMediaRow($replaceRow);
            $this->log('gallery_add_' . ($isVideo?'video':'image'), 'service_images', "service $serviceId: $filename");
            echo json_encode(['success' => true]);
        } catch (\Throwable $e) {
            // Most likely cause: sql/media_gallery_migration.sql (adds the
            // media_type/video_provider columns) hasn't been run yet, so
            // the INSERT above fails with an "unknown column" error.
            error_log('[ELLCY] serviceGalleryAdd failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success'=>false,'message'=>'Could not save this media. If this keeps happening, make sure sql/media_gallery_migration.sql has been run against the database.']);
        }
    }

    public function serviceGallerySetPrimary(string $imageId): void {
        $this->requireAuth();
        header('Content-Type: application/json');
        if (!Security::verifyCsrf()) { echo json_encode(['success'=>false,'message'=>'Invalid token']); return; }

        try {
            $row = Database::fetchOne('SELECT * FROM service_images WHERE id = ?', [(int)$imageId]);
            if (!$row) { echo json_encode(['success'=>false,'message'=>'Not found']); return; }
            // Only one primary per service — clear any existing one first.
            Database::query('UPDATE service_images SET is_primary = 0 WHERE service_id = ?', [$row['service_id']]);
            Database::query('UPDATE service_images SET is_primary = 1 WHERE id = ?', [(int)$imageId]);
            $this->log('gallery_set_primary', 'service_images', "ID $imageId");
            echo json_encode(['success' => true]);
        } catch (\Throwable $e) {
            error_log('[ELLCY] serviceGallerySetPrimary failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success'=>false,'message'=>'Could not update primary media.']);
        }
    }

    public function serviceGalleryDelete(string $imageId): void {
        $this->requireAuth();
        header('Content-Type: application/json');
        if (!Security::verifyCsrf()) { echo json_encode(['success'=>false,'message'=>'Invalid token']); return; }

        try {
            $row = Database::fetchOne('SELECT * FROM service_images WHERE id = ?', [(int)$imageId]);
            if (!$row) { echo json_encode(['success'=>false,'message'=>'Not found']); return; }

            $this->removeServiceMediaRow($row);
            $this->log('gallery_delete', 'service_images', "ID $imageId");
            echo json_encode(['success' => true]);
        } catch (\Throwable $e) {
            error_log('[ELLCY] serviceGalleryDelete failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success'=>false,'message'=>'Could not delete this media item.']);
        }
    }

    public function serviceGalleryReorder(string $imageId): void {
        $this->requireAuth();
        header('Content-Type: application/json');
        if (!Security::verifyCsrf()) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Invalid token']); return; }
        $direction = ($_POST['direction'] ?? '') === 'up' ? 'up' : (($_POST['direction'] ?? '') === 'down' ? 'down' : '');
        if ($direction === '') { echo json_encode(['success'=>false,'message'=>'Invalid direction']); return; }
        $row = Database::fetchOne('SELECT id,service_id,sort_order FROM service_images WHERE id=?', [(int)$imageId]);
        if (!$row) { echo json_encode(['success'=>false,'message'=>'Media not found']); return; }
        $operator = $direction === 'up' ? '<' : '>';
        $order = $direction === 'up' ? 'DESC' : 'ASC';
        $other = Database::fetchOne(
            "SELECT id,sort_order FROM service_images WHERE service_id=? AND sort_order $operator ? ORDER BY sort_order $order,id $order LIMIT 1",
            [(int)$row['service_id'], (int)$row['sort_order']]
        );
        if ($other) {
            Database::getInstance()->beginTransaction();
            try {
                Database::query('UPDATE service_images SET sort_order=? WHERE id=?', [(int)$other['sort_order'], (int)$row['id']]);
                Database::query('UPDATE service_images SET sort_order=? WHERE id=?', [(int)$row['sort_order'], (int)$other['id']]);
                Database::getInstance()->commit();
            } catch (Throwable $e) {
                Database::getInstance()->rollBack();
                throw $e;
            }
        }
        $this->log('gallery_reorder', 'service_images', "ID $imageId $direction");
        echo json_encode(['success'=>true]);
    }

    // ── Bookings ─────────────────────────────────────────────────────
    public function bookingsList(): void {
        $this->requireAuth();
        $q      = Security::sanitizeString($_GET['q'] ?? '', 100);
        $status = Security::sanitizeString($_GET['status'] ?? '', 30);
        $page   = max(1, Security::sanitizeInt($_GET['page'] ?? 1));
        $offset = ($page - 1) * ITEMS_PER_PAGE;

        $filters = [];
        if ($q)      $filters['search'] = $q;
        if ($status) $filters['status'] = $status;

        $orders       = Order::getAll($filters, ITEMS_PER_PAGE, $offset);
        $orderUploads = Upload::forOrders(array_column($orders, 'id'));
        foreach ($orders as &$order) {
            $order['reference_uploads'] = $orderUploads[(int)$order['id']] ?? [];
        }
        unset($order);
        $total_count  = Order::countAll($filters);
        $total_pages  = (int)ceil($total_count / ITEMS_PER_PAGE);
        $current_page = $page;

        $status_counts  = [];
        foreach (['pending','confirmed','in_progress','completed','cancelled'] as $st) {
            $status_counts[$st] = Order::countAll(['status'=>$st]);
        }
        $pending_orders = $status_counts['pending'];
        $new_requests   = RequestCall::countAll('new');
        require VIEWS_PATH . '/admin/bookings.php';
    }

    public function bookingUpdate(string $id): void {
        $this->requireAuth();
        header('Content-Type: application/json');
        if (!Security::verifyCsrf()) { echo json_encode(['success'=>false,'message'=>'Invalid token']); return; }
        $status    = Security::sanitizeString($_POST['status'] ?? '', 30);
        $adminNote = Security::sanitizeString($_POST['admin_note'] ?? '', 500);
        Order::updateStatus((int)$id, $status, $adminNote);
        $this->log('booking_update', 'orders', "ID $id → $status");
        echo json_encode(['success' => true]);
    }

    // ── Call Requests ────────────────────────────────────────────────
    public function requestsList(): void {
        $this->requireAuth();
        $q      = Security::sanitizeString($_GET['q'] ?? '', 100);
        $status = Security::sanitizeString($_GET['status'] ?? '', 30);
        $page   = max(1, Security::sanitizeInt($_GET['page'] ?? 1));
        $offset = ($page - 1) * ITEMS_PER_PAGE;

        $filters = [];
        if ($q)      $filters['search'] = $q;
        if ($status) $filters['status'] = $status;

        $requests     = RequestCall::getAll($filters, ITEMS_PER_PAGE, $offset);
        $requestUploads = Upload::forRequests(array_column($requests, 'id'));
        foreach ($requests as &$request) {
            $request['reference_uploads'] = $requestUploads[(int)$request['id']] ?? [];
        }
        unset($request);
        $total_count  = RequestCall::countAll($status);
        $total_pages  = (int)ceil($total_count / ITEMS_PER_PAGE);
        $current_page = $page;

        $status_counts  = [];
        foreach (['new','called','completed','spam'] as $st) {
            $status_counts[$st] = RequestCall::countAll($st);
        }
        $pending_orders = Order::getStats()['pending'];
        $new_requests   = $status_counts['new'];
        require VIEWS_PATH . '/admin/requests.php';
    }

    public function requestUpdate(string $id): void {
        $this->requireAuth();
        header('Content-Type: application/json');
        if (!Security::verifyCsrf()) { echo json_encode(['success'=>false,'message'=>'Invalid token']); return; }
        $status = Security::sanitizeString($_POST['status'] ?? '', 30);
        $note   = Security::sanitizeString($_POST['admin_note'] ?? '', 300);
        RequestCall::updateStatus((int)$id, $status, $note);
        $this->log('rfc_update', 'request_for_call', "ID $id → $status");
        echo json_encode(['success' => true]);
    }

    // ── Decoration Enquiries (Stage + Light) ──────────────────────────
    public function decorationEnquiries(): void {
        $this->requireAuth();
        $type   = in_array($_GET['tab'] ?? '', ['stage','light'], true) ? $_GET['tab'] : 'stage';
        $status = Security::sanitizeString($_GET['status'] ?? '', 30);
        $table  = $type === 'stage' ? 'stage_decoration_enquiries' : 'light_decoration_enquiries';

        $where  = [];
        $params = [];
        if ($status) { $where[] = 'enquiry_status = ?'; $params[] = $status; }
        $sql = "SELECT * FROM $table" . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY created_at DESC LIMIT 200';
        try {
            $enquiries = Database::fetchAll($sql, $params);
        } catch (Exception $e) {
            $enquiries = []; // table may not exist yet — see sql/enquiries_decoration.sql
        }

        $status_counts = ['new'=>0,'contacted'=>0,'converted'=>0,'closed'=>0];
        try {
            foreach (Database::fetchAll("SELECT enquiry_status, COUNT(*) c FROM $table GROUP BY enquiry_status") as $row) {
                $status_counts[$row['enquiry_status']] = (int)$row['c'];
            }
        } catch (Exception $e) {}

        $page_title  = 'Decoration Enquiries';
        $active_page = 'decoration_enquiries';
        $current_tab = $type;
        $pending_orders = Order::getStats()['pending'];
        $new_requests   = RequestCall::countAll('new');
        $new_decoration_enquiries = $this->countNewDecorationEnquiries();
        require VIEWS_PATH . '/admin/decoration_enquiries.php';
    }

    public function decorationEnquiryUpdate(string $type, string $id): void {
        $this->requireAuth();
        header('Content-Type: application/json');
        if (!Security::verifyCsrf()) { echo json_encode(['success'=>false,'message'=>'Invalid token']); return; }
        if (!in_array($type, ['stage','light'], true)) { echo json_encode(['success'=>false,'message'=>'Invalid type']); return; }
        $table  = $type === 'stage' ? 'stage_decoration_enquiries' : 'light_decoration_enquiries';
        $status = Security::sanitizeString($_POST['status'] ?? '', 30);
        if (!in_array($status, ['new','contacted','converted','closed'], true)) {
            echo json_encode(['success'=>false,'message'=>'Invalid status']); return;
        }
        Database::query("UPDATE $table SET enquiry_status = ? WHERE enquiry_id = ?", [$status, (int)$id]);
        $this->log('decoration_enquiry_update', $table, "ID $id → $status");
        echo json_encode(['success' => true]);
    }

    private function countNewDecorationEnquiries(): int {
        $total = 0;
        foreach (['stage_decoration_enquiries', 'light_decoration_enquiries'] as $table) {
            try {
                $row = Database::fetchOne("SELECT COUNT(*) c FROM $table WHERE enquiry_status='new'");
                $total += (int)($row['c'] ?? 0);
            } catch (Exception $e) {}
        }
        return $total;
    }

    // ── Settings ─────────────────────────────────────────────────────
    public function settings(): void {
        $this->requireAuth();
        $page_title     = 'Settings';
        $active_page    = 'settings';
        $pending_orders = Order::getStats()['pending'];
        $new_requests   = RequestCall::countAll('new');
        $flash_message  = $_SESSION['flash']['msg'] ?? null;
        $flash_type     = $_SESSION['flash']['type'] ?? 'success';
        unset($_SESSION['flash']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::requireCsrf();
            $allowed = ['site_name','site_tagline','contact_phone','contact_email','contact_address','maintenance'];
            foreach ($allowed as $key) {
                if (isset($_POST[$key])) {
                    $val = Security::sanitizeString($_POST[$key], 300);
                    Database::query(
                        'INSERT INTO site_settings (setting_key,setting_val) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_val=?',
                        [$key, $val, $val]
                    );
                }
            }
            $this->log('settings_update', 'site_settings', 'Admin updated settings');
            $flash_message = 'Settings saved successfully.';
            $flash_type    = 'success';
        }
        $settings_rows = Database::fetchAll('SELECT * FROM site_settings');
        $settings      = array_column($settings_rows, 'setting_val', 'setting_key');
        require VIEWS_PATH . '/admin/settings.php';
    }

    // ── Helpers ──────────────────────────────────────────────────────
    private function handleOptionalMediaThumbnail(): array {
        if (empty($_FILES['thumbnail']['name'])) return [null, null];
        $inspection = Security::inspectImageUpload($_FILES['thumbnail'], UPLOAD_MAX_SIZE, ['image/jpeg','image/png','image/webp']);
        if ($inspection['errors']) return [null, implode(' ', $inspection['errors'])];
        if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0755, true) && !is_dir(UPLOAD_DIR)) {
            return [null, 'The media upload directory is unavailable.'];
        }
        $filename = 'thumb_' . bin2hex(random_bytes(10)) . '.' . $inspection['extension'];
        if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], UPLOAD_DIR . $filename)) {
            return [null, 'Could not save the video thumbnail.'];
        }
        return ['/uploads/services/' . $filename, null];
    }

    private function saveServicePackages(int $serviceId, array $rows): void {
        foreach (array_slice($rows, 0, 30, true) as $packageId => $raw) {
            if (!is_array($raw)) continue;
            $packageId = (int)$packageId;
            $existing = Database::fetchOne('SELECT id FROM service_packages WHERE id=? AND service_id=?', [$packageId, $serviceId]);
            if (!$existing) continue;
            $label = Security::sanitizeString((string)($raw['label'] ?? ''), 100);
            $slug = strtolower(Security::sanitizeString((string)($raw['slug'] ?? ''), 120));
            $slug = trim(preg_replace('/[^a-z0-9-]+/', '-', $slug), '-');
            if ($label === '' || $slug === '') continue;
            $price = round(max(0, min(10000000, (float)($raw['price'] ?? 0))), 2);
            $description = Security::sanitizeString((string)($raw['description'] ?? ''), 1000);
            $inclusions = preg_split('/\R+/', (string)($raw['inclusions'] ?? ''), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $inclusions = array_slice(array_map(fn($line) => Security::sanitizeString($line, 180), $inclusions), 0, 30);
            $status = ($raw['status'] ?? '') === 'inactive' ? 'inactive' : 'active';
            Database::query(
                'UPDATE service_packages SET label=?,slug=?,price=?,description=?,inclusions_json=?,status=? WHERE id=? AND service_id=?',
                [$label, $slug, $price, $description, json_encode($inclusions, JSON_UNESCAPED_UNICODE), $status, $packageId, $serviceId]
            );
        }
        $this->log('service_packages_update', 'service_packages', "service $serviceId");
    }

    private function removeServiceMediaRow(array $row): void {
        $path = (string)($row['path'] ?? '');
        if (($row['media_type'] ?? 'image') !== 'video' || empty($row['video_provider'])) {
            $filePath = ROOT_PATH . $path;
            if (str_starts_with($path, '/uploads/services/') && is_file($filePath)) @unlink($filePath);
        }
        $thumbnail = (string)($row['thumbnail'] ?? '');
        if (str_starts_with($thumbnail, '/uploads/services/')) {
            $thumbnailPath = ROOT_PATH . $thumbnail;
            if (is_file($thumbnailPath)) @unlink($thumbnailPath);
        }
        Database::query('DELETE FROM service_images WHERE id=?', [(int)$row['id']]);
    }

    private function extractServiceData(): array {
        return [
            'category_id'       => Security::sanitizeInt($_POST['category_id'] ?? 0),
            'title'             => Security::sanitizeString($_POST['title'] ?? '', 200),
            'slug'              => Security::sanitizeString($_POST['slug'] ?? '', 220),
            'short_description' => Security::sanitizeString($_POST['short_description'] ?? '', 500),
            'description'       => Security::sanitizeString($_POST['description'] ?? '', 5000),
            'price'             => (float)($_POST['price'] ?? 0),
            'price_unit'        => Security::sanitizeString($_POST['price_unit'] ?? '', 50),
            'page_template'     => in_array($_POST['page_template']??'', ['sd','cm','snk','bnc','custom']) ? $_POST['page_template'] : 'sd',
            'rating'            => max(0, min(5, (float)($_POST['rating'] ?? 4.5))),
            'tags'              => Security::sanitizeString($_POST['tags'] ?? '', 300),
            'availability'      => Security::sanitizeString($_POST['availability'] ?? '', 200),
            'meta_title'        => Security::sanitizeString($_POST['meta_title'] ?? '', 200),
            'meta_description'  => Security::sanitizeString($_POST['meta_description'] ?? '', 500),
            'sort_order'        => Security::sanitizeInt($_POST['sort_order'] ?? 0),
            'featured'          => isset($_POST['featured']) ? 1 : 0,
            'status'            => in_array($_POST['status']??'', ['active','inactive','draft']) ? $_POST['status'] : 'active',
        ];
    }

    private function handleImageUpload(): ?string {
        if (empty($_FILES['image']['tmp_name'])) return null;
        $inspection = Security::inspectImageUpload($_FILES['image']);
        if ($inspection['errors']) return null;
        $ext      = $inspection['extension'];
        $filename = 'svc_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dest     = UPLOAD_DIR . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            // Must match UPLOAD_DIR (config/app.php), which is /uploads/services/
            return '/uploads/services/' . $filename;
        }
        return null;
    }

    private function log(string $action, string $target, string $detail = ''): void {
        try {
            Database::query(
                'INSERT INTO activity_logs (user_id,action,target,detail,ip_address) VALUES (?,?,?,?,?)',
                [$_SESSION['admin_id'] ?? null, $action, $target, $detail, Security::getIp()]
            );
        } catch (Exception $e) {}
    }
}
