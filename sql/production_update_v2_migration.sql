-- ============================================================
-- ELLCY — Production Update v2 Migration
-- Adds:
--   1. catering_staff_matrix — Excel-sourced staff calculation
--      lookup table for Catering Boys (Banana Leaf / Buffet).
--   2. Photography split into two individual package services.
--   3. Chenda Melam image reference fix (listing vs description).
-- Safe to re-run (guarded with NOT EXISTS / INSERT IGNORE).
-- ============================================================
USE ellcy_db;

-- ── 1. CATERING STAFF CALCULATION MATRIX ────────────────────
-- Sourced directly from the client-provided Excel sheets:
--   "Catering Boys Banana Leaf Style" and "Catering Boys Buff Style".
-- guest_count is the exact guest-count row from the sheet (50..1000).
-- dish_band is one of: 0-10, 10-20, 20-30, 30-40.
CREATE TABLE IF NOT EXISTS catering_staff_matrix (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  style        ENUM('banana_leaf','buffet') NOT NULL,
  guest_count  INT UNSIGNED NOT NULL,
  dish_band    ENUM('0-10','10-20','20-30','30-40') NOT NULL,
  workers      INT UNSIGNED NOT NULL,
  UNIQUE KEY uq_style_guest_dish (style, guest_count, dish_band),
  INDEX idx_style_guest (style, guest_count)
) ENGINE=InnoDB;

-- Banana Leaf Style
INSERT INTO catering_staff_matrix (style, guest_count, dish_band, workers) VALUES
('banana_leaf',50,'0-10',4),  ('banana_leaf',50,'10-20',6),  ('banana_leaf',50,'20-30',7),  ('banana_leaf',50,'30-40',8),
('banana_leaf',100,'0-10',6), ('banana_leaf',100,'10-20',8), ('banana_leaf',100,'20-30',11),('banana_leaf',100,'30-40',13),
('banana_leaf',150,'0-10',8), ('banana_leaf',150,'10-20',10),('banana_leaf',150,'20-30',13),('banana_leaf',150,'30-40',15),
('banana_leaf',200,'0-10',11),('banana_leaf',200,'10-20',12),('banana_leaf',200,'20-30',15),('banana_leaf',200,'30-40',16),
('banana_leaf',250,'0-10',13),('banana_leaf',250,'10-20',13),('banana_leaf',250,'20-30',15),('banana_leaf',250,'30-40',18),
('banana_leaf',300,'0-10',15),('banana_leaf',300,'10-20',15),('banana_leaf',300,'20-30',18),('banana_leaf',300,'30-40',19),
('banana_leaf',350,'0-10',15),('banana_leaf',350,'10-20',16),('banana_leaf',350,'20-30',18),('banana_leaf',350,'30-40',20),
('banana_leaf',400,'0-10',17),('banana_leaf',400,'10-20',18),('banana_leaf',400,'20-30',20),('banana_leaf',400,'30-40',21),
('banana_leaf',450,'0-10',18),('banana_leaf',450,'10-20',18),('banana_leaf',450,'20-30',22),('banana_leaf',450,'30-40',23),
('banana_leaf',500,'0-10',21),('banana_leaf',500,'10-20',22),('banana_leaf',500,'20-30',25),('banana_leaf',500,'30-40',26),
('banana_leaf',550,'0-10',21),('banana_leaf',550,'10-20',23),('banana_leaf',550,'20-30',25),('banana_leaf',550,'30-40',27),
('banana_leaf',600,'0-10',23),('banana_leaf',600,'10-20',25),('banana_leaf',600,'20-30',28),('banana_leaf',600,'30-40',30),
('banana_leaf',650,'0-10',23),('banana_leaf',650,'10-20',25),('banana_leaf',650,'20-30',28),('banana_leaf',650,'30-40',31),
('banana_leaf',700,'0-10',25),('banana_leaf',700,'10-20',26),('banana_leaf',700,'20-30',30),('banana_leaf',700,'30-40',33),
('banana_leaf',750,'0-10',25),('banana_leaf',750,'10-20',26),('banana_leaf',750,'20-30',30),('banana_leaf',750,'30-40',34),
('banana_leaf',800,'0-10',27),('banana_leaf',800,'10-20',30),('banana_leaf',800,'20-30',32),('banana_leaf',800,'30-40',35),
('banana_leaf',850,'0-10',27),('banana_leaf',850,'10-20',30),('banana_leaf',850,'20-30',32),('banana_leaf',850,'30-40',36),
('banana_leaf',900,'0-10',30),('banana_leaf',900,'10-20',32),('banana_leaf',900,'20-30',35),('banana_leaf',900,'30-40',38),
('banana_leaf',950,'0-10',30),('banana_leaf',950,'10-20',32),('banana_leaf',950,'20-30',35),('banana_leaf',950,'30-40',38),
('banana_leaf',1000,'0-10',32),('banana_leaf',1000,'10-20',35),('banana_leaf',1000,'20-30',37),('banana_leaf',1000,'30-40',40)
ON DUPLICATE KEY UPDATE workers=VALUES(workers);

