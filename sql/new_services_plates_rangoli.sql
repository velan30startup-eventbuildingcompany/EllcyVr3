-- ============================================================
-- ELLCY — New Services: Plates Decoration & Flower Rangoli
-- Adds the category hierarchy + individual priced services so
-- these show up correctly in the admin panel and so the gallery
-- component (js/media-gallery.js) has something to aggregate.
-- Safe to re-run.
-- ============================================================
USE ellcy_db;

-- ── Categories ──────────────────────────────────────────────
INSERT IGNORE INTO service_categories (parent_id, name, slug, description, image, sort_order)
VALUES (NULL, 'Plates Decoration', 'plates-decoration', 'Decorated aarti and seer plates for poojas, weddings and ceremonies.', '/uploads/services/stage.webp', 60);

INSERT INTO service_categories (parent_id, name, slug, description, image, sort_order)
SELECT id, 'Aarti Plates', 'aarti-plates', 'Decorated aarti plates for poojas and religious ceremonies.', '/uploads/services/stage.webp', 0
FROM service_categories WHERE slug = 'plates-decoration'
AND NOT EXISTS (SELECT 1 FROM service_categories WHERE slug = 'aarti-plates');

INSERT INTO service_categories (parent_id, name, slug, description, image, sort_order)
SELECT id, 'Seer Plates', 'seer-plates', 'Elegant seer plate decoration for wedding trousseau presentations.', '/uploads/services/stage.webp', 1
FROM service_categories WHERE slug = 'plates-decoration'
AND NOT EXISTS (SELECT 1 FROM service_categories WHERE slug = 'seer-plates');

INSERT IGNORE INTO service_categories (parent_id, name, slug, description, image, sort_order)
VALUES (NULL, 'Flower Rangoli', 'flower-rangoli', 'Fresh flower rangoli designs in multiple sizes for entrances and celebrations.', '/uploads/services/stage.webp', 61);

-- ── Services (one row per size/count, matching the individual description pages) ──

-- Aarti Plates
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, '9 Plates', 'aarti-plates-9', 'Compact aarti plate set for smaller poojas and home ceremonies.',
       'Compact aarti plate set — ideal for smaller poojas and home ceremonies.', 1499, 'per set', '/uploads/services/stage.webp', 'aarti-plates', 'active'
FROM service_categories WHERE slug='aarti-plates' AND NOT EXISTS (SELECT 1 FROM services WHERE slug='aarti-plates-9');

INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, '11 Plates', 'aarti-plates-11', 'Traditional 11-plate set for standard temple and wedding rituals.',
       'Traditional 11-plate set for standard temple and wedding rituals.', 1999, 'per set', '/uploads/services/stage.webp', 'aarti-plates', 'active'
FROM service_categories WHERE slug='aarti-plates' AND NOT EXISTS (SELECT 1 FROM services WHERE slug='aarti-plates-11');

INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, '15 Plates', 'aarti-plates-15', 'Larger set for grand poojas and multi-family ceremonies.',
       'Larger set for grand poojas and multi-family ceremonies.', 2999, 'per set', '/uploads/services/stage.webp', 'aarti-plates', 'active'
FROM service_categories WHERE slug='aarti-plates' AND NOT EXISTS (SELECT 1 FROM services WHERE slug='aarti-plates-15');

INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, '21 Plates', 'aarti-plates-21', 'Full auspicious 21-plate set for major temple events and large weddings.',
       'Full auspicious 21-plate set for major temple events and large weddings.', 3999, 'per set', '/uploads/services/stage.webp', 'aarti-plates', 'active'
FROM service_categories WHERE slug='aarti-plates' AND NOT EXISTS (SELECT 1 FROM services WHERE slug='aarti-plates-21');

-- Seer Plates
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, '9 Plates', 'seer-plates-9', 'Elegant 9 seer plate set for intimate trousseau presentations.',
       'Elegant 9 seer plate set for intimate trousseau presentations.', 2499, 'per set', '/uploads/services/stage.webp', 'seer-plates', 'active'
FROM service_categories WHERE slug='seer-plates' AND NOT EXISTS (SELECT 1 FROM services WHERE slug='seer-plates-9');

INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, '11 Plates', 'seer-plates-11', 'Traditional 11 seer plate set with premium decorative styling.',
       'Traditional 11 seer plate set with premium decorative styling.', 3499, 'per set', '/uploads/services/stage.webp', 'seer-plates', 'active'
FROM service_categories WHERE slug='seer-plates' AND NOT EXISTS (SELECT 1 FROM services WHERE slug='seer-plates-11');

INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, '15 Plates', 'seer-plates-15', 'Grand 15 seer plate set for larger wedding trousseau ceremonies.',
       'Grand 15 seer plate set for larger wedding trousseau ceremonies.', 4999, 'per set', '/uploads/services/stage.webp', 'seer-plates', 'active'
FROM service_categories WHERE slug='seer-plates' AND NOT EXISTS (SELECT 1 FROM services WHERE slug='seer-plates-15');

INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, '21 Plates', 'seer-plates-21', 'Full 21 seer plate luxury set for the most elaborate presentations.',
       'Full 21 seer plate luxury set for the most elaborate presentations.', 6999, 'per set', '/uploads/services/stage.webp', 'seer-plates', 'active'
FROM service_categories WHERE slug='seer-plates' AND NOT EXISTS (SELECT 1 FROM services WHERE slug='seer-plates-21');

-- Flower Rangoli
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, '3 x 3 Feet', 'flower-rangoli-3x3', 'Compact fresh-flower rangoli for entrances and small courtyards.',
       'Compact fresh-flower rangoli — perfect for entrances and small courtyards.', 2999, 'per rangoli', '/uploads/services/stage.webp', 'flower-rangoli', 'active'
FROM service_categories WHERE slug='flower-rangoli' AND NOT EXISTS (SELECT 1 FROM services WHERE slug='flower-rangoli-3x3');

INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, '4 x 4 Feet', 'flower-rangoli-4x4', 'Medium-sized rangoli with richer floral detailing for main entrances.',
       'Medium-sized rangoli with richer floral detailing for main entrances.', 4499, 'per rangoli', '/uploads/services/stage.webp', 'flower-rangoli', 'active'
FROM service_categories WHERE slug='flower-rangoli' AND NOT EXISTS (SELECT 1 FROM services WHERE slug='flower-rangoli-4x4');

INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, '5 x 5 Feet', 'flower-rangoli-5x5', 'Large statement rangoli for wedding halls and grand entryways.',
       'Large statement rangoli for wedding halls and grand entryways.', 6499, 'per rangoli', '/uploads/services/stage.webp', 'flower-rangoli', 'active'
FROM service_categories WHERE slug='flower-rangoli' AND NOT EXISTS (SELECT 1 FROM services WHERE slug='flower-rangoli-5x5');

INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, '6 x 6 Feet', 'flower-rangoli-6x6', 'Premium extra-large rangoli, the centrepiece for major celebrations.',
       'Premium extra-large rangoli — the centrepiece for major celebrations.', 8999, 'per rangoli', '/uploads/services/stage.webp', 'flower-rangoli', 'active'
FROM service_categories WHERE slug='flower-rangoli' AND NOT EXISTS (SELECT 1 FROM services WHERE slug='flower-rangoli-6x6');
