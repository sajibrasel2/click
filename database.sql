-- Database structure for Adsterra click tracker

CREATE DATABASE IF NOT EXISTS `adsterra_clicks` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `adsterra_clicks`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(150) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `sites` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `ad_url` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `clicks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `site_id` INT NOT NULL,
  `user_id` INT NULL,
  `clicked_at` DATETIME NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  FOREIGN KEY (`site_id`) REFERENCES `sites`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`username`, `password`, `fullname`, `role`) VALUES
('user1', '$2y$10$NzPJRrFSwTda0eNg37g1nu0l1l7tOIu61QO96Zb1E5TG3gyvXxh16', 'Site 1 Manager', 'user'),
('user2', '$2y$10$NzPJRrFSwTda0eNg37g1nu0l1l7tOIu61QO96Zb1E5TG3gyvXxh16', 'Site 2 Manager', 'user'),
('user3', '$2y$10$NzPJRrFSwTda0eNg37g1nu0l1l7tOIu61QO96Zb1E5TG3gyvXxh16', 'Site 3 Manager', 'user'),
('user4', '$2y$10$NzPJRrFSwTda0eNg37g1nu0l1l7tOIu61QO96Zb1E5TG3gyvXxh16', 'Site 4 Manager', 'user'),
('user5', '$2y$10$NzPJRrFSwTda0eNg37g1nu0l1l7tOIu61QO96Zb1E5TG3gyvXxh16', 'Site 5 Manager', 'user');

INSERT INTO `sites` (`name`, `url`, `ad_url`) VALUES
('সাইট ১', 'https://example.com/site1', 'https://example.com/site1'),
('সাইট ২', 'https://example.com/site2', 'https://example.com/site2'),
('সাইট ৩', 'https://example.com/site3', 'https://example.com/site3'),
('সাইট ৪', 'https://example.com/site4', 'https://example.com/site4'),
('সাইট ৫', 'https://example.com/site5', 'https://example.com/site5');
