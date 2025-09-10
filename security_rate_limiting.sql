-- Tabla para rate limiting de login
CREATE TABLE IF NOT EXISTS `siona_pos`.`login_attempts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ip_address` VARCHAR(45) NOT NULL,
  `email` VARCHAR(150) NULL,
  `attempts` INT(11) DEFAULT 1,
  `blocked_until` DATETIME NULL,
  `last_attempt` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_ip_email` (`ip_address` ASC, `email` ASC),
  INDEX `idx_blocked_until` (`blocked_until` ASC)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;