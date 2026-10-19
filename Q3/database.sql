CREATE DATABASE IF NOT EXISTS java_student_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE java_student_portal;
CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(80) NOT NULL UNIQUE,
 password_hash VARCHAR(100) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS students (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 roll_number VARCHAR(30) NOT NULL UNIQUE,
 email VARCHAR(190) NOT NULL,
 course VARCHAR(150) NOT NULL,
 file_path VARCHAR(500) NULL,
 file_type VARCHAR(100) NULL
) ENGINE=InnoDB;
-- Create a demo user after building: use the included AdminSeed utility, or insert a BCrypt hash generated with org.mindrot.jbcrypt.BCrypt.
