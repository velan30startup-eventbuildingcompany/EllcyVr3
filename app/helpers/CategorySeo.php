<?php
/** SEO copy and structured content for public service-category pages. */
final class CategorySeo
{
    public static function all(): array
    {
        $commonFaq = static fn(string $service, string $starting): array => [
            ['How early should I book ' . $service . ' in Chennai?', 'Popular wedding dates can fill quickly. Shortlist the service once your date and venue are confirmed, then check availability and inclusions before booking.'],
            ['Can I compare packages before booking?', 'Yes. ELLCY presents available package sizes, inclusions and prices so you can compare options before adding a service to your cart or requesting a call.'],
            ['What is the starting price?', $starting . ' Final pricing can vary by date, duration, location, team size and selected add-ons.'],
        ];

        return [
            'dj' => self::entry(
                'Wedding DJ Services in Chennai | Packages & Prices',
                'Compare wedding DJ services in Chennai, including affordable DJ packages, sound systems and lighting for receptions, sangeet and celebrations.',
                'Wedding DJ Services in Chennai',
                'Find a DJ for a wedding, reception, sangeet, engagement or private celebration in Chennai. ELLCY helps you compare DJ packages by event size, music setup, sound coverage and lighting requirements. Popular searches such as “DJ services near me”, “wedding DJ Chennai” and “DJ under ₹10,000” usually reflect customers who want a clear starting budget and a dependable team. ELLCY listings show actual available packages so you can choose an option that fits your celebration instead of relying on an unrealistic price promise.',
                ['Affordable DJ packages in Chennai', 'Wedding and reception sound systems', 'DJ with lighting for sangeet and parties'],
                $commonFaq('a wedding DJ', 'Available ELLCY DJ packages currently start around ₹9,999.')
            ),
            'decoration' => self::entry(
                'Wedding Decoration Services in Chennai | ELLCY',
                'Explore wedding decoration services in Chennai for marriage stages, receptions, floral backdrops, lighting and venue styling.',
                'Wedding Decoration Services in Chennai',
                'Explore stage decoration, lighting and venue styling for Tamil weddings, receptions, engagements and family occasions. Customers searching for “wedding decorators Chennai”, “marriage stage decoration” or “reception decoration near me” are usually comparing style, venue coverage and budget. ELLCY keeps these choices organised, helping you move from inspiration to a bookable package with clearer pricing and service details.',
                ['Marriage and reception stage décor', 'Floral, backdrop and venue styling', 'Indoor and outdoor lighting decoration'],
                $commonFaq('wedding decoration', 'Decoration prices depend on the selected design, venue size, materials and installation requirements.')
            ),
            'photography' => self::entry(
                'Wedding Photography in Chennai | Packages & Video',
                'Compare wedding photography packages in Chennai for pre-wedding, post-wedding, reception, marriage, candid video and drone coverage.',
                'Wedding Photography and Videography in Chennai',
                'Compare photographers and coverage standards for pre-wedding shoots, post-wedding stories, receptions and marriage ceremonies. ELLCY organises popular searches—including “wedding photographers Chennai”, “candid photography Chennai”, “traditional photo and video” and “pre wedding shoot Chennai”—into clear service paths. Review package choices such as photo count, video duration, locations and coverage team before continuing to the description page.',
                ['Pre-wedding and post-wedding photography', 'Traditional, candid, video and drone coverage', 'Reception and marriage photography packages'],
                $commonFaq('wedding photography', 'Packages vary by coverage type, team size, deliverables, locations and event duration.')
            ),
            'food' => self::entry(
                'Wedding Catering Services in Chennai | Menu Packages',
                'Explore wedding catering services in Chennai for breakfast, lunch and dinner with vegetarian, non-vegetarian, buffet and banana-leaf options.',
                'Wedding Catering Services in Chennai',
                'Plan breakfast, lunch and dinner for a Tamil wedding, reception or family function. Searches such as “wedding catering services Chennai”, “marriage catering near me”, “vegetarian wedding menu” and “catering price per plate Chennai” show that customers need both menu clarity and dependable service. ELLCY helps you browse meal types, dish counts and serving formats before choosing a suitable package.',
                ['Tamil wedding breakfast, lunch and dinner', 'Vegetarian and non-vegetarian menu choices', 'Buffet and banana-leaf serving options'],
                $commonFaq('wedding catering', 'Catering prices depend on menu, guest count, meal, service style and venue requirements.')
            ),
            'musical-band' => self::entry(
                'Wedding Music Performers in Chennai | ELLCY',
                'Book traditional wedding music performers in Chennai, including Chenda Melam, brass bands, Melam Set and Nadhaswaram with Thavil.',
                'Traditional Wedding Music Performers in Chennai',
                'Bring energy and tradition to a Tamil wedding, temple celebration, procession or grand entrance. ELLCY groups Chenda Melam, wedding brass bands, Melam Set and Nadhaswaram with Thavil in one place. This supports high-intent searches such as “Chenda Melam Chennai”, “wedding band Chennai”, “Nadhaswaram for marriage” and “traditional wedding music near me”.',
                ['Chenda Melam ensembles', 'Wedding brass band and Melam Set', 'Nadhaswaram and Thavil for marriage ceremonies'],
                $commonFaq('traditional wedding music', 'Prices vary by ensemble type, member count, performance duration and time slot.')
            ),
            'chenda-melam' => self::entry(
                'Chenda Melam in Chennai | Wedding Packages & Prices',
                'Compare Chenda Melam packages in Chennai for wedding welcomes, Muhurtham ceremonies, processions and evening entrances.',
                'Chenda Melam for Weddings in Chennai',
                'Book the powerful rhythm of Chenda Melam for a marriage welcome, Muhurtham, temple occasion or couple entrance. ELLCY offers different ensemble sizes so families searching for “Chenda Melam Chennai”, “Kerala Chenda Melam for wedding” or “Chenda Melam price” can compare a suitable performance instead of guessing what the team includes.',
                ['Wedding welcomes and processions', 'Multiple ensemble sizes', 'Morning, evening and celebration time slots'],
                $commonFaq('Chenda Melam', 'Pricing depends on the ensemble size, schedule, travel and performance duration.')
            ),
            'band-set' => self::entry(
                'Wedding Brass Band in Chennai | Packages & Prices',
                'Compare wedding brass band packages in Chennai for marriage processions, baraat, couple entrances and ceremonial welcomes.',
                'Wedding Brass Band Services in Chennai',
                'Create a memorable marriage procession or reception entrance with a wedding brass band. ELLCY lists compact and grand ensembles for searches such as “wedding band Chennai”, “brass band for marriage”, “baraat band near me” and “band set price Chennai”. Choose by member count, occasion and the scale of your venue.',
                ['Marriage procession music', 'Compact to grand band ensembles', 'Ceremonial welcome and couple-entry performances'],
                $commonFaq('a wedding brass band', 'Prices vary by member count, performance duration, location and ceremonial requirements.')
            ),
            'bridal-groom-styling' => self::entry(
                'Bridal Makeup & Groom Makeover in Chennai | ELLCY',
                'Compare bridal makeup and groom makeover packages in Chennai with trial booking, styling standards and brochure options.',
                'Bridal and Groom Makeover in Chennai',
                'Explore bridal makeup and groom styling for engagements, receptions and Tamil wedding ceremonies. ELLCY organises Silver, Gold, Platinum and Diamond standards so customers searching for “bridal makeup artist Chennai”, “groom makeover Chennai” or “bridal makeup packages” can compare a suitable level and book a trial before the event.',
                ['Bridal makeup and hair styling', 'Groom grooming and makeover', 'Book-trial and brochure support'],
                $commonFaq('bridal or groom styling', 'Pricing depends on the selected standard, look, products, trial and event schedule.')
            ),
            'catering-boys' => self::entry(
                'Catering Boys & Welcome Girls in Chennai | ELLCY',
                'Book catering staff and welcome girls in Chennai for weddings, receptions, buffet service and guest hospitality.',
                'Catering Staff and Welcome Girls in Chennai',
                'Arrange trained catering boys and welcome girls for marriage halls, receptions and special occasions. People searching for “catering boys Chennai”, “buffet service staff” or “welcome girls for wedding” need dependable staffing based on guest count, service format and duration. ELLCY provides separate options for hospitality roles and meal periods.',
                ['Breakfast, lunch and dinner service staff', 'Buffet and banana-leaf hospitality', 'Welcome girls for guest reception'],
                $commonFaq('event hospitality staff', 'Staffing estimates depend on guest count, meal format, number of dishes and service duration.')
            ),
            'entertainment-activities' => self::entry(
                'Wedding Entertainment Services in Chennai | ELLCY',
                'Discover wedding entertainment services in Chennai including mascots, 360 camera, photo booths and interactive guest experiences.',
                'Wedding Entertainment Services in Chennai',
                'Add interactive moments to weddings, receptions, birthdays and corporate occasions with guest-friendly entertainment. ELLCY groups human dolls, 360-degree camera experiences and photo booths for searches such as “wedding entertainment Chennai”, “360 photo booth Chennai” and “mascot for event”. Compare formats and packages before booking.',
                ['Human dolls and mascots', '360-degree camera experiences', 'Photo-booth packages for guests'],
                $commonFaq('event entertainment', 'Prices vary by activity, equipment, staffing and event duration.')
            ),
            'snacks-stalls' => self::entry(
                'Live Food & Snacks Stalls in Chennai | ELLCY',
                'Book live food and snacks stalls in Chennai for weddings, receptions and events, including popcorn, chaat and celebration counters.',
                'Live Food and Snacks Stalls in Chennai',
                'Give guests a lively food experience with counters for snacks and celebration favourites. ELLCY helps customers searching for “live food counters Chennai”, “wedding snacks stall” and “chaat counter for reception” compare stall types and service quantities for their occasion.',
                ['Wedding and reception snack counters', 'Live preparation and service stalls', 'Flexible quantities for guest counts'],
                $commonFaq('snacks stalls', 'Pricing depends on stall type, serving quantity, duration and venue logistics.')
            ),
            'bouncers' => self::entry(
                'Event Security & Bouncers in Chennai | ELLCY',
                'Book professional event bouncers in Chennai for weddings, receptions, celebrity appearances, college events and crowd management.',
                'Event Bouncers and Security in Chennai',
                'Plan orderly guest movement and venue access with event security support. ELLCY helps users searching for “bouncers for events Chennai”, “wedding security Chennai” and “crowd management staff” choose the number of personnel required for the venue, attendance and schedule.',
                ['Wedding and reception security', 'Guest access and crowd management', 'Flexible team sizes'],
                $commonFaq('event bouncers', 'Pricing depends on team size, duty hours, venue, crowd size and event requirements.')
            ),
            'dancers' => self::entry(
                'Wedding Dancers in Chennai | Dance Team Packages',
                'Book male, female and co-ed wedding dancers in Chennai with DJ-inclusive and performance-only package choices.',
                'Wedding Dance Teams in Chennai',
                'Add a choreographed performance to a sangeet, reception, college event or special celebration. ELLCY groups male, female and co-ed teams for searches such as “wedding dancers Chennai”, “dance troupe for reception” and “sangeet dancers near me”, with clear filters for packages with or without a DJ.',
                ['Male, female and co-ed dance teams', 'With-DJ and without-DJ options', 'Wedding, sangeet and reception performances'],
                $commonFaq('a wedding dance team', 'Prices vary by team size, rehearsal, performance duration, DJ inclusion and venue.')
            ),
            'real-flowers' => self::entry(
                'Wedding Flower Decoration in Chennai | ELLCY',
                'Compare real and artificial wedding flower decoration in Chennai for reception stages, marriage rituals and venue styling.',
                'Wedding Flower Decoration in Chennai',
                'Choose flowers for a reception or marriage ceremony through a clear occasion-based flow. ELLCY supports searches such as “wedding flower decoration Chennai”, “reception floral décor” and “real flower stage decoration”, while allowing customers to compare real and artificial flower packages before opening the service description.',
                ['Reception and marriage floral decoration', 'Real and artificial flower choices', 'Stage, backdrop and ceremony styling'],
                $commonFaq('wedding flower decoration', 'Pricing depends on flower type, design, stage size, season and venue setup.')
            ),
            'flower-rangoli' => self::entry(
                'Flower Rangoli in Chennai | Wedding Rangoli Packages',
                'Book flower rangoli decoration in Chennai for weddings, entrances and auspicious occasions with 3×3, 4×4, 5×5 and 6×6 designs.',
                'Flower Rangoli Decoration in Chennai',
                'Welcome guests with fresh floral colour at a wedding entrance, stage area or traditional ceremony. ELLCY gives customers searching for “flower rangoli Chennai”, “wedding entrance rangoli” and “pookalam decoration” direct size filters, starting from compact 3×3-foot designs.',
                ['3×3 to 6×6-foot rangoli sizes', 'Wedding and reception entrances', 'Traditional floral floor decoration'],
                $commonFaq('flower rangoli', 'Pricing depends on size, floral materials, design detail and installation location.')
            ),
            'car-entry' => self::entry(
                'Wedding Car Entry in Chennai | Luxury Car Packages',
                'Explore wedding car entry services in Chennai with luxury BMW Series, X and M model choices for couple arrivals and receptions.',
                'Wedding Car Entry Services in Chennai',
                'Create a distinctive couple arrival with a luxury car selected for the wedding, reception or photo moment. ELLCY supports searches such as “wedding car rental Chennai”, “BMW wedding car Chennai” and “luxury bridal entry car” with grouped model filters and clear package descriptions.',
                ['BMW Series wedding cars', 'BMW X and M model options', 'Marriage and reception couple entries'],
                $commonFaq('a wedding car entry', 'Pricing depends on car model, duration, distance, date and decoration requirements.')
            ),
            'fake-jewellery' => self::entry(
                'Bridal Jewellery Rental in Chennai | ELLCY',
                'Explore bridal imitation jewellery rental in Chennai with gold, silver and Kundan-style options for marriage and reception looks.',
                'Bridal Jewellery Rental in Chennai',
                'Complete bridal styling with coordinated imitation jewellery for a Muhurtham, reception or engagement. ELLCY helps customers searching for “bridal jewellery rental Chennai”, “temple jewellery for rent” and “Kundan jewellery rental” compare available styles and upload a reference image where supported.',
                ['Gold, silver and Kundan-style sets', 'Marriage and reception styling', 'Reference-image support'],
                $commonFaq('bridal jewellery rental', 'Pricing depends on the style, set contents, rental duration and availability.')
            ),
            'plates-decoration' => self::entry(
                'Aarti & Seer Plate Decoration in Chennai | ELLCY',
                'Book decorated Aarti and Seer plates in Chennai for engagement, wedding and traditional Tamil marriage ceremonies.',
                'Aarti and Seer Plate Decoration in Chennai',
                'Present gifts and ceremonial items with coordinated decorated plates for engagements and Tamil weddings. ELLCY supports searches such as “seer plate decoration Chennai”, “Aarti plate decoration” and “wedding plate decoration” with quantity-based choices for different family requirements.',
                ['Aarti plate decoration', 'Seer plates for Tamil weddings', 'Multiple plate-count packages'],
                $commonFaq('decorated wedding plates', 'Pricing depends on plate count, materials, theme and customisation.')
            ),
            'enter-show-down' => self::entry(
                'Wedding Entry Effects in Chennai | ELLCY',
                'Explore wedding entry effects in Chennai for couple entrances, stage moments and receptions, including safe visual celebration options.',
                'Wedding Entry Effects in Chennai',
                'Make a couple entrance or stage reveal more memorable with a visual effect matched to the venue. ELLCY helps customers searching for “wedding entry effects Chennai”, “bride groom entry ideas” and “reception entry setup” compare available effects, quantity limits and venue suitability.',
                ['Couple and bridal entry effects', 'Reception and stage reveal moments', 'Quantity-based package filters'],
                $commonFaq('wedding entry effects', 'Pricing and suitability depend on effect type, quantity, venue rules and safety requirements.')
            ),
        ];
    }

