-- ===============================================
-- Database Structure (Clean)
-- Generated from Laravel Migrations
-- ===============================================

-- ===============================================
-- BASE TABLES
-- ===============================================

-- Users Table
CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role ENUM('admin', 'hr', 'employee') DEFAULT 'employee',
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  remember_token VARCHAR(100) NULL,
  must_change_password BOOLEAN DEFAULT TRUE,
  password_changed_at TIMESTAMP NULL,
  employee_id BIGINT UNSIGNED NULL UNIQUE,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_users_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL
);

-- Geographical Tables
CREATE TABLE countries (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL,
  phonecode SMALLINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

CREATE TABLE states (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  country_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_states_country FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE CASCADE,
  INDEX idx_country_id (country_id)
);

CREATE TABLE cities (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  state_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_cities_state FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE CASCADE,
  INDEX idx_state_id (state_id)
);

-- Organization Tables
CREATE TABLE departments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

CREATE TABLE designations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL UNIQUE,
  description TEXT NULL,
  level ENUM('junior', 'pleno', 'senior') NULL,
  base_salary DECIMAL(8, 2) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

-- ===============================================
-- EMPLOYEE TABLES
-- ===============================================

CREATE TABLE employees (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  country_id BIGINT UNSIGNED NOT NULL,
  state_id BIGINT UNSIGNED NOT NULL,
  city_id BIGINT UNSIGNED NOT NULL,
  department_id BIGINT UNSIGNED NOT NULL,
  designation_id BIGINT UNSIGNED NULL,
  first_name VARCHAR(255) NOT NULL,
  middle_name VARCHAR(255) NULL,
  last_name VARCHAR(255) NOT NULL,
  gender ENUM('male', 'female', 'n/a') NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  nss CHAR(10) NOT NULL UNIQUE,
  nif CHAR(10) NOT NULL UNIQUE,
  phone_number VARCHAR(50) NOT NULL,
  observations TEXT NULL,
  address VARCHAR(1024) NOT NULL,
  zip_code CHAR(10) NOT NULL,
  date_of_birth DATE NOT NULL,
  date_hired DATE NOT NULL,
  is_active BOOLEAN DEFAULT TRUE,
  deleted_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_employees_country FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE CASCADE,
  CONSTRAINT fk_employees_state FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE CASCADE,
  CONSTRAINT fk_employees_city FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE,
  CONSTRAINT fk_employees_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
  CONSTRAINT fk_employees_designation FOREIGN KEY (designation_id) REFERENCES designations(id) ON DELETE CASCADE,
  INDEX idx_email (email),
  INDEX idx_nss (nss),
  INDEX idx_nif (nif),
  INDEX idx_department_id (department_id),
  INDEX idx_is_active (is_active),
  INDEX idx_date_hired (date_hired)
);

-- Add foreign key for users.employee_id
ALTER TABLE users ADD CONSTRAINT fk_users_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL;

-- ===============================================
-- CONTRACT TABLES
-- ===============================================

CREATE TABLE contract_types (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL UNIQUE,
  label VARCHAR(255) NOT NULL,
  description TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

CREATE TABLE contracts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  employee_id BIGINT UNSIGNED NULL,
  contract_type ENUM('full_time', 'temporary', 'internship', 'non_defined') DEFAULT 'non_defined',
  salary DECIMAL(10, 2) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NULL,
  status ENUM('active', 'terminated', 'suspended') DEFAULT 'active',
  date_hired DATE NOT NULL,
  deleted_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_contracts_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
  INDEX idx_employee_id (employee_id),
  INDEX idx_date_hired (date_hired),
  INDEX idx_status (status)
);

-- ===============================================
-- WORK & TIME TRACKING TABLES
-- ===============================================

CREATE TABLE worklogs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  employee_id BIGINT UNSIGNED NOT NULL,
  date DATE NOT NULL,
  description TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_worklogs_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
  INDEX idx_employee_id (employee_id),
  INDEX idx_date (date)
);

CREATE TABLE attendances (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  employee_id BIGINT UNSIGNED NOT NULL,
  work_date DATE NOT NULL,
  start_time TIME NULL,
  break_start TIME NULL,
  break_end TIME NULL,
  end_time TIME NULL,
  hours_worked INT NULL,
  extra_hours INT NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_attendances_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
  UNIQUE KEY unique_employee_date (employee_id, work_date),
  INDEX idx_employee_id (employee_id),
  INDEX idx_work_date (work_date)
);

