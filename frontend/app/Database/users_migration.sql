-- TilDamu.kz: Users table for authentication
-- Run this AFTER the main schema.sql

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('patient','parent','therapist','admin','researcher','developer') NOT NULL DEFAULT 'patient',
  `child_id` int(11) DEFAULT NULL COMMENT 'For patient/client role — linked child',
  `therapist_id` int(11) DEFAULT NULL COMMENT 'For therapist role — linked therapist profile',
  `avatar_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_child_id` (`child_id`),
  KEY `idx_users_therapist_id` (`therapist_id`),
  CONSTRAINT `fk_users_child` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_users_therapist` FOREIGN KEY (`therapist_id`) REFERENCES `therapists` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
