CREATE TABLE IF NOT EXISTS `commands_queue` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `command` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `command_pid` bigint DEFAULT NULL,
  `parent_command_id` bigint DEFAULT NULL,
  `status` enum('created','started','success','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `command_pid` (`command_pid`),
  KEY `parent_command_id` (`parent_command_id`),
  CONSTRAINT `commands_execution_log_ibfk_2` FOREIGN KEY (`parent_command_id`) REFERENCES `commands_queue` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;