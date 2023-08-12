CREATE TABLE IF NOT EXISTS `emails_send_log` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `type` enum('before_3_days','before_1_day','after_expire_subscription') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `command_id` bigint NOT NULL,
  `email_id` bigint NOT NULL,
  `confirmed` smallint unsigned NOT NULL,
  `checked` smallint unsigned NOT NULL,
  `valid` smallint unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email_id` (`email_id`),
  KEY `emails_send_log_FK` (`command_id`),
  CONSTRAINT `emails_send_log_FK` FOREIGN KEY (`command_id`) REFERENCES `commands_queue` (`id`),
  CONSTRAINT `emails_send_log_ibfk_2` FOREIGN KEY (`email_id`) REFERENCES `emails` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;