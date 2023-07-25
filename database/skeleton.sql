CREATE DATABASE IF NOT EXISTS `app` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

CREATE TABLE `users` (
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `validts` bigint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uuid`),
  UNIQUE KEY `users_username_IDX` (`username`) USING BTREE,
  KEY `users_validts_IDX` (`validts`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `emails` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `checked` tinyint(1) NOT NULL DEFAULT '0',
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  `is_last` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `emails_email_user_uuid_IDX` (`email`,`user_uuid`) USING BTREE,
  KEY `emails_user_uuid_IDX` (`user_uuid`,`is_last`) USING BTREE,
  CONSTRAINT `emails_ibfk_1` FOREIGN KEY (`user_uuid`) REFERENCES `users` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE PROCEDURE IF NOT EXISTS fill_data()
BEGIN

	DECLARE i INT DEFAULT 0;
	DECLARE first_of_sequence INT DEFAULT 100;
	DECLARE iteration_of_sequence INT DEFAULT 10;
	DECLARE amount_of_rows INT DEFAULT 0;
    DECLARE coeff_q INT DEFAULT 2;
    DECLARE percent_to_update INT DEFAULT 1;

   	SET foreign_key_checks = 0;
	TRUNCATE emails;
	TRUNCATE users;
	TRUNCATE emails_send_log;
	TRUNCATE commands_queue;
	SET foreign_key_checks = 1;

	#geometric sequence
	SET amount_of_rows = first_of_sequence + (coeff_q*(first_of_sequence*POW(coeff_q, iteration_of_sequence - 1)) - first_of_sequence)/(coeff_q -1);

	#generate first users package
	WHILE i < first_of_sequence DO
		INSERT INTO `users` (`uuid`,`username`,`validts`) VALUES (
			UUID()
			, CONCAT_WS('-', 'username', i)
			, 0
		);
		SET i = i + 1;
	END WHILE;

	SET i = 0;

	#insert users by packages
	WHILE i < iteration_of_sequence DO
		INSERT INTO `users` (`uuid`,`username`,`validts`)
		SELECT
			UUID()
			, CONCAT_WS('-', `username`, i)
			, 0
		FROM `users`;
		SET i = i + 1;
	END WHILE;

	#insert emails for everyone users
	INSERT INTO `emails` (`email`, `user_uuid`, `confirmed`, `checked`, `valid`, `is_last`)
	SELECT
		CONCAT('email', ROW_NUMBER() OVER (), '@', 'localhost.ru')
		, `uuid`
		, 0
		, 0
		, 0
		, 1
	FROM `users`;

	SET percent_to_update = amount_of_rows * 0.2;

	#set validts
	WITH `users_to_update` AS (
		SELECT `uuid`
		, FLOOR(RAND()*(5-1))*1 as `random_integer`
		FROM `users`
		ORDER BY RAND()
		LIMIT percent_to_update
	)
	UPDATE users, `users_to_update`
	SET validts = CASE
		WHEN random_integer = 0 THEN UNIX_TIMESTAMP() + 86400 #expires after 1 day
		WHEN random_integer = 1 THEN UNIX_TIMESTAMP() + 259200 #expires after 3 day
		WHEN random_integer = 2 THEN UNIX_TIMESTAMP() + 864000 #expires after 10 day
		ELSE UNIX_TIMESTAMP() - 864000 #expired
	END
	WHERE users.`uuid` = `users_to_update`.`uuid`;

	SET percent_to_update = amount_of_rows * 0.15;

	#set confirmed
    UPDATE emails
	SET confirmed = 1
	ORDER BY RAND()
	LIMIT percent_to_update;
END