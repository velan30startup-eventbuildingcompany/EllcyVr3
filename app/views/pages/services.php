<?php
$type = Security::sanitizeString($_GET['type'] ?? '', 80);
if ($type === 'music-performers') {
    $type = 'musical-band';
}

$musicPages = [
    'musical-band' => [
        'title' => 'Music Performers',
        'parent' => true,
        'cards' => [
            [
                'title' => 'Chenda Melam',
                'price' => 11994,
                'image' => 'music-chenda-melam.jpg',
                'description' => 'Traditional Kerala Chenda Melam percussion ensemble for processions and celebrations.',
                'tag' => 'Traditional percussion',
                'href' => APP_URL . '/services?type=chenda-melam',
            ],
            [
                'title' => 'Band Set',
                'price' => 11994,
                'image' => 'music-band-set.jpg',
                'description' => 'Professional brass band for baraat, processions and grand entry ceremonies.',
                'tag' => 'Brass band ensemble',
                'href' => APP_URL . '/services?type=band-set',
            ],
            [
                'title' => 'Melam Set',
                'price' => 7994,
                'image' => 'music-melam-set.jpg',
                'description' => 'Traditional melam set for poojas, home events and large temple celebrations.',
                'tag' => 'Traditional melam',
                'href' => APP_URL . '/services?type=melam-set',
            ],
            [
                'title' => 'Nadhaswaram & Thavil',
                'price' => 2999,
                'image' => 'music-nadhaswaram-thavil.jpg',
                'description' => 'Classical Nadhaswaram and Thavil duo for wedding rituals and auspicious ceremonies.',
                'tag' => 'Classical live ensemble',
                'href' => APP_URL . '/services?type=nadhaswaram-thavil',
            ],
        ],
    ],
    'chenda-melam' => [
        'title' => 'Chenda Melam',
        'cards' => [
            ['key'=>'6m',  'title'=>'6 Members',  'price'=>11994, 'description'=>'Small performance. Best for pooja / home events.'],
            ['key'=>'8m',  'title'=>'8 Members',  'price'=>15992, 'description'=>'Medium sound impact. Suitable for small functions.'],
            ['key'=>'10m', 'title'=>'10 Members', 'price'=>19990, 'description'=>'Balanced performance. Ideal for weddings.'],
            ['key'=>'12m', 'title'=>'12 Members', 'price'=>23988, 'description'=>'Medium-large performance. Ideal for weddings & special occasions.'],
            ['key'=>'15m', 'title'=>'15 Members', 'price'=>29985, 'description'=>'High energy performance. Temple & grand events.'],
            ['key'=>'18m', 'title'=>'18 Members', 'price'=>35982, 'description'=>'Powerful traditional setup. Large celebrations.'],
            ['key'=>'20m', 'title'=>'20 Members', 'price'=>39980, 'description'=>'Grand Chenda Melam. Festival-level performance.'],
        ],
        'image' => 'music-chenda-melam.jpg',
        'detail' => APP_URL . '/services/music-performers/chenda-melam/',
    ],
    'band-set' => [
        'title' => 'Band Set',
        'cards' => [
            ['key'=>'bs-6',  'title'=>'6 Members',  'price'=>11994, 'description'=>'Compact 6-member brass band for intimate wedding entries and smaller processions.'],
            ['key'=>'bs-8',  'title'=>'8 Members',  'price'=>15992, 'description'=>'8-member ensemble delivering a fuller brass sound for mid-sized wedding processions.'],
            ['key'=>'bs-10', 'title'=>'10 Members', 'price'=>19990, 'description'=>'Impressive 10-member brass band for larger wedding ceremonies and grand entries.'],
            ['key'=>'bs-12', 'title'=>'12 Members', 'price'=>23988, 'description'=>'Grand 12-member ensemble with uniformed performers and drum major for large processions.'],
            ['key'=>'bs-15', 'title'=>'15 Members', 'price'=>29985, 'description'=>'Premium 15-member brass band with LED costumes and choreographed drum majors.'],
            ['key'=>'bs-18', 'title'=>'18 Members', 'price'=>35982, 'description'=>'Elite 18-member ensemble delivering a wall of sound for extravagant weddings.'],
            ['key'=>'bs-20', 'title'=>'20 Members', 'price'=>39980, 'description'=>'Our flagship 20-member full brass band - the ultimate grand entry experience.'],
        ],
        'image' => 'music-band-set.jpg',
        'detail' => APP_URL . '/services/music-performers/band-set/',
    ],
    'melam-set' => [
        'title' => 'Melam Set',
        'cards' => [
            ['key'=>'ms-4',  'title'=>'4 Members',  'price'=>7994,  'description'=>'Compact 4-member melam set for intimate ceremonies, home poojas and smaller festive occasions.'],
            ['key'=>'ms-6',  'title'=>'6 Members',  'price'=>11994, 'description'=>'6-member traditional percussion ensemble for mid-sized processions and auspicious family functions.'],
            ['key'=>'ms-8',  'title'=>'8 Members',  'price'=>15992, 'description'=>'8-member melam set delivering a fuller, resonant sound for wedding processions.'],
            ['key'=>'ms-10', 'title'=>'10 Members', 'price'=>19990, 'description'=>'Grand 10-member ensemble for larger wedding processions and temple festival ceremonies.'],
            ['key'=>'ms-12', 'title'=>'12 Members', 'price'=>23988, 'description'=>'12-member percussion ensemble creating a powerful atmosphere for grand weddings.'],
            ['key'=>'ms-15', 'title'=>'15 Members', 'price'=>29985, 'description'=>'Premium 15-member melam set for large-scale processions and cultural celebrations.'],
            ['key'=>'ms-18', 'title'=>'18 Members', 'price'=>35982, 'description'=>'Elite 18-member ensemble delivering an immersive wall of percussion for grand events.'],
            ['key'=>'ms-20', 'title'=>'20 Members', 'price'=>39980, 'description'=>'Our flagship 20-member grand procession ensemble - the ultimate traditional melam experience.'],
        ],
        'image' => 'music-melam-set.jpg',
        'detail' => APP_URL . '/services/music-performers/melam-set/',
    ],
    'nadhaswaram-thavil' => [
        'title' => 'Nadhaswaram & Thavil',
        'parent' => true,
        'cards' => [
            [
                'title'=>'Reception', 'price'=>2999, 'image'=>'music-nadhaswaram-thavil.jpg',
                'description'=>'Auspicious live music for reception welcomes, couple entries and celebrations.',
                'tag'=>'2 to 8 members', 'href'=>APP_URL . '/services?type=nadhaswaram-reception',
            ],
            [
                'title'=>'Marriage', 'price'=>12999, 'image'=>'music-nadhaswaram-thavil.jpg',
                'description'=>'Traditional Nadhaswaram and Thavil ensembles for wedding rituals and processions.',
                'tag'=>'6 to 12 members', 'href'=>APP_URL . '/services?type=nadhaswaram-marriage',
            ],
        ],
    ],
    'nadhaswaram-reception' => [
        'title' => 'Nadhaswaram & Thavil - Reception',
        'breadcrumb_tail' => 'Reception',
        'cards' => [
            ['key'=>'rec-2', 'title'=>'2 Members', 'price'=>2999, 'description'=>'Compact Nadhaswaram & Thavil duo - perfect for intimate reception entries and auspicious welcomes.'],
            ['key'=>'rec-4', 'title'=>'4 Members', 'price'=>4999, 'description'=>'Balanced 4-member ensemble bringing a fuller, richer sound to mid-sized reception ceremonies.'],
            ['key'=>'rec-6', 'title'=>'6 Members', 'price'=>6999, 'description'=>'Rich 6-member ensemble for grand reception entries with elevated festive energy.'],
            ['key'=>'rec-8', 'title'=>'8 Members', 'price'=>9999, 'description'=>'Full 8-member ensemble delivering a powerful, celebratory welcome for large receptions.'],
        ],
        'image' => 'music-nadhaswaram-thavil.jpg',
        'detail' => APP_URL . '/services/music-performers/nadhaswaram-thavil/',
    ],
    'nadhaswaram-marriage' => [
        'title' => 'Nadhaswaram & Thavil - Marriage',
        'breadcrumb_tail' => 'Marriage',
        'cards' => [
            ['key'=>'mar-6',  'title'=>'6 Members',  'price'=>12999, 'description'=>'6-member ensemble for wedding rituals and processions, blending tradition with festive energy.'],
            ['key'=>'mar-8',  'title'=>'8 Members',  'price'=>15999, 'description'=>'8-member ensemble bringing a fuller, more resonant sound to grand wedding ceremonies.'],
            ['key'=>'mar-10', 'title'=>'10 Members', 'price'=>17999, 'description'=>'Grand 10-member ensemble ideal for larger weddings and elaborate procession routes.'],
            ['key'=>'mar-12', 'title'=>'12 Members', 'price'=>19999, 'description'=>'Our largest ensemble - 12 musicians for a truly grand, temple-festival-scale wedding celebration.'],
        ],
        'image' => 'music-nadhaswaram-thavil.jpg',
        'detail' => APP_URL . '/services/music-performers/nadhaswaram-thavil/',
    ],
];

