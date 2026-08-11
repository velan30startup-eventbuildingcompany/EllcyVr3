-- ============================================================
-- ELLCY — Production Update v3
-- Amazon-style header/account widget + Forgot Password flow.
-- Safe to run multiple times (guards with IF NOT EXISTS /
-- INFORMATION_SCHEMA checks so it never errors on a re-run).
-- ============================================================

-- ── 1. PASSWORD RESETS ───────────────────────────────────────
-- Stores only a HASH of the reset token (never the raw token) so
-- a leaked database never reveals a usable link. The raw token is
-- only ever emailed to the user and never persisted.
CREATE TABLE IF NOT EXISTS password_resets (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id      INT UNSIGNED NOT NULL,
  token_hash   VARCHAR(64) NOT NULL,      -- sha256(raw token), hex
  expires_at   DATETIME NOT NULL,
  used_at      DATETIME DEFAULT NULL,
  ip_address   VARCHAR(45),
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user (user_id),
  INDEX idx_token_hash (token_hash),
  INDEX idx_expires (expires_at),
  CONSTRAINT fk_password_resets_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── 2. USERS — optional profile fields used by Account Settings ──
-- Added only if missing, so this migration is safe to re-run and
-- safe to run against a database that already has these columns
-- from a previous manual change.
SET @col_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'address'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE users ADD COLUMN address VARCHAR(255) DEFAULT NULL AFTER phone',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ── 3. ORDERS — index user_id for fast "My Orders" lookups ───
SET @idx_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND INDEX_NAME = 'idx_user_id'
);
SET @sql := IF(@idx_exists = 0,
  'ALTER TABLE orders ADD INDEX idx_user_id (user_id)',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
