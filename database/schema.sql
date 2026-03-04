-- ============================================================
-- PATS — NTS Clone Database Schema
-- Run against: pats (MySQL 8)
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ── Users & Roles ─────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cnic`          VARCHAR(15) NOT NULL UNIQUE COMMENT '13-digit CNIC without dashes',
    `name`          VARCHAR(120) NOT NULL,
    `email`         VARCHAR(150) NOT NULL UNIQUE,
    `phone`         VARCHAR(15) NOT NULL,
    `password`      VARCHAR(255) NOT NULL,
    `role`          ENUM('super_admin','admin','data_entry','candidate') NOT NULL DEFAULT 'candidate',
    `is_verified`   TINYINT(1) NOT NULL DEFAULT 0,
    `otp`           VARCHAR(6) NULL,
    `otp_expires_at` DATETIME NULL,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `candidates` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`      INT UNSIGNED NOT NULL UNIQUE,
    `dob`          DATE NULL,
    `gender`       ENUM('male','female','other') NULL,
    `religion`     VARCHAR(30) NULL,
    `domicile`     VARCHAR(60) NULL,
    `province`     VARCHAR(60) NULL,
    `address`      TEXT NULL,
    `photo_path`   VARCHAR(255) NULL,
    `cnic_copy_path` VARCHAR(255) NULL,
    `profile_locked` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `education_history` (
    `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `candidate_id`     INT UNSIGNED NOT NULL,
    `degree`           VARCHAR(80) NOT NULL COMMENT 'e.g. Bachelor, Master, Matric',
    `subject`          VARCHAR(100) NULL,
    `institution`      VARCHAR(150) NULL,
    `passing_year`     YEAR NULL,
    `grade`            VARCHAR(20) NULL COMMENT 'e.g. A, 1st Division, 3.8 CGPA',
    `certificate_path` VARCHAR(255) NULL,
    `created_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`candidate_id`) REFERENCES `candidates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Projects & Jobs ───────────────────────────────────────

CREATE TABLE IF NOT EXISTS `projects` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`           VARCHAR(200) NOT NULL,
    `org_name`       VARCHAR(200) NOT NULL COMMENT 'Advertising organization name',
    `logo_path`      VARCHAR(255) NULL,
    `description`    TEXT NULL COMMENT 'Job advertisement / notification text',
    `open_date`      DATE NULL COMMENT 'Application open date',
    `close_date`     DATE NULL COMMENT 'Application close date',
    `test_date`      DATE NULL COMMENT 'Physical test / exam date',
    `status`         ENUM('draft','open','closed','result_declared') NOT NULL DEFAULT 'draft',
    `created_by`     INT UNSIGNED NOT NULL,
    `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs` (
    `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_id`        INT UNSIGNED NOT NULL,
    `title`             VARCHAR(150) NOT NULL,
    `department`        VARCHAR(120) NULL,
    `bps_grade`         VARCHAR(20) NULL COMMENT 'e.g. BPS-17, BPS-14',
    `total_seats`       SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    `min_qualification` VARCHAR(100) NULL,
    `age_min`           TINYINT UNSIGNED NULL,
    `age_max`           TINYINT UNSIGNED NULL,
    `domicile_required` VARCHAR(60) NULL COMMENT 'Province/district required or NULL for open',
    `fee`               DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Test Centers & Slots ──────────────────────────────────

CREATE TABLE IF NOT EXISTS `test_centers` (
    `id`        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`      VARCHAR(150) NOT NULL,
    `city`      VARCHAR(80) NOT NULL,
    `province`  VARCHAR(80) NOT NULL,
    `address`   TEXT NULL,
    `map_url`   VARCHAR(500) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `project_centers` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT UNSIGNED NOT NULL,
    `center_id`  INT UNSIGNED NOT NULL,
    UNIQUE KEY `uq_project_center` (`project_id`, `center_id`),
    FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`center_id`)  REFERENCES `test_centers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `center_slots` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_id`   INT UNSIGNED NOT NULL,
    `center_id`    INT UNSIGNED NOT NULL,
    `slot_date`    DATE NOT NULL,
    `slot_time`    TIME NOT NULL COMMENT 'e.g. 09:00:00',
    `total_seats`  SMALLINT UNSIGNED NOT NULL DEFAULT 50,
    `booked_seats` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`center_id`)  REFERENCES `test_centers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Applications & Payments ───────────────────────────────

CREATE TABLE IF NOT EXISTS `applications` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `candidate_id` INT UNSIGNED NOT NULL,
    `job_id`       INT UNSIGNED NOT NULL,
    `slot_id`      INT UNSIGNED NOT NULL,
    `challan_ref`  VARCHAR(30) NULL UNIQUE COMMENT 'Unique challan reference number',
    `status`       ENUM('submitted','fee_paid','scheduled','result_declared') NOT NULL DEFAULT 'submitted',
    `applied_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_candidate_job` (`candidate_id`, `job_id`) COMMENT 'One application per job per candidate',
    FOREIGN KEY (`candidate_id`) REFERENCES `candidates`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`job_id`)       REFERENCES `jobs`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`slot_id`)      REFERENCES `center_slots`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `payments` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `application_id` INT UNSIGNED NOT NULL UNIQUE,
    `amount`         DECIMAL(8,2) NOT NULL,
    `method`         ENUM('challan','online') NOT NULL DEFAULT 'challan',
    `transaction_id` VARCHAR(100) NULL COMMENT 'Bank ref or online TXN ID',
    `bank_name`      VARCHAR(80) NULL,
    `deposit_date`   DATE NULL,
    `verified_by`    INT UNSIGNED NULL,
    `verified_at`    DATETIME NULL,
    `status`         ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending',
    `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`application_id`) REFERENCES `applications`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`verified_by`)    REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Roll Numbers & Results ────────────────────────────────

CREATE TABLE IF NOT EXISTS `roll_numbers` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `application_id` INT UNSIGNED NOT NULL UNIQUE,
    `roll_number`    VARCHAR(30) NOT NULL UNIQUE,
    `assigned_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`application_id`) REFERENCES `applications`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `results` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `application_id` INT UNSIGNED NOT NULL UNIQUE,
    `roll_number`    VARCHAR(30) NOT NULL,
    `score`          DECIMAL(6,2) NULL,
    `percentage`     DECIMAL(5,2) NULL,
    `status`         ENUM('pass','fail','absent','withheld') NOT NULL,
    `rank`           INT UNSIGNED NULL,
    `remarks`        VARCHAR(255) NULL,
    `uploaded_by`    INT UNSIGNED NULL,
    `uploaded_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`application_id`) REFERENCES `applications`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`uploaded_by`)    REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── SMS Logs & Queue ──────────────────────────────────────

CREATE TABLE IF NOT EXISTS `sms_log` (
    `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `recipient`         VARCHAR(15) NOT NULL,
    `message`           TEXT NOT NULL,
    `event_type`        VARCHAR(50) NULL COMMENT 'e.g. registration_otp, payment_confirmed',
    `status`            ENUM('sent','failed') NOT NULL DEFAULT 'sent',
    `provider_response` TEXT NULL,
    `sent_at`           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sms_queue` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_id`   INT UNSIGNED NULL,
    `recipient`    VARCHAR(15) NOT NULL,
    `message`      TEXT NOT NULL,
    `status`       ENUM('pending','sent','failed') NOT NULL DEFAULT 'pending',
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── General ───────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `announcements` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title`      VARCHAR(200) NOT NULL,
    `body`       TEXT NOT NULL,
    `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `audit_log` (
    `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`     INT UNSIGNED NULL,
    `action`      VARCHAR(50) NOT NULL COMMENT 'e.g. create, update, delete',
    `table_name`  VARCHAR(60) NOT NULL,
    `record_id`   INT UNSIGNED NULL,
    `old_val`     JSON NULL,
    `new_val`     JSON NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
