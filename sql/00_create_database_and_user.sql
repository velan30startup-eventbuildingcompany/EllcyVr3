-- ============================================================
-- ELLCY — Database + Dedicated User Setup
-- Run this FIRST, as root, before ellcy_schema.sql
--
--   mysql -u root -p < 00_create_database_and_user.sql
--
-- (or paste it into phpMyAdmin's SQL tab while logged in as root)
--
-- Why: using MySQL's "root" user with no password (the XAMPP
-- default) for a live application is bad practice — root has
-- unlimited privileges over every database on the server. This
-- creates a separate account that can only touch ellcy_db.
-- ============================================================

CREATE DATABASE IF NOT EXISTS ellcy_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Dedicated application user — scoped to ellcy_db only.
CREATE USER IF NOT EXISTS 'ellcy_user'@'localhost'
  IDENTIFIED BY 'CHANGE_THIS_BEFORE_RUNNING';

GRANT ALL PRIVILEGES ON ellcy_db.* TO 'ellcy_user'@'localhost';

FLUSH PRIVILEGES;

-- ============================================================
-- Credentials created:
--   Host:     localhost
--   Database: ellcy_db
--   User:     ellcy_user
--   Password: set a unique value before running this file
--
-- Put the same value in the ELLCY_DB_PASS environment variable.
-- Never commit the real password to this project.
-- ============================================================
