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
            $usesPhpDetail = in_array($serviceRoute, [
                'enter-show-down',
                'fake-jewellery',
                'jewellery/gold-style',
                'jewellery/silver-style',
                'jewellery/kundan-style',
                'entertainment-activities',
                'photography/photo-package',
                'photography/photo-video',
                'real-flowers',
                'flower-rangoli',
            ], true) || preg_match(
                '#^plates-decoration/(?:aarti|seer)-plates/(?:9|11|15|21)-plates$#',
                $serviceRoute
            ) || preg_match('#^flower-rangoli/(?:3x3|4x4|5x5|6x6)-feet$#', $serviceRoute);

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

        /* Force the latest shared responsive and calculator assets after an
           update; several legacy templates otherwise reuse a stale browser copy. */
        foreach (['cart.css', 'header2.css', 'category.css', 'category.js', 'auth.js', 'bouncer.css', 'catering-admin-data.js', 'catering-staff-calc.js', 'detail-single-media.css', 'media-gallery.css', 'media-gallery.js', 'rfc-shared.css', 'services.css', 'services.js', 'service-desc.css', 'service-desc.js'] as $assetName) {
            $assetPattern = preg_quote($assetName, '/');
            $html = preg_replace_callback(
                '/(?<url>[^"\']*\/' . $assetPattern . ')(?:\?[^"\']*)?/i',
                static fn(array $match): string => $match['url'] . '?v=20260831.1',
                $html
            ) ?? $html;
        }

        /* Every service template receives the shared account/header styles.
           Older templates load auth.js but omit cart.css, which leaves the
           injected hamburger visible on desktop and pushes ELLCY to center. */
        if ($directory === 'services' && !preg_match('/\/cart\.css(?:\?[^"\']*)?["\']/i', $html)) {
            $sharedHeaderCss = '<link rel="stylesheet" href="' . Security::e(APP_URL . '/css/cart.css?v=20260831.1') . '"/>';
            $html = preg_replace('/<\/head>/i', $sharedHeaderCss . '</head>', $html, 1) ?? $html;
        }

        /* Apply the same one-media detail rule after each legacy template's
           own stylesheet so older mosaic-specific !important rules cannot
           reintroduce the duplicate images on desktop or tablet. */
        if ($directory === 'services' && !preg_match('/\/detail-single-media\.css(?:\?[^"\']*)?["\']/i', $html)) {
            $singleMediaCss = '<link rel="stylesheet" href="' . Security::e(APP_URL . '/css/detail-single-media.css?v=20260831.1') . '"/>';
            $html = preg_replace('/<\/head>/i', $singleMediaCss . '</head>', $html, 1) ?? $html;
        }

        if ($directory === 'pages') {
            $baseHref = Security::e(APP_URL . '/pages/');
            $baseTag = '<base href="' . $baseHref . '"/>';
            $html = preg_replace('/<head(\s[^>]*)?>/i', '$0' . $baseTag, $html, 1) ?? $html;
        }

        header('Content-Type: text/html; charset=UTF-8');
        echo $html;
        return true;
    }
}
