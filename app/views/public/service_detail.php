<?php
declare(strict_types=1);

/** @var string $serviceRoute Supplied by LegacyPage::render(). */
$base = rtrim(APP_URL, '/');
$asset = static fn(string $name): string => $base . '/uploads/services/' . ltrim($name, '/');
$e = static fn(string $value): string => Security::e($value);

$common = [
    'rating' => '4.7',
    'availability' => 'Booking Available All Year',
    'subtags' => 'Professional | Reliable | Event Ready',
    'priceMeta' => 'Quality | Elegant | Memorable',
    'showPkgPills' => true,
    'showSlotPills' => false,
    'keepTemplateTitle' => true,
    'reviews' => [
        ['name' => 'Priya S.', 'stars' => 5, 'text' => 'The team was professional, punctual and made our celebration feel truly special.'],
        ['name' => 'Arun K.', 'stars' => 5, 'text' => 'Easy to book, well organised and exactly as promised.'],
    ],
    'packages' => [],
];

$cfg = $common;
$categoryLabel = 'Event Services';
$categorySlug = '';
$portfolio = [];
$showReferenceUpload = false;

if ($serviceRoute === 'enter-show-down') {
    $categoryLabel = 'Enter Show Down';
    $categorySlug = 'enter-show-down';
    $cfg = array_replace($common, [
        'serviceKey' => 'enter-show-down',
        'serviceName' => 'Enter Show Down',
        'slug' => 'enter-show-down',
        'img' => $asset('entershow-pyro-show.jpg'),
        'availability' => 'Available for Weddings, Receptions & Grand Entries',
        'subtags' => 'Safe Setup | Trained Crew | Perfect Timing',
        'priceMeta' => 'Dramatic | Celebratory | Unforgettable',
        'catalogCards' => false,
        'showPkgPills' => false,
        'hideCards' => true,
        'showQty' => true,
        'qtyLabel' => 'Number of Entry Effects',
        'minQty' => 15,
        'defaultQty' => 15,
        'maxQty' => 50,
        'overviewHtml' => '<div class="sd-rich-overview"><h2>Make your entrance the moment everyone remembers</h2><p>Choose a professionally managed celebration effect for the couple entry, first dance, stage reveal or special announcement. Our trained team handles positioning, timing and safe operation at the venue.</p><div class="sd-feature-list"><span><i class="fa-solid fa-shield-halved"></i> Safety-checked equipment</span><span><i class="fa-solid fa-stopwatch"></i> Cue-perfect coordination</span><span><i class="fa-solid fa-people-group"></i> On-site trained crew</span></div></div>',
        'packages' => [
            ['key'=>'pyro-show','label'=>'Pyro Show','price'=>299,'adminSlug'=>'pyro-show','img'=>$asset('entershow-pyro-show.jpg'),'desc'=>'A choreographed cold-pyro burst for grand entries and stage highlights.'],
            ['key'=>'entry-pot-fag','label'=>'Entry Pot Fog','price'=>459,'adminSlug'=>'entry-pot-fag','img'=>$asset('entershow-entry-pot-fog.jpg'),'desc'=>'Low-lying entry fog that creates a cinematic walkway for the couple.'],
            ['key'=>'paper-blast','label'=>'Paper Blast','price'=>299,'adminSlug'=>'paper-blast','img'=>$asset('entershow-paper-blast.jpg'),'desc'=>'A high-energy confetti blast for entries, first dances and celebrations.'],
            ['key'=>'rose-blast','label'=>'Rose Blast','price'=>299,'adminSlug'=>'rose-blast','img'=>$asset('entershow-rose-blast.jpg'),'desc'=>'An elegant rose-petal shower for romantic couple moments.'],
            ['key'=>'bollon-blast','label'=>'Balloon Blast','price'=>599,'adminSlug'=>'balloon-blast','img'=>$asset('entershow-balloon-blast.jpg'),'desc'=>'A festive balloon release designed for a joyful crowd moment.'],
            ['key'=>'stage-fog-setup','label'=>'Stage Fog Setup','price'=>599,'adminSlug'=>'stage-fog-setup','img'=>$asset('entershow-stage-fog.jpg'),'desc'=>'Professional atmospheric fog for stage reveals and performances.'],
            ['key'=>'gun-paper-blast','label'=>'Gun Paper Blast','price'=>499,'adminSlug'=>'gun-paper-blast','img'=>$asset('entershow-gun-paper-shot.jpg'),'desc'=>'A handheld confetti effect for a precisely timed celebration cue.'],
        ],
    ]);
} elseif ($serviceRoute === 'entertainment-activities') {
    $categoryLabel = 'Entertainment Activities';
    $categorySlug = 'entertainment-activities';
    $type = Security::sanitizeString($_GET['type'] ?? 'photo-booth', 40);
    $entertainment = [
        'human-doll' => [
            'serviceKey'=>'human-doll','adminSlug'=>'human-doll-mascots','serviceName'=>'Human Doll (Mascots)','slug'=>'human-doll-mascots',
            'img'=>$asset('entertainment-mascots.jpg'),'rating'=>'4.5','availability'=>'Available for Birthdays, Weddings & Corporate Events',
            'subtags'=>'Interactive Characters | Photo Moments | Trained Performers','priceMeta'=>'Playful | Friendly | Memorable',
            'showQty'=>true,'qtyLabel'=>'Number of Mascots','maxQty'=>10,
            'overviewHtml'=>'<div class="sd-rich-overview"><h2>Bring your celebration to life</h2><p>Our friendly character performers welcome guests, pose for photographs and keep children entertained throughout the event. Every costume is cleaned, presentation-ready and handled by a trained performer.</p><div class="sd-feature-list"><span><i class="fa-solid fa-face-smile"></i> Guest interaction</span><span><i class="fa-solid fa-camera"></i> Photo moments</span><span><i class="fa-solid fa-sparkles"></i> Premium costumes</span></div></div>',
            'packages'=>[
                ['key'=>'hd-cute','label'=>'Cute Mascot','price'=>2499,'img'=>$asset('entertainment-mascots.jpg'),'desc'=>'A cheerful character performer for children’s events and welcome moments.'],
                ['key'=>'hd-giant','label'=>'Giant Mascot','price'=>3899,'img'=>$asset('entertainment-mascots.jpg'),'desc'=>'A larger-than-life mascot designed to create a striking guest experience.'],
                ['key'=>'hd-cartoon','label'=>'Cartoon Mascot','price'=>2899,'img'=>$asset('entertainment-mascots.jpg'),'desc'=>'A popular cartoon-style character for themed parties and celebrations.'],
                ['key'=>'hd-couple','label'=>'Couple Mascot','price'=>5699,'img'=>$asset('entertainment-mascots.jpg'),'desc'=>'A coordinated mascot pair for receptions and couple celebrations.'],
            ],
        ],
        '360-camera' => [
            'serviceKey'=>'360-camera','adminSlug'=>'360-degree-camera','serviceName'=>'360° Degree Camera','slug'=>'360-degree-camera',
            'img'=>$asset('entertainment-360-camera.jpg'),'rating'=>'4.7','availability'=>'Available for Weddings, Birthdays & Engagements',
            'subtags'=>'360° Video | Slow Motion | Instant Sharing','priceMeta'=>'Immersive | Shareable | Spectacular',
            'overviewHtml'=>'<div class="sd-rich-overview"><h2>Every angle. One unforgettable clip.</h2><p>Guests step onto the platform while the camera creates a smooth, cinematic 360° video. Our operator manages the setup, guides every guest and prepares share-ready clips during your event.</p><div class="sd-feature-list"><span><i class="fa-solid fa-video"></i> Smooth 360° capture</span><span><i class="fa-solid fa-share-nodes"></i> Instant sharing</span><span><i class="fa-solid fa-user-gear"></i> Dedicated operator</span></div></div>',
            'packages'=>[
                ['key'=>'cam-iphone','label'=>'With iPhone','price'=>13899,'img'=>$asset('entertainment-360-camera.jpg'),'desc'=>'Premium 4K slow-motion capture with instant digital delivery.'],
                ['key'=>'cam-no-iphone','label'=>'Without iPhone','price'=>11899,'img'=>$asset('entertainment-360-camera.jpg'),'desc'=>'A complete 360° rotating camera experience with an on-site operator.'],
            ],
        ],
        'photo-booth' => [
            'serviceKey'=>'photo-booth','adminSlug'=>'photo-booth','serviceName'=>'Photo Booth','slug'=>'photo-booth',
            'img'=>$asset('photobooth.png'),'rating'=>'4.4','availability'=>'Available for Engagements, Weddings & Receptions',
            'subtags'=>'Instant Prints | Props | Digital Sharing','priceMeta'=>'Creative | Social | Fun',
            'overviewHtml'=>'<div class="sd-rich-overview"><h2>A fun photo corner your guests will keep returning to</h2><p>Professional lighting, creative props and instant keepsakes turn every pose into a polished memory. The booth is managed by our crew so guests can simply step in, smile and share.</p><div class="sd-feature-list"><span><i class="fa-solid fa-print"></i> Instant keepsakes</span><span><i class="fa-solid fa-wand-magic-sparkles"></i> Props included</span><span><i class="fa-solid fa-camera-retro"></i> Studio lighting</span></div></div>',
            'packages'=>[
                ['key'=>'pb-1','label'=>'Frame 1','price'=>17999,'img'=>$asset('photobooth.png'),'desc'=>'Single-frame photo booth with props, prints and digital sharing.'],
                ['key'=>'pb-2','label'=>'Frame 2','price'=>18499,'img'=>$asset('photobooth.png'),'desc'=>'Two coordinated frame choices with themed props and instant prints.'],
                ['key'=>'pb-3','label'=>'Frame 3','price'=>19999,'img'=>$asset('photobooth.png'),'desc'=>'Three frame styles for larger guest groups and more variety.'],
                ['key'=>'pb-4','label'=>'Frame 4','price'=>20499,'img'=>$asset('photobooth.png'),'desc'=>'Our complete four-frame booth experience for premium events.'],
            ],
        ],
    ];
    $cfg = array_replace($common, $entertainment[$type] ?? $entertainment['photo-booth']);
} elseif (str_starts_with($serviceRoute, 'jewellery/') || $serviceRoute === 'fake-jewellery') {
    $categoryLabel = 'Fake Jewellery';
    $categorySlug = 'fake-jewellery';
    $style = str_starts_with($serviceRoute, 'jewellery/') ? substr($serviceRoute, strlen('jewellery/')) : Security::sanitizeString($_GET['type'] ?? 'gold-style', 40);
    $styles = [
        'gold-style' => ['Gold Style Jewellery','gold-style-jewellery','fake-jewellery-gold','jewellery-gold.jpg',3000,'Gold Finish | Bridal | Lightweight','A rich gold-finish set for traditional bridal looks and elegant event styling.'],
        'silver-style' => ['Silver Style Jewellery','silver-style-jewellery','fake-jewellery-silver','jewellery-silver.jpg',2500,'Silver Finish | Contemporary | Lightweight','A refined silver-finish set for contemporary, fusion and traditional outfits.'],
        'kundan-style' => ['Kundan Style Jewellery','kundan-style-jewellery','fake-jewellery-kundan','jewellery-kundan.jpg',3500,'Kundan Work | Royal | Bridal','A detailed Kundan-style set created for regal bridal and ceremony looks.'],
    ];
    $j = $styles[$style] ?? $styles['gold-style'];
    $showReferenceUpload = true;
    $cfg = array_replace($common, [
        'serviceKey'=>$j[2],'adminSlug'=>$j[1],'serviceName'=>$j[0],'slug'=>$j[2],'img'=>$asset($j[3]),'rating'=>'4.8',
        'availability'=>'Available for Weddings, Receptions & Photo Shoots','subtags'=>$j[5],'priceMeta'=>'Elegant | Authentic-Look | Affordable',
        'showPkgPills'=>false,
        'catalogCards'=>true,
        'overviewHtml'=>'<div class="sd-rich-overview"><h2>Complete your look with confidence</h2><p>'.$j[6].' Each set is selected for camera-ready shine and comfortable wear, then presented clean and event-ready.</p><div class="sd-feature-list"><span><i class="fa-solid fa-gem"></i> Premium finish</span><span><i class="fa-solid fa-feather"></i> Comfortable wear</span><span><i class="fa-solid fa-camera"></i> Photo-ready styling</span></div></div>',
        'packages'=>[['key'=>$style,'label'=>$j[0],'price'=>$j[4],'img'=>$asset($j[3]),'desc'=>$j[6]]],
    ]);
} elseif (preg_match('#^plates-decoration/(aarti|seer)-plates/(9|11|15|21)-plates$#', $serviceRoute, $m)) {
    $kind = $m[1];
    $count = (int)$m[2];
    $categoryLabel = $kind === 'aarti' ? 'Aarti Plates' : 'Seer Plates';
    $categorySlug = $kind . '-plates';
    $prices = [
        'aarti'=>[9=>1499,11=>1999,15=>2999,21=>3999],
        'seer'=>[9=>2499,11=>3499,15=>4999,21=>6999],
    ];
    $serviceName = $categoryLabel . ' — ' . $count . ' Plates';
    $cfg = array_replace($common, [
        'serviceKey'=>$categorySlug.'-'.$count,'adminSlug'=>$categorySlug.'-'.$count,'serviceName'=>$serviceName,'slug'=>$categorySlug.'-'.$count,
        'img'=>$asset('aarthi-plates.jpg'),'rating'=>'4.7','availability'=>'Available for Weddings & Traditional Ceremonies',
        'subtags'=>'Coordinated Theme | Premium Finish | Ceremony Ready','priceMeta'=>'Traditional | Elegant | Customisable',
        'showPkgPills'=>false,
        'catalogCards'=>true,
        'overviewHtml'=>'<div class="sd-rich-overview"><h2>Beautifully coordinated for your ceremony</h2><p>This '.$count.'-plate '.$categoryLabel.' arrangement is styled as one complete presentation with coordinated colours, floral accents and traditional finishing. The set arrives ready for your wedding or family ceremony.</p><div class="sd-feature-list"><span><i class="fa-solid fa-palette"></i> Coordinated styling</span><span><i class="fa-solid fa-seedling"></i> Floral accents</span><span><i class="fa-solid fa-circle-check"></i> Ready to present</span></div></div>',
        'packages'=>[['key'=>(string)$count,'label'=>$count.' Plates','price'=>$prices[$kind][$count],'img'=>$asset('aarthi-plates.jpg'),'desc'=>'A coordinated '.$count.'-plate '.$categoryLabel.' presentation for weddings and ceremonies.']],
    ]);
} elseif ($serviceRoute === 'flower-rangoli' || preg_match('#^flower-rangoli/(3x3|4x4|5x5|6x6)-feet$#', $serviceRoute, $rangoliMatch)) {
    $categoryLabel = 'Flower Rangoli';
    $categorySlug = 'flower-rangoli';
    $sizeKey = $rangoliMatch[1] ?? Security::sanitizeString($_GET['size'] ?? '3x3', 10);
    $sizes = [
        '3x3' => ['3 × 3 Feet', 2999, 'Compact fresh-flower rangoli for entrances and small courtyards.'],
        '4x4' => ['4 × 4 Feet', 4499, 'A medium floral rangoli with richer detailing for main entrances.'],
        '5x5' => ['5 × 5 Feet', 6499, 'A large statement rangoli for wedding halls and grand entryways.'],
        '6x6' => ['6 × 6 Feet', 8999, 'A premium extra-large rangoli for major celebrations.'],
    ];
    if (!isset($sizes[$sizeKey])) $sizeKey = '3x3';
    $rangoli = $sizes[$sizeKey];
    $cfg = array_replace($common, [
        'serviceKey'=>'flower-rangoli-'.$sizeKey,
        'adminSlug'=>'flower-rangoli-'.$sizeKey,
        'serviceName'=>'Flower Rangoli — '.$rangoli[0],
        'slug'=>'flower-rangoli-'.$sizeKey,
        'img'=>$asset('flowers-decoration-2.jpg'),
        'rating'=>'4.8',
        'availability'=>'Available for Weddings, Receptions & Celebrations',
        'subtags'=>'Fresh Flowers | Custom Colours | Venue Ready',
        'priceMeta'=>'Fresh | Traditional | Handcrafted',
        'showPkgPills'=>false,
        'catalogCards'=>true,
        'overviewHtml'=>'<div class="sd-rich-overview"><h2>A fresh floral welcome for your celebration</h2><p>'.$rangoli[2].' Our decorators coordinate the colours and flower selection, then prepare the complete design at your venue.</p><div class="sd-feature-list"><span><i class="fa-solid fa-seedling"></i> Fresh flowers</span><span><i class="fa-solid fa-palette"></i> Coordinated colours</span><span><i class="fa-solid fa-circle-check"></i> On-site setup</span></div></div>',
        'packages'=>[['key'=>$sizeKey,'label'=>$rangoli[0],'price'=>$rangoli[1],'img'=>$asset('flowers-decoration-2.jpg'),'desc'=>$rangoli[2]]],
    ]);
} elseif ($serviceRoute === 'real-flowers') {
    $categoryLabel = 'Real Flowers';
    $categorySlug = 'real-flowers';
    $flowerOverview = '<div class="sd-rich-overview"><h2>Fresh floral styling for every celebration</h2><p>Choose fresh or premium artificial flowers for your reception or marriage ceremony. Our decorators coordinate the stage, entry, mandapam and focal arrangements as one polished event look.</p><div class="sd-feature-list"><span><i class="fa-solid fa-seedling"></i> Event-ready blooms</span><span><i class="fa-solid fa-palette"></i> Coordinated colours</span><span><i class="fa-solid fa-people-group"></i> Professional setup crew</span></div></div>';
    $cfg = array_replace($common, [
        'serviceKey'=>'real-flowers','adminSlug'=>'real-flowers','serviceName'=>'Real Flower Decoration','slug'=>'real-flowers',
        'img'=>$asset('flowers-decoration-1.jpg'),'rating'=>'4.8','availability'=>'Available for Weddings, Receptions & Celebrations',
        'subtags'=>'Rose | Jasmine | Marigold | Orchid | Artificial Flowers','priceMeta'=>'Fresh | Elegant | Venue Ready',
        'showGroupPills'=>true,'groupLabel'=>'Select Occasion','pillLabel'=>'Select Flower Type','overviewHtml'=>$flowerOverview,
        'catalogCards'=>true,
        'groups'=>[
            ['key'=>'reception','label'=>'Reception','packages'=>[
                ['key'=>'103','label'=>'Fresh Real Flowers','price'=>5000,'img'=>$asset('flowers-decoration-1.jpg'),'desc'=>'Fresh rose, jasmine and marigold styling for the reception stage, entry and guest areas.'],
                ['key'=>'104','label'=>'Artificial Flowers','price'=>6000,'img'=>$asset('flowers-decoration-2.jpg'),'desc'=>'Premium lifelike blooms that stay camera-ready throughout the reception.'],
            ]],
            ['key'=>'marriage','label'=>'Marriage','packages'=>[
                ['key'=>'113','label'=>'Fresh Real Flowers','price'=>5000,'img'=>$asset('flowers-decoration-1.jpg'),'desc'=>'Traditional fresh floral styling for the mandapam, garlands and ceremony spaces.'],
                ['key'=>'114','label'=>'Artificial Flowers','price'=>6000,'img'=>$asset('flowers-decoration-2.jpg'),'desc'=>'Colour-coordinated artificial flowers for a lasting marriage ceremony setup.'],
            ]],
        ],
    ]);
} elseif (str_starts_with($serviceRoute, 'photography/')) {
    $categoryLabel = 'Photography';
    $categorySlug = 'photography';
    $isVideo = str_ends_with($serviceRoute, 'photo-video');
    $cfg = array_replace($common, [
        'serviceKey'=>$isVideo ? 'photography-photo-video' : 'photography-photo-package',
        'adminSlug'=>$isVideo ? 'photography-photo-video' : 'photography-photo-package',
        'serviceName'=>$isVideo ? 'Photography — Photo + Video' : 'Photography — Photo Package',
        'slug'=>$isVideo ? 'photography-photo-video' : 'photography-photo-package',
        'img'=>$asset($isVideo ? 'photography.jpg' : 'photo.png'),'rating'=>'4.8',
        'availability'=>'Booking Available All Year','subtags'=>$isVideo ? 'Candid Photography | Cinematic Film | Edited Delivery' : 'Candid Photography | Edited Gallery | Full-Day Coverage',
        'priceMeta'=>'Natural | Story-led | Timeless','showPkgPills'=>false,
        'overviewHtml'=>$isVideo
            ? '<div class="sd-rich-overview"><h2>Your celebration, preserved in photographs and motion</h2><p>A coordinated photo and video team documents the atmosphere, rituals and spontaneous moments without interrupting the natural flow of your event.</p><div class="sd-feature-list"><span><i class="fa-solid fa-camera"></i> Candid & traditional photos</span><span><i class="fa-solid fa-film"></i> Cinematic event film</span><span><i class="fa-solid fa-wand-magic-sparkles"></i> Professionally edited delivery</span></div><h3>What you receive</h3><ul class="sd-inclusions"><li>Full-day event coverage</li><li>High-resolution edited photo gallery</li><li>Cinematic highlight film and complete ceremony video</li><li>Secure digital delivery for easy family sharing</li></ul></div>'
            : '<div class="sd-rich-overview"><h2>Honest moments, beautifully photographed</h2><p>Your dedicated photographer captures the people, details and emotions that make the day yours—from quiet preparations to the final celebration.</p><div class="sd-feature-list"><span><i class="fa-solid fa-camera"></i> Candid & posed coverage</span><span><i class="fa-solid fa-images"></i> Curated edited gallery</span><span><i class="fa-solid fa-cloud-arrow-down"></i> High-resolution delivery</span></div><h3>What you receive</h3><ul class="sd-inclusions"><li>Full-day professional photography</li><li>Carefully colour-corrected high-resolution images</li><li>Family, couple and event-detail portraits</li><li>Private digital gallery for downloading and sharing</li></ul></div>',
        'packages'=>[['key'=>$isVideo ? 'photo-video' : 'photo-package','label'=>$isVideo ? 'Photo + Video' : 'Photo Package','price'=>$isVideo ? 30000 : 25000,'img'=>$asset($isVideo ? 'photography.jpg' : 'photo.png'),'desc'=>$isVideo ? 'Complete photography and cinematic video coverage.' : 'Full-day professional photography with an edited digital gallery.']],
    ]);
    $portfolio = [
        [$asset($isVideo ? 'photography.jpg' : 'photo.png'), $isVideo ? 'Wedding film and photography moment' : 'Wedding photography moment'],
        [$asset($isVideo ? 'photo.png' : 'photography.jpg'), 'Celebration highlights'],
    ];
}

