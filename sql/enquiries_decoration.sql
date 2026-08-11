-- ============================================================
-- ELLCY — Stage Decoration & Light Decoration Enquiries
-- Run this after ellcy_schema.sql (adds two new tables; safe to
-- re-run, uses CREATE TABLE IF NOT EXISTS).
-- ============================================================
USE ellcy_db;

CREATE TABLE IF NOT EXISTS stage_decoration_enquiries (
  enquiry_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_name  VARCHAR(100)  NOT NULL,
  phone_number   VARCHAR(20)   NOT NULL,
  email          VARCHAR(150)  NULL,
  event_date     DATE          NULL,
  budget_range   VARCHAR(40)   NULL,
  location       VARCHAR(255)  NOT NULL,
  flower_type    ENUM('real','artificial') NULL,
  venue_image    VARCHAR(255)  NULL,
  ip_address     VARCHAR(45)   NULL,
  created_at     DATETIME      DEFAULT CURRENT_TIMESTAMP,
  enquiry_status ENUM('new','contacted','converted','closed') DEFAULT 'new',
  admin_note     VARCHAR(500)  NULL,
  INDEX idx_status (enquiry_status),
  INDEX idx_created (created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS light_decoration_enquiries (
  enquiry_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_name  VARCHAR(100)  NOT NULL,
  phone_number   VARCHAR(20)   NOT NULL,
  email          VARCHAR(150)  NULL,
  event_date     DATE          NULL,
  budget_range   VARCHAR(40)   NULL,
  location       VARCHAR(255)  NOT NULL,
  arch_required  ENUM('yes','no') NULL,
  venue_image    VARCHAR(255)  NULL,
  ip_address     VARCHAR(45)   NULL,
  created_at     DATETIME      DEFAULT CURRENT_TIMESTAMP,
  enquiry_status ENUM('new','contacted','converted','closed') DEFAULT 'new',
  admin_note     VARCHAR(500)  NULL,
  INDEX idx_status (enquiry_status),
  INDEX idx_created (created_at)
) ENGINE=InnoDB;