CREATE TABLE hourbanks (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  employee_id BIGINT UNSIGNED NOT NULL,
  balance_hours DECIMAL(8, 2) DEFAULT 0,
  last_accrual_date DATE NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_hourbanks_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
  INDEX idx_employee_id (employee_id)
);

-- ===============================================
-- TIME OFF TABLES
-- ===============================================

CREATE TABLE timeoff_categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  key VARCHAR(255) NOT NULL UNIQUE,
  label VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

CREATE TABLE timeoffs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  employee_id BIGINT UNSIGNED NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  type VARCHAR(255) DEFAULT 'vacation',
  category_id BIGINT UNSIGNED NULL,
  status VARCHAR(255) DEFAULT 'pending',
  reason TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_timeoffs_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
  CONSTRAINT fk_timeoffs_category FOREIGN KEY (category_id) REFERENCES timeoff_categories(id) ON DELETE SET NULL,
  INDEX idx_employee_id (employee_id),
  INDEX idx_start_date (start_date),
  INDEX idx_status (status)
);

-- ===============================================
-- BENEFITS TABLE
-- ===============================================

CREATE TABLE benefits (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  employee_id BIGINT UNSIGNED NOT NULL,
  type VARCHAR(255) NOT NULL,
  provider VARCHAR(255) NULL,
  details TEXT NULL,
  start_date DATE NULL,
  end_date DATE NULL,
  active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_benefits_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
  INDEX idx_employee_id (employee_id),
  INDEX idx_type (type)
);

-- ===============================================
-- AUDIT & LOGGING TABLES
-- ===============================================

CREATE TABLE activity_log (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  log_name VARCHAR(255) NULL,
  description TEXT NULL,
  subject_id BIGINT UNSIGNED NULL,
  subject_type VARCHAR(255) NULL,
  causer_id BIGINT UNSIGNED NULL,
  causer_type VARCHAR(255) NULL,
  properties JSON NULL,
  batch_uuid VARCHAR(255) NULL,
  event VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_log_name (log_name),
  INDEX idx_subject (subject_id, subject_type),
  INDEX idx_causer (causer_id, causer_type)
);

-- ===============================================
-- LARAVEL FRAMEWORK TABLES
-- ===============================================

CREATE TABLE password_reset_tokens (
  email VARCHAR(255) PRIMARY KEY,
  token VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NULL
);

CREATE TABLE sessions (
  id VARCHAR(255) PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  ip_address VARCHAR(45) NULL,
  user_agent TEXT NULL,
  payload LONGTEXT NOT NULL,
  last_activity INT NOT NULL,
  CONSTRAINT fk_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_id (user_id),
  INDEX idx_last_activity (last_activity)
);

CREATE TABLE cache (
  key VARCHAR(255) PRIMARY KEY,
  value LONGTEXT NOT NULL,
  expiration INT NOT NULL
);

CREATE TABLE jobs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  queue VARCHAR(255) NOT NULL,
  payload LONGTEXT NOT NULL,
  attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
  reserved_at INT UNSIGNED NULL,
  available_at INT UNSIGNED NOT NULL,
  created_at INT UNSIGNED NOT NULL,
  INDEX idx_queue (queue)
);

CREATE TABLE job_batches (
  id VARCHAR(255) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  total_jobs INT NOT NULL,
  pending_jobs INT NOT NULL,
  failed_jobs INT NOT NULL,
  failed_job_ids LONGTEXT NOT NULL,
  options MEDIUMTEXT NULL,
  cancelled_at INT UNSIGNED NULL,
  created_at INT UNSIGNED NOT NULL,
  finished_at INT UNSIGNED NULL
);

CREATE TABLE failed_jobs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid VARCHAR(255) UNIQUE NOT NULL,
  connection TEXT NOT NULL,
  queue TEXT NOT NULL,
  payload LONGTEXT NOT NULL,
  exception LONGTEXT NOT NULL,
  failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===============================================
-- END OF DATABASE STRUCTURE
-- ===============================================
