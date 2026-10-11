-- ============================================================
-- Job2You — Schéma de la base de données
-- MySQL 8.4+
-- ============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;

-- ============================================================
-- TABLE: users
-- ============================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(255)    NOT NULL,
  `email`             VARCHAR(255)    NOT NULL,
  `phone`             VARCHAR(15)     DEFAULT NULL,
  `photo_path`        VARCHAR(255)    DEFAULT NULL,
  `role`              ENUM('candidate','admin','recruiter') NOT NULL DEFAULT 'candidate',
  `status`            ENUM('pending','active','inactive','suspended') NOT NULL DEFAULT 'pending',
  `plan`              VARCHAR(20)     NOT NULL DEFAULT 'free',
  `sms_quota`         INT             NOT NULL DEFAULT 2,
  `sms_sent`          INT             NOT NULL DEFAULT 0,
  `email_verified_at` TIMESTAMP       NULL DEFAULT NULL,
  `password`          VARCHAR(255)    NOT NULL,
  `remember_token`    VARCHAR(100)    DEFAULT NULL,
  `created_at`        TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`        TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: candidate_profile
-- ============================================================
DROP TABLE IF EXISTS `candidate_profile`;
CREATE TABLE `candidate_profile` (
  `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`               BIGINT UNSIGNED NOT NULL,
  `desired_jobs`          VARCHAR(50)     DEFAULT NULL,
  `location`              VARCHAR(150)    DEFAULT NULL,
  `city`                  VARCHAR(150)    DEFAULT NULL,
  `region`                VARCHAR(150)    DEFAULT NULL,
  `mobility`              VARCHAR(50)     NOT NULL DEFAULT 'local',
  `latitude`              DECIMAL(10,7)   DEFAULT NULL,
  `longitude`             DECIMAL(10,7)   DEFAULT NULL,
  `experience_level`      ENUM('junior','mid','senior') NOT NULL DEFAULT 'junior',
  `education_level`       VARCHAR(100)    DEFAULT NULL,
  `contract_preferences`  VARCHAR(255)    DEFAULT NULL,
  `contract_type`         VARCHAR(50)     DEFAULT NULL,
  `desired_salary`        INT UNSIGNED    DEFAULT NULL,
  `sectors`               JSON            DEFAULT NULL,
  `alerts_enabled`        TINYINT(1)      NOT NULL DEFAULT 0,
  `profile_source`        VARCHAR(100)    DEFAULT NULL,
  `created_at`            TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`            TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `candidate_profile_user_id_unique` (`user_id`),
  CONSTRAINT `candidate_profile_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: skills
-- ============================================================
DROP TABLE IF EXISTS `skills`;
CREATE TABLE `skills` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(100)    NOT NULL,
  `normalized_name`  VARCHAR(100)    NOT NULL,
  `created_at`       TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`       TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `skills_normalized_name_unique` (`normalized_name`),
  KEY `idx_normalized` (`normalized_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: candidate_skill
-- ============================================================
DROP TABLE IF EXISTS `candidate_skill`;
CREATE TABLE `candidate_skill` (
  `candidate_profile_id` BIGINT UNSIGNED NOT NULL,
  `skill_id`             BIGINT UNSIGNED NOT NULL,
  `level`                TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `weight`               DECIMAL(3,2)    NOT NULL DEFAULT 1.00,
  PRIMARY KEY (`candidate_profile_id`, `skill_id`),
  KEY `candidate_skill_skill_id_foreign` (`skill_id`),
  CONSTRAINT `candidate_skill_candidate_profile_id_foreign`
    FOREIGN KEY (`candidate_profile_id`) REFERENCES `candidate_profile` (`id`) ON DELETE CASCADE,
  CONSTRAINT `candidate_skill_skill_id_foreign`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: sources
-- ============================================================
DROP TABLE IF EXISTS `sources`;
CREATE TABLE `sources` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(100)    NOT NULL,
  `base_url`      VARCHAR(255)    DEFAULT NULL,
  `collector_key` VARCHAR(100)    NOT NULL,
  `last_run_at`   TIMESTAMP       NULL DEFAULT NULL,
  `created_at`    TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`    TIMESTAMP       NULL DEFAULT NULL,
  `active`        TINYINT(1)      NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sources_collector_key_unique` (`collector_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: offers
-- ============================================================
DROP TABLE IF EXISTS `offers`;
CREATE TABLE `offers` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `source_id`     BIGINT UNSIGNED NOT NULL,
  `external_id`   VARCHAR(100)    NOT NULL,
  `title`         VARCHAR(200)    NOT NULL,
  `company`       VARCHAR(150)    DEFAULT NULL,
  `location`      VARCHAR(150)    DEFAULT NULL,
  `description`   TEXT            DEFAULT NULL,
  `contract_type` VARCHAR(50)     DEFAULT NULL,
  `source_url`    VARCHAR(500)    DEFAULT NULL,
  `published_at`  DATETIME        DEFAULT NULL,
  `expires_at`    DATETIME        DEFAULT NULL,
  `fingerprint`   VARCHAR(64)     DEFAULT NULL,
  `status`        ENUM('active','expired','archived') NOT NULL DEFAULT 'active',
  `created_at`    TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`    TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_source_external` (`source_id`, `external_id`),
  KEY `idx_fingerprint` (`fingerprint`),
  KEY `idx_status` (`status`),
  CONSTRAINT `offers_source_id_foreign`
    FOREIGN KEY (`source_id`) REFERENCES `sources` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: offer_skills
-- ============================================================
DROP TABLE IF EXISTS `offer_skills`;
CREATE TABLE `offer_skills` (
  `offer_id`  BIGINT UNSIGNED NOT NULL,
  `skill_id`  BIGINT UNSIGNED NOT NULL,
  `required`  TINYINT(1)      NOT NULL DEFAULT 1,
  `weight`    DECIMAL(3,2)    NOT NULL DEFAULT 1.00,
  PRIMARY KEY (`offer_id`, `skill_id`),
  KEY `offer_skills_skill_id_foreign` (`skill_id`),
  CONSTRAINT `offer_skills_offer_id_foreign`
    FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `offer_skills_skill_id_foreign`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: matches
-- ============================================================
DROP TABLE IF EXISTS `matches`;
CREATE TABLE `matches` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`      BIGINT UNSIGNED NOT NULL,
  `offer_id`     BIGINT UNSIGNED NOT NULL,
  `score`        DECIMAL(4,2)    NOT NULL,
  `details_json` JSON            DEFAULT NULL,
  `created_at`   TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`   TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_offer` (`user_id`, `offer_id`),
  KEY `matches_offer_id_foreign` (`offer_id`),
  KEY `idx_score` (`score`),
  CONSTRAINT `matches_offer_id_foreign`
    FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matches_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: plans
-- ============================================================
DROP TABLE IF EXISTS `plans`;
CREATE TABLE `plans` (
  `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`               VARCHAR(100)    NOT NULL,
  `price`              DECIMAL(10,2)   NOT NULL,
  `duration_days`      INT             NOT NULL,
  `sms_quota`          INT             NOT NULL DEFAULT 0,
  `cv_parsing_enabled` TINYINT(1)      NOT NULL DEFAULT 0,
  `active`             TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`         TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`         TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: subscriptions
-- ============================================================
DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE `subscriptions` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`       BIGINT UNSIGNED NOT NULL,
  `plan_id`       BIGINT UNSIGNED NOT NULL,
  `starts_at`     DATETIME        NOT NULL,
  `ends_at`       DATETIME        DEFAULT NULL,
  `sms_remaining` INT             NOT NULL DEFAULT 0,
  `status`        ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at`    TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`    TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_plan_id_foreign` (`plan_id`),
  KEY `idx_user_status` (`user_id`, `status`),
  CONSTRAINT `subscriptions_plan_id_foreign`
    FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subscriptions_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: payments
-- ============================================================
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`            BIGINT UNSIGNED NOT NULL,
  `plan_id`            BIGINT UNSIGNED NOT NULL,
  `provider`           VARCHAR(50)     NOT NULL,
  `provider_reference` VARCHAR(50)     NOT NULL,
  `amount`             DECIMAL(10,2)   NOT NULL,
  `status`             ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `payload_reference`  TEXT            DEFAULT NULL,
  `created_at`         TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`         TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_provider_reference_unique` (`provider_reference`),
  KEY `payments_user_id_foreign` (`user_id`),
  KEY `payments_plan_id_foreign` (`plan_id`),
  CONSTRAINT `payments_plan_id_foreign`
    FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: sms_logs
-- ============================================================
DROP TABLE IF EXISTS `sms_logs`;
CREATE TABLE `sms_logs` (
  `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`            BIGINT UNSIGNED NOT NULL,
  `offer_id`           BIGINT UNSIGNED NOT NULL,
  `subscription_id`    BIGINT UNSIGNED NOT NULL,
  `status`             ENUM('pending','sent','failed') NOT NULL DEFAULT 'pending',
  `provider_reference` VARCHAR(150)    DEFAULT NULL,
  `sent_at`            TIMESTAMP       NULL DEFAULT NULL,
  `created_at`         TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`         TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sms_logs_user_id_foreign` (`user_id`),
  KEY `sms_logs_offer_id_foreign` (`offer_id`),
  KEY `sms_logs_subscription_id_foreign` (`subscription_id`),
  CONSTRAINT `sms_logs_offer_id_foreign`
    FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sms_logs_subscription_id_foreign`
    FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sms_logs_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: cv_parses
-- ============================================================
DROP TABLE IF EXISTS `cv_parses`;
CREATE TABLE `cv_parses` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`        BIGINT UNSIGNED NOT NULL,
  `file_path`      VARCHAR(500)    NOT NULL,
  `status`         ENUM('pending','processing','done','failed') NOT NULL DEFAULT 'pending',
  `extracted_json` JSON            DEFAULT NULL,
  `validated_at`   TIMESTAMP       NULL DEFAULT NULL,
  `created_at`     TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`     TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cv_parses_user_id_foreign` (`user_id`),
  CONSTRAINT `cv_parses_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: experiences
-- ============================================================
DROP TABLE IF EXISTS `experiences`;
CREATE TABLE `experiences` (
  `id`                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `candidate_profile_id` BIGINT UNSIGNED NOT NULL,
  `company`              VARCHAR(150)    NOT NULL,
  `position`             VARCHAR(150)    NOT NULL,
  `start_date`           DATE            NOT NULL,
  `end_date`             DATE            DEFAULT NULL,
  `description`          TEXT            DEFAULT NULL,
  `created_at`           TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`           TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `experiences_candidate_profile_id_foreign` (`candidate_profile_id`),
  CONSTRAINT `experiences_candidate_profile_id_foreign`
    FOREIGN KEY (`candidate_profile_id`) REFERENCES `candidate_profile` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: educations
-- ============================================================
DROP TABLE IF EXISTS `educations`;
CREATE TABLE `educations` (
  `id`                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `candidate_profile_id` BIGINT UNSIGNED NOT NULL,
  `school`               VARCHAR(150)    NOT NULL,
  `degree`               VARCHAR(150)    NOT NULL,
  `field`                VARCHAR(150)    DEFAULT NULL,
  `start_date`           DATE            NOT NULL,
  `end_date`             DATE            DEFAULT NULL,
  `created_at`           TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`           TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `educations_candidate_profile_id_foreign` (`candidate_profile_id`),
  CONSTRAINT `educations_candidate_profile_id_foreign`
    FOREIGN KEY (`candidate_profile_id`) REFERENCES `candidate_profile` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Restauration des checks
-- ============================================================
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;