    public static function forSlug(string $slug): ?array
    {
        $slug = strtolower(trim($slug));
        if ($slug === 'music-performers') $slug = 'musical-band';
        if ($slug === 'make-over') $slug = 'bridal-groom-styling';
        return self::all()[$slug] ?? null;
    }

    public static function canonicalPath(string $slug): string
    {
        return '/services/category/' . rawurlencode(strtolower(trim($slug))) . '/';
    }

    public static function renderSection(array $seo): string
    {
        $e = static fn(string $value): string => Security::e($value);
        $highlights = '';
        foreach ($seo['highlights'] as $item) {
            $highlights .= '<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i><span>' . $e($item) . '</span></li>';
        }
        $faqs = '';
        foreach ($seo['faq'] as $faq) {
            $faqs .= '<details><summary>' . $e($faq[0]) . '</summary><p>' . $e($faq[1]) . '</p></details>';
        }
        return '<section class="category-seo" aria-labelledby="category-seo-title">'
            . '<div class="category-seo__inner"><div class="category-seo__copy">'
            . '<p class="category-seo__eyebrow">Plan your Chennai celebration</p>'
            . '<h2 id="category-seo-title">' . $e($seo['heading']) . '</h2>'
            . '<p>' . $e($seo['intro']) . '</p><ul>' . $highlights . '</ul>'
            . '<div class="category-seo__actions"><a href="' . $e(APP_URL . '/request-for-call') . '">Enquire Now</a>'
            . '<a class="secondary" href="' . $e(APP_URL . '/blog/complete-wedding-event-planning-chennai') . '">Read the ELLCY planning guide</a></div></div>'
            . '<div class="category-seo__faq"><h3>Frequently asked questions</h3>' . $faqs . '</div></div></section>';
    }

