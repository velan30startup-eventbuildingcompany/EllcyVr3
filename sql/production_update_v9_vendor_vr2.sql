CREATE TABLE IF NOT EXISTS vendor_applications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_name VARCHAR(180) NOT NULL,
  contact_name VARCHAR(180) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  service_category VARCHAR(80) NOT NULL,
  city VARCHAR(120) NOT NULL,
  website VARCHAR(255) NULL,
  note TEXT NULL,
  status ENUM('new','reviewing','approved','rejected') NOT NULL DEFAULT 'new',
  ip_hash CHAR(64) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_vendor_status_created (status, created_at),
  INDEX idx_vendor_email (email),
  INDEX idx_vendor_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