-- Boys Buffet Style
INSERT INTO catering_staff_matrix (style, guest_count, dish_band, workers) VALUES
('buffet',50,'0-10',5),  ('buffet',50,'10-20',8),  ('buffet',50,'20-30',9),  ('buffet',50,'30-40',10),
('buffet',100,'0-10',7), ('buffet',100,'10-20',9), ('buffet',100,'20-30',11),('buffet',100,'30-40',12),
('buffet',150,'0-10',9), ('buffet',150,'10-20',10),('buffet',150,'20-30',11),('buffet',150,'30-40',13),
('buffet',200,'0-10',12),('buffet',200,'10-20',13),('buffet',200,'20-30',14),('buffet',200,'30-40',15),
('buffet',250,'0-10',14),('buffet',250,'10-20',15),('buffet',250,'20-30',15),('buffet',250,'30-40',16),
('buffet',300,'0-10',16),('buffet',300,'10-20',17),('buffet',300,'20-30',17),('buffet',300,'30-40',18),
('buffet',350,'0-10',16),('buffet',350,'10-20',18),('buffet',350,'20-30',18),('buffet',350,'30-40',20),
('buffet',400,'0-10',18),('buffet',400,'10-20',18),('buffet',400,'20-30',19),('buffet',400,'30-40',22),
('buffet',450,'0-10',18),('buffet',450,'10-20',20),('buffet',450,'20-30',21),('buffet',450,'30-40',24),
('buffet',500,'0-10',22),('buffet',500,'10-20',23),('buffet',500,'20-30',23),('buffet',500,'30-40',24),
('buffet',550,'0-10',22),('buffet',550,'10-20',23),('buffet',550,'20-30',24),('buffet',550,'30-40',26),
('buffet',600,'0-10',24),('buffet',600,'10-20',24),('buffet',600,'20-30',25),('buffet',600,'30-40',28),
('buffet',650,'0-10',24),('buffet',650,'10-20',24),('buffet',650,'20-30',26),('buffet',650,'30-40',28),
('buffet',700,'0-10',27),('buffet',700,'10-20',27),('buffet',700,'20-30',27),('buffet',700,'30-40',30),
('buffet',750,'0-10',28),('buffet',750,'10-20',28),('buffet',750,'20-30',30),('buffet',750,'30-40',32),
('buffet',800,'0-10',30),('buffet',800,'10-20',31),('buffet',800,'20-30',32),('buffet',800,'30-40',34),
('buffet',850,'0-10',32),('buffet',850,'10-20',32),('buffet',850,'20-30',33),('buffet',850,'30-40',36),
('buffet',900,'0-10',32),('buffet',900,'10-20',32),('buffet',900,'20-30',34),('buffet',900,'30-40',38),
('buffet',950,'0-10',34),('buffet',950,'10-20',34),('buffet',950,'20-30',34),('buffet',950,'30-40',42),
('buffet',1000,'0-10',35),('buffet',1000,'10-20',35),('buffet',1000,'20-30',36),('buffet',1000,'30-40',42)
ON DUPLICATE KEY UPDATE workers=VALUES(workers);

-- ── 2. PHOTOGRAPHY — split into two individually routable packages ──
-- (Matches the client-provided reference: "₹25,000/day Photo Package"
--  and "₹30,000/day Photo + Video".)
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, page_template, image, rating, tags, availability, status)
SELECT id, 'Photography — Photo Package', 'photography-photo-package',
  'Professional wedding photography — photo-only coverage for your full-day event.',
  'Our Photo Package covers your entire event with a dedicated professional photographer, capturing every candid and posed moment from start to finish. Edited high-resolution images delivered digitally after the event.',
  25000, 'per day', 'sd', '/uploads/services/photo.webp', 4.8, 'photography,photo-only', 'Booking Available All Year', 'active'
FROM service_categories WHERE slug='photography'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='photography-photo-package');

INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, page_template, image, rating, tags, availability, status)
SELECT id, 'Photography — Photo + Video', 'photography-photo-video',
  'Complete photo and cinematic video coverage for your full-day event.',
  'Our Photo + Video package pairs a professional photographer with a videography team for full cinematic coverage of your event, including edited highlight reels alongside your edited photo gallery.',
  30000, 'per day', 'sd', '/uploads/services/photo.webp', 4.9, 'photography,photo-video', 'Booking Available All Year', 'active'
FROM service_categories WHERE slug='photography'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='photography-photo-video');

-- ── 3. CHENDA MELAM — fix listing image reference ───────────
-- The listing card was pointing at the generic musical_band.png
-- placeholder; the description page already uses the correct
-- dedicated chenda-melam.png asset. Point the DB row at the same
-- correct asset so listing and description page match.
UPDATE services
SET image = '/uploads/services/chenda-melam.webp'
WHERE slug LIKE 'chenda-melam%' OR slug LIKE '%chenda-melam';

UPDATE service_categories
SET image = '/uploads/services/chenda-melam.webp'
WHERE slug = 'chenda-melam';
