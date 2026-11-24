-- PsyCare - Patient Tracker
-- Database schema (matches db.php: host localhost, user root, database psycare_db)
-- Import with:  mysql -u root -p < database.sql
-- or phpMyAdmin: Import tab -> choose this file

CREATE DATABASE IF NOT EXISTS psycare_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE psycare_db;

-- ---------------------------------------------------------
-- Practitioner accounts (register.php / login.php)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name     VARCHAR(100) NOT NULL,
  username      VARCHAR(30)  NOT NULL,
  email         VARCHAR(150) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Patients (add_patient.php / edit_patient.php / delete_patient.php)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS patients (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name  VARCHAR(150) NOT NULL,
  age        TINYINT UNSIGNED NOT NULL,
  contact    VARCHAR(100) DEFAULT NULL,
  disorder   VARCHAR(200) NOT NULL,
  status     ENUM('Active','Improving','Stable','Discharged') NOT NULL DEFAULT 'Active',
  notes      TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_patients_name (full_name),
  KEY idx_patients_status (status)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Appointments (add_appointment.php / edit_appointment.php)
-- Deleted automatically when their patient is deleted.
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS appointments (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  patient_id INT UNSIGNED NOT NULL,
  appt_date  DATE         NOT NULL,
  appt_time  TIME         NOT NULL,
  purpose    VARCHAR(255) DEFAULT NULL,
  room       VARCHAR(50)  NOT NULL,
  status     ENUM('Scheduled','Completed','Cancelled') NOT NULL DEFAULT 'Scheduled',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_appt_patient (patient_id),
  KEY idx_appt_slot (room, appt_date, appt_time, status),
  CONSTRAINT fk_appt_patient FOREIGN KEY (patient_id)
    REFERENCES patients (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Notes (add_notes.php / edit_notes.php / notes.php)
-- Deleted automatically when their patient is deleted.
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS notes (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  patient_id INT UNSIGNED NOT NULL,
  title      VARCHAR(120) NOT NULL,
  note_text  TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_notes_patient (patient_id),
  KEY idx_notes_updated (updated_at),
  CONSTRAINT fk_notes_patient FOREIGN KEY (patient_id)
    REFERENCES patients (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Activity history (history.php). Stores the patient NAME, not an id,
-- so entries stay readable after a patient is removed.
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS history (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  action_type  VARCHAR(50)  NOT NULL,
  patient_name VARCHAR(150) NOT NULL,
  details      VARCHAR(255) DEFAULT NULL,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_history_created (created_at)
) ENGINE=InnoDB;
