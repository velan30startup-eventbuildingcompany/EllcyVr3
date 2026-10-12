-- ELLCY v11: Clerk identity linkage and persistent cloud media metadata.
-- Safe to re-run on MySQL 8 / Aiven MySQL.

SET @col_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='users' AND COLUMN_NAME='clerk_user_id');
SET @sql := IF(@col_exists=0, 'ALTER TABLE users ADD COLUMN clerk_user_id VARCHAR(80) NULL AFTER id', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='users' AND INDEX_NAME='uq_users_clerk_user_id');
SET @sql := IF(@idx_exists=0, 'ALTER TABLE users ADD UNIQUE INDEX uq_users_clerk_user_id (clerk_user_id)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='service_images' AND COLUMN_NAME='storage_provider');
SET @sql := IF(@col_exists=0, 'ALTER TABLE service_images ADD COLUMN storage_provider VARCHAR(30) NULL AFTER thumbnail', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='service_images' AND COLUMN_NAME='storage_key');
SET @sql := IF(@col_exists=0, 'ALTER TABLE service_images ADD COLUMN storage_key VARCHAR(255) NULL AFTER storage_provider', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='service_images' AND COLUMN_NAME='thumbnail_storage_key');
SET @sql := IF(@col_exists=0, 'ALTER TABLE service_images ADD COLUMN thumbnail_storage_key VARCHAR(255) NULL AFTER storage_key', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

