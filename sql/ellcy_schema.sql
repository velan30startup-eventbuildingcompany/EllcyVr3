-- ============================================================
-- ELLCY — Complete MySQL 8 Schema  v1.0
-- Run: mysql -u root -p < ellcy_schema.sql
-- ============================================================
CREATE DATABASE IF NOT EXISTS ellcy_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ellcy_db;

-- 1. SITE SETTINGS
CREATE TABLE site_settings (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_val TEXT,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO site_settings (setting_key, setting_val) VALUES
  ('site_name','ELLCY'),
  ('site_tagline','Chennai\'s Premier Event Services Platform'),
  ('contact_phone','+91 123-456-789'),
  ('contact_email','info@ellcy.in'),
  ('contact_address','Chennai, Tamil Nadu'),
  ('currency_symbol','₹'),
  ('maintenance','0');

-- 2. USERS
CREATE TABLE users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  phone         VARCHAR(20),
  password_hash VARCHAR(255),
  role          ENUM('user','admin','superadmin') DEFAULT 'user',
  status        ENUM('active','inactive','banned') DEFAULT 'active',
  last_login    DATETIME,
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email), INDEX idx_role (role), INDEX idx_status (status)
) ENGINE=InnoDB;

-- 3. SERVICE CATEGORIES
CREATE TABLE service_categories (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  parent_id   INT UNSIGNED DEFAULT NULL,
  name        VARCHAR(100) NOT NULL,
  slug        VARCHAR(120) NOT NULL UNIQUE,
  description TEXT,
  image       VARCHAR(300),
  sort_order  INT DEFAULT 0,
  hidden      TINYINT(1) DEFAULT 0,
  status      ENUM('active','inactive') DEFAULT 'active',
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (parent_id) REFERENCES service_categories(id) ON DELETE SET NULL,
  INDEX idx_slug (slug), INDEX idx_parent (parent_id)
) ENGINE=InnoDB;

-- 4. SERVICES
CREATE TABLE services (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id       INT UNSIGNED NOT NULL,
  parent_service_id INT UNSIGNED DEFAULT NULL,
  title             VARCHAR(200) NOT NULL,
  slug              VARCHAR(220) NOT NULL UNIQUE,
  short_description VARCHAR(500),
  description       TEXT,
  price             DECIMAL(10,2) DEFAULT 0.00,
  price_unit        VARCHAR(50),
  page_template     ENUM('sd','cm','snk','bnc','custom') DEFAULT 'sd',
  image             VARCHAR(300),
  rating            DECIMAL(2,1) DEFAULT 4.5,
  tags              VARCHAR(300),
  availability      VARCHAR(200),
  meta_title        VARCHAR(200),
  meta_description  VARCHAR(500),
  sort_order        INT DEFAULT 0,
  featured          TINYINT(1) DEFAULT 0,
  status            ENUM('active','inactive','draft') DEFAULT 'active',
  created_at        DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE RESTRICT,
  FOREIGN KEY (parent_service_id) REFERENCES services(id) ON DELETE SET NULL,
  INDEX idx_slug (slug), INDEX idx_category (category_id), INDEX idx_status (status),
  FULLTEXT idx_search (title, short_description, description, tags)
) ENGINE=InnoDB;

-- 5. SERVICE PACKAGES
CREATE TABLE service_packages (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  service_id INT UNSIGNED NOT NULL,
  pkg_key    VARCHAR(50) NOT NULL,
  label      VARCHAR(100) NOT NULL,
  price      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  duration   VARCHAR(100),
  description TEXT,
  is_default TINYINT(1) DEFAULT 0,
  sort_order INT DEFAULT 0,
  status     ENUM('active','inactive') DEFAULT 'active',
  FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
  UNIQUE KEY uq_svc_key (service_id, pkg_key),
  INDEX idx_service (service_id)
) ENGINE=InnoDB;