$page = $musicPages[$type] ?? $musicPages['musical-band'];

if (empty($page['parent'])) {
    foreach ($page['cards'] as $index => $card) {
        $page['cards'][$index]['image'] = $page['image'];
        $page['cards'][$index]['tag'] = $card['title'] . ' live ensemble';
        $page['cards'][$index]['href'] = $page['detail'] . '?pkg=' . rawurlencode($card['key']);
        $page['cards'][$index]['exact_price'] = true;
    }
}

$page_title = 'ELLCY | ' . $page['title'];
$meta_title = $page_title;
$meta_description = 'Browse ' . $page['title'] . ' packages and prices from ELLCY event services in Chennai.';
$extra_css = ['header2.css', 'services.css'];
$skip_data_js = true;
require VIEWS_PATH . '/layouts/header.php';

$renderCard = static function (array $card): void {
    $imageUrl = APP_URL . '/uploads/services/' . $card['image'];
    $isExactPrice = !empty($card['exact_price']);
    ?>
    <a class="php-music-card" href="<?= Security::e($card['href']) ?>" aria-label="View <?= Security::e($card['title']) ?>">
      <div class="php-music-card__image">
        <img src="<?= Security::e($imageUrl) ?>" alt="<?= Security::e($card['title']) ?>" loading="lazy"/>
      </div>
      <div class="php-music-card__body">
        <div class="php-music-card__top">
          <h2><?= Security::e($card['title']) ?></h2>
          <span class="php-music-card__rating" aria-label="Rated 4.5 out of 5">
            <i class="fa-solid fa-star" aria-hidden="true"></i> 4.5
          </span>
        </div>
        <p class="php-music-card__description"><?= Security::e($card['description']) ?></p>
        <div class="php-music-card__price-row">
          <span class="php-music-card__price-label"><?= $isExactPrice ? 'Package Price' : 'Starting Package' ?></span>
          <strong class="php-music-card__price">&#8377;<?= number_format((float)$card['price'], 0) ?><?php if (!$isExactPrice): ?><small> onwards</small><?php endif; ?></strong>
        </div>
        <span class="php-music-card__tag">
          <i class="fa-solid fa-medal" aria-hidden="true"></i>
          <?= Security::e($card['tag']) ?>
        </span>
      </div>
    </a>
    <?php
};
?>