    public static function jsonLd(array $seo, string $canonicalUrl): string
    {
        $faqEntities = array_map(static fn(array $faq): array => [
            '@type' => 'Question',
            'name' => $faq[0],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq[1]],
        ], $seo['faq']);
        $data = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Service', '@id' => $canonicalUrl . '#service',
                    'name' => $seo['heading'], 'description' => $seo['description'],
                    'url' => $canonicalUrl,
                    'provider' => ['@type' => 'Organization', '@id' => rtrim(APP_URL, '/') . '/#organization', 'name' => 'ELLCY'],
                    'areaServed' => ['@type' => 'City', 'name' => 'Chennai'],
                ],
                ['@type' => 'FAQPage', 'mainEntity' => $faqEntities],
                ['@type' => 'BreadcrumbList', 'itemListElement' => [
                    ['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>rtrim(APP_URL,'/').'/'],
                    ['@type'=>'ListItem','position'=>2,'name'=>'Event Services','item'=>rtrim(APP_URL,'/').'/services'],
                    ['@type'=>'ListItem','position'=>3,'name'=>$seo['heading'],'item'=>$canonicalUrl],
                ]],
            ],
        ];
        return (string)json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
    }

    private static function entry(string $title, string $description, string $heading, string $intro, array $highlights, array $faq): array
    {
        return compact('title', 'description', 'heading', 'intro', 'highlights', 'faq');
    }
}
