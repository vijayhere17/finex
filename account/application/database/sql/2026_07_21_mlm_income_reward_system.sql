-- =============================================================================
-- MLM Income System Extension SQL
-- Reuses existing tables — adds required columns only. No duplicate modules.
-- Run after backup. Prefer: php artisan migrate
-- =============================================================================

-- ---- users: registration fee + locked reward bonus ----
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `registration_fee` DECIMAL(18,2) NOT NULL DEFAULT 1,
  ADD COLUMN IF NOT EXISTS `reward_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `locked_reward_bonus` DECIMAL(18,2) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `unlocked_reward_bonus` DECIMAL(18,2) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `expired_reward_bonus` DECIMAL(18,2) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `locked_reward_lock_date` DATETIME NULL,
  ADD COLUMN IF NOT EXISTS `locked_reward_expiry_date` DATETIME NULL,
  ADD COLUMN IF NOT EXISTS `sponsor_unlock_done` TINYINT NOT NULL DEFAULT 0;

-- MySQL versions without IF NOT EXISTS on ADD COLUMN — use guarded statements:
-- ALTER TABLE `users` ADD COLUMN `registration_fee` DECIMAL(18,2) NOT NULL DEFAULT 1;
-- ALTER TABLE `users` ADD COLUMN `reward_id` BIGINT UNSIGNED NOT NULL DEFAULT 0;
-- ALTER TABLE `users` ADD COLUMN `locked_reward_bonus` DECIMAL(18,2) NOT NULL DEFAULT 0;
-- ALTER TABLE `users` ADD COLUMN `unlocked_reward_bonus` DECIMAL(18,2) NOT NULL DEFAULT 0;
-- ALTER TABLE `users` ADD COLUMN `expired_reward_bonus` DECIMAL(18,2) NOT NULL DEFAULT 0;
-- ALTER TABLE `users` ADD COLUMN `locked_reward_lock_date` DATETIME NULL;
-- ALTER TABLE `users` ADD COLUMN `locked_reward_expiry_date` DATETIME NULL;
-- ALTER TABLE `users` ADD COLUMN `sponsor_unlock_done` TINYINT NOT NULL DEFAULT 0;

-- ---- turnover_reward_masters: qualification + weekly salary ----
ALTER TABLE `turnover_reward_masters`
  ADD COLUMN `title` VARCHAR(100) NULL AFTER `milestone_order`,
  ADD COLUMN `required_directs` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `title`,
  ADD COLUMN `required_team` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `required_directs`,
  ADD COLUMN `required_self_business` DECIMAL(18,2) NOT NULL DEFAULT 0 AFTER `required_team`,
  ADD COLUMN `required_team_business` DECIMAL(18,2) NOT NULL DEFAULT 0 AFTER `required_self_business`,
  ADD COLUMN `weekly_salary` DECIMAL(18,2) NOT NULL DEFAULT 0 AFTER `cash_reward`;

-- ---- turnover_reward_achievers: weekly salary payout tracking ----
ALTER TABLE `turnover_reward_achievers`
  ADD COLUMN `weekly_salary` DECIMAL(18,2) NOT NULL DEFAULT 0 AFTER `cash_reward`,
  ADD COLUMN `directs_count` INT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN `team_count` INT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN `self_business` DECIMAL(18,2) NOT NULL DEFAULT 0,
  ADD COLUMN `team_business` DECIMAL(18,2) NOT NULL DEFAULT 0,
  ADD COLUMN `return_date` DATE NULL,
  ADD COLUMN `last_paid_at` DATETIME NULL,
  ADD COLUMN `weeks_paid` INT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN `status` TINYINT NOT NULL DEFAULT 0;

ALTER TABLE `turnover_reward_achievers` ADD INDEX `turnover_reward_achievers_status_index` (`status`);

-- ---- Seed / upsert 7 business-plan rewards ----
INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
VALUES
  (1, 'Reward 1', 5, 20, 100, 5000, 5000, 10, 10, NOW(), NOW()),
  (2, 'Reward 2', 6, 50, 200, 12000, 12000, 20, 20, NOW(), NOW()),
  (3, 'Reward 3', 7, 100, 300, 20000, 20000, 50, 50, NOW(), NOW()),
  (4, 'Reward 4', 8, 200, 500, 50000, 50000, 100, 100, NOW(), NOW()),
  (5, 'Reward 5', 9, 500, 700, 100000, 100000, 200, 200, NOW(), NOW()),
  (6, 'Reward 6', 10, 1000, 1000, 250000, 250000, 300, 300, NOW(), NOW()),
  (7, 'Reward 7', 14, 2000, 1500, 500000, 500000, 500, 500, NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `required_directs` = VALUES(`required_directs`),
  `required_team` = VALUES(`required_team`),
  `required_self_business` = VALUES(`required_self_business`),
  `required_team_business` = VALUES(`required_team_business`),
  `turnover_amount` = VALUES(`turnover_amount`),
  `cash_reward` = VALUES(`cash_reward`),
  `weekly_salary` = VALUES(`weekly_salary`),
  `updated_at` = NOW();

-- Note: ON DUPLICATE KEY requires unique on milestone_order (already present).

-- =============================================================================
-- earning_type reference (ewallet_logs)
-- 1  Direct Sponsor Income
-- 2  Daily ROI
-- 3  Cashback
-- 4  Level Income (ROI Override, 200 levels)
-- 5  Legacy Salary (dormant)
-- 6  DMC Leadership
-- 7  Reward Salary (weekly)
-- 8  Booster Income
-- 9  Life Time Reward
-- 10 Locked Reward Unlock (no TDS / fees)
-- =============================================================================
