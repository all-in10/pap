DROP DATABASE IF EXISTS `teamcore`;
CREATE DATABASE `teamcore` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `teamcore`;

-- ============================================
-- 1. COUNTRIES TABLE
-- ============================================
CREATE TABLE `countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL UNIQUE,
  `code` varchar(2) NOT NULL UNIQUE,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. STATES TABLE
-- ============================================
CREATE TABLE `states` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `country_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(5) NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  UNIQUE KEY `unique_state_per_country` (`country_id`, `code`),
  CONSTRAINT `fk_states_country_id` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. CITIES TABLE
-- ============================================
CREATE TABLE `cities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `state_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  KEY `idx_cities_state_id` (`state_id`),
  CONSTRAINT `fk_cities_state_id` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. DESIGNATIONS TABLE
-- ============================================
CREATE TABLE `designations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL UNIQUE,
  `base_salary` decimal(10, 2) NOT NULL,
  `level` enum('junior','senior','manager','director') NOT NULL DEFAULT 'junior',
  `description` text NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  KEY `idx_designations_level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. DEPARTMENTS TABLE
-- ============================================
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL UNIQUE,
  `description` text NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  KEY `idx_departments_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. EMPLOYEES TABLE
-- ============================================
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `phone` varchar(20) NULL,
  `date_of_birth` date NULL,
  `gender` enum('male','female','other') NULL,
  `national_id` varchar(20) NULL UNIQUE,
  `address` varchar(255) NULL,
  `city_id` bigint unsigned NULL,
  `state_id` bigint unsigned NULL,
  `country_id` bigint unsigned NULL,
  `postal_code` varchar(20) NULL,
  `department_id` bigint unsigned NOT NULL,
  `designation_id` bigint unsigned NOT NULL,
  `joining_date` date NOT NULL,
  `employment_status` enum('active','inactive','suspended','terminated') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  `deleted_at` timestamp NULL,
  KEY `idx_employees_deleted_at` (`deleted_at`),
  KEY `idx_employees_email` (`email`),
  KEY `idx_employees_department_id` (`department_id`),
  KEY `idx_employees_designation_id` (`designation_id`),
  KEY `idx_employees_city_id` (`city_id`),
  KEY `idx_employees_state_id` (`state_id`),
  KEY `idx_employees_country_id` (`country_id`),
  KEY `idx_employees_employment_status` (`employment_status`),
  KEY `idx_employees_joining_date` (`joining_date`),
  CONSTRAINT `fk_employees_department_id` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `fk_employees_designation_id` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`),
  CONSTRAINT `fk_employees_city_id` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_employees_state_id` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_employees_country_id` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. CONTRACT TYPES TABLE
-- ============================================
CREATE TABLE `contract_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL UNIQUE,
  `description` text NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. CONTRACTS TABLE
-- ============================================
CREATE TABLE `contracts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `employee_id` bigint unsigned NOT NULL,
  `contract_type_id` bigint unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NULL,
  `salary` decimal(10, 2) NOT NULL,
  `status` enum('active','inactive','suspended','terminated') NOT NULL DEFAULT 'active',
  `terms` text NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  `deleted_at` timestamp NULL,
  KEY `idx_contracts_deleted_at` (`deleted_at`),
  KEY `idx_contracts_employee_id` (`employee_id`),
  KEY `idx_contracts_contract_type_id` (`contract_type_id`),
  KEY `idx_contracts_status` (`status`),
  KEY `idx_contracts_start_date` (`start_date`),
  KEY `idx_contracts_end_date` (`end_date`),
  CONSTRAINT `fk_contracts_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_contracts_contract_type_id` FOREIGN KEY (`contract_type_id`) REFERENCES `contract_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. ATTENDANCES TABLE
-- ============================================
CREATE TABLE `attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `employee_id` bigint unsigned NOT NULL,
  `clock_in` timestamp NOT NULL,
  `clock_out` timestamp NULL,
  `break_start` timestamp NULL,
  `break_end` timestamp NULL,
  `duration_in_minutes` int unsigned NULL,
  `status` enum('checked_in','checked_out','on_break') NOT NULL DEFAULT 'checked_in',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  KEY `idx_attendances_employee_id` (`employee_id`),
  KEY `idx_attendances_clock_in` (`clock_in`),
  KEY `idx_attendances_status` (`status`),
  CONSTRAINT `fk_attendances_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 10. WORKLOGS TABLE
-- ============================================
CREATE TABLE `worklogs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `employee_id` bigint unsigned NOT NULL,
  `work_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NULL,
  `break_start` time NULL,
  `break_end` time NULL,
  `hours_worked` decimal(5, 2) NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `notes` text NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  KEY `idx_worklogs_employee_id` (`employee_id`),
  KEY `idx_worklogs_work_date` (`work_date`),
  KEY `idx_worklogs_status` (`status`),
  CONSTRAINT `fk_worklogs_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 11. HOURBANKS TABLE