-- 6. SERVICE IMAGES
CREATE TABLE service_images (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  service_id INT UNSIGNED NOT NULL,
  path       VARCHAR(300) NOT NULL,
  alt        VARCHAR(200),
  is_primary TINYINT(1) DEFAULT 0,
  sort_order INT DEFAULT 0,
  FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. SERVICE REVIEWS
CREATE TABLE service_reviews (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  service_id  INT UNSIGNED NOT NULL,
  reviewer    VARCHAR(100) NOT NULL,
  rating      TINYINT NOT NULL,
  review_text TEXT,
  approved    TINYINT(1) DEFAULT 0,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
  INDEX idx_service (service_id), INDEX idx_approved (approved)
) ENGINE=InnoDB;

-- 8. ORDERS / BOOKINGS
CREATE TABLE orders (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_ref      VARCHAR(20) NOT NULL UNIQUE,
  user_id        INT UNSIGNED DEFAULT NULL,
  name           VARCHAR(100) NOT NULL,
  email          VARCHAR(150),
  phone          VARCHAR(20) NOT NULL,
  event_type     VARCHAR(100),
  event_date     DATE,
  event_venue    VARCHAR(300),
  event_time     VARCHAR(50),
  guest_count    INT,
  items_json     LONGTEXT NOT NULL,
  subtotal       DECIMAL(10,2) DEFAULT 0.00,
  discount       DECIMAL(10,2) DEFAULT 0.00,
  total          DECIMAL(10,2) DEFAULT 0.00,
  note           TEXT,
  status         ENUM('pending','confirmed','in_progress','completed','cancelled') DEFAULT 'pending',
  payment_status ENUM('unpaid','partial','paid') DEFAULT 'unpaid',
  admin_note     TEXT,
  created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_ref (order_ref), INDEX idx_phone (phone),
  INDEX idx_status (status), INDEX idx_date (event_date)
) ENGINE=InnoDB;

-- 9. REQUEST FOR CALL
CREATE TABLE request_for_call (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  phone      VARCHAR(20) NOT NULL,
  service    VARCHAR(200),
  best_time  VARCHAR(50),
  note       TEXT,
  status     ENUM('new','called','completed','spam') DEFAULT 'new',
  admin_note TEXT,
  ip_address VARCHAR(45),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_phone (phone), INDEX idx_status (status), INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- 10. ACTIVITY LOGS
CREATE TABLE activity_logs (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    INT UNSIGNED DEFAULT NULL,
  action     VARCHAR(100) NOT NULL,
  target     VARCHAR(200),
  detail     TEXT,
  ip_address VARCHAR(45),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_user (user_id), INDEX idx_action (action)
) ENGINE=InnoDB;

-- 11. RATE LIMITING
CREATE TABLE rate_limits (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip_address VARCHAR(45) NOT NULL,
  action     VARCHAR(50) NOT NULL,
  attempts   INT DEFAULT 1,
  window_end DATETIME NOT NULL,
  INDEX idx_ip_action (ip_address, action),
  INDEX idx_window (window_end)
) ENGINE=InnoDB;

-- ── SEED DATA ────────────────────────────────────────────────
INSERT INTO service_categories (parent_id,name,slug,description,image,sort_order,hidden) VALUES
(NULL,'Decoration','decoration','Stage and venue decoration','/public/uploads/services/stage.png',1,0),
(NULL,'Photography','photography','Photography packages','/public/uploads/services/photo.png',2,0),
(NULL,'Dancers','dancers','Professional dance teams','/public/uploads/services/dancers.png',3,1),
(NULL,'Music Performers','music-performers','Live music performers','/public/uploads/services/musical_band.png',4,0),
(NULL,'DJ','dj','Professional DJ services','/public/uploads/services/dj.png',5,0),
(NULL,'Catering','catering-boys','Catering and welcome staff','/public/uploads/services/cateringboys.png',6,0),
(NULL,'Entertainment Activities','entertainment-activities','Fun entertainment booths','/public/uploads/services/photobooth.png',7,0),
(NULL,'Car Entry','car-entry','Decorated car entry','/public/uploads/services/stage.png',8,0),
(NULL,'Flowers','flowers','Fresh and artificial flowers','/public/uploads/services/stage.png',9,0),
(NULL,'Fake Jewellery','fake-jewellery','Artificial jewellery for brides','/public/uploads/services/stage.png',10,0),
(NULL,'Snacks Stalls','snacks-stalls','Food and snack stations','/public/uploads/services/snacks.png',11,0),
(NULL,'Bouncers','bouncers','Event security','/public/uploads/services/bouncer.png',12,0),
(NULL,'Enter Show Down','enter-show-down','Pyro and stage effects','/public/uploads/services/stage.png',13,0),
(NULL,'Bridal & Groom Styling','bridal-groom-styling','Bridal and groom makeover','/public/uploads/services/bridal.png',14,0),
(NULL,'Mehendi','mehendi','Mehendi/henna artists','/public/uploads/services/mehandi.png',15,0),
(NULL,'Invitation','invitation','Wedding invitations','/public/uploads/services/invitation.png',16,1),
(NULL,'Food','food','Food and catering solutions','/public/uploads/services/catering.png',17,0),
(NULL,'Aarthi Plate','aarthi-plate','Traditional aarthi plate','/public/uploads/services/stage.png',18,1);

-- Dancer sub-categories
INSERT INTO service_categories (parent_id,name,slug,image,sort_order)
SELECT id,'Male Dance Team','dancers-male','/public/uploads/services/dancers.png',1 FROM service_categories WHERE slug='dancers';
INSERT INTO service_categories (parent_id,name,slug,image,sort_order)
SELECT id,'Female Dance Team','dancers-female','/public/uploads/services/dancers.png',2 FROM service_categories WHERE slug='dancers';
INSERT INTO service_categories (parent_id,name,slug,image,sort_order)
SELECT id,'Co-ed Team','dancers-coed','/public/uploads/services/dancers.png',3 FROM service_categories WHERE slug='dancers';

-- Car Entry sub-categories
INSERT INTO service_categories (parent_id,name,slug,image,sort_order)
SELECT id,'Normal Cars','car-entry-normal','/public/uploads/services/stage.png',1 FROM service_categories WHERE slug='car-entry';
INSERT INTO service_categories (parent_id,name,slug,image,sort_order)
SELECT id,'Luxury Cars','car-entry-luxury','/public/uploads/services/stage.png',2 FROM service_categories WHERE slug='car-entry';

-- Flower sub-categories
INSERT INTO service_categories (parent_id,name,slug,image,sort_order)
SELECT id,'Reception','flowers-reception','/public/uploads/services/stage.png',1 FROM service_categories WHERE slug='flowers';
INSERT INTO service_categories (parent_id,name,slug,image,sort_order)
SELECT id,'Marriage','flowers-marriage','/public/uploads/services/stage.png',2 FROM service_categories WHERE slug='flowers';