if (empty($cfg['serviceKey'])) {
    http_response_code(404);
    return;
}

$fallbackImage = (string)$cfg['img'];
$adminSlug = (string)($cfg['adminSlug'] ?? '');
$metaDescription = strip_tags((string)($cfg['overviewHtml'] ?? 'Book professional event services from ELLCY in Chennai.'));
$metaDescription = mb_substr(preg_replace('/\s+/', ' ', $metaDescription) ?? '', 0, 155);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <meta name="description" content="<?= $e($metaDescription) ?>"/>
  <meta name="robots" content="index, follow"/>
  <meta name="referrer" content="strict-origin-when-cross-origin"/>
  <meta property="og:title" content="ELLCY | <?= $e((string)$cfg['serviceName']) ?>"/>
  <meta property="og:description" content="<?= $e($metaDescription) ?>"/>
  <meta property="og:type" content="website"/>
  <meta property="og:image" content="<?= $e($fallbackImage) ?>"/>
  <link rel="canonical" href="<?= $e($base . '/services/' . trim($serviceRoute, '/') . '/') ?>"/>
  <title>ELLCY | <?= $e((string)$cfg['serviceName']) ?></title>
  <script type="application/ld+json"><?= json_encode(['@context'=>'https://schema.org','@type'=>'Service','name'=>(string)$cfg['serviceName'],'description'=>$metaDescription,'image'=>$fallbackImage,'areaServed'=>['@type'=>'City','name'=>'Chennai'],'provider'=>['@type'=>'Organization','name'=>'ELLCY','url'=>$base],'offers'=>['@type'=>'Offer','priceCurrency'=>'INR','price'=>(float)($cfg['packages'][0]['price'] ?? 0),'availability'=>'https://schema.org/InStock','url'=>$base.'/services/'.trim($serviceRoute,'/').'/']], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG) ?></script>
  <script type="application/ld+json"><?= json_encode(['@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>[['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>$base.'/'],['@type'=>'ListItem','position'=>2,'name'=>$categoryLabel,'item'=>$base.'/services?type='.$categorySlug],['@type'=>'ListItem','position'=>3,'name'=>(string)$cfg['serviceName'],'item'=>$base.'/services/'.trim($serviceRoute,'/').'/']]], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG) ?></script>
  <link rel="stylesheet" href="<?= $e($base) ?>/css/style.css"/>
  <link rel="stylesheet" href="<?= $e($base) ?>/css/service-desc.css?v=20260812.1"/>
  <link rel="stylesheet" href="<?= $e($base) ?>/css/media-gallery.css?v=20260811.5"/>
  <link rel="stylesheet" href="<?= $e($base) ?>/css/cart.css?v=20260812.1"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" defer></script>
