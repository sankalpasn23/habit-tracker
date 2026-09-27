-- =============================================
-- HabitForge — MySQL Database Schema
-- Database Name: habit_tracker
-- =============================================

-- Create the database
CREATE DATABASE IF NOT EXISTS `habit_tracker`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `habit_tracker`;

-- =============================================
-- TABLE: users
-- Stores registered user accounts
-- =============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(30) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL COMMENT 'Hashed with password_hash()',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- TABLE: habits
-- Stores user-defined habits with categories
-- =============================================
CREATE TABLE IF NOT EXISTS `habits` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `category` VARCHAR(50) NOT NULL DEFAULT 'general',
  `emoji` VARCHAR(10) DEFAULT '✨',
  `frequency` VARCHAR(20) DEFAULT 'daily' COMMENT 'daily, weekdays, weekends, custom',
  `goal` VARCHAR(100) DEFAULT NULL COMMENT 'Optional daily goal description',
  `current_streak` INT(11) DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_habits_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- TABLE: habit_logs
-- Tracks daily habit completions
-- =============================================
CREATE TABLE IF NOT EXISTS `habit_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `habit_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `log_date` DATE NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_daily_log` (`habit_id`, `user_id`, `log_date`),
  KEY `idx_user_date` (`user_id`, `log_date`),
  CONSTRAINT `fk_logs_habit` FOREIGN KEY (`habit_id`) REFERENCES `habits` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- TABLE: messages
-- Stores contact form submissions
-- =============================================
CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(200) DEFAULT 'No Subject',
  `message` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- SAMPLE DATA (Optional — for testing)
-- =============================================

-- Insert a test user (password: Test1234)
INSERT INTO `users` (`username`, `email`, `password`, `created_at`) VALUES
('testuser', 'test@habitforge.app', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW());

-- Insert sample habits for the test user
INSERT INTO `habits` (`user_id`, `name`, `category`, `emoji`, `frequency`, `goal`, `current_streak`) VALUES
(1, 'Morning Meditation', 'mindfulness', '🧘', 'daily', '10 minutes', 12),
(1, 'Read 20 Pages', 'learning', '📖', 'daily', '20 pages', 8),
(1, 'Workout', 'fitness', '💪', 'daily', '30 minutes', 5),
(1, 'Drink 8 Glasses of Water', 'health', '💧', 'daily', '8 glasses', 15),
(1, 'Write in Journal', 'mindfulness', '📝', 'daily', '1 entry', 3),
(1, 'Practice Coding', 'learning', '💻', 'weekdays', '1 hour', 10);

-- Insert sample habit logs for today (to show some as completed)
INSERT INTO `habit_logs` (`habit_id`, `user_id`, `log_date`) VALUES
(1, 1, CURDATE()),
(2, 1, CURDATE()),
(4, 1, CURDATE());
