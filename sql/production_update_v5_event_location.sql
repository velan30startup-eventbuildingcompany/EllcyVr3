-- ============================================================
-- Ellcy.in — Production update v5
-- "Decoration" category renamed to "Event Location" on the site
-- (frontend-only rename, category slug 'decoration' is unchanged
-- so no service/booking data needs to move).
--
-- Adds storage for up to 4 event-location reference photos that
-- a customer can attach to their booking, alongside the existing
-- `event_venue` (Mahal / venue name) text field.
--
-- Safe to re-run (guarded with INFORMATION_SCHEMA check).
-- ============================================================

SET @col_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'event_venue_images'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE orders ADD COLUMN event_venue_images VARCHAR(1000) DEFAULT NULL AFTER event_venue',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Stores a JSON array of up to 4 relative paths, e.g.
-- ["/uploads/venues/a1b2c3.jpg","/uploads/venues/d4e5f6.jpg"]
-- Kept as a plain VARCHAR (not native JSON type) for compatibility
-- with older MySQL/MariaDB versions still common on shared XAMPP/
-- cPanel hosting — the app reads/writes it with json_encode/decode.
