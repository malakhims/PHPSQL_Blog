-- Full table structure with `visible` column for new users
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category` varchar(50) DEFAULT 'Uncategorized',
  `tags` varchar(255) DEFAULT '',
  `anchor_name` varchar(16) NOT NULL DEFAULT 'unknown',
  `visible` ENUM('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- For old users: if the table already exists, just add `visible` column
ALTER TABLE `logs`
  ADD COLUMN IF NOT EXISTS `visible` ENUM('y','n') NOT NULL DEFAULT 'y';

-- Set existing NULL values to 'n' so old drafts are hidden
UPDATE `logs`
SET `visible` = 'n'
WHERE `visible` IS NULL;
