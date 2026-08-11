<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <meta name="robots" content="noindex, nofollow"/>
  <meta name="csrf" content="<?= Security::csrfToken() ?>"/>
  <title>ELLCY Admin — <?= htmlspecialchars($page_title ?? 'Dashboard') ?></title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" defer></script>
  <style>
    :root{--pri:#6a1b9a;--pri-d:#5c1690;--pri-l:#f4e9ff;--sidebar:240px;--hdr:60px;--text:#1a1a2e;--txt2:#555;--border:#e0d5f0;--bg:#f8f5ff;--white:#fff;--success:#059669;--warn:#d97706;--danger:#dc2626}
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--text);min-height:100vh}
    a{text-decoration:none;color:inherit}
    /* SIDEBAR */
    .sidebar{position:fixed;left:0;top:0;bottom:0;width:var(--sidebar);background:#1a0a2e;color:#d4b8f0;display:flex;flex-direction:column;z-index:100;overflow-y:auto}
    .sidebar-logo{padding:20px 24px;font-size:1.4rem;font-weight:900;color:#fff;letter-spacing:-.04em;border-bottom:1px solid rgba(255,255,255,.08)}
    .sidebar-logo span{color:#c084fc}
    .sidebar-menu{flex:1;padding:12px 0}
    .sidebar-section{font-size:.68rem;font-weight:700;color:#9171c0;text-transform:uppercase;letter-spacing:.1em;padding:16px 24px 6px}
    .sidebar-item{display:flex;align-items:center;gap:12px;padding:11px 24px;font-size:.875rem;font-weight:500;color:#c9b3e8;border-left:3px solid transparent;transition:all .18s;cursor:pointer}
    .sidebar-item:hover,.sidebar-item.active{background:rgba(192,132,252,.12);color:#fff;border-left-color:#c084fc}
    .sidebar-item i{width:18px;text-align:center;font-size:.9rem;color:#9171c0}
    .sidebar-item.active i,.sidebar-item:hover i{color:#c084fc}
    .sidebar-badge{margin-left:auto;background:#dc2626;color:#fff;font-size:.65rem;font-weight:700;padding:2px 7px;border-radius:999px;min-width:20px;text-align:center}
    .sidebar-footer{padding:16px 24px;border-top:1px solid rgba(255,255,255,.08);font-size:.8rem;color:#9171c0}
    .sidebar-footer a{color:#c084fc}
    /* MAIN */
    .admin-main{margin-left:var(--sidebar);min-height:100vh;display:flex;flex-direction:column}
    /* TOP BAR */
    .admin-topbar{height:var(--hdr);background:var(--white);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 28px;gap:16px;position:sticky;top:0;z-index:90}
    .topbar-title{font-size:1rem;font-weight:700;color:var(--text);flex:1}
    .topbar-user{display:flex;align-items:center;gap:10px;font-size:.85rem;color:var(--txt2)}
    .topbar-avatar{width:36px;height:36px;border-radius:50%;background:var(--pri-l);display:flex;align-items:center;justify-content:center;color:var(--pri);font-weight:700;font-size:.85rem}
    /* CONTENT */
    .admin-content{flex:1;padding:28px}
    /* STAT CARDS */
    .stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:18px;margin-bottom:28px}
    .stat-card{background:var(--white);border-radius:14px;padding:22px 24px;border:1px solid var(--border);display:flex;align-items:center;gap:18px}
    .stat-icon{width:52px;height:52px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0}
    .stat-icon.purple{background:#f4e9ff;color:var(--pri)}
    .stat-icon.green{background:#d1fae5;color:var(--success)}
    .stat-icon.amber{background:#fef3c7;color:var(--warn)}
    .stat-icon.red{background:#fee2e2;color:var(--danger)}
    .stat-label{font-size:.8rem;font-weight:600;color:var(--txt2);margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em}
    .stat-value{font-size:1.65rem;font-weight:800;color:var(--text);line-height:1}
    /* DATA TABLE */
    .data-card{background:var(--white);border-radius:14px;border:1px solid var(--border);overflow:hidden;margin-bottom:24px}
    .data-card-hdr{display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid var(--border)}
    .data-card-title{font-size:.95rem;font-weight:700;color:var(--text)}
    .btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:8px;font-size:.82rem;font-weight:700;font-family:inherit;cursor:pointer;border:none;transition:all .18s}
    .btn-primary{background:var(--pri);color:#fff;box-shadow:0 2px 8px rgba(106,27,154,.25)}
    .btn-primary:hover{background:var(--pri-d);transform:translateY(-1px)}
    .btn-sm{padding:6px 12px;font-size:.75rem}
    .btn-outline{background:transparent;color:var(--pri);border:1.5px solid var(--pri)}
    .btn-outline:hover{background:var(--pri-l)}
    .btn-danger{background:var(--danger);color:#fff}
    .btn-danger:hover{background:#b91c1c}
    .btn-success{background:var(--success);color:#fff}
    .data-table{width:100%;border-collapse:collapse}
    .data-table th{text-align:left;padding:12px 16px;font-size:.75rem;font-weight:700;color:var(--txt2);text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid var(--border);background:#faf7ff}
    .data-table td{padding:14px 16px;font-size:.85rem;color:var(--text);border-bottom:1px solid var(--border)}
    .data-table tr:last-child td{border-bottom:none}
    .data-table tr:hover td{background:#fdfbff}
    .badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;font-size:.72rem;font-weight:700;letter-spacing:.03em}
    .badge-green{background:#d1fae5;color:#065f46}
    .badge-amber{background:#fef3c7;color:#92400e}
    .badge-red{background:#fee2e2;color:#991b1b}
    .badge-blue{background:#dbeafe;color:#1e40af}
    .badge-purple{background:#f4e9ff;color:var(--pri)}
    /* FORM ELEMENTS */
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
    .form-group{margin-bottom:18px}
    .form-label{display:block;font-size:.82rem;font-weight:700;color:var(--text);margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
    .form-input,.form-select,.form-textarea{width:100%;padding:10px 13px;border:1.5px solid var(--border);border-radius:9px;background:#fafafa;font-size:.9rem;font-family:inherit;color:var(--text);outline:none;transition:border-color .18s,box-shadow .18s}
    .form-input:focus,.form-select:focus,.form-textarea:focus{border-color:var(--pri);box-shadow:0 0 0 3px rgba(106,27,154,.1);background:#fff}
    .form-textarea{min-height:100px;resize:vertical}
    .service-form-shell{max-width:1180px}
    .service-form-grid{display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:24px;align-items:start}
    .admin-form-pair{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    .admin-package-grid{gap:10px;margin-top:12px}
    .gallery-mode-row{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px}
    /* FILTER BAR */
    .filter-bar{display:flex;align-items:center;gap:12px;padding:14px 24px;border-bottom:1px solid var(--border);flex-wrap:wrap}
    .filter-search{flex:1;min-width:200px;padding:8px 13px;border:1.5px solid var(--border);border-radius:8px;font-size:.85rem;font-family:inherit;outline:none;transition:border-color .18s}
    .filter-search:focus{border-color:var(--pri)}
    /* PAGINATION */
    .pagination{display:flex;gap:6px;align-items:center;justify-content:center;padding:18px}
    .page-btn{width:36px;height:36px;border:1.5px solid var(--border);border-radius:8px;background:var(--white);font-size:.82rem;font-weight:600;color:var(--text);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .18s}
    .page-btn:hover,.page-btn.active{background:var(--pri);color:#fff;border-color:var(--pri)}
    /* NOTIFICATION */
    .notif{display:none;position:fixed;bottom:24px;right:24px;background:var(--text);color:#fff;padding:14px 20px;border-radius:12px;font-size:.875rem;font-weight:600;z-index:9999;box-shadow:0 8px 30px rgba(0,0,0,.2);max-width:360px}
    .notif.show{display:flex;align-items:center;gap:10px;animation:slideIn .3s ease}
    .notif.success{background:var(--success)}
    .notif.error{background:var(--danger)}
    @keyframes slideIn{from{transform:translateY(16px);opacity:0}to{transform:translateY(0);opacity:1}}
    /* MOBILE SIDEBAR */
    @media(max-width:768px){
      .sidebar{transform:translateX(-100%);transition:transform .28s}
      .sidebar.open{transform:translateX(0)}
      .admin-main{margin-left:0}
      .sidebar-toggle{display:flex}
      .form-grid{grid-template-columns:1fr}
      .admin-content{padding:18px 14px}
      .service-form-grid{grid-template-columns:1fr}
      .service-form-shell{max-width:none}
      .admin-form-pair{grid-template-columns:1fr}
      .data-card{overflow:visible}
      .gallery-mode-btn{flex:1 1 100%;justify-content:center}
      #galleryGrid{grid-template-columns:repeat(2,minmax(0,1fr))!important}
    }
    .sidebar-toggle{display:none;align-items:center;justify-content:center;width:38px;height:38px;border-radius:9px;border:1.5px solid var(--border);background:var(--white);cursor:pointer;font-size:1rem;color:var(--text)}
    @media(max-width:768px){.sidebar-toggle{display:flex}}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:99}
    .sidebar-overlay.show{display:block}
  </style>
</head>
<body>

<!-- SIDEBAR -->
<nav class="sidebar" id="sidebar">
  <div class="sidebar-logo">ELLCY <span>Admin</span></div>
  <div class="sidebar-menu">
    <div class="sidebar-section">Main</div>
    <a class="sidebar-item <?= ($active_page??'')==='dashboard'?'active':'' ?>" href="<?= APP_URL ?>/admin">
      <i class="fa-solid fa-gauge"></i> Dashboard
    </a>

    <div class="sidebar-section">Services</div>
    <a class="sidebar-item <?= ($active_page??'')==='services'?'active':'' ?>" href="<?= APP_URL ?>/admin/services">
      <i class="fa-solid fa-layer-group"></i> All Services
    </a>
    <a class="sidebar-item <?= ($active_page??'')==='categories'?'active':'' ?>" href="<?= APP_URL ?>/admin/categories">
      <i class="fa-solid fa-tags"></i> Categories
    </a>
    <a class="sidebar-item" href="<?= APP_URL ?>/admin/services/create">
      <i class="fa-solid fa-plus"></i> Add Service
    </a>

    <div class="sidebar-section">Bookings</div>
    <a class="sidebar-item <?= ($active_page??'')==='bookings'?'active':'' ?>" href="<?= APP_URL ?>/admin/bookings">
      <i class="fa-solid fa-calendar-check"></i> Bookings
      <?php if (!empty($pending_orders)): ?>
      <span class="sidebar-badge"><?= (int)$pending_orders ?></span>
      <?php endif; ?>
    </a>
    <a class="sidebar-item <?= ($active_page??'')==='requests'?'active':'' ?>" href="<?= APP_URL ?>/admin/requests">
      <i class="fa-solid fa-phone-volume"></i> Call Requests
      <?php if (!empty($new_requests)): ?>
      <span class="sidebar-badge"><?= (int)$new_requests ?></span>
      <?php endif; ?>
    </a>
    <a class="sidebar-item <?= ($active_page??'')==='decoration_enquiries'?'active':'' ?>" href="<?= APP_URL ?>/admin/decoration-enquiries">
      <i class="fa-solid fa-lightbulb"></i> Decoration Enquiries
      <?php if (!empty($new_decoration_enquiries)): ?>
      <span class="sidebar-badge"><?= (int)$new_decoration_enquiries ?></span>
      <?php endif; ?>
    </a>

    <div class="sidebar-section">System</div>
    <a class="sidebar-item <?= ($active_page??'')==='users'?'active':'' ?>" href="<?= APP_URL ?>/admin/users">
      <i class="fa-solid fa-users"></i> Users
    </a>
    <a class="sidebar-item <?= ($active_page??'')==='settings'?'active':'' ?>" href="<?= APP_URL ?>/admin/settings">
      <i class="fa-solid fa-gear"></i> Settings
    </a>
    <a class="sidebar-item" href="<?= APP_URL ?>/" target="_blank">
      <i class="fa-solid fa-arrow-up-right-from-square"></i> View Website
    </a>
  </div>
  <div class="sidebar-footer">
    Logged in as <strong><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></strong><br>
    <a href="<?= APP_URL ?>/admin/logout" style="color:#f87171">Logout</a>
  </div>
</nav>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- MAIN -->
<script>window.ELLCY_BASE = <?= json_encode(APP_BASE, JSON_UNESCAPED_SLASHES) ?>;</script>
<div class="admin-main">
  <div class="admin-topbar">
    <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle menu">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div class="topbar-title"><?= htmlspecialchars($page_title ?? 'Dashboard') ?></div>
    <div class="topbar-user">
      <div class="topbar-avatar"><?= strtoupper(substr($_SESSION['admin_name']??'A',0,1)) ?></div>
      <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>
    </div>
  </div>
  <div class="admin-content">
