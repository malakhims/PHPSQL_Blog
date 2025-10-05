-- Add `visible` column if it doesn't exist (for old users)
ALTER TABLE `logs`
  ADD COLUMN IF NOT EXISTS `visible` ENUM('y','n') NOT NULL DEFAULT 'y';

-- Optional: ensure old rows are updated to a default visible value
UPDATE `logs`
SET `visible` = 'y'
WHERE `visible` IS NULL;
