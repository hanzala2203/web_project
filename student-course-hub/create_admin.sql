-- Create users table if not exists
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','admin') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create admin user (password will be: Admin@123)
INSERT INTO `users` (`username`, `email`, `password`, `role`) 
VALUES ('admin', 'admin@example.com', '$2y$10$Gg0KhvGRnHKoYGgLcBqKV.YZxrNQHBBcwWvBPHFAUvRswD6EHBbZy', 'admin');