</head>
<body class="sd-body <?= $portfolio ? 'photo-detail-page' : '' ?> <?= !empty($cfg['catalogCards']) ? 'catalog-card-detail-page' : '' ?>">
<header class="sd-topbar sd-mob" role="banner">
  <a class="sd-mobile-context" href="<?= $e($base) ?>/services?type=<?= $e($categorySlug) ?>"><?= $e((string)$cfg['serviceName']) ?></a>
  <a href="<?= $e($base) ?>/cart" class="sd-cart-mob" aria-label="View cart"><i class="fa-solid fa-cart-shopping"></i><span>Cart</span><span class="cart-badge" style="display:none">0</span></a>
</header>

<header class="sd-dsk-hdr sd-dsk" role="banner">
  <a class="sd-logo" href="<?= $e($base) ?>/" aria-label="ELLCY Home">ELLCY</a>
  <a href="<?= $e($base) ?>/cart" class="sd-cart-dsk" aria-label="View cart"><i class="fa-solid fa-cart-shopping"></i><span>Cart</span><span class="cart-badge" style="display:none">0</span></a>
</header>

<nav class="breadcrumb sd-dsk" aria-label="Breadcrumb">
  <a href="<?= $e($base) ?>/">Home</a><span class="sd-bc-sep">/</span>
  <a href="<?= $e($base) ?>/services?type=<?= $e($categorySlug) ?>"><?= $e($categoryLabel) ?></a><span class="sd-bc-sep">/</span>
  <span><?= $e((string)$cfg['serviceName']) ?></span>