<style>
  .php-music-page { min-height: 65vh; padding-top: 28px; padding-bottom: 52px; }
  .php-music-page .breadcrumb { margin-bottom: 10px; }
  .php-music-page .page-heading { margin: 0 0 24px; font-size: 27px; color: #111827; }
  .php-music-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 22px; align-items: stretch; }
  .php-music-grid--occasions { grid-template-columns: repeat(2, minmax(0, 280px)); }
  .php-music-card { display: flex; min-width: 0; flex-direction: column; overflow: hidden; color: #17131d; background: #fff; border: 1px solid rgba(34, 15, 48, .04); border-radius: 17px; box-shadow: 0 4px 16px rgba(33, 20, 42, .09); text-decoration: none; transition: transform .2s ease, box-shadow .2s ease; }
  .php-music-card:hover { color: #17131d; transform: translateY(-5px); box-shadow: 0 12px 28px rgba(72, 28, 105, .15); }
  .php-music-card:focus-visible { outline: 3px solid rgba(107, 33, 168, .25); outline-offset: 4px; }
  .php-music-card__image { width: 100%; height: 198px; overflow: hidden; background: #f2e8fb; }
  .php-music-card__image img { display: block; width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
  .php-music-card:hover .php-music-card__image img { transform: scale(1.035); }
  .php-music-card__body { display: flex; flex: 1; flex-direction: column; gap: 12px; padding: 16px; }
  .php-music-card__top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
  .php-music-card__top h2 { min-width: 0; margin: 0; color: #17131d; font-size: 17px; font-weight: 800; line-height: 1.15; }
  .php-music-card__rating { display: inline-flex; flex-shrink: 0; align-items: center; gap: 4px; color: #202033; font-size: 13px; font-weight: 800; line-height: 1; }
  .php-music-card__rating i { font-size: 12px; }
  .php-music-card__description { display: -webkit-box; min-height: 64px; overflow: hidden; color: #655c69; font-size: 13.5px; line-height: 1.55; -webkit-box-orient: vertical; -webkit-line-clamp: 3; line-clamp: 3; }
  .php-music-card__price-row { display: flex; align-items: baseline; justify-content: space-between; gap: 9px; margin-top: auto; padding-top: 13px; border-top: 1px dashed #e4dce9; }
  .php-music-card__price-label { color: #857b89; font-size: 12px; font-weight: 600; }
  .php-music-card__price { color: #6b21a8; font-size: 18px; font-weight: 800; white-space: nowrap; }
  .php-music-card__price small { color: #857b89; font-size: 11px; font-weight: 600; }
  .php-music-card__tag { display: inline-flex; align-items: center; align-self: flex-start; gap: 6px; max-width: 100%; padding: 6px 11px; color: #6b21a8; background: #f3e8ff; border-radius: 999px; font-size: 12px; font-weight: 700; line-height: 1.2; }
  .php-music-card__tag i { flex-shrink: 0; }
  @media (max-width: 1000px) {
    .php-music-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .php-music-grid--occasions { grid-template-columns: repeat(2, minmax(0, 280px)); }
  }
  @media (max-width: 760px) {
    .php-music-page { padding-top: 22px; }
    .php-music-page .page-heading { font-size: 23px; }
    .php-music-grid, .php-music-grid--occasions { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    .php-music-card__image { height: 170px; }
    .php-music-card__body { gap: 10px; padding: 13px; }
    .php-music-card__top { flex-direction: column-reverse; gap: 8px; }
    .php-music-card__description { min-height: 82px; -webkit-line-clamp: 4; line-clamp: 4; }
    .php-music-card__price-row { align-items: flex-start; flex-direction: column; gap: 3px; }
  }
  @media (max-width: 440px) {
    .php-music-page { width: 94%; }
    .php-music-grid, .php-music-grid--occasions { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .php-music-card__image { height: 145px; }
    .php-music-card__body { padding: 11px; }
    .php-music-card__top h2 { font-size: 14px; }
    .php-music-card__description { min-height: 78px; font-size: 12px; }
    .php-music-card__price { font-size: 15px; }
    .php-music-card__tag { padding: 5px 8px; font-size: 10.5px; white-space: normal; }
  }
</style>

<header class="header new-header" role="banner">
  <button class="hdr-back-btn" onclick="_ellcySmartBack('<?= APP_URL ?>/services?type=musical-band')" aria-label="Go back">
    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i><span>Back</span>
  </button>
  <span class="hdr-mobile-title"><?= Security::e($page['title']) ?></span>
  <a class="logo" href="<?= APP_URL ?>/" aria-label="ELLCY Home">ELLCY</a>
  <a href="<?= APP_URL ?>/cart" class="cart-header-btn hdr-cart-right" aria-label="View cart">
    <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
    <span class="cart-btn-label">Cart</span>
    <span class="cart-badge" style="display:none">0</span>
  </a>
</header>

<main class="container service-page php-music-page" role="main">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= APP_URL ?>/">Home</a>
    <?php if ($type !== 'musical-band'): ?>
      / <a href="<?= APP_URL ?>/services?type=musical-band">Music Performers</a>
      <?php if (!empty($page['breadcrumb_tail'])): ?>
        / <a href="<?= APP_URL ?>/services?type=nadhaswaram-thavil">Nadhaswaram &amp; Thavil</a>
        / <?= Security::e($page['breadcrumb_tail']) ?>
      <?php else: ?>
        / <?= Security::e($page['title']) ?>
      <?php endif; ?>
    <?php else: ?>
      / Music Performers
    <?php endif; ?>
  </nav>
  <h1 class="page-heading"><?= Security::e($page['title']) ?></h1>
  <div class="php-music-grid<?= $type === 'nadhaswaram-thavil' ? ' php-music-grid--occasions' : '' ?>">
    <?php foreach ($page['cards'] as $card): ?>
      <?php $renderCard($card); ?>
    <?php endforeach; ?>
  </div>
</main>

<?php
$extra_js = [];
$inline_js = "function _ellcySmartBack(u){if(window.history.length>1&&document.referrer&&document.referrer.includes(window.location.hostname)){window.history.back();}else{window.location.href=u;}}";
require VIEWS_PATH . '/layouts/footer.php';
?>
