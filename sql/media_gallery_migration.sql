-- ============================================================
-- ELLCY — Gallery Media Type Migration
-- Adds video support to service_images so each service can have
-- a mixed gallery of photos AND videos (Amazon-style thumbnail
-- gallery: shows video first if present, else photos).
-- Safe to re-run.
-- ============================================================
USE ellcy_db;

ALTER TABLE service_images
  ADD COLUMN IF NOT EXISTS media_type ENUM('image','video') NOT NULL DEFAULT 'image' AFTER path,
  ADD COLUMN IF NOT EXISTS video_provider ENUM('upload','youtube','vimeo') NULL AFTER media_type,
  ADD COLUMN IF NOT EXISTS thumbnail VARCHAR(300) NULL AFTER video_provider;

-- `path` holds: an uploaded file path (image or video), OR a full
-- YouTube/Vimeo URL when video_provider is 'youtube'/'vimeo'.
-- `thumbnail` is optional — a poster image for external video links
-- (uploaded videos can auto-generate a thumbnail client-side).