</nav>

<div class="sd-mob" style="position:relative">
  <div class="eg-gallery" data-eg-service="<?= $e($adminSlug) ?>" data-eg-category="<?= $e($categorySlug) ?>" data-eg-fallback="<?= $e($fallbackImage) ?>" data-eg-api-base="<?= $e($base) ?>/"></div>
  <span class="sd-hero-rating sd-rating-val" style="position:absolute;top:10px;right:10px;z-index:2"></span>
</div>

<div class="sd-dsk-hero sd-dsk">
  <div class="eg-gallery" data-eg-service="<?= $e($adminSlug) ?>" data-eg-category="<?= $e($categorySlug) ?>" data-eg-fallback="<?= $e($fallbackImage) ?>" data-eg-api-base="<?= $e($base) ?>/"></div>
  <div class="sd-info">
    <div class="sd-title-row"><h1 class="sd-title"><?= $e((string)$cfg['serviceName']) ?></h1><span class="sd-rating-chip sd-rating-val"></span></div>
    <div class="sd-chips"><span class="sd-chip"><i class="fa-solid fa-calendar-check"></i> Booking Available All Year</span><span class="sd-chip"><i class="fa-solid fa-users"></i> Experienced Team</span><span class="sd-chip"><i class="fa-solid fa-location-dot"></i> Chennai &amp; Surrounding Areas</span></div>
    <p class="sd-avail"></p><p class="sd-subtags"></p>
    <div class="sd-group-section"><div class="sd-group-label">Select Occasion</div><div class="sd-group-pills" id="sdGroupPillsD"></div></div>
    <div class="sd-pkg-section"><div class="sd-pkg-label">Select Package</div><div class="sd-pkg-pills" id="sdPkgPillsD"></div></div>
    <div class="sd-slot-section"><div class="sd-slot-label">Preferred Time Slot</div><div class="sd-slot-pills"><button class="sd-slot-pill active" data-slot="Morning">Morning</button><button class="sd-slot-pill" data-slot="Evening">Evening</button><button class="sd-slot-pill" data-slot="Both">Both</button></div></div>
    <div class="sd-dsk-price-block"><div class="sd-dsk-price-line"><span class="sd-price-val" id="sdPriceD">0</span><span class="sd-price-meta-d"></span></div><div class="sd-dsk-ctas"><button class="sd-btn-cart" id="btnCartD" type="button"><i class="fa-solid fa-cart-shopping"></i> Add to Cart</button><a class="sd-btn-call" href="<?= $e($base) ?>/request-for-call"><i class="fa-solid fa-phone"></i> Request for Call</a></div></div>
  </div>
