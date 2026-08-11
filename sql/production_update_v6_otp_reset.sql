-- ============================================================
-- Ellcy.in — Production update v6
-- Forgot Password now emails a 6-digit code (verified on-site)
-- instead of a clickable reset link.
--
-- password_resets.token_hash is reused to store sha256(code)
-- instead of sha256(long random token) — no column rename needed.
-- This just adds a guess-attempt counter so a 6-digit code can be
-- locked out after repeated wrong guesses.
--
-- Safe to re-run (guarded with INFORMATION_SCHEMA check).
-- ============================================================

SET @col_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'password_resets' AND COLUMN_NAME = 'attempts'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE password_resets ADD COLUMN attempts TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER ip_address',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