-- ============================================
CREATE TABLE `hourbanks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `employee_id` bigint unsigned NOT NULL UNIQUE,
  `total_hours` decimal(8, 2) NOT NULL DEFAULT 0.00,
  `balance` decimal(8, 2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  CONSTRAINT `fk_hourbanks_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 12. TIMEOFF CATEGORIES TABLE
-- ============================================
CREATE TABLE `timeoff_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL UNIQUE,
  `type` enum('paid','unpaid','compensated') NOT NULL DEFAULT 'paid',
  `description` text NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 13. TIMEOFFS TABLE
-- ============================================
CREATE TABLE `timeoffs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `employee_id` bigint unsigned NOT NULL,
  `timeoff_category_id` bigint unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `duration_days` decimal(5, 2) NOT NULL,
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `reason` text NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  KEY `idx_timeoffs_employee_id` (`employee_id`),
  KEY `idx_timeoffs_timeoff_category_id` (`timeoff_category_id`),
  KEY `idx_timeoffs_status` (`status`),
  KEY `idx_timeoffs_start_date` (`start_date`),
  KEY `idx_timeoffs_end_date` (`end_date`),
  CONSTRAINT `fk_timeoffs_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_timeoffs_timeoff_category_id` FOREIGN KEY (`timeoff_category_id`) REFERENCES `timeoff_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 14. BENEFITS TABLE
-- ============================================
CREATE TABLE `benefits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `employee_id` bigint unsigned NOT NULL,
  `benefit_type` varchar(255) NOT NULL,
  `amount` decimal(10, 2) NOT NULL,
  `effective_date` date NOT NULL,
  `expiration_date` date NULL,
  `status` enum('active','inactive','expired') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  KEY `idx_benefits_employee_id` (`employee_id`),
  KEY `idx_benefits_status` (`status`),
  KEY `idx_benefits_effective_date` (`effective_date`),
  KEY `idx_benefits_expiration_date` (`expiration_date`),
  CONSTRAINT `fk_benefits_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SEED DATA
-- ============================================

INSERT INTO `countries` (`name`, `code`) VALUES ('Portugal', 'PT'), ('Brazil', 'BR');

INSERT INTO `designations` (`name`, `base_salary`, `level`) VALUES 
('Junior Developer', 1500.00, 'junior'),
('Senior Developer', 2500.00, 'senior'),
('Project Manager', 2000.00, 'manager'),
('Director', 3500.00, 'director');

INSERT INTO `departments` (`name`, `description`) VALUES 
('IT', 'Information Technology'),
('Human Resources', 'Human Resources'),
('Finance', 'Finance Department'),
('Operations', 'Operations Team');

INSERT INTO `contract_types` (`name`, `description`) VALUES 
('Full-Time', 'Full-time employment contract'),
('Part-Time', 'Part-time employment contract'),
('Fixed-Term', 'Fixed-term employment contract'),
('Intern', 'Internship contract');

INSERT INTO `timeoff_categories` (`name`, `type`) VALUES 
('Vacation', 'paid'),
('Sick Leave', 'paid'),
('Unpaid Leave', 'unpaid'),
('Compensatory Time', 'compensated');

-- ============================================
-- END OF SCRIPT
-- ============================================