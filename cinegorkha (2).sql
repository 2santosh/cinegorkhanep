-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 25, 2025 at 08:19 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cinegorkha`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `profile_pic` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `name`, `email`, `password`, `profile_pic`) VALUES
(1, 'System Administrator', 'admin@gmail.com', '90b9aa7e25f80cf4f64e990b78a9fc5ebd6cecad', 'admin-icn.png');

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `log_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cast`
--

CREATE TABLE `cast` (
  `cast_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `genre_id` int(20) NOT NULL,
  `genre_name` varchar(200) NOT NULL,
  `description` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`genre_id`, `genre_name`, `description`) VALUES
(1, 'Action', 'Action films feature sequences of fast-paced fighting, stunts, and chases.'),
(2, 'Comedy', 'Comedy films are light-hearted and designed to make audiences laugh.'),
(3, 'Sci-Fi', 'Science Fiction films use speculative, science-based depictions of phenomena.'),
(4, 'Horror', 'Horror films are designed to frighten, shock, or disgust audiences.'),
(5, 'Drama', 'Drama films are serious, plot-driven, and focus on realistic characters.');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `like_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `movie_id` int(20) NOT NULL,
  `title` varchar(200) NOT NULL,
  `movie_code` varchar(200) NOT NULL,
  `genre_id` int(20) NOT NULL,
  `description` text NOT NULL,
  `release_year` year(4) NOT NULL,
  `duration` int(5) NOT NULL COMMENT 'Duration in minutes',
  `language` varchar(100) DEFAULT 'English',
  `poster_url` varchar(255) DEFAULT NULL,
  `trailer_url` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT 0.0,
  `rental_price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`movie_id`, `title`, `movie_code`, `genre_id`, `description`, `release_year`, `duration`, `language`, `poster_url`, `trailer_url`, `video_url`, `rating`, `rental_price`, `stock`, `created_at`) VALUES
(1, 'The Matrix', 'MV-398217054', 1, 'A computer hacker learns about the true nature of reality and his role in the war against its controllers.', '1999', 136, 'English', 'poster_matrix.jpg', 'https://youtube.com/watch?v=vKQi3bBA1y8', 'matrix_hd.mp4', 8.7, 4.99, 10, '2025-08-25 17:08:15.000000');

-- --------------------------------------------------------

--
-- Table structure for table `movie_cast`
--

CREATE TABLE `movie_cast` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `cast_id` int(11) NOT NULL,
  `character_name` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlists`
--

CREATE TABLE `playlists` (
  `playlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlists`
--

INSERT INTO `playlists` (`playlist_id`, `user_id`, `name`, `created_at`) VALUES
(1, 1, 'My Favorites', '2025-08-25 17:08:16');

-- --------------------------------------------------------

--
-- Table structure for table `playlist_movies`
--

CREATE TABLE `playlist_movies` (
  `id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlist_movies`
--

INSERT INTO `playlist_movies` (`id`, `playlist_id`, `movie_id`, `added_at`) VALUES
(1, 1, 1, '2025-08-25 17:08:16');

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `rental_id` int(20) NOT NULL,
  `rental_code` varchar(200) NOT NULL,
  `movie_id` int(20) NOT NULL,
  `title` varchar(200) NOT NULL,
  `user_id` int(20) NOT NULL,
  `user_name` varchar(200) NOT NULL,
  `rental_price` decimal(10,2) NOT NULL,
  `rental_date` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rentals`
--

INSERT INTO `rentals` (`rental_id`, `rental_code`, `movie_id`, `title`, `user_id`, `user_name`, `rental_price`, `rental_date`) VALUES
(1, 'TRX-EonxGhZ5', 1, 'The Matrix', 1, 'Sonim Lama', 4.99, '2025-08-25 17:08:15.000000');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `subscription_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','expired','canceled') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `sys_name` varchar(255) NOT NULL,
  `sys_tagline` varchar(255) NOT NULL,
  `sys_logo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `sys_name`, `sys_tagline`, `sys_logo`) VALUES
(1, 'CineGorkha', 'Your one-stop shop for entertainment.', 'cinegorkha_logo.png');

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `upload_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`upload_id`, `admin_id`, `movie_id`, `uploaded_at`) VALUES
(1, 1, 1, '2025-08-25 17:08:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `profile_pic` varchar(200) DEFAULT NULL,
  `user_number` varchar(200) NOT NULL,
  `login_status` enum('online','offline') NOT NULL DEFAULT 'offline',
  `last_login` datetime DEFAULT NULL,
  `total_watch_time` int(11) NOT NULL DEFAULT 0,
  `role` enum('guest','user','admin','super_admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `profile_pic`, `user_number`, `login_status`, `last_login`, `total_watch_time`, `role`) VALUES
(1, 'Sonim Lama', 'sonim@gmail.com', '4637801e844fe91185cd683db98defeb1a4e67e9', 'pexels-pixabay-33045.jpg', 'MOVIE-USER-8201', 'offline', NULL, 45, 'user'),
(2, 'John Doe', 'user@example.com', '$2y$10$examplehash', NULL, '', 'offline', NULL, 0, 'user'),
(3, 'Admin One', 'admin@example.com', '$2y$10$examplehash', NULL, '', 'offline', NULL, 0, 'admin'),
(4, 'Super Admin', 'superadmin@example.com', '$2y$10$examplehash', NULL, '', 'offline', NULL, 0, 'super_admin');

-- --------------------------------------------------------

--
-- Table structure for table `watchlist`
--

CREATE TABLE `watchlist` (
  `watchlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `watch_history`
