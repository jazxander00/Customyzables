
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
DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `actor_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_actor_id_foreign` (`actor_id`),
  KEY `audit_logs_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  CONSTRAINT `audit_logs_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `donation_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `donation_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `donation_id` bigint(20) unsigned NOT NULL,
  `actor_id` bigint(20) unsigned DEFAULT NULL,
  `from_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `donation_events_donation_id_foreign` (`donation_id`),
  KEY `donation_events_actor_id_foreign` (`actor_id`),
  CONSTRAINT `donation_events_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `donation_events_donation_id_foreign` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `donation_events` WRITE;
/*!40000 ALTER TABLE `donation_events` DISABLE KEYS */;
INSERT INTO `donation_events` VALUES (1,1,2,NULL,'listed','Donation published to the feed','2026-10-18 00:55:34','2026-10-20 00:55:34'),(2,2,6,NULL,'listed','Donation published to the feed','2026-10-16 00:55:34','2026-10-20 00:55:34'),(3,3,2,NULL,'listed','Donation published to the feed','2026-10-19 00:55:34','2026-10-20 00:55:34'),(4,4,7,NULL,'listed','Donation published to the feed','2026-10-19 00:55:34','2026-10-20 00:55:34'),(5,5,6,NULL,'listed','Donation published to the feed','2026-09-30 00:55:34','2026-10-20 00:55:34'),(6,6,7,NULL,'listed','Donation published to the feed','2026-10-17 00:55:34','2026-10-20 00:55:34'),(7,7,2,NULL,'listed','Donation published to the feed','2026-10-15 00:55:34','2026-10-20 00:55:34'),(8,8,6,NULL,'listed','Donation published to the feed','2026-10-11 00:55:34','2026-10-20 00:55:34'),(9,9,7,NULL,'listed','Donation published to the feed','2026-10-06 00:55:34','2026-10-20 00:55:34'),(10,10,2,NULL,'listed','Donation published to the feed','2026-10-14 00:55:34','2026-10-20 00:55:34');
/*!40000 ALTER TABLE `donation_events` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `donation_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `donation_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `donation_id` bigint(20) unsigned NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `donation_images_donation_id_foreign` (`donation_id`),
  CONSTRAINT `donation_images_donation_id_foreign` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `donation_images` WRITE;
/*!40000 ALTER TABLE `donation_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `donation_images` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `donation_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `donation_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `donation_id` bigint(20) unsigned NOT NULL,
  `upcycler_id` bigint(20) unsigned NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `scheduled_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `donation_requests_donation_id_upcycler_id_unique` (`donation_id`,`upcycler_id`),
  KEY `donation_requests_upcycler_id_foreign` (`upcycler_id`),
  KEY `donation_requests_status_index` (`status`),
  CONSTRAINT `donation_requests_donation_id_foreign` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `donation_requests_upcycler_id_foreign` FOREIGN KEY (`upcycler_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `donation_requests` WRITE;
/*!40000 ALTER TABLE `donation_requests` DISABLE KEYS */;
INSERT INTO `donation_requests` VALUES (1,1,3,'We would cut these into 15 tote bags for a school order in Basak. I can collect this weekend — I have a tricycle.','pending',NULL,NULL,NULL,NULL,'2026-10-19 00:55:34','2026-10-20 00:55:34'),(2,1,4,'Interested in the jackets only, for a patchwork throw for the parish hall. Happy to take the jeans too if nobody else wants them.','pending',NULL,NULL,NULL,NULL,'2026-10-19 04:55:34','2026-10-20 00:55:34'),(3,2,3,'Perfect for our rag-weaving batch next week. We can pick up from the Gun-ob drop box.','accepted',NULL,NULL,'2026-10-17 00:55:34',NULL,'2026-10-16 00:55:34','2026-10-20 00:55:34'),(4,8,3,'Need this for cushion covers — we have a cafe client waiting. Can collect Thursday morning.','accepted','2026-10-22 01:30:00','Meet at the barangay hall parking area','2026-10-12 00:55:34',NULL,'2026-10-11 00:55:34','2026-10-20 00:55:34'),(5,9,3,'We will sort these into patchwork panels. Collected already — thank you!','completed',NULL,NULL,'2026-10-07 00:55:34','2026-10-08 00:55:34','2026-10-06 00:55:34','2026-10-20 00:55:34');
/*!40000 ALTER TABLE `donation_requests` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `donations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `donations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `weight_kg` decimal(6,2) NOT NULL DEFAULT 0.00,
  `item_count` int(10) unsigned NOT NULL DEFAULT 1,
  `barangay` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `handoff_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pickup',
  `pickup_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `available_from` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'listed',
  `views` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `donations_user_id_foreign` (`user_id`),
  KEY `donations_status_category_barangay_index` (`status`,`category`,`barangay`),
  KEY `donations_category_index` (`category`),
  KEY `donations_barangay_index` (`barangay`),
  KEY `donations_status_index` (`status`),
  CONSTRAINT `donations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `donations` WRITE;
/*!40000 ALTER TABLE `donations` DISABLE KEYS */;
INSERT INTO `donations` VALUES (1,2,'Pre-loved denim jackets & jeans (5 pcs)','Heavy 12–14oz cotton, no stretch, washed twice. Three jackets and two pairs of jeans with blown knees — the denim itself is still strong.\n\nGreat for tote bags, apron pockets, or patchwork panels. One jacket has a small paint spot on the sleeve.','denim','gently_used',4.80,5,'Mactan','pickup','Purok Kalubihan, near Mactan Elementary School gate',NULL,'requested',0,'2026-10-18 00:55:34','2026-10-18 00:55:34'),(2,6,'Clean cotton T-shirts & scraps (12 pcs)','Assorted plain tones from a hotel laundry run — white, sand, olive. Zero graphic prints, washed thoroughly. A few have thin collars; the body fabric is fine. Ideal for rag weaving or braided coasters.','cotton','wearable',3.20,12,'Gun-ob','dropoff',NULL,NULL,'accepted',0,'2026-10-16 00:55:34','2026-10-16 00:55:34'),(3,2,'Natural sand linen remnants','Leftover from a local coastal cafe curtain tailoring job. High-grade unbleached linen with minor water spots, easily cleaned.\n\nAbout 6 running metres in irregular cuts — enough for napkins, bread bags, or a summer waistcoat.','linen','flawed',2.40,8,'Marigondon','pickup','Marigondon Heights, block 4 lot 12',NULL,'listed',0,'2026-10-19 00:55:34','2026-10-19 00:55:34'),(4,7,'White & khaki twill fabric bundles','Resort staff uniform surplus. Heavy-duty poly-cotton twill, colour-fast and tear-resistant after many washes. Ideal for structural book bags, tool rolls, and outdoor cushion covers.','blend','gently_used',6.00,14,'Punta Engaño','dropoff',NULL,NULL,'listed',0,'2026-10-19 00:55:34','2026-10-19 00:55:34'),(5,6,'Hotel bed linen offcuts (white)','300-thread-count cotton percale offcuts from a linen replacement cycle. Clean, professionally laundered, some light grey marks near the hem. Roughly 40 pieces between A4 and A2 size.','cotton','gently_used',9.50,40,'Mactan','pickup','Mactan resort service yard, ask for housekeeping',NULL,'completed',0,'2026-09-30 00:55:34','2026-09-30 00:55:34'),(6,7,'Knitwear pile — sweaters & cardigans','Wool-blend knits with moth nibbles on two pieces. Felted after a hot wash, which makes them perfect for slippers, mittens, and patchwork throws. No synthetic fibre labels left, so treat as mixed wool.','knit','flawed',5.40,9,'Punta Engaño','dropoff',NULL,NULL,'listed',0,'2026-10-17 00:55:34','2026-10-17 00:55:34'),(7,2,'School uniform scraps — navy & white','Offcuts from a tailoring shop that handles school uniforms. Poly-cotton, mostly navy with white piping. Small pieces, best for patchwork or appliqué.','blend','scrap',1.80,25,'Basak','dropoff',NULL,NULL,'listed',0,'2026-10-15 00:55:34','2026-10-15 00:55:34'),(8,6,'Curtain panels — heavy cotton canvas','Four full-length panels removed during a renovation. Sun-faded on one side, structurally sound. Excellent weight for market bags, cushion covers, or a sturdy apron.','linen','wearable',7.20,4,'Gun-ob','pickup','Gun-ob, near the parish multi-hall',NULL,'scheduled',0,'2026-10-11 00:55:34','2026-10-11 00:55:34'),(9,7,'Denim offcuts from a tailoring shop','Legs, waistbands, and pocket bags from a month of alterations. Indigo and black mix. Roughly two sacks — bring a car if you can.','denim','scrap',8.40,60,'Poblacion','pickup','Poblacion tailoring row, beside the public market',NULL,'received',0,'2026-10-06 00:55:34','2026-10-06 00:55:34'),(10,2,'Baby clothes bundle (0–2 years)','Soft cotton onesies and sleepsuits, washed in fragrance-free detergent. Two have small stains at the collar. Good for quilting or doll clothes.','cotton','gently_used',2.10,18,'Mactan','dropoff',NULL,NULL,'listed',0,'2026-10-14 00:55:34','2026-10-14 00:55:34');
/*!40000 ALTER TABLE `donations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `donation_id` bigint(20) unsigned NOT NULL,
  `request_id` bigint(20) unsigned DEFAULT NULL,
  `sender_id` bigint(20) unsigned NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_request_id_foreign` (`request_id`),
  KEY `messages_sender_id_foreign` (`sender_id`),
  KEY `messages_donation_id_created_at_index` (`donation_id`,`created_at`),
  CONSTRAINT `messages_donation_id_foreign` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `donation_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,2,3,6,'Hi Johnriel — approved. The box is at the Gun-ob drop box, labelled with your name.',NULL,'2026-10-17 00:55:34','2026-10-20 00:55:34'),(2,2,3,3,'Salamat! I will collect tomorrow morning and send photos once the first batch is woven.',NULL,'2026-10-17 02:55:34','2026-10-20 00:55:34');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2024_01_01_000100_create_donations_table',1),(6,'2024_01_01_000200_create_donation_images_table',1),(7,'2024_01_01_000300_create_donation_requests_table',1),(8,'2024_01_01_000400_create_messages_table',1),(9,'2024_01_01_000500_create_donation_events_table',1),(10,'2024_01_01_000600_create_audit_logs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'donor',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verified_at` timestamp NULL DEFAULT NULL,
  `is_suspended` tinyint(1) NOT NULL DEFAULT 0,
  `barangay` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organization` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Customyzables Admin','admin@customyzables.test',NULL,'$2y$10$8eJzMdybT5R0R7jdB2/11eHoQ2f.JVWPhwds1poHuQy.L17W419IW','admin',1,'2026-10-20 00:55:34',0,'Poblacion',NULL,NULL,NULL,NULL,NULL,'2026-10-20 00:55:34','2026-10-20 00:55:34'),(2,'Elaine Ena B. Villaluna','elaine@customyzables.test',NULL,'$2y$10$0HnBBhOQdz5uGLhBeSeXyuF4AgxGTPi0LBoayw6meHvVhZdNsZkFO','donor',0,NULL,0,'Mactan','0917 000 0001','Villaluna Residence','Clearing out a decade of denim. Happy to hand it to someone who will actually use it.',NULL,NULL,'2026-10-20 00:55:34','2026-10-20 00:55:34'),(3,'Johnriel Dave L. Legara','johnriel@customyzables.test',NULL,'$2y$10$9u2efrl.oWiHqGx3erWF7uewZXFhv28L7kO054ZTMF4GZRRy2bBUC','upcycler',1,'2026-10-20 00:55:34',0,'Basak','0917 000 0002','Basak Upcycling Co-op','We turn hotel linen and denim into school bags and floor rugs.',NULL,NULL,'2026-10-20 00:55:34','2026-10-20 00:55:34'),(4,'Kuya Ben','ben@customyzables.test',NULL,'$2y$10$mKPJl8VAtnRDSx2daG8VNen.QAvoJXmrr31IO5bz2QI1WU0t.Ykci','upcycler',1,'2026-10-20 00:55:34',0,'Gun-ob',NULL,'Gun-ob Parish Craft Circle','Parish volunteers making rags, rugs, and tote bags.',NULL,NULL,'2026-10-20 00:55:34','2026-10-20 00:55:34'),(5,'Marisol Tan','marisol@customyzables.test',NULL,'$2y$10$Aj6q2Lhc3AKRWe9hZaxG7.y.AC5pZwtxguvtWkzsZbq2noE5nnMPq','upcycler',0,NULL,0,'Marigondon',NULL,'Marigondon Crafters','Handwoven coasters and patchwork aprons.',NULL,NULL,'2026-10-20 00:55:34','2026-10-20 00:55:34'),(6,'Maria S. Dela Cruz','maria@customyzables.test',NULL,'$2y$10$7A95UsLfjuuoAt3S5QrUNua4F08zmiQR4NdHs1s6QiSQGlLHkAieG','donor',0,NULL,0,'Mactan',NULL,NULL,'Hotel housekeeping supervisor. We cycle uniforms twice a year.',NULL,NULL,'2026-10-20 00:55:34','2026-10-20 00:55:34'),(7,'Kyra Bautista','kyra@customyzables.test',NULL,'$2y$10$KKbUE8McIjWuLuEygA0s1.eMA3sLFPXXy6bLLpEXvhXow2TJrg52O','donor',0,NULL,0,'Punta Engaño',NULL,'Punta Engaño Resort Hub',NULL,NULL,NULL,'2026-10-20 00:55:34','2026-10-20 00:55:34');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