</div>

<main class="sd-main" role="main">
  <div class="sd-mob">
    <div class="sd-title-row"><h1 class="sd-title"></h1><span class="sd-rating-chip sd-rating-val"></span></div>
    <p class="sd-avail"></p><p class="sd-subtags"></p>
    <div class="sd-group-section"><div class="sd-group-label">Select Occasion</div><div class="sd-group-pills" id="sdGroupPillsM"></div></div>
    <div class="sd-pkg-section"><div class="sd-pkg-label">Select Package</div><div class="sd-pkg-pills" id="sdPkgPillsM"></div></div>
    <div class="sd-slot-section"><div class="sd-slot-label">Preferred Time Slot</div><div class="sd-slot-pills"><button class="sd-slot-pill active" data-slot="Morning">Morning</button><button class="sd-slot-pill" data-slot="Evening">Evening</button><button class="sd-slot-pill" data-slot="Both">Both</button></div></div>
    <div class="sd-price-block"><span class="sd-price-val" id="sdPrice">0</span><span class="sd-price-meta"></span></div>
    <div class="sd-cta-row"><button class="sd-btn-cart" id="btnCartM" type="button"><i class="fa-solid fa-cart-shopping"></i> Add to Cart</button><a class="sd-btn-call" href="<?= $e($base) ?>/request-for-call"><i class="fa-solid fa-phone"></i> Request for Call</a></div>
  </div>

  <div class="sd-tabs" role="tablist">
    <button type="button" class="sd-tab active" data-tab="overview" role="tab" aria-selected="true">Overview</button>
    <button type="button" class="sd-tab" data-tab="reviews" role="tab" aria-selected="false">Reviews</button>
    <?php if ($portfolio): ?><button type="button" class="sd-tab" data-tab="portfolio" role="tab" aria-selected="false">Portfolio</button><?php endif; ?>
  </div>
  <div class="sd-tab-body" id="tabOverview" role="tabpanel"></div>
  <div class="sd-tab-body hidden" id="tabReviews" role="tabpanel"><p>No reviews yet.</p></div>
  <?php if ($portfolio): ?>
  <div class="sd-tab-body hidden" id="tabPortfolio" role="tabpanel"><div class="photo-portfolio-grid">
    <?php foreach ($portfolio as $item): ?><figure><img src="<?= $e($item[0]) ?>" alt="<?= $e($item[1]) ?>" loading="lazy"/><figcaption><?= $e($item[1]) ?></figcaption></figure><?php endforeach; ?>
  </div></div>
  <?php endif; ?>

  <?php if ($showReferenceUpload): ?>
  <section class="jewellery-reference-card" aria-labelledby="referenceHeading">
    <div><span class="jewellery-optional">Optional</span><h2 id="referenceHeading">Share a design or colour reference</h2><p>Upload one sample image so our team can match your preferred jewellery style or outfit colour more closely.</p></div>
    <label class="jewellery-upload-control"><input type="file" id="jewelleryReferenceInput" accept="image/jpeg,image/png,image/webp"/><i class="fa-solid fa-cloud-arrow-up"></i><span>Choose reference image</span></label>
    <div id="jewelleryReferencePreview" class="jewellery-reference-preview" hidden></div>
    <p id="jewelleryReferenceStatus" class="jewellery-reference-status" aria-live="polite"></p>
  </section>
  <?php endif; ?>

  <section class="sd-cards-section"><h2 class="sd-cards-heading">Packages &amp; Pricing</h2><div class="sd-cards-grid" id="sdCardsGrid" role="list"></div></section>
