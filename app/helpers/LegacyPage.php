<?php
/**
 * Safely renders the existing HTML templates through the PHP front controller.
 * A base URL is injected for /pages templates so their original relative CSS,
 * JavaScript and navigation links continue to work on clean routes such as
 * /category, /services and /cart.
 */
final class LegacyPage
{
    public static function render(string $directory, string $relativeFile): bool
    {
        $relativeFile = rawurldecode(trim(str_replace('\\', '/', $relativeFile), '/'));
        if ($relativeFile === '' || str_contains($relativeFile, '..') ||
            str_contains($relativeFile, "\0") ||
            !preg_match('/^[a-zA-Z0-9_\/-]+\.html$/', $relativeFile)) {
            http_response_code(400);
            return false;
        }

        if (!in_array($directory, ['pages', 'services'], true)) {
            http_response_code(404);
            return false;
        }

        /* The latest service-detail experience is rendered by PHP so clean
           XAMPP routes, admin-managed pricing and media, SEO metadata and
           responsive markup all have one authoritative implementation. */
        if ($directory === 'services') {
            $serviceRoute = preg_replace('#/index\.html$#i', '', $relativeFile) ?? '';

            /* Preserve old detail links while serving the current grouped,
               filter-based PHP detail pages. */
            if (preg_match('#^flowers/(reception|marriage)-(real|artificial)$#', $serviceRoute, $flowerRoute)) {
                $flowerPackages = [
                    'reception-real' => '103', 'reception-artificial' => '104',
                    'marriage-real' => '113', 'marriage-artificial' => '114',
                ];
                $_GET['group'] = $flowerRoute[1];
                $_GET['pkg'] = $flowerPackages[$flowerRoute[1] . '-' . $flowerRoute[2]];
                $serviceRoute = 'real-flowers';
            }

            if (preg_match('#^car-entry/luxury-cars/bmw/(7-series|3-series|2-series|x3|x1|m4|m2)$#', $serviceRoute, $bmwRoute)) {
                $_GET['pkg'] = $bmwRoute[1];
                $serviceRoute = 'car-entry/luxury-cars/bmw/' .
                    (in_array($bmwRoute[1], ['7-series', '3-series', '2-series'], true) ? 'series' : 'x-m-models');
            }

            if ($serviceRoute === 'photography/photo-package') {
                $serviceRoute = 'photography/pre-wedding/raj-photography';
            } elseif ($serviceRoute === 'photography/photo-video') {
                $serviceRoute = 'photography/reception-marriage/traditional-photo-video';
            }

            $usesPhpDetail = in_array($serviceRoute, [
                'enter-show-down',
                'fake-jewellery',
                'jewellery/gold-style',
                'jewellery/silver-style',
                'jewellery/kundan-style',
                'entertainment-activities',
                'real-flowers',
                'flower-rangoli',
                'car-entry/luxury-cars/bmw/series',
                'car-entry/luxury-cars/bmw/x-m-models',
            ], true) || preg_match(
                '#^plates-decoration/(?:aarti|seer)-plates/(?:9|11|15|21)-plates$#',
                $serviceRoute
            ) || preg_match('#^flower-rangoli/(?:3x3|4x4|5x5|6x6)-feet$#', $serviceRoute)
              || preg_match('#^real-flowers/(?:reception|marriage)$#', $serviceRoute)
              || preg_match('#^make-over/(?:bridal|groom)/(?:silver|gold|platinum|diamond)$#', $serviceRoute)
              || preg_match('#^photography/pre-wedding/(?:raj-photography|photo-ventures|moments-studio|lenscraft-chennai)$#', $serviceRoute)
              || preg_match('#^photography/post-wedding/(?:raj-photography|photo-ventures|moments-studio|lenscraft-chennai)$#', $serviceRoute)
              || preg_match('#^photography/reception-marriage/(?:traditional-photo-video|traditional-candid-photo|traditional-candid-drone|complete-candid-drone)$#', $serviceRoute)
              || preg_match('#^photography/reception-marriage/(?:traditional-photo-video|traditional-candid-photo|traditional-candid-drone|complete-candid-drone)/(?:raj-photography|photo-ventures|moments-studio|lenscraft-chennai)$#', $serviceRoute);

            if ($usesPhpDetail) {
                require VIEWS_PATH . '/public/service_detail.php';
                return true;
            }
        }

        $approvedRoot = realpath(ROOT_PATH . '/' . $directory);
        $resolvedFile = realpath(ROOT_PATH . '/' . $directory . '/' . $relativeFile);
        if ($approvedRoot === false || $resolvedFile === false || !is_file($resolvedFile)) {
            http_response_code(404);
            return false;
        }

        $approvedPrefix = rtrim(str_replace('\\', '/', $approvedRoot), '/') . '/';
        if (!str_starts_with(str_replace('\\', '/', $resolvedFile), $approvedPrefix)) {
            http_response_code(404);
            return false;
        }

        $html = file_get_contents($resolvedFile);
        if ($html === false) {
            http_response_code(500);
            return false;
        }

        /* Canonical and page-specific SEO metadata must reflect the routed
           public URL, not the historical .html file path embedded in older
           templates. This keeps every service independently indexable. */
        $requestPath = (string)(parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/');
        if (APP_BASE !== '' && str_starts_with($requestPath, APP_BASE)) {
            $requestPath = substr($requestPath, strlen(APP_BASE)) ?: '/';
        }
        $canonicalUrl = rtrim(APP_URL, '/') . '/' . ltrim($requestPath, '/');
        $canonicalTag = '<link rel="canonical" href="' . Security::e($canonicalUrl) . '"/>';
        if (preg_match('#<link\s+rel=["\']canonical["\'][^>]*>#i', $html)) {
            $html = preg_replace('#<link\s+rel=["\']canonical["\'][^>]*>#i', $canonicalTag, $html, 1) ?? $html;
        } else {
            $html = preg_replace('/<\/head>/i', $canonicalTag . '</head>', $html, 1) ?? $html;
        }

        $seoPages = [
            '/services/music-performers/chenda-melam/' => ['Chenda Melam for Weddings in Chennai | ELLCY', 'Explore traditional Chenda Melam for weddings in Chennai, including morning, evening and full-celebration experiences with skilled percussion ensembles.'],
            '/services/music-performers/band-set/' => ['Wedding Brass Band Experiences in Chennai | ELLCY', 'Compare ELLCY wedding brass band experiences in Chennai, from compact six-member bands to flagship twenty-member ceremonial ensembles.'],
            '/services/dj/' => ['Wedding DJ Services in Chennai | ELLCY', 'Explore ELLCY DJ entertainment and lighting experiences for weddings, receptions and celebrations in Chennai.'],
            '/services/decoration/' => ['Wedding Decoration Services in Chennai | ELLCY', 'Discover wedding stage décor, venue decoration and lighting experiences for celebrations in Chennai.'],
            '/services/stage-decoration/' => ['Wedding Stage Decoration in Chennai | ELLCY', 'Plan an elegant wedding stage with floral styling, backdrops and venue-ready decoration experiences in Chennai.'],
            '/services/photography/' => ['Wedding Photography Services in Chennai | ELLCY', 'Explore wedding photography and video experiences for pre-wedding, reception and marriage celebrations in Chennai.'],
            '/services/bridal-groom-styling/' => ['Bridal & Groom Make Over in Chennai | ELLCY', 'Discover bridal and groom make over experiences, trial bookings and celebration-ready styling in Chennai.'],
            '/services/food/' => ['Wedding Catering Services in Chennai | ELLCY', 'Explore wedding catering, meal styles and celebration dining options for events in Chennai.'],
        ];
        if (isset($seoPages[$requestPath])) {
            [$seoTitle, $seoDescription] = $seoPages[$requestPath];
            $safeTitle = Security::e($seoTitle);
            $safeDescription = Security::e($seoDescription);
            $html = preg_replace('#<title>.*?</title>#is', '<title>' . $safeTitle . '</title>', $html, 1) ?? $html;
            if (preg_match('#<meta\s+name=["\']description["\'][^>]*>#i', $html)) {
                $html = preg_replace('#<meta\s+name=["\']description["\'][^>]*>#i', '<meta name="description" content="' . $safeDescription . '"/>', $html, 1) ?? $html;
            }
            $ogTags = '<meta property="og:title" content="' . $safeTitle . '"/>'
                . '<meta property="og:description" content="' . $safeDescription . '"/>'
                . '<meta property="og:type" content="website"/>'
                . '<meta property="og:url" content="' . Security::e($canonicalUrl) . '"/>'
                . '<meta property="og:image" content="' . Security::e(rtrim(PUBLIC_URL, '/') . '/uploads/branding/ellcy-og-2026.jpg') . '"/>'
                . '<meta property="og:image:width" content="1200"/>'
                . '<meta property="og:image:height" content="630"/>'
                . '<meta property="og:locale" content="en_IN"/>'
                . '<meta property="og:site_name" content="ELLCY"/>'
                . '<meta name="twitter:card" content="summary_large_image"/>';
            $html = preg_replace('/<\/head>/i', $ogTags . '</head>', $html, 1) ?? $html;

            $serviceNames = [
                '/services/music-performers/chenda-melam/' => 'Traditional Chenda Melam',
                '/services/music-performers/band-set/' => 'Wedding Brass Band',
                '/services/dj/' => 'Wedding DJ Entertainment',
                '/services/decoration/' => 'Wedding Decoration',
                '/services/stage-decoration/' => 'Wedding Stage Decoration',
                '/services/photography/' => 'Wedding Photography',
                '/services/bridal-groom-styling/' => 'Bridal & Groom Make Over',
                '/services/food/' => 'Wedding Catering',
            ];
            $serviceName = $serviceNames[$requestPath];
            $structuredData = [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'Service',
                        '@id' => $canonicalUrl . '#service',
                        'name' => $serviceName,
                        'description' => $seoDescription,
                        'url' => $canonicalUrl,
                        'provider' => [
                            '@type' => 'Organization',
                            '@id' => rtrim(APP_URL, '/') . '/#organization',
                            'name' => 'ELLCY',
                            'url' => APP_URL,
                        ],
                        'areaServed' => ['@type' => 'City', 'name' => 'Chennai'],
                        'serviceType' => $serviceName,
                    ],
                    [
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => rtrim(APP_URL, '/') . '/'],
                            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Services', 'item' => rtrim(APP_URL, '/') . '/services'],
                            ['@type' => 'ListItem', 'position' => 3, 'name' => $serviceName, 'item' => $canonicalUrl],
                        ],
                    ],
                ],
            ];
            $jsonLd = json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
            if (is_string($jsonLd)) {
                $html = preg_replace('/<\/head>/i', '<script type="application/ld+json">' . $jsonLd . '</script></head>', $html, 1) ?? $html;
            }
        }

        if (preg_match('#^/(?:cart|booking|account|login|register|forgot-password|reset-password)(?:/|$)#', $requestPath)) {
            $html = preg_replace('#<meta\s+name=["\']robots["\'][^>]*>#i', '<meta name="robots" content="noindex, nofollow"/>', $html, 1) ?? $html;
        }

        /* Force the latest shared responsive and calculator assets after an
           update; several legacy templates otherwise reuse a stale browser copy. */
        foreach (['style.css', 'brand.css', 'cart.css', 'header2.css', 'category.css', 'category.js', 'data.js', 'auth.js', 'bouncer.css', 'catering-admin-data.js', 'catering-staff-calc.js', 'chenda-melam.css', 'detail-single-media.css', 'media-gallery.css', 'media-gallery.js', 'rfc-shared.css', 'services.css', 'services.js', 'service-desc.css', 'service-desc.js'] as $assetName) {
            $assetPattern = preg_quote($assetName, '/');
            $html = preg_replace_callback(
                '/(?<url>[^"\']*\/' . $assetPattern . ')(?:\?[^"\']*)?/i',
                static fn(array $match): string => $match['url'] . '?v=20261015.3',
                $html
            ) ?? $html;
        }

        /* Font Awesome's JavaScript build transfers and parses more than a
           megabyte before DOMContentLoaded. The CSS build renders the same
           icon markup without blocking page interactivity. */
        $html = preg_replace(
            '#<script\s+src=["\']https://cdnjs\.cloudflare\.com/ajax/libs/font-awesome/6\.5\.0/js/all\.min\.js["\']\s*(?:defer)?\s*></script>#i',
            '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>',
            $html
        ) ?? $html;

        /* Every service template receives the shared account/header styles.
           Older templates load auth.js but omit cart.css, which leaves the
           injected hamburger visible on desktop and pushes ELLCY to center. */
        if ($directory === 'services' && !preg_match('/\/cart\.css(?:\?[^"\']*)?["\']/i', $html)) {
            $sharedHeaderCss = '<link rel="stylesheet" href="' . Security::e(APP_URL . '/css/cart.css?v=20261014.2') . '"/>';
            $html = preg_replace('/<\/head>/i', $sharedHeaderCss . '</head>', $html, 1) ?? $html;
        }

        /* Replace the historical text-only wordmark on every routed legacy
           page without editing hundreds of archived templates individually. */
        if (!preg_match('/\/brand\.css(?:\?[^"\']*)?["\']/i', $html)) {
            $brandCss = '<link rel="stylesheet" href="' . Security::e(PUBLIC_URL . '/css/brand.css?v=20260908.5') . '"/>';
            $html = preg_replace('/<\/head>/i', $brandCss . '</head>', $html, 1) ?? $html;
        }

        if (!preg_match('/favicon\.svg/i', $html)) {
            $favicons = '<link rel="icon" type="image/svg+xml" href="' . Security::e(PUBLIC_URL . '/uploads/branding/favicon.svg?v=20260922.1') . '"/>'
                . '<link rel="alternate icon" type="image/png" sizes="32x32" href="' . Security::e(PUBLIC_URL . '/uploads/branding/favicon-32.png') . '"/>'
                . '<link rel="apple-touch-icon" sizes="180x180" href="' . Security::e(PUBLIC_URL . '/uploads/branding/apple-touch-icon.png') . '"/>';
            $html = preg_replace('/<\/head>/i', $favicons . '</head>', $html, 1) ?? $html;
        }

        /* Apply the same one-media detail rule after each legacy template's
           own stylesheet so older mosaic-specific !important rules cannot
           reintroduce the duplicate images on desktop or tablet. */
        if ($directory === 'services' && !preg_match('/\/detail-single-media\.css(?:\?[^"\']*)?["\']/i', $html)) {
            $singleMediaCss = '<link rel="stylesheet" href="' . Security::e(APP_URL . '/css/detail-single-media.css?v=20260831.1') . '"/>';
            $html = preg_replace('/<\/head>/i', $singleMediaCss . '</head>', $html, 1) ?? $html;
        }


        if ($directory === 'services' && preg_match('#^dancers/(?:male|female|coed)-team/\d+-members/index\.html$#', $relativeFile)) {
            $dancerCss = '<link rel="stylesheet" href="' . Security::e(APP_URL . '/css/dancer-package-filter.css?v=20260908.2') . '"/>';
            $dancerJs = '<script src="' . Security::e(APP_URL . '/js/dancer-package-filter.js?v=20260908.2') . '"></script>';
            $html = preg_replace('/<\/head>/i', $dancerCss . '</head>', $html, 1) ?? $html;
            $html = preg_replace('/<\/body>/i', $dancerJs . '</body>', $html, 1) ?? $html;
        }

        if ($directory === 'pages') {
            $baseHref = Security::e(APP_URL . '/pages/');
            $baseTag = '<base href="' . $baseHref . '"/>';
            $html = preg_replace('/<head(\s[^>]*)?>/i', '$0' . $baseTag, $html, 1) ?? $html;
        }

        /* Apply the two-day lead-time rule to every legacy booking or
           service enquiry date field, including decoration pages. */
        if (!preg_match('/booking-date-policy\.js/i', $html)) {
            $datePolicy = '<script src="' . Security::e(PUBLIC_URL . '/js/booking-date-policy.js?v=20260924.1') . '"></script>';
            $html = preg_replace('/<\/body>/i', $datePolicy . '</body>', $html, 1) ?? $html;
        }

        header('Content-Type: text/html; charset=UTF-8');
        echo $html;
        return true;
    }
}
