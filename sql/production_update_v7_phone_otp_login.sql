-- ============================================================
-- Ellcy.in — Production update v7
-- Adds phone-number + OTP login, and a login_history table for
-- auditing every login attempt (used to detect "first time this
-- phone/email has ever tried to log in" so we can tell the user
-- to create an account instead of silently failing or, worse,
-- silently creating one for them).
--
-- Safe to re-run (every change is guarded with an
-- INFORMATION_SCHEMA / SHOW INDEX check first).
-- ============================================================

-- 1. users.phone needs to be lookup-able and unique so "log in with
--    this phone number" resolves to exactly one account. NULLs are
--    still allowed (MySQL permits multiple NULLs in a UNIQUE index),
--    so existing accounts with no phone on file are unaffected.
--
--    NOTE: if you already have two or more accounts sharing the same
--    non-null phone number, this ALTER will fail with a duplicate-key
--    error — those need to be resolved by hand (pick which account
--    keeps the number) before re-running this file.
SET @idx_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND INDEX_NAME = 'uniq_phone'
);
SET @duplicate_phones := (
  SELECT COUNT(*)
  FROM (
    SELECT phone
    FROM users
    WHERE phone IS NOT NULL AND phone <> ''
    GROUP BY phone
    HAVING COUNT(*) > 1
  ) AS duplicates
);
SET @sql := IF(@idx_exists = 0 AND @duplicate_phones = 0,
  'ALTER TABLE users ADD UNIQUE INDEX uniq_phone (phone)',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. otp_logins — one row per OTP send. A phone can have several
--    rows over time (old ones just expire); the most recent
--    unexpired, unconsumed row is the active code.
CREATE TABLE IF NOT EXISTS otp_logins (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  phone       VARCHAR(20) NOT NULL,
  otp_hash    VARCHAR(64) NOT NULL,      -- sha256(code), never the raw code
  attempts    TINYINT UNSIGNED NOT NULL DEFAULT 0,
  consumed_at DATETIME DEFAULT NULL,
  expires_at  DATETIME NOT NULL,
  ip_address  VARCHAR(45),
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_phone (phone),
  INDEX idx_expires (expires_at)
) ENGINE=InnoDB;

-- 3. login_history — every login attempt, password or OTP, success
--    or failure. user_id is NULL when the identifier didn't match
--    any account (so we can tell "never tried before" apart from
--    "tried and got the password/OTP wrong").
CREATE TABLE IF NOT EXISTS login_history (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED DEFAULT NULL,
  identifier  VARCHAR(150) NOT NULL,     -- the email or phone typed in
  method      ENUM('password','otp') NOT NULL,
  success     TINYINT(1) NOT NULL DEFAULT 0,
  ip_address  VARCHAR(45),
  user_agent  VARCHAR(255),
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_identifier (identifier),
  INDEX idx_user (user_id),
  INDEX idx_created (created_at)
) ENGINE=InnoDB;