</main>

<footer class="site-footer" role="contentinfo"><div class="footer-inner"><div class="footer-brand"><div class="footer-logo">ELLCY</div><p class="footer-text">Creating unforgettable moments across Chennai.</p></div><div class="footer-col"><h4>Quick Links</h4><ul><li><a href="<?= $e($base) ?>/">Home</a></li><li><a href="<?= $e($base) ?>/services">Event Services</a></li><li><a href="<?= $e($base) ?>/booking">Book Now</a></li></ul></div><div class="footer-col"><h4>Contact</h4><p class="footer-contact-item">+91 123-456-789</p><p class="footer-contact-item">info@ellcy.in</p><p class="footer-contact-item">Chennai, Tamil Nadu</p></div><div class="footer-col"><h4>Book Your Event</h4><a class="footer-enquiry-btn" href="<?= $e($base) ?>/booking"><i class="fa-solid fa-calendar-check"></i> Book Now</a></div></div><div class="footer-divider"></div><div class="footer-bottom"><p><span id="year"></span> &copy; ELLCY — All Rights Reserved.</p></div></footer>

<script>window.SD_CONFIG = <?= json_encode($cfg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
<script src="<?= $e($base) ?>/services/data.js"></script>
<script src="<?= $e($base) ?>/js/auth.js?v=20260812.1"></script>
<script src="<?= $e($base) ?>/js/cart.js"></script>
<?php if ($showReferenceUpload): ?><script>window.ELLCY_JEWELLERY_SERVICE = <?= json_encode((string)$cfg['serviceKey']) ?>;</script><script src="<?= $e($base) ?>/js/jewellery-reference.js?v=20260811.2"></script><?php endif; ?>
<script src="<?= $e($base) ?>/js/media-gallery.js?v=20260811.5"></script>
<script src="<?= $e($base) ?>/js/service-desc.js?v=20260812.1"></script>
</body>
</html>
