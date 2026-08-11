-- ELLCY production update v8
-- Requested service cleanup, centralized DJ packages, media metadata,
-- and securely attachable customer reference uploads.

SET NAMES utf8mb4;

ALTER TABLE service_images
  ADD COLUMN IF NOT EXISTS media_type ENUM('image','video') NOT NULL DEFAULT 'image' AFTER service_id,
  ADD COLUMN IF NOT EXISTS video_provider ENUM('upload','youtube','vimeo') DEFAULT NULL AFTER path,
  ADD COLUMN IF NOT EXISTS thumbnail VARCHAR(300) DEFAULT NULL AFTER video_provider,
  ADD COLUMN IF NOT EXISTS status ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER sort_order,
  ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER status,
  ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE service_packages
  ADD COLUMN IF NOT EXISTS slug VARCHAR(120) DEFAULT NULL AFTER pkg_key,
  ADD COLUMN IF NOT EXISTS inclusions_json LONGTEXT DEFAULT NULL AFTER description,
  ADD COLUMN IF NOT EXISTS image VARCHAR(300) DEFAULT NULL AFTER inclusions_json,
  ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER status,
  ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

CREATE TABLE IF NOT EXISTS uploads (
  id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  upload_type   VARCHAR(50) NOT NULL,
  service_slug  VARCHAR(220) NOT NULL,
  path          VARCHAR(300) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  mime_type     VARCHAR(100) NOT NULL,
  file_size     INT UNSIGNED NOT NULL,
  token_hash    CHAR(64) NOT NULL,
  user_id       INT UNSIGNED DEFAULT NULL,
  order_id      INT UNSIGNED DEFAULT NULL,
  request_id    INT UNSIGNED DEFAULT NULL,
  status        ENUM('temporary','attached','deleted') NOT NULL DEFAULT 'temporary',
  expires_at    DATETIME DEFAULT NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_upload_token (token_hash),
  INDEX idx_upload_order (order_id),
  INDEX idx_upload_request (request_id),
  INDEX idx_upload_expiry (status, expires_at),
  CONSTRAINT fk_upload_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_upload_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
  CONSTRAINT fk_upload_request FOREIGN KEY (request_id) REFERENCES request_for_call(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Wedding > Food: only the three intended services remain active in this category.
UPDATE services s
JOIN service_categories c ON c.id = s.category_id
SET s.status = 'inactive'
WHERE c.slug = 'food'
  AND s.slug NOT IN ('breakfast-catering','lunch-catering','dinner-catering');

UPDATE services s
JOIN service_categories c ON c.id = s.category_id
SET s.status = 'active',
    s.sort_order = FIELD(s.slug,'breakfast-catering','lunch-catering','dinner-catering')
WHERE c.slug = 'food'
  AND s.slug IN ('breakfast-catering','lunch-catering','dinner-catering');

-- Entertainment uses the original three-service flow. Keep the later
-- consolidated row for history, but do not expose it on customer surfaces.
UPDATE services s
JOIN service_categories c ON c.id = s.category_id
SET s.status = 'active'
WHERE c.slug = 'entertainment-activities'
  AND s.slug IN ('human-doll-mascots','360-degree-camera','photo-booth');

INSERT INTO services
  (category_id,title,slug,short_description,description,price,price_unit,page_template,image,tags,sort_order,status)
SELECT c.id, 'Entertainment Activities', 'entertainment-activities-service',
       'Interactive entertainment for weddings, parties and celebrations.',
       'A single, easy-to-book entertainment service planned for your event, venue and guest profile.',
       6000, 'per event', 'sd', '/uploads/services/fun.png',
       'entertainment,activities,event fun', 1, 'active'
FROM service_categories c WHERE c.slug='entertainment-activities'
ON DUPLICATE KEY UPDATE
  category_id=VALUES(category_id), title=VALUES(title), short_description=VALUES(short_description),
  description=VALUES(description), price=VALUES(price), price_unit=VALUES(price_unit),
  image=VALUES(image), tags=VALUES(tags), sort_order=VALUES(sort_order), status='inactive';

UPDATE services s
JOIN service_categories c ON c.id = s.category_id
SET s.status = 'inactive'
WHERE c.slug = 'entertainment-activities'
  AND s.slug = 'entertainment-activities-service';

-- Flower Rangoli is retired from all customer surfaces but retained for history.
UPDATE service_categories SET status='inactive', hidden=1 WHERE slug='flower-rangoli';
UPDATE services s JOIN service_categories c ON c.id=s.category_id
SET s.status='inactive' WHERE c.slug='flower-rangoli';

-- DJ is one service with seven centrally managed packages.
INSERT INTO services
  (category_id,title,slug,short_description,description,price,price_unit,page_template,image,tags,sort_order,status)
SELECT c.id, 'Professional DJ Experience', 'dj-experience',
       'Professional DJ, sound and lighting packages for every celebration size.',
       'Choose a centralized DJ package with the right sound, lighting and production setup for your event.',
       9999, 'per event', 'sd', '/uploads/services/dj.png', 'dj,music,sound,lighting', 1, 'active'
FROM service_categories c WHERE c.slug='dj'
ON DUPLICATE KEY UPDATE
  category_id=VALUES(category_id), title=VALUES(title), short_description=VALUES(short_description),
  description=VALUES(description), price=VALUES(price), image=VALUES(image), tags=VALUES(tags), status='active';

UPDATE services s JOIN service_categories c ON c.id=s.category_id
SET s.status='inactive'
WHERE c.slug='dj' AND s.slug<>'dj-experience';

INSERT INTO service_packages
  (service_id,pkg_key,slug,label,price,duration,description,inclusions_json,image,is_default,sort_order,status)
SELECT s.id, p.pkg_key, p.slug, p.label, p.price, 'per event', p.description, p.inclusions, '/uploads/services/dj.png', p.is_default, p.sort_order, 'active'
FROM services s
JOIN (
  SELECT 'starter' pkg_key,'dj-starter' slug,'DJ Starter Package' label,9999 price,'Quality sound, basic LED lighting and a curated playlist for intimate celebrations.' description,'["Professional DJ","Quality sound system","Basic LED lighting","Curated playlist"]' inclusions,1 is_default,1 sort_order
  UNION ALL SELECT 'silver','dj-silver','DJ Silver Package',14999,'Enhanced sound and lighting for celebrations of up to 200 guests.','["Professional DJ","Enhanced sound system","Moving-head lights","Fog machine"]',0,2
  UNION ALL SELECT 'gold','dj-gold','DJ Gold Package',17999,'Premium sound and effects with a customized playlist.','["Professional DJ","Sound towers","LED wash lights","Laser effects","Customized playlist"]',0,3
  UNION ALL SELECT 'platinum','dj-platinum','DJ Platinum Package',24999,'High-impact setup for celebrations of up to 500 guests.','["Professional DJ","Dual subwoofers","Full LED stage rig","Haze machines","Live mixing"]',0,4
  UNION ALL SELECT 'diamond','dj-diamond','DJ Diamond Package',34999,'Touring-grade sound and a full moving-head truss system.','["Professional DJ","Line-array speakers","Moving-head truss","Confetti cannons","CO2 effects"]',0,5
  UNION ALL SELECT 'ultra','dj-ultra','DJ Ultra Package',47999,'Concert-level sound, video wall and professional hosting.','["Professional DJ","Concert-level sound","LED video wall","Professional MC"]',0,6
  UNION ALL SELECT 'grand','dj-grand-celebration','DJ Grand Celebration Package',59999,'Full production package for a showpiece celebration.','["Professional DJ","Full production sound and lighting","Pyrotechnic effects","Mirror ball","Sound engineer"]',0,7
) p
JOIN service_categories c ON c.id=s.category_id AND c.slug='dj'
WHERE s.slug='dj-experience'
ON DUPLICATE KEY UPDATE
  slug=VALUES(slug), label=VALUES(label), price=VALUES(price), duration=VALUES(duration),
  description=VALUES(description), inclusions_json=VALUES(inclusions_json), image=VALUES(image),
  is_default=VALUES(is_default), sort_order=VALUES(sort_order), status='active';

-- Ensure package slugs are globally unique after older rows have been populated.
UPDATE service_packages SET slug=CONCAT('package-',id) WHERE slug IS NULL OR slug='';
SET @has_pkg_slug_unique := (
  SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema=DATABASE() AND table_name='service_packages' AND index_name='uq_package_slug'
);
SET @pkg_slug_sql := IF(@has_pkg_slug_unique=0,
  'ALTER TABLE service_packages ADD UNIQUE KEY uq_package_slug (slug)', 'SELECT 1');
PREPARE pkg_slug_stmt FROM @pkg_slug_sql;
EXECUTE pkg_slug_stmt;
DEALLOCATE PREPARE pkg_slug_stmt;