--

CREATE TABLE `watch_history` (
  `history_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `watch_time` int(11) NOT NULL DEFAULT 0 COMMENT 'minutes watched in this session',
  `watched_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `watch_history`
--

INSERT INTO `watch_history` (`history_id`, `user_id`, `movie_id`, `watch_time`, `watched_at`) VALUES
(1, 1, 1, 45, '2025-08-25 17:08:16');

--
-- Triggers `watch_history`
--
DELIMITER $$
CREATE TRIGGER `wh_ad_sub_time` AFTER DELETE ON `watch_history` FOR EACH ROW BEGIN
  UPDATE `users`
    SET `total_watch_time` = GREATEST(0, `total_watch_time` - OLD.`watch_time`)
  WHERE `user_id` = OLD.`user_id`;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `wh_ai_add_time` AFTER INSERT ON `watch_history` FOR EACH ROW BEGIN
  UPDATE `users`
    SET `total_watch_time` = `total_watch_time` + NEW.`watch_time`
  WHERE `user_id` = NEW.`user_id`;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `wh_au_adj_time` AFTER UPDATE ON `watch_history` FOR EACH ROW BEGIN
  UPDATE `users`
    SET `total_watch_time` = `total_watch_time` + (NEW.`watch_time` - OLD.`watch_time`)
  WHERE `user_id` = NEW.`user_id`;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `uniq_admins_email` (`email`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_admin_logs_admin_date` (`admin_id`,`created_at`);

--
-- Indexes for table `cast`
--
ALTER TABLE `cast`
  ADD PRIMARY KEY (`cast_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`genre_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`like_id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`movie_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`movie_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `movie_cast`
--
ALTER TABLE `movie_cast`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `cast_id` (`cast_id`);

--
-- Indexes for table `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`playlist_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `playlist_movies`
--
ALTER TABLE `playlist_movies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_playlist_movie` (`playlist_id`,`movie_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`rental_id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`upload_id`),
  ADD UNIQUE KEY `uniq_admin_movie_upload` (`admin_id`,`movie_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uniq_users_email` (`email`);

--
-- Indexes for table `watchlist`
--
ALTER TABLE `watchlist`
  ADD PRIMARY KEY (`watchlist_id`),
  ADD UNIQUE KEY `unique_watchlist` (`user_id`,`movie_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `watch_history`
--
ALTER TABLE `watch_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `idx_wh_user_date` (`user_id`,`watched_at`),
  ADD KEY `idx_wh_movie_date` (`movie_id`,`watched_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cast`
--
ALTER TABLE `cast`
  MODIFY `cast_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `genre_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `movie_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `movie_cast`
--
ALTER TABLE `movie_cast`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playlists`
--
ALTER TABLE `playlists`
  MODIFY `playlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `playlist_movies`
--
ALTER TABLE `playlist_movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `rental_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `subscription_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `upload_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `watchlist`
--
ALTER TABLE `watchlist`
  MODIFY `watchlist_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `watch_history`
--
ALTER TABLE `watch_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE;

--
-- Constraints for table `movies`
--
ALTER TABLE `movies`
  ADD CONSTRAINT `movies_ibfk_1` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`genre_id`);

--
-- Constraints for table `movie_cast`
--
ALTER TABLE `movie_cast`
  ADD CONSTRAINT `movie_cast_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movie_cast_ibfk_2` FOREIGN KEY (`cast_id`) REFERENCES `cast` (`cast_id`) ON DELETE CASCADE;

--
-- Constraints for table `playlists`
--
ALTER TABLE `playlists`
  ADD CONSTRAINT `playlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `playlist_movies`
--
ALTER TABLE `playlist_movies`
  ADD CONSTRAINT `playlist_movies_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`playlist_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `playlist_movies_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE;

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`),
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `uploads`
--
ALTER TABLE `uploads`
  ADD CONSTRAINT `uploads_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `uploads_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE;

--
-- Constraints for table `watchlist`
--
ALTER TABLE `watchlist`
  ADD CONSTRAINT `watchlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `watchlist_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE;

--
-- Constraints for table `watch_history`
--
ALTER TABLE `watch_history`
  ADD CONSTRAINT `watch_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `watch_history_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
