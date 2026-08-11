-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: ellcy_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `target` varchar(200) DEFAULT NULL,
  `detail` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_action` (`action`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,1,'admin_login','users','Login from ::1','::1','2026-07-21 20:59:51'),(2,1,'service_update','services','ID 14','::1','2026-07-21 21:00:47'),(3,1,'service_update','services','ID 14','::1','2026-07-21 21:01:22'),(4,1,'service_update','services','ID 14','::1','2026-07-21 21:01:35'),(5,1,'service_update','services','ID 14','::1','2026-07-21 21:01:52'),(6,1,'service_update','services','ID 14','::1','2026-07-21 21:02:54'),(7,1,'service_create','services','ID 75: Ellcy - one time','::1','2026-07-21 21:04:00'),(8,1,'service_update','services','ID 75','::1','2026-07-21 21:04:08'),(9,1,'service_update','services','ID 75','::1','2026-07-21 21:04:57'),(10,1,'service_update','services','ID 75','::1','2026-07-21 21:05:24'),(11,1,'service_update','services','ID 75','::1','2026-07-21 21:05:50'),(12,1,'settings_update','site_settings','Admin updated settings','::1','2026-07-21 21:10:13'),(13,1,'settings_update','site_settings','Admin updated settings','::1','2026-07-21 21:10:25'),(14,1,'admin_login','users','Login from ::1','::1','2026-07-21 21:17:54'),(15,1,'admin_login','users','Login from ::1','::1','2026-07-22 11:14:13'),(16,1,'admin_login','users','Login from ::1','::1','2026-07-22 11:23:43'),(17,1,'admin_login','users','Login from ::1','::1','2026-07-22 17:42:29'),(18,1,'admin_login','users','Login from ::1','::1','2026-07-29 19:39:31'),(19,1,'admin_login','users','Login from ::1','::1','2026-07-30 10:23:18'),(20,1,'admin_login','users','Login from ::1','::1','2026-08-01 11:56:16'),(21,1,'admin_login','users','Login from ::1','::1','2026-08-01 13:24:12'),(22,1,'service_update','services','ID 35','::1','2026-08-01 13:25:51'),(23,1,'service_update','services','ID 35','::1','2026-08-01 13:27:12'),(24,1,'service_update','services','ID 35','::1','2026-08-01 13:27:25');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_ref` varchar(20) NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `event_type` varchar(100) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_venue` varchar(300) DEFAULT NULL,
  `event_time` varchar(50) DEFAULT NULL,
  `guest_count` int(11) DEFAULT NULL,
  `items_json` longtext NOT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `status` enum('pending','confirmed','in_progress','completed','cancelled') DEFAULT 'pending',
  `payment_status` enum('unpaid','partial','paid') DEFAULT 'unpaid',
  `admin_note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_ref` (`order_ref`),
  KEY `user_id` (`user_id`),
  KEY `idx_ref` (`order_ref`),
  KEY `idx_phone` (`phone`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`event_date`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rate_limits`
--

DROP TABLE IF EXISTS `rate_limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rate_limits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `action` varchar(50) NOT NULL,
  `attempts` int(11) DEFAULT 1,
  `window_end` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ip_action` (`ip_address`,`action`),
  KEY `idx_window` (`window_end`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rate_limits`
--

LOCK TABLES `rate_limits` WRITE;
/*!40000 ALTER TABLE `rate_limits` DISABLE KEYS */;
INSERT INTO `rate_limits` VALUES (46,'::1','user_login',1,'2026-08-02 17:56:10');
/*!40000 ALTER TABLE `rate_limits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `request_for_call`
--

DROP TABLE IF EXISTS `request_for_call`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request_for_call` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(20) NOT NULL,
  `service` varchar(200) DEFAULT NULL,
  `best_time` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` enum('new','called','completed','spam') DEFAULT 'new',
  `admin_note` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_phone` (`phone`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request_for_call`
--

LOCK TABLES `request_for_call` WRITE;
/*!40000 ALTER TABLE `request_for_call` DISABLE KEYS */;
/*!40000 ALTER TABLE `request_for_call` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_categories`
--

DROP TABLE IF EXISTS `service_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(300) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `hidden` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_parent` (`parent_id`),
  CONSTRAINT `service_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `service_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_categories`
--

LOCK TABLES `service_categories` WRITE;
/*!40000 ALTER TABLE `service_categories` DISABLE KEYS */;
INSERT INTO `service_categories` VALUES (1,NULL,'Decoration','decoration','Stage and venue decoration','/public/uploads/services/stage.png',1,0,'active','2026-07-16 17:26:30'),(2,NULL,'Photography','photography','Photography packages','/public/uploads/services/photo.png',2,0,'active','2026-07-16 17:26:30'),(3,NULL,'Dancers','dancers','Professional dance teams','/public/uploads/services/dancers.png',3,1,'active','2026-07-16 17:26:30'),(4,NULL,'Music Performers','music-performers','Live music performers','/public/uploads/services/musical_band.png',4,0,'active','2026-07-16 17:26:30'),(5,NULL,'DJ','dj','Professional DJ services','/public/uploads/services/dj.png',5,0,'active','2026-07-16 17:26:30'),(6,NULL,'Catering','catering-boys','Catering and welcome staff','/public/uploads/services/cateringboys.png',6,0,'active','2026-07-16 17:26:30'),(7,NULL,'Entertainment Activities','entertainment-activities','Fun entertainment booths','/public/uploads/services/photobooth.png',7,0,'active','2026-07-16 17:26:30'),(8,NULL,'Car Entry','car-entry','Decorated car entry','/public/uploads/services/stage.png',8,0,'active','2026-07-16 17:26:30'),(9,NULL,'Flowers','flowers','Fresh and artificial flowers','/public/uploads/services/stage.png',9,0,'active','2026-07-16 17:26:30'),(10,NULL,'Fake Jewellery','fake-jewellery','Artificial jewellery for brides','/public/uploads/services/stage.png',10,0,'active','2026-07-16 17:26:30'),(11,NULL,'Snacks Stalls','snacks-stalls','Food and snack stations','/public/uploads/services/snacks.png',11,0,'active','2026-07-16 17:26:30'),(12,NULL,'Bouncers','bouncers','Event security','/public/uploads/services/bouncer.png',12,0,'active','2026-07-16 17:26:30'),(13,NULL,'Enter Show Down','enter-show-down','Pyro and stage effects','/public/uploads/services/stage.png',13,0,'active','2026-07-16 17:26:30'),(14,NULL,'Bridal & Groom Styling','bridal-groom-styling','Bridal and groom makeover','/public/uploads/services/bridal.png',14,0,'active','2026-07-16 17:26:30'),(15,NULL,'Mehendi','mehendi','Mehendi/henna artists','/public/uploads/services/mehandi.png',15,0,'active','2026-07-16 17:26:30'),(16,NULL,'Invitation','invitation','Wedding invitations','/public/uploads/services/invitation.png',16,0,'active','2026-07-16 17:26:30'),(17,NULL,'Food','food','Food and catering solutions','/public/uploads/services/catering.png',17,0,'active','2026-07-16 17:26:30'),(18,NULL,'Aarthi Plate','aarthi-plate','Traditional aarthi plate','/public/uploads/services/stage.png',18,0,'active','2026-07-16 17:26:30'),(19,3,'Male Dance Team','dancers-male',NULL,'/public/uploads/services/dancers.png',1,0,'active','2026-07-16 17:26:30'),(20,3,'Female Dance Team','dancers-female',NULL,'/public/uploads/services/dancers.png',2,0,'active','2026-07-16 17:26:30'),(21,3,'Co-ed Team','dancers-coed',NULL,'/public/uploads/services/dancers.png',3,0,'active','2026-07-16 17:26:30'),(22,8,'Normal Cars','car-entry-normal',NULL,'/public/uploads/services/stage.png',1,0,'active','2026-07-16 17:26:30'),(23,8,'Luxury Cars','car-entry-luxury',NULL,'/public/uploads/services/stage.png',2,0,'active','2026-07-16 17:26:30'),(24,9,'Reception','flowers-reception',NULL,'/public/uploads/services/stage.png',1,0,'active','2026-07-16 17:26:30'),(25,9,'Marriage','flowers-marriage',NULL,'/public/uploads/services/stage.png',2,0,'active','2026-07-16 17:26:30'),(26,1,'Light Decoration','light-decoration','Indoor & outdoor professional lighting setups for your event venue.','/uploads/services/stage.png',0,0,'active','2026-07-16 17:26:30'),(27,1,'Stage Decoration','stage-decoration','Elegant stage setups, backdrops, floral arrangements and full hall transformations.','/uploads/services/stage.png',0,0,'active','2026-07-16 17:26:30'),(28,4,'Chenda Melam','chenda-melam','Traditional Kerala Chenda Melam percussion ensemble for processions and celebrations.','/uploads/services/stage.png',0,0,'active','2026-07-16 17:26:30'),(29,4,'Nadhaswaram & Thavil','nadhaswaram-thavil','Classical Nadhaswaram and Thavil duo for wedding rituals and auspicious ceremonies.','/uploads/services/stage.png',0,0,'active','2026-07-16 17:26:30'),(30,4,'Band Set','band-set','Professional brass band for baraat, processions and grand entry ceremonies.','/uploads/services/stage.png',0,0,'active','2026-07-16 17:26:30'),(31,4,'Melam Set','melam-set','Traditional melam set for poojas, home events and large temple celebrations.','/uploads/services/stage.png',0,0,'active','2026-07-16 17:26:30'),(32,NULL,'Real Flowers','real-flowers','Fresh and artificial flower decoration for weddings.','/uploads/services/stage.png',50,0,'active','2026-07-16 17:26:30');
/*!40000 ALTER TABLE `service_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_images`
--

DROP TABLE IF EXISTS `service_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` int(10) unsigned NOT NULL,
  `path` varchar(300) NOT NULL,
  `alt` varchar(200) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `service_images_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_images`
--

LOCK TABLES `service_images` WRITE;
/*!40000 ALTER TABLE `service_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_packages`
--

DROP TABLE IF EXISTS `service_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_packages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` int(10) unsigned NOT NULL,
  `pkg_key` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `duration` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_svc_key` (`service_id`,`pkg_key`),
  KEY `idx_service` (`service_id`),
  CONSTRAINT `service_packages_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_packages`
--

LOCK TABLES `service_packages` WRITE;
/*!40000 ALTER TABLE `service_packages` DISABLE KEYS */;
INSERT INTO `service_packages` VALUES (1,74,'wedding','Wedding',80000.00,NULL,NULL,1,0,'active'),(2,74,'prewedding','Pre-Wedding',160000.00,NULL,NULL,0,0,'active'),(3,74,'postwedding','Post-Wedding',160000.00,NULL,NULL,0,0,'active'),(4,74,'engagement','Engagement',80000.00,NULL,NULL,1,0,'active');
/*!40000 ALTER TABLE `service_packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_reviews`
--

DROP TABLE IF EXISTS `service_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` int(10) unsigned NOT NULL,
  `reviewer` varchar(100) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `review_text` text DEFAULT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_service` (`service_id`),
  KEY `idx_approved` (`approved`),
  CONSTRAINT `service_reviews_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_reviews`
--

LOCK TABLES `service_reviews` WRITE;
/*!40000 ALTER TABLE `service_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `parent_service_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `price_unit` varchar(50) DEFAULT NULL,
  `page_template` enum('sd','cm','snk','bnc','custom') DEFAULT 'sd',
  `image` varchar(300) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 4.5,
  `tags` varchar(300) DEFAULT NULL,
  `availability` varchar(200) DEFAULT NULL,
  `meta_title` varchar(200) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `featured` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive','draft') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `parent_service_id` (`parent_service_id`),
  KEY `idx_slug` (`slug`),
  KEY `idx_category` (`category_id`),
  KEY `idx_status` (`status`),
  FULLTEXT KEY `idx_search` (`title`,`short_description`,`description`,`tags`),
  CONSTRAINT `services_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`id`),
  CONSTRAINT `services_ibfk_2` FOREIGN KEY (`parent_service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,26,NULL,'Light Set Up In Party Hall','light-set-up-in-party-hall','Professional indoor party hall lighting setup with RGB LED strips, fairy lights, spotlights, and ambient colour-changing fixtures. Transforms any hall into a stunning venue.','Professional indoor party hall lighting setup with RGB LED strips, fairy lights, spotlights, and ambient colour-changing fixtures. Transforms any hall into a stunning venue.',0.00,'per event','sd','/uploads/services/lighting.png',4.5,'light-decoration',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(2,26,NULL,'Light Set Up In Out Door','light-set-up-in-out-door','High-impact outdoor lighting setup with weatherproof LED fixtures, string lights, uplighters, and powerful floodlights. Perfect for open-air events, lawns and rooftop celebrations.','High-impact outdoor lighting setup with weatherproof LED fixtures, string lights, uplighters, and powerful floodlights. Perfect for open-air events, lawns and rooftop celebrations.',0.00,'per event','sd','/uploads/services/lighting.png',4.5,'light-decoration',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(3,27,NULL,'Party Hall Decoration','party-hall-decoration','Breathtaking party hall stage setups crafted with premium backdrops, LED panels, floral arrangements and full mood lighting — designed to impress every guest.','Breathtaking party hall stage setups crafted with premium backdrops, LED panels, floral arrangements and full mood lighting — designed to impress every guest.',0.00,'per event','sd','/uploads/services/stage.png',4.5,'stage-decoration',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(4,27,NULL,'Outdoor Decoration','outdoor-decoration','Grand outdoor stage setups built for open-air events with weather-resistant structures, floral archways, draping and atmospheric lighting installations.','Grand outdoor stage setups built for open-air events with weather-resistant structures, floral archways, draping and atmospheric lighting installations.',0.00,'per event','sd','/uploads/services/stage.png',4.5,'stage-decoration',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(5,27,NULL,'Hotel Decoration','hotel-decoration','Luxury hotel venue decoration packages including stage design, table centrepieces, floral walls, entrance arches and complete hall transformation.','Luxury hotel venue decoration packages including stage design, table centrepieces, floral walls, entrance arches and complete hall transformation.',0.00,'per event','sd','/uploads/services/stage.png',4.5,'stage-decoration',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(6,17,NULL,'Dinner Catering','dinner-catering','Full-course dinner catering service with multiple cuisines, live counters, and professional serving staff for your event.','Full-course dinner catering service with multiple cuisines, live counters, and professional serving staff for your event.',450.00,'per event','sd','/uploads/services/catering.png',4.5,'food',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(7,17,NULL,'Breakfast Catering','breakfast-catering','Fresh and wholesome breakfast spread with South Indian, North Indian and continental options. Ideal for morning ceremonies.','Fresh and wholesome breakfast spread with South Indian, North Indian and continental options. Ideal for morning ceremonies.',250.00,'per event','sd','/uploads/services/catering.png',4.5,'food',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(8,17,NULL,'Lunch Catering','lunch-catering','Elaborate lunch catering with traditional thali meals, buffet spreads and live cooking stations for afternoon events.','Elaborate lunch catering with traditional thali meals, buffet spreads and live cooking stations for afternoon events.',350.00,'per event','sd','/uploads/services/catering.png',4.5,'food',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(9,5,NULL,'DJ Starter Package','dj-starter-package','Entry-level DJ setup with quality sound system, basic LED lighting and a curated playlist for small, intimate celebrations.','Entry-level DJ setup with quality sound system, basic LED lighting and a curated playlist for small, intimate celebrations.',9999.00,'per event','sd','/uploads/services/dj.png',4.5,'dj',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(10,5,NULL,'DJ Silver Package','dj-silver-package','Mid-range DJ package with enhanced sound system, moving head lights and fog machine. Great for up to 200 guests.','Mid-range DJ package with enhanced sound system, moving head lights and fog machine. Great for up to 200 guests.',14999.00,'per event','sd','/uploads/services/dj.png',4.5,'dj',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(11,5,NULL,'DJ Gold Package','dj-gold-package','Premium DJ experience with professional-grade sound towers, LED wash lights, laser effects and a customised playlist.','Premium DJ experience with professional-grade sound towers, LED wash lights, laser effects and a customised playlist.',17999.00,'per event','sd','/uploads/services/dj.png',4.5,'dj',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(12,5,NULL,'DJ Platinum Package','dj-platinum-package','High-impact DJ setup with dual sub-woofers, full LED stage rig, haze machines and live mixing. Perfect for 500 guests.','High-impact DJ setup with dual sub-woofers, full LED stage rig, haze machines and live mixing. Perfect for 500 guests.',24999.00,'per event','sd','/uploads/services/dj.png',4.5,'dj',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(13,5,NULL,'DJ Diamond Package','dj-diamond-package','Elite DJ performance with touring-grade line-array speakers, full moving-head truss system, confetti cannons and CO₂ jets.','Elite DJ performance with touring-grade line-array speakers, full moving-head truss system, confetti cannons and CO₂ jets.',34999.00,'per event','sd','/uploads/services/dj.png',4.5,'dj',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(14,5,NULL,'Luxury DJ','luxury-dj','Luxury DJ event experience with concert-level sound, full-colour LED video wall backdrop and a professional MC.','Luxury DJ event experience with concert-level sound, full-colour LED video wall backdrop and a professional MC.',47999.00,'per event','sd','/uploads/services/svc_109ab313a7698a8c.png',4.5,'dj','Available All Year','','',0,1,'active','2026-07-16 17:26:30','2026-07-21 21:02:54'),(15,5,NULL,'DJ Grand Celebration Package','dj-grand-celebration-package','The ultimate DJ package — full production sound & lighting, pyrotechnic sparks, mirror ball, cold fire jets and a sound engineer.','The ultimate DJ package — full production sound & lighting, pyrotechnic sparks, mirror ball, cold fire jets and a sound engineer.',59999.00,'per event','sd','/uploads/services/dj.png',4.5,'dj',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(16,28,NULL,'Chenda Melam – Standard','chenda-melam-standard','Traditional Kerala Chenda Melam percussion ensemble with experienced artists performing authentic rhythmic beats for processions and auspicious ceremonies.','Traditional Kerala Chenda Melam percussion ensemble with experienced artists performing authentic rhythmic beats for processions and auspicious ceremonies.',12000.00,'per event','sd','/uploads/services/musical_band.png',4.5,'chenda-melam',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(17,28,NULL,'Chenda Melam – Grand Procession','chenda-melam-grand-procession','Large-scale Chenda Melam troupe with full brass and percussion ensemble ideal for wedding processions, temple festivals and grand cultural events.','Large-scale Chenda Melam troupe with full brass and percussion ensemble ideal for wedding processions, temple festivals and grand cultural events.',22000.00,'per event','sd','/uploads/services/musical_band.png',4.5,'chenda-melam',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(18,29,NULL,'Nadhaswaram & Thavil – Classic','nadhaswaram-thavil-classic','Traditional Nadhaswaram and Thavil duo performance for auspicious ceremonies, wedding rituals and processions. Brings divine blessings and festive energy.','Traditional Nadhaswaram and Thavil duo performance for auspicious ceremonies, wedding rituals and processions. Brings divine blessings and festive energy.',8000.00,'per event','sd','/uploads/services/nadhaswaram.png',4.5,'nadhaswaram-thavil',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(19,29,NULL,'Nadhaswaram & Thavil – Grand','nadhaswaram-thavil-grand','Full ensemble Nadhaswaram and Thavil group performance ideal for grand weddings, temple events and large-scale cultural celebrations.','Full ensemble Nadhaswaram and Thavil group performance ideal for grand weddings, temple events and large-scale cultural celebrations.',15000.00,'per event','sd','/uploads/services/nadhaswaram.png',4.5,'nadhaswaram-thavil',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(20,30,NULL,'Band Set – 6 Members','band-set-6-members','Compact 6-member brass band for intimate wedding entries and smaller processions. Uniformed performers with a curated wedding classics repertoire.','Compact 6-member brass band for intimate wedding entries and smaller processions. Uniformed performers with a curated wedding classics repertoire.',11994.00,'per event','sd','/uploads/services/bandset.png',4.5,'band-set',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(21,30,NULL,'Band Set – 8 Members','band-set-8-members','8-member ensemble delivering a fuller brass sound, ideal for mid-sized wedding processions and grand entry ceremonies.','8-member ensemble delivering a fuller brass sound, ideal for mid-sized wedding processions and grand entry ceremonies.',15992.00,'per event','sd','/uploads/services/bandset.png',4.5,'band-set',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(22,30,NULL,'Band Set – 10 Members','band-set-10-members','Impressive 10-member brass band for larger wedding ceremonies, baarats and grand entries with high energy and showmanship.','Impressive 10-member brass band for larger wedding ceremonies, baarats and grand entries with high energy and showmanship.',19990.00,'per event','sd','/uploads/services/bandset.png',4.5,'band-set',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(23,30,NULL,'Band Set – 12 Members','band-set-12-members','Grand 12-member ensemble with uniformed performers and drum major. A commanding presence for large-scale wedding processions.','Grand 12-member ensemble with uniformed performers and drum major. A commanding presence for large-scale wedding processions.',23988.00,'per event','sd','/uploads/services/bandset.png',4.5,'band-set',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(24,30,NULL,'Band Set – 15 Members','band-set-15-members','Premium 15-member brass band with LED costumes and choreographed drum majors. Makes every procession a visual and musical spectacle.','Premium 15-member brass band with LED costumes and choreographed drum majors. Makes every procession a visual and musical spectacle.',29985.00,'per event','sd','/uploads/services/bandset.png',4.5,'band-set',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(25,30,NULL,'Band Set – 18 Members','band-set-18-members','Elite 18-member ensemble delivering a wall of sound and dazzling performance. Perfect for extravagant weddings and grand baraat celebrations.','Elite 18-member ensemble delivering a wall of sound and dazzling performance. Perfect for extravagant weddings and grand baraat celebrations.',35982.00,'per event','sd','/uploads/services/bandset.png',4.5,'band-set',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(26,30,NULL,'Band Set – 20 Members','band-set-20-members','Our flagship 20-member full brass band — the ultimate grand entry experience with full LED production, drum corps and maximum energy.','Our flagship 20-member full brass band — the ultimate grand entry experience with full LED production, drum corps and maximum energy.',39980.00,'per event','sd','/uploads/services/bandset.png',4.5,'band-set',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(27,31,NULL,'Melam Set – 4 Members','melam-set-4-members','Compact 4-member melam procession set for intimate ceremonies, home poojas and smaller festive occasions.','Compact 4-member melam procession set for intimate ceremonies, home poojas and smaller festive occasions.',7994.00,'per event','sd','/uploads/services/musical_band.png',4.5,'melam-set',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(28,31,NULL,'Melam Set – 6 Members','melam-set-6-members','6-member traditional percussion ensemble, ideal for mid-sized processions, griha pravesams and auspicious family functions.','6-member traditional percussion ensemble, ideal for mid-sized processions, griha pravesams and auspicious family functions.',11994.00,'per event','sd','/uploads/services/musical_band.png',4.5,'melam-set',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(29,31,NULL,'Melam Set – 8 Members','melam-set-8-members','8-member melam set delivering a fuller, more resonant sound for wedding processions and temple festival ceremonies.','8-member melam set delivering a fuller, more resonant sound for wedding processions and temple festival ceremonies.',15992.00,'per event','sd','/uploads/services/musical_band.png',4.5,'melam-set',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(30,31,NULL,'Melam Set – 10 Members','melam-set-10-members','Grand 10-member ensemble ideal for larger wedding processions, temple festivals and elaborate ceremonial routes.','Grand 10-member ensemble ideal for larger wedding processions, temple festivals and elaborate ceremonial routes.',19990.00,'per event','sd','/uploads/services/musical_band.png',4.5,'melam-set',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(31,31,NULL,'Melam Set – 12 Members','melam-set-12-members','12-member percussion ensemble creating a powerful, rhythmic atmosphere for grand weddings and major festival events.','12-member percussion ensemble creating a powerful, rhythmic atmosphere for grand weddings and major festival events.',23988.00,'per event','sd','/uploads/services/musical_band.png',4.5,'melam-set',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(32,31,NULL,'Melam Set – 15 Members','melam-set-15-members','Premium 15-member melam set for large-scale processions and elaborate cultural celebrations with full devotional energy.','Premium 15-member melam set for large-scale processions and elaborate cultural celebrations with full devotional energy.',29985.00,'per event','sd','/uploads/services/musical_band.png',4.5,'melam-set',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(33,31,NULL,'Melam Set – 18 Members','melam-set-18-members','Elite 18-member ensemble delivering an immersive wall of percussion for extravagant wedding processions and grand events.','Elite 18-member ensemble delivering an immersive wall of percussion for extravagant wedding processions and grand events.',35982.00,'per event','sd','/uploads/services/musical_band.png',4.5,'melam-set',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(34,31,NULL,'Melam Set – 20 Members','melam-set-20-members','Our flagship 20-member grand procession ensemble — the ultimate traditional melam experience for the most prestigious ceremonies.','Our flagship 20-member grand procession ensemble — the ultimate traditional melam experience for the most prestigious ceremonies.',39980.00,'per event','sd','/uploads/services/musical_band.png',4.5,'melam-set',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(35,12,NULL,'Event Security & Bouncers','event-security-bouncers','Professional event security and bouncers for crowd management, entry control and event safety.','Professional event security and bouncers for crowd management, entry control and event safety.',1400.00,'per event','sd','/uploads/services/svc_966ab575d0ff4f41.jpeg',4.5,'bouncers','Available All Year','','',0,0,'active','2026-07-16 17:26:30','2026-08-01 13:27:25'),(36,7,NULL,'360 Degree Camera','360-degree-camera','Immersive 360° camera booth for your event — captures slow-motion videos of guests for instant sharing and lasting memories.','Immersive 360° camera booth for your event — captures slow-motion videos of guests for instant sharing and lasting memories.',8000.00,'per event','sd','/uploads/services/photobooth.png',4.5,'entertainment-activities',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(37,7,NULL,'Photo Booth','photo-booth','Fully branded photo booth with props, instant prints and digital sharing. A crowd favourite at weddings and parties.','Fully branded photo booth with props, instant prints and digital sharing. A crowd favourite at weddings and parties.',6000.00,'per event','sd','/uploads/services/photobooth.png',4.5,'entertainment-activities',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(38,7,NULL,'Human Doll (Mascots)','human-doll-mascots','Life-size human doll and mascot characters for entertaining guests, photo opportunities and themed event experiences. Available in Cute, Giant, Cartoon and Couple styles.','Life-size human doll and mascot characters for entertaining guests, photo opportunities and themed event experiences. Available in Cute, Giant, Cartoon and Couple styles.',2499.00,'per event','sd','/uploads/services/fun.png',4.5,'entertainment-activities',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(39,11,NULL,'Cotton Candy','cotton-candy','Classic fluffy cotton candy stall with multiple flavours and colours. A sweet treat loved by guests of all ages.','Classic fluffy cotton candy stall with multiple flavours and colours. A sweet treat loved by guests of all ages.',3000.00,'per event','sd','/uploads/services/snacks.png',4.5,'snacks-stalls',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(40,11,NULL,'Pop Corn','pop-corn','Freshly popped flavoured popcorn stall with savoury and sweet varieties. Perfect for evening events and receptions.','Freshly popped flavoured popcorn stall with savoury and sweet varieties. Perfect for evening events and receptions.',2500.00,'per event','sd','/uploads/services/snacks.png',4.5,'snacks-stalls',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(41,11,NULL,'Chocolate Foundation','chocolate-foundation','Elegant chocolate fountain with dipping options — fruits, marshmallows and wafers. A showpiece treat for your event.','Elegant chocolate fountain with dipping options — fruits, marshmallows and wafers. A showpiece treat for your event.',5000.00,'per event','sd','/uploads/services/snacks.png',4.5,'snacks-stalls',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(42,11,NULL,'Fruit Salad','fruit-salad','Fresh seasonal fruit salad station with cream and honey dressing options. Healthy and refreshing for all guests.','Fresh seasonal fruit salad station with cream and honey dressing options. Healthy and refreshing for all guests.',2000.00,'per event','sd','/uploads/services/snacks.png',4.5,'snacks-stalls',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(43,11,NULL,'Ice Cream','ice-cream','Premium ice cream parlour stall with multiple flavours and toppings. Served in cups and cones for your guests.','Premium ice cream parlour stall with multiple flavours and toppings. Served in cups and cones for your guests.',3500.00,'per event','sd','/uploads/services/snacks.png',4.5,'snacks-stalls',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(44,11,NULL,'Mojito & Tea','mojito-tea','Live mojito and tea counter with fresh mint mojitos, lemon coolers and specialty teas to keep your guests refreshed.','Live mojito and tea counter with fresh mint mojitos, lemon coolers and specialty teas to keep your guests refreshed.',4000.00,'per event','sd','/uploads/services/snacks.png',4.5,'snacks-stalls',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(45,13,NULL,'Pyro Show','pyro-show','Spectacular choreographed pyro burst with colourful aerial effects for grand entries and stage reveals.','Spectacular choreographed pyro burst with colourful aerial effects for grand entries and stage reveals.',299.00,'per event','sd','/uploads/services/stage.png',4.5,'enter-show-down',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(46,13,NULL,'Entry Pot Fag','entry-pot-fag','Dramatic entry pot fog effect that creates a mystical low-lying fog for bride/groom entries and stage entrances.','Dramatic entry pot fog effect that creates a mystical low-lying fog for bride/groom entries and stage entrances.',459.00,'per event','sd','/uploads/services/stage.png',4.5,'enter-show-down',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(47,13,NULL,'Paper Blast','paper-blast','High-energy confetti paper cannon blast for entries, first dance and grand celebration moments.','High-energy confetti paper cannon blast for entries, first dance and grand celebration moments.',299.00,'per event','sd','/uploads/services/stage.png',4.5,'enter-show-down',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(48,13,NULL,'Rose Blast','rose-blast','Romantic rose petal blast that showers the couple with fragrant petals during special moments.','Romantic rose petal blast that showers the couple with fragrant petals during special moments.',299.00,'per event','sd','/uploads/services/stage.png',4.5,'enter-show-down',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(49,13,NULL,'Balloon Blast','balloon-blast','Exciting balloon blast with hundreds of balloons released simultaneously for celebrations and photo moments.','Exciting balloon blast with hundreds of balloons released simultaneously for celebrations and photo moments.',599.00,'per event','sd','/uploads/services/stage.png',4.5,'enter-show-down',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(50,13,NULL,'Stage Fog Setup','stage-fog-setup','Professional stage fog machine setup that creates dramatic atmospheric effects for performances and entries.','Professional stage fog machine setup that creates dramatic atmospheric effects for performances and entries.',599.00,'per event','sd','/uploads/services/stage.png',4.5,'enter-show-down',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(51,13,NULL,'Gun Paper Blast','gun-paper-blast','Handheld confetti gun blast for instant celebration effects — perfect for couple entry and first dance moments.','Handheld confetti gun blast for instant celebration effects — perfect for couple entry and first dance moments.',499.00,'per event','sd','/uploads/services/stage.png',4.5,'enter-show-down',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(52,6,NULL,'Welcome Girls – Breakfast','welcome-girls-breakfast','Graceful welcome girls greeting and welcoming your guests at breakfast. Fixed price booking.','Graceful welcome girls greeting and welcoming your guests at breakfast. Fixed price booking.',1500.00,'per event','sd','/uploads/services/welcomegirls.png',4.5,'catering-boys',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(53,6,NULL,'Welcome Girls – Lunch','welcome-girls-lunch','Graceful welcome girls greeting and welcoming your guests at lunch. Fixed price booking.','Graceful welcome girls greeting and welcoming your guests at lunch. Fixed price booking.',1500.00,'per event','sd','/uploads/services/welcomegirls.png',4.5,'catering-boys',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(54,6,NULL,'Welcome Girls – Dinner','welcome-girls-dinner','Graceful welcome girls greeting and welcoming your guests at dinner. Fixed price booking.','Graceful welcome girls greeting and welcoming your guests at dinner. Fixed price booking.',1500.00,'per event','sd','/uploads/services/welcomegirls.png',4.5,'catering-boys',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(55,6,NULL,'Catering Boys – Breakfast','catering-boys-breakfast','Uniformed catering boys serving breakfast at your event. Fixed price booking.','Uniformed catering boys serving breakfast at your event. Fixed price booking.',750.00,'per event','sd','/uploads/services/cateringboys.png',4.5,'catering-boys',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(56,6,NULL,'Catering Boys – Lunch','catering-boys-lunch','Uniformed catering boys serving lunch at your event. Fixed price booking.','Uniformed catering boys serving lunch at your event. Fixed price booking.',750.00,'per event','sd','/uploads/services/cateringboys.png',4.5,'catering-boys',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(57,6,NULL,'Catering Boys – Dinner','catering-boys-dinner','Uniformed catering boys serving dinner at your event. Fixed price booking.','Uniformed catering boys serving dinner at your event. Fixed price booking.',750.00,'per event','sd','/uploads/services/cateringboys.png',4.5,'catering-boys',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(58,3,NULL,'Only Male Team','only-male-team','High-energy all-male dance troupe performing Bollywood, folk and western styles to energise your event. Choose 4, 5, 7 or 9 members.','High-energy all-male dance troupe performing Bollywood, folk and western styles to energise your event. Choose 4, 5, 7 or 9 members.',11196.00,'per event','sd','/uploads/services/dancers.png',4.5,'dancers',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(59,3,NULL,'Only Female Team','only-female-team','Graceful all-female dance performance team with classical, semi-classical and contemporary repertoire. Choose 4, 5, 7 or 9 members.','Graceful all-female dance performance team with classical, semi-classical and contemporary repertoire. Choose 4, 5, 7 or 9 members.',15196.00,'per event','sd','/uploads/services/dancers.png',4.5,'dancers',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(60,3,NULL,'Co-ED Man & Women Team','co-ed-man-women-team','Dynamic mixed-gender dance troupe with choreographed group performances for weddings and grand events. Choose 4, 6, 8, 10 or 12 members.','Dynamic mixed-gender dance troupe with choreographed group performances for weddings and grand events. Choose 4, 6, 8, 10 or 12 members.',12998.00,'per event','sd','/uploads/services/dancers.png',4.5,'dancers',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(61,16,NULL,'Digital Wedding Invitation','digital-wedding-invitation','Beautifully designed digital wedding invitation with animations, music and personalised details. Shared instantly via WhatsApp and social media.','Beautifully designed digital wedding invitation with animations, music and personalised details. Shared instantly via WhatsApp and social media.',2000.00,'per event','sd','/uploads/services/stage.png',4.5,'invitation',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(62,32,NULL,'Reception — Real Flowers','reception-real-flowers','Fresh real flower stage, entry arch and table arrangements for your reception — roses, jasmine and marigold sourced every morning.','Fresh real flower stage, entry arch and table arrangements for your reception — roses, jasmine and marigold sourced every morning.',5000.00,'per event','sd','/uploads/services/stage.png',4.5,'real-flowers,reception,real',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(63,32,NULL,'Reception — Artificial Flowers','reception-artificial-flowers','Premium quality artificial flower stage and decor for reception — lifelike blooms that stay perfect all day without wilting.','Premium quality artificial flower stage and decor for reception — lifelike blooms that stay perfect all day without wilting.',6000.00,'per event','sd','/uploads/services/stage.png',4.5,'real-flowers,reception,artificial',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(64,32,NULL,'Marriage — Real Flowers','marriage-real-flowers','Lush real flower mandapam, garlands and venue decoration for the wedding ceremony — traditional fragrant blooms for an authentic setup.','Lush real flower mandapam, garlands and venue decoration for the wedding ceremony — traditional fragrant blooms for an authentic setup.',5000.00,'per event','sd','/uploads/services/stage.png',4.5,'real-flowers,marriage,real',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(65,32,NULL,'Marriage — Artificial Flowers','marriage-artificial-flowers','Long-lasting artificial flower mandapam and bridal-path decoration for the marriage ceremony — vibrant colours that photograph beautifully.','Long-lasting artificial flower mandapam and bridal-path decoration for the marriage ceremony — vibrant colours that photograph beautifully.',6000.00,'per event','sd','/uploads/services/stage.png',4.5,'real-flowers,marriage,artificial',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(66,10,NULL,'Gold Style Jewellery','gold-style-jewellery','Premium gold-finish fashion jewellery sets for brides and bridesmaids — necklaces, bangles, earrings and maang tikka.','Premium gold-finish fashion jewellery sets for brides and bridesmaids — necklaces, bangles, earrings and maang tikka.',3000.00,'per event','sd','/uploads/services/stage.png',4.5,'fake-jewellery',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(67,10,NULL,'Silver Style Jewellery','silver-style-jewellery','Elegant silver-finish fashion jewellery sets for weddings and ceremonies — oxidised and contemporary designs available.','Elegant silver-finish fashion jewellery sets for weddings and ceremonies — oxidised and contemporary designs available.',2500.00,'per event','sd','/uploads/services/stage.png',4.5,'fake-jewellery',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(68,10,NULL,'Kundan Style Jewellery','kundan-style-jewellery','Traditional Kundan jewellery sets with intricate stonework — perfect for bridal and ethnic ceremony looks.','Traditional Kundan jewellery sets with intricate stonework — perfect for bridal and ethnic ceremony looks.',3500.00,'per event','sd','/uploads/services/stage.png',4.5,'fake-jewellery',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(69,8,NULL,'Normal Cars Entry','normal-cars-entry','Stylish decorated normal car entry for bride and groom with floral decorations and ribbon arrangements.','Stylish decorated normal car entry for bride and groom with floral decorations and ribbon arrangements.',5000.00,'per event','sd','/uploads/services/stage.png',4.5,'car-entry',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(70,8,NULL,'Luxury Cars Entry','luxury-cars-entry','Premium luxury car entry package with high-end vehicles decorated for your grand wedding arrival.','Premium luxury car entry package with high-end vehicles decorated for your grand wedding arrival.',15000.00,'per event','sd','/uploads/services/stage.png',4.5,'car-entry',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(71,18,NULL,'Traditional Aarthi Plate','traditional-aarthi-plate','Beautifully decorated traditional aarthi plate with diyas, flowers and accessories for wedding and religious ceremonies.','Beautifully decorated traditional aarthi plate with diyas, flowers and accessories for wedding and religious ceremonies.',1500.00,'per event','sd','/uploads/services/stage.png',4.5,'aarthi-plate',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(72,14,NULL,'Bridal Makeup & Styling','bridal-makeup-styling','Complete bridal makeup with HD and airbrush techniques, hair styling, saree draping and jewellery coordination for your big day.','Complete bridal makeup with HD and airbrush techniques, hair styling, saree draping and jewellery coordination for your big day.',12000.00,'per event','sd','/uploads/services/bridal.png',4.5,'bridal-groom-styling',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(73,14,NULL,'Mehanti Bridal','mehanti-bridal','Full bridal Mehndi with intricate traditional patterns from renowned artists. Includes detailed design on both hands and feet.','Full bridal Mehndi with intricate traditional patterns from renowned artists. Includes detailed design on both hands and feet.',8000.00,'per event','sd','/uploads/services/mehandi.png',4.5,'bridal-groom-styling',NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(74,2,NULL,'ELLCY Photography Package','ellcy-photography-package','Our complete wedding photography package captures every precious moment of your celebration — from the first look to the last dance. Includes full-day coverage by a professional ph','Our complete wedding photography package captures every precious moment of your celebration — from the first look to the last dance. Includes full-day coverage by a professional photographer, 300+ edited high-resolution photos, a private online gallery, and a premium printed album delivered within 30 days.',80000.00,'per event','sd','/uploads/services/photo.png',4.5,NULL,NULL,NULL,NULL,0,0,'active','2026-07-16 17:26:30','2026-07-16 17:26:30'),(75,5,NULL,'Ellcy - one time','ellcy-one-time','','test',34000.00,'','sd','',2.1,'','Available All Year','','',0,0,'draft','2026-07-21 21:04:00','2026-07-21 21:05:50');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_val` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES (1,'site_name','ELLCY','2026-07-16 11:56:29'),(2,'site_tagline','Chennais Premier Event Services Platform','2026-07-21 15:40:13'),(3,'contact_phone','+91 123-456-789','2026-07-16 11:56:29'),(4,'contact_email','info@ellcy.in','2026-07-16 11:56:29'),(5,'contact_address','Chennai, Tamil Nadu','2026-07-16 11:56:29'),(6,'currency_symbol','₹','2026-07-16 11:56:29'),(7,'maintenance','0','2026-07-21 15:40:25');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('user','admin','superadmin') DEFAULT 'user',
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'heyitsbright','heyitsbright@ellcy.in',NULL,'$2y$12$C6BhUDXmEa4NJEJZlgPt2uf0mqAgtBGlHOOTCKKONyQZ5uysDpY.6','superadmin','active','2026-08-02 21:25:10','2026-07-16 17:26:55','2026-08-02 21:25:10'),(5,'heyitsbright','ceo@abblabs.io','+917305792178','$2y$12$YRfbrjYcPYf7oxgARfFtZ.pQlYA0LwywschKXFf8K/uJ.RRXQ2o4W','user','active',NULL,'2026-07-28 23:58:30','2026-07-28 23:58:30'),(6,'test','test@elly.in','+917305792178','$2y$12$/rUIC1l3xxd0bgrjl.bKLOB5MBL6Rac6LHte/PMfPNroerw.MKRgW','user','active',NULL,'2026-07-29 09:53:45','2026-07-29 09:53:45'),(7,'velan','velanstartup.30@gmail.com','+919361011717','$2y$12$lIoGWdep.8Db6WUMFUpXq.Qdts9ff1LwVSF9Ww90UTBvNC8xSNdza','user','active',NULL,'2026-07-29 19:38:57','2026-07-29 19:38:57');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'ellcy_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-04 16:28:33
